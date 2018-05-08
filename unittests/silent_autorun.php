<?php

// SimpleTest silent reporter
class SilentReporter extends SimpleReporter {

    public function __construct() {
        parent::__construct();
    }

    public function paintGroupStart($test_name, $size) {}
    public function paintGroupEnd($test_name) {}
    public function paintCaseStart($test_name) {}
    public function paintCaseEnd($test_name) {}
    public function paintMethodStart($test_name) {}
    public function paintMethodEnd($test_name) {}
    public function paintPass($message) {}

    public function paintFail($message) {
        parent::paintFail($message);
        print $this->getFailCount() . ") $message\n";
        $breadcrumb = $this->getTestList();
        $this->print_breadcrumb($breadcrumb);
        print "\n";
    }

    public function paintError($message) {
        parent::paintError($message);
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        $this->print_breadcrumb($breadcrumb);
        print "\n";
    }

    public function paintException($exception) {
        parent::paintException($exception);
        $message    = 'Unexpected exception of type [' . get_class($exception) .
                '] with message [' . $exception->getMessage() .
                '] in [' . $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        print "Exception " . $this->getExceptionCount() . "!\n$message\n";
        $breadcrumb = $this->getTestList();
        $this->print_breadcrumb($breadcrumb);
        print "\n";
    }

    public function paintSkip($message) {}
    public function paintMessage($message) {}
    public function paintFormattedMessage($message) {}
    public function paintSignal($type, $payload) {}
    public function error($type, $payload) {}

    // --- helpers ---
    private function print_breadcrumb($breadcrumb) {
        array_shift($breadcrumb);
        if(empty($breadcrumb) === false) {
            $breadcrumb = array_reverse($breadcrumb);
            print "\tin " . implode("\n\tin ", $breadcrumb);
        }
    }
}

/**
 *  Autorunner which runs all tests cases found in a file
 *  that includes this module.
 *  @package    SimpleTest
 */
/* * #@+
 * include simpletest files
 */

$root = dirname(__DIR__);
$dir  = sprintf('%s/vendor/simpletest/simpletest', $root);
require_once $dir . '/unit_tester.php';
require_once $dir . '/mock_objects.php';
require_once $dir . '/collector.php';
require_once $dir . '/default_reporter.php';
/* * #@- */

$GLOBALS['SIMPLETEST_AUTORUNNER_INITIAL_CLASSES'] = get_declared_classes();
$GLOBALS['SIMPLETEST_AUTORUNNER_INITIAL_PATH']    = getcwd();
register_shutdown_function('simpletest_autorun');

/**
 *    Exit handler to run all recent test cases and exit system if in CLI
 */
function simpletest_autorun() {
    chdir($GLOBALS['SIMPLETEST_AUTORUNNER_INITIAL_PATH']);
    if (tests_have_run()) {
        return;
    }
    $result = run_local_tests();
    if (SimpleReporter::inCli()) {
        exit($result ? 0 : 1);
    }
}

/**
 *    run all recent test cases if no test has
 *    so far been run. Uses the DefaultReporter which can have
 *    it's output controlled with SimpleTest::prefer().
 *    @return boolean/null false if there were test failures, true if
 *                         there were no failures, null if tests are
 *                         already running
 */
function run_local_tests() {
    try {
        if (tests_have_run()) {
            return;
        }
        $candidates = capture_new_classes();
        $loader     = new SimpleFileLoader();
        $suite      = $loader->createSuiteFromClasses(
                basename(initial_file()), $loader->selectRunnableTests($candidates));
        return $suite->run(new SilentReporter());
    } catch (Exception $stack_frame_fix) {
        print $stack_frame_fix->getMessage();
        return false;
    }
}

/**
 *    Checks the current test context to see if a test has
 *    ever been run.
 *    @return boolean        True if tests have run.
 */
function tests_have_run() {
    $context = SimpleTest::getContext();
    if ($context) {
        return (boolean) $context->getTest();
    }
    return false;
}

/**
 *    The first autorun file.
 *    @return string        Filename of first autorun script.
 */
function initial_file() {
    static $file = false;
    if (!$file) {
        if (isset($_SERVER, $_SERVER['SCRIPT_FILENAME'])) {
            $file = $_SERVER['SCRIPT_FILENAME'];
        } else {
            $included_files = get_included_files();
            $file           = reset($included_files);
        }
    }
    return $file;
}

/**
 *    Every class since the first autorun include. This
 *    is safe enough if require_once() is always used.
 *    @return array        Class names.
 */
function capture_new_classes() {
    global $SIMPLETEST_AUTORUNNER_INITIAL_CLASSES;
    return array_map('strtolower', array_diff(get_declared_classes(), $SIMPLETEST_AUTORUNNER_INITIAL_CLASSES ?
                    $SIMPLETEST_AUTORUNNER_INITIAL_CLASSES : array()));
}
