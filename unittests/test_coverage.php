<?php
$root = dirname(__DIR__);
require_once($root . '/library/php/autoloader.php');
autoloader::library($root);

// --- run it ---
ob_start();

$src_dir    = $argv[1]; // source directory for test files
$silent_run = ($argv[2] ?? ''); // will not open browser if present
$blacklist  = helper_fs::blacklist();

$runner = new test_runner($root, $blacklist, $silent_run);
$runner->run_tests($src_dir);

error_log(ob_get_clean()); // deferred output

class test_runner {

    const valid_unittest   = '/^unittests(\/[a-z_]+)+\.test\.php$/'; // the convention
    //
    const report_testname  = 'mserrano - test_runner';
    const report_dest_html = '/tmp/php-coverage-report';
    const report_dest_xml  = '/tmp/php-coverage-report/index.xml';
    const report_browser   = 'google-chrome';

    protected $root;
    protected $blacklist;
    protected $coverage;
    protected $error;
    protected $test_count;
    protected $silent;

    public function __construct($root_dir, $blacklist, $silent) {
        $this->root      = $root_dir;
        $this->blacklist = $blacklist;

        require_once($this->root . '/vendor/autoload.php');
        require_once($this->root . '/unittests/silent_autorun.php');

        $filter           = new SebastianBergmann\CodeCoverage\Filter();
        $this->coverage   = new SebastianBergmann\CodeCoverage\CodeCoverage(null, $filter);
        $this->error      = null;
        $this->test_count = 0;
        $this->silent     = (empty($silent) == false);

        $this->output_start_banner();
    }

    public function __destruct() {
        $this->coverage->stop();

        if (is_null($this->error) === true) {
            $this->create_coverage_report();
            $this->open_report_in_browser();
            $this->open_report_in_terminal();
        } else {
            $this->output_error_message();
        }
    }

    public function run_tests($arg) {
        $dir = rtrim($arg, '/');

        $this->whitelist_corresponding($dir);
        $this->test_directory($dir);
        $this->coverage->start(test_runner::report_testname);

        if ($this->test_count === 0) {
            $this->error = array(
                'file'        => 'NO TESTS',
                'msg'         => 'none or all invalid',
                'exclamation' => helper_bash::exclamation('kamehameha'),
            );
        }
    }

    // --- output ---
    private function output_start_banner() {
        $arrows = helper_bash::style('cyan:blink', '>>>');
        $date   = helper_bash::style('cyan', date('Y-m-d H:i:s (g:i:s A e)'));

        echo sprintf("%s%s%s TEST RUN: %s%s%s", PHP_EOL, PHP_EOL, $arrows, $date, PHP_EOL, PHP_EOL);
    }

    private function output_report_decorator() {
        echo sprintf("%s%s", helper_bash::style('cyan', '+++'), PHP_EOL);
    }

    private function output_coverage_test($info) {
        $statements = '';
        if ($info['statements'] !== 0) {
            $stmt_coverage = (($info['coveredstatements'] / $info['statements']) * 100);
            $statements    = sprintf('%.2f%%', $stmt_coverage);
        }
        $list_path  = explode('/', $info['file']);
        $class_name = array_pop($list_path);

        $o_path     = sprintf("%s", implode('/', $list_path));
        $o_class    = helper_bash::style('cyan', '/' . $class_name);
        $o_stmt     = helper_bash::style('blue:light_gray_bg', $statements);
        $o_untested = '';
        if ($info['methods'] > $info['coveredmethods'] === true) {
            $count_missing = ($info['methods'] - $info['coveredmethods']);
            $o_untested    = sprintf(" - %s method%s untested !!", helper_bash::style('bold', $count_missing), ($count_missing > 1 ? 's' : ''));
        }
        echo sprintf('%s%s: %s%s%s', $o_path, $o_class, $o_stmt, $o_untested, PHP_EOL);
    }

    private function output_end_stats($total) {
        $label_lines_covered = helper_bash::style('bold', 'Total Line Coverage');
        $lines_covered       = helper_bash::style('blue:light_gray_bg', sprintf("%.2f%%", $total['stat']['percent']));
        $lines_as_fration    = sprintf(" (%s/%s) lines tested", $total['covered']['lines'], $total['uncovered']['lines']);
        $blue_dot            = helper_bash::style('cyan', '.');
        $count_missing       = helper_bash::style('bold', $total['stat']['missing']);
        $blue_exclamation    = helper_bash::style('cyan', ' !!');

        $total_lines = sprintf("%s: %s%s%s", $label_lines_covered, $lines_covered, $lines_as_fration, $blue_dot);
        if ($total['stat']['missing'] > 0) {
            $total_methods = sprintf("%s method%s untested%s", $count_missing, ($total['stat']['missing'] > 1 ? 's' : ''), $blue_exclamation);
        }
        echo sprintf("%s %s%s", $total_lines, $total_methods ?? '', PHP_EOL);
    }

    private function output_error_message() {
        $file = helper_bash::style('light_red:inverted', $this->error['file']);
        $deco = helper_bash::style('light_red', '!!');
        $msg  = helper_bash::style('light_red', $this->error['msg']);

        echo sprintf("%s%s %s %s %s%s%s", PHP_EOL, $deco, $file, $deco, $msg, $this->error['exclamation'], PHP_EOL);
    }

    // --- functions ---
    protected function open_report_in_browser() {
        if ($this->silent === false) {
            exec(sprintf('%s tmp/php-coverage-report/index.html', test_runner::report_browser));
        }
    }

    private function open_report_in_terminal() {
        $xml_file = file_get_contents($this->root . test_runner::report_dest_xml);
        $report   = simplexml_load_string($xml_file);

        $this->output_report_decorator();
        $total = array(
            'covered'   => array('lines' => 0, 'methods' => 0),
            'uncovered' => array('lines' => 0, 'methods' => 0),
            'stat'      => array('percent' => 0, 'missing' => 0),
        );
        foreach ($report->project as $obj) {
            foreach ($obj->file as $file_info) {
                $info = array(
                    'file'                => (string) $file_info->attributes()->name,
                    'methods'             => (integer) $file_info->metrics->attributes()->methods,
                    'coveredmethods'      => (integer) $file_info->metrics->attributes()->coveredmethods,
                    'conditionals'        => (integer) $file_info->metrics->attributes()->conditionals,
                    'coveredconditionals' => (integer) $file_info->metrics->attributes()->coveredconditionals,
                    'statements'          => (integer) $file_info->metrics->attributes()->statements,
                    'coveredstatements'   => (integer) $file_info->metrics->attributes()->coveredstatements,
                );
                $this->output_coverage_test($info);

                $total['covered']['lines']     += $info['coveredstatements'];
                $total['uncovered']['lines']   += $info['statements'];
                $total['covered']['methods']   += $info['coveredmethods'];
                $total['uncovered']['methods'] += $info['methods'];
            }
            $total['stat']['percent'] = (($total['covered']['lines'] / $total['uncovered']['lines']) * 100);
            $total['stat']['missing'] = ($total['uncovered']['methods'] - $total['covered']['methods']);
        }
        $this->output_report_decorator();
        $this->output_end_stats($total);
    }

    protected function create_coverage_report() {
        $writer = new SebastianBergmann\CodeCoverage\Report\Html\Facade();
        $writer->process($this->coverage, $this->root . test_runner::report_dest_html);

        $writer = new SebastianBergmann\CodeCoverage\Report\Clover();
        $writer->process($this->coverage, $this->root . test_runner::report_dest_xml);

        $writer = null;
    }

    protected function whitelist_corresponding($ut_root) {
        $is_subdir = (count(explode('/', $ut_root)) !== 1);
        if ($is_subdir === true) {
            $actual_dir = $this->strip_ut_dir($ut_root);
            if (is_dir($ut_root) === false) {
                // if file, test only that file
                list($ut_dir, $path, $classname) = $this->dissect_convention($ut_root);
                $actual_file = sprintf('%s/%s.php', $path, $classname);
                $this->do_whitelist($actual_file);
            } else {
                // if subdir of unittests, traverse it (the actual subdir)
                $this->traverse_directory($actual_dir, array($this, 'do_whitelist'));
            }
        } else {
            // foreach subdir in unittests, traverse the actual subdir
            $files = glob(sprintf('%s/*', $ut_root));
            foreach ($files as $path) {
                if (is_dir($path) === true) {
                    $actual_dir = $this->strip_ut_dir($path);
                    $this->traverse_directory($actual_dir, array($this, 'do_whitelist'));
                } else {
                    $this->do_whitelist($path); // for files at subdir root
                }
            }
        }
    }

    protected function test_directory($dir) {
        if (is_dir($dir) === true) {
            $this->traverse_directory($dir, array($this, 'do_test_case'));
        } else {
            $this->do_test_case($dir); // single file
        }
    }

    // --- helpers ---
    private function do_whitelist($path) {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name      = pathinfo($path, PATHINFO_BASENAME);

        if (($extension === 'php') && (in_array($name, $this->blacklist) === false)) {
            $list_dir = explode('/', pathinfo($path, PATHINFO_DIRNAME));
            if (in_array('csts', $list_dir) === false) { // ignore /csts
                $class = sprintf('./%s', $path);
                $this->coverage->filter()->addDirectoryToWhitelist($class);
            }
        }
    }

    private function do_test_case($ut_filename) {
        $name = pathinfo($ut_filename, PATHINFO_BASENAME);
        if (in_array($name, $this->blacklist) === false) {
            list($ut_dir, $path, $classname) = $this->dissect_convention($ut_filename);

            $utclass = sprintf('./%s/%s/%s.test.php', $ut_dir, $path, $classname);
            $class   = sprintf('./%s/%s.php', $path, $classname);

            $continue = true;
            $continue = $continue && (preg_match(test_runner::valid_unittest, $ut_filename, $matches));
            $continue = $continue && (empty($matches[0]) === false); // matched the full pattern
            $continue = $continue && (file_exists($class) === true);
            $continue = $continue && (file_exists($utclass) === true);

            if ($continue === true) {
                require_once($utclass);

                $this->test_count += 1;
            } else {
                $this->error = array(
                    'file'        => $ut_filename,
                    'msg'         => 'no corresponding class found',
                    'exclamation' => helper_bash::exclamation('flip'),
                );
            }
        }
    }

    private function traverse_directory($dir, $function) {
        $files = glob(sprintf('%s/*', $dir));
        foreach ($files as $path) {
            if (is_dir($path) === true) {
                $this->traverse_directory($path, $function);
            } else {
                call_user_func($function, $path);
            }
        }
    }

    private function strip_ut_dir($path) {
        $list_path = explode('/', $path);
        array_shift($list_path);

        return sprintf('%s', implode('/', $list_path));
    }

    private function dissect_convention($ut_filename) {
        $path      = explode('.', $ut_filename)[0];
        $list_path = explode('/', $path);
        $ut_dir    = array_shift($list_path);
        $classname = array_pop($list_path);

        return array($ut_dir, join('/', $list_path), $classname);
    }

    private function exclamation($type) {
        $dot_dot_dot = helper_bash::style('light_red', '...');
        $guy         = helper_bash::style('bold', '(╯°□°）╯');
        $swoosh      = array(
            'flip'       => helper_bash::style('light_cyan:blink', '︵'),
            'kamehameha' => helper_bash::style('light_cyan:blink', '=====)'),
        );
        $table       = helper_bash::style('yellow', '┻━┻');

        return sprintf("%s %s%s %s", $dot_dot_dot, $guy, $swoosh[$type], $table);
    }

}
