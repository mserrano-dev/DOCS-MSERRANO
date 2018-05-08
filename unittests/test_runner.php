<?php
$root = dirname(__DIR__);
require_once($root . '/vendor/autoload.php');
require_once($root . '/library/php/autoloader.php');
autoloader::library($root);

// --- run it ---
$src_dir    = $argv[1]; // source directory for test files
$is_initial = $argv[2] ?? true; // will treat as descendent if present
$blacklist  = helper_fs::blacklist();

$obj    = new test_runner($root, $is_initial);
$to_run = $obj->run_tests($src_dir, $blacklist); // recursive, will return the base case

if (is_null($to_run) === false) {
    $single_test = new test_file($root, $to_run);
    $single_test->run_test();
}

class test_runner {

    protected $root;
    protected $is_initial;
    protected $list_error;
    protected $lock_file;

    public function __construct($root_dir, $is_initial) {
        $this->root       = $root_dir;
        $this->is_initial = ($is_initial === true);
        $this->list_error = array();
        $this->lock_file  = new lock_file($this->root . '/tmp/php-coverage-report/lock.file');

        if ($this->is_initial === true) {
            $this->lock_file->get_lock();
            $this->output_start_banner();
        }
    }

    public function __destruct() {
        if ($this->is_initial === true) {
            $this->lock_file->release();
            $this->output_end_banner();
        }
    }

    public function run_tests($src_dir, $blacklist) {
        $return = null;
        if (empty($src_dir) === false) {
            if (is_dir($src_dir) === true) {
                $pattern = sprintf('%s/*', rtrim($src_dir, '/'));
                $files   = glob($pattern);
                foreach ($files as $path) {
                    $this->do_command(sprintf('php %s %s -d %s', __FILE__, $path, '2>&1'));
                }
            } else {
                if ($this->is_initial === true) {
                    $this->do_command(sprintf('php %s %s -d %s', __FILE__, $src_dir, '2>&1'));
                } else {
                    $name = pathinfo($src_dir, PATHINFO_BASENAME);
                    if (in_array($name, $blacklist) === false) {
                        $return = $src_dir; // base case: src_dir is file, run tests
                    }
                }
            }
        }
        return $return;
    }

    // --- functions ---
    protected function output_start_banner() {

        $arrows = helper_bash::style('cyan:blink', '>>>');
        $date   = helper_bash::style('cyan', date('Y-m-d H:i:s (g:i:s A e)'));

        echo sprintf('%1$s%1$s%1$s%2$s TEST RUN: %3$s%1$s%1$s', PHP_EOL, $arrows, $date);
        echo sprintf("%s%s", helper_bash::style('cyan', '+++'), PHP_EOL);
    }

    protected function output_end_banner() {
        echo sprintf("%s%s", helper_bash::style('cyan', '+++'), PHP_EOL);
        foreach ($this->list_error as $name => $err_lines) {
            $o_arrows = helper_bash::style('light_red', '>>>');
            $o_name   = helper_bash::style('bold:light_red', sprintf("%s", $name));
            $o_lines  = helper_bash::style('light_red', implode("\e[0m,\e[91m", $err_lines));
            echo sprintf("%s error on %s - line%s: %s%s", $o_arrows, $o_name, (count($err_lines) > 1 ? 's' : ''), $o_lines, PHP_EOL);
        }
    }

    // --- helpers ---
    private function do_command($cmd) {
        $output = array();
        exec($cmd, $output); // 0=stdin, 1=stdout, 2=stderr or check /usr/include/unistd.h
        foreach ($output as $line) {
            $pattern = '/test_runner.php|OK|FAILURES!!!|Test cases run:.+/';
            if (preg_match($pattern, $line) === 0) {
                echo sprintf("%s %s", $line, PHP_EOL);
            }
            if (preg_match('/(\/[a-z_\.]+\.php).*line ([0-9]+)/', $line, $matches) !== 0) {
                $name        = $matches[1];
                $line_number = $matches[2];

                if (isset($this->list_error[$name]) === false) {
                    $this->list_error[$name] = array();
                }
                $this->list_error[$name][] = $line_number;
            }
        }
    }

}

class lock_file {

    protected $filename;
    protected $lock;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function get_lock() {
        touch($this->filename);
        $this->lock = fopen($this->filename, 'r+');
        flock($this->lock, LOCK_EX);
    }

    public function release() {
        flock($this->lock, LOCK_UN);
        fclose($this->lock);
        if (file_exists($this->filename) === true) {
            unlink($this->filename);
        }
    }

}

class test_file {

    protected $root;
    protected $ut_dir;
    protected $path;
    protected $name;
    //
    protected $ut_class;
    protected $class;
    protected $coverage;
    protected $error;

    public function __construct($root_dir, $ut_file) {
        $this->root = $root_dir;
        list($this->ut_dir, $this->path, $this->name) = $this->dissect_convention($ut_file);

        $this->ut_class = sprintf('%s/%s/%s/%s.test.php', $this->root, $this->ut_dir, $this->path, $this->name);
        $this->class    = sprintf('%s/%s/%s.php', $this->root, $this->path, $this->name);

        $this->error = null;
        if (file_exists($this->class) === false) {
            $this->error = array(
                'file'        => $this->ut_class,
                'msg'         => 'no corresponding class found',
                'exclamation' => helper_bash::exclamation('flip'),
            );
        }

        $this->coverage = new SebastianBergmann\CodeCoverage\CodeCoverage;
        $this->coverage->filter()->addDirectoryToWhitelist($this->class);
        $this->coverage->start($this->ut_class);
    }

    public function __destruct() {
        $this->coverage->stop();

        if (is_null($this->error) === true) {
            $this->output_report_to_terminal();
        } else {
            $this->output_error_message();
        }
    }

    public function run_test() {
        if (is_null($this->error) === true) {
            require_once('./unittests/silent_autorun.php');
            require_once($this->ut_class);
            require_once($this->class);
        }
    }

    // --- functions ---
    protected function output_report_to_terminal() {
        $report = $this->coverage->getReport();
        $info   = array();
        foreach ($report as $item) {
            $classes = $item->getClassesAndTraits();
            foreach ($classes as $className => $class) {
                if ($className === $this->name) {
                    $count_missing = 0;
                    foreach ($class['methods'] as $method_name => $method_info) {
                        if ($method_info['coverage'] === 0) {
                            $count_missing += 1;
                        }
                    }
                    $info['missing']           = $count_missing;
                    $info['coverage']          = $class['coverage'];
                    $info['statements']        = $class['executableLines'];
                    $info['coveredstatements'] = $class['executedLines'];
                }
            }
        }
        $this->output_coverage_test($info);
    }

    protected function output_coverage_test($info) {
        $o_path  = helper_bash::style('', sprintf('/%s/%s', $this->ut_dir, $this->path));
        $o_class = helper_bash::style('cyan', '/' . sprintf('%s.test.php', $this->name));
        $o_stmt  = helper_bash::style('blue:light_gray_bg', sprintf('%.2f%%', $info['coverage']));
        if ($info['missing'] > 0) {
            $o_untested = sprintf(" - %s method%s untested !!", helper_bash::style('bold', $info['missing']), ($info['missing'] > 1 ? 's' : ''));
        }
        echo sprintf('%s%s: %s%s%s', $o_path, $o_class, $o_stmt, $o_untested ?? '', PHP_EOL);
    }

    protected function output_error_message() {
        $file = helper_bash::style('light_red:inverted', $this->error['file']);
        $deco = helper_bash::style('light_red', '!!');
        $msg  = helper_bash::style('light_red', $this->error['msg']);

        echo sprintf("%s %s %s %s%s%s", $deco, $file, $deco, $msg, $this->error['exclamation'], PHP_EOL);
    }

    // --- helpers ---
    private function dissect_convention($ut_path) {
        $info       = pathinfo($ut_path);
        $class_name = explode('.', $info['filename'])[0];

        $list_path = explode('/', $info['dirname']);
        $ut_dir    = array_shift($list_path);

        return array($ut_dir, join('/', $list_path), $class_name);
    }

}
