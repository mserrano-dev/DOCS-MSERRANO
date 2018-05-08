<?php

class ut_autoloader extends autoloader {

    const max_depth = 2;
    const ut_mode   = true;

}

class autoloaderTest extends UnitTestCase {

    protected $lib_path;

    function setUp() {
        // convention: if a valid folder is 'name' + 's', then a valid file is name_FILE.php
        $this->lib_path = helper_fs::prefix_root('/tmp/library/');
        helper_fs::create_dir($this->lib_path . 'php/d1s/d1_d2s/d1_d2_d3s/');

        $this->create_valid_class('php/d1s/d1_util_A.php');
        $this->create_valid_class('php/d1s/d1_util_B.php');
        $this->create_valid_class('php/d1s/d1_d2s/d1_d2_file.php');
        $this->create_valid_class('php/d1s/d1_d2s/d1_invalid.php');
        $this->create_valid_class('php/d1s/d1_d2s/d1_d2_d3s/d1_d2_d3_final.php');
    }

    function tearDown() {
        helper_fs::delete_all($this->lib_path);
    }

    function test_library() {
        // CASE: ignore invalid convention, ignore depth 3
        ut_autoloader::library(helper_fs::prefix_root('tmp'));

        $this->assertTrue(class_exists('d1_util_A') === true, 'depth1 file not loaded');
        $this->assertTrue(class_exists('d1_util_B') === true, 'depth1 file not loaded');
        $this->assertTrue(class_exists('d1_d2_file') === true, 'depth2 file not loaded');
        $this->assertTrue(class_exists('d1_d2_d3_final') === false, 'loaded more classes than settings allow!');
        $this->assertTrue(class_exists('d1_invalid') === false, 'convention is not respected!');
    }

    // --- helpers ---
    function create_valid_class($path) {
        $name     = pathinfo($path, PATHINFO_FILENAME);
        $contents = sprintf('<?php class %s {}', $name);
        helper_fs::create_file($this->lib_path . $path, $contents);
    }

}
