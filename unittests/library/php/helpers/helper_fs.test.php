<?php

class helper_fsTest extends UnitTestCase {

    public $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
        helper_fs::create_dir(helper_fs::prefix_root('/tmp/helper_fs/'));
    }

    function tearDown() {
        helper_fs::delete_all(helper_fs::prefix_root('/tmp/helper_fs/'));
    }

    function test_blacklist() {
        $obj = helper_fs::blacklist();
        $res = (is_array($obj) === true);
        $this->assertTrue($res);
    }

    function test_project_root() {
        $root = helper_fs::project_root();
        $res  = glob(sprintf('%s/*', $root));

        $exp = sprintf('%s/library', $root);
        $this->assertTrue(in_array($exp, $res));

        $exp = sprintf('%s/unittests', $root);
        $this->assertTrue(in_array($exp, $res));

        $exp = sprintf('%s/frontend', $root);
        $this->assertTrue(in_array($exp, $res));
    }

    function test_prefix_root() {
        $tests = array(
            // CASE: empty
            array(__LINE__,
                '', // path
                helper_fs::project_root() . '/', // expected
            ),
            // CASE: no beginning slash
            array(__LINE__,
                ($this->rnd + 0),
                helper_fs::project_root() . '/' . ($this->rnd + 0),
            ),
            // CASE: contains beginning slash
            array(__LINE__,
                '/' . ($this->rnd + 0),
                helper_fs::project_root() . '/' . ($this->rnd + 0),
            ),
        );
        foreach ($tests as $vars) {
            list($line, $path, $exp) = $vars;
            $err_msg = "{$line} - %s";

            $res = helper_fs::prefix_root($path);
            $this->assertEqual($exp, $res, $err_msg);
        }
    }

    function test_create_file() {
        $tests = array(
            // CASE: empty content
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs/empty'), // file
                '', // content
                '', // expected
            ),
            // CASE: random content
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs/test_create_file'),
                ($this->rnd + 0),
                ($this->rnd + 0),
            ),
            // CASE: already exists
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs/test_create_file'),
                ($this->rnd + 1),
                ($this->rnd + 0),
            ),
        );
        foreach ($tests as $vars) {
            list($line, $file, $content, $exp) = $vars;
            $err_msg = "{$line} - %s";

            helper_fs::create_file($file, $content);

            if ($this->assertTrue(file_exists($file), $err_msg) === true) {
                $res = file_get_contents($file);
                $this->assertEqual($exp, $res, $err_msg);
            }
        }
    }

    function test_create_dir() {
        $tests = array(
            // CASE: empty
            array(__LINE__,
                '', // path
                false, // expected
            ),
            // CASE: dir already exists
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs'), // created in setup
                true,
            ),
            // CASE: dir does not exist (depth 1)
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs/test_create_dir'),
                true,
            ),
            // CASE: dir does not exist (depth 3)
            array(__LINE__,
                helper_fs::prefix_root('/tmp/helper_fs/test_create_dir/depth2/depth3'),
                true,
            ),
        );
        foreach ($tests as $vars) {
            list($line, $path, $exp) = $vars;
            $err_msg = "{$line} - %s";

            helper_fs::create_dir($path);

            $res = (is_dir($path) === true);
            $this->assertEqual($exp, $res, $err_msg);
        }
    }

    function test_delete_all() {
        $dir = helper_fs::prefix_root('/tmp/helper_fs/delete_all');

        // CASE: single file 
        helper_fs::create_dir($dir);
        $path = helper_fs::prefix_root('/tmp/helper_fs/delete_all/file1.txt');
        helper_fs::create_file($path);
        $this->assertTrue(file_exists($path) === true);
        helper_fs::delete_all($path);
        $this->assertTrue(file_exists($path) === false);
        helper_fs::delete_dir($dir);

        // CASE: directory - depth1
        helper_fs::create_dir($dir);
        $path_1 = helper_fs::prefix_root('/tmp/helper_fs/delete_all/file1.txt');
        $path_2 = helper_fs::prefix_root('/tmp/helper_fs/delete_all/file2.txt');
        helper_fs::create_file($path_1);
        helper_fs::create_file($path_2);
        $this->assertTrue(file_exists($path_1) === true);
        $this->assertTrue(file_exists($path_2) === true);

        helper_fs::delete_all($dir);
        $this->assertTrue(file_exists($path_1) === false);
        $this->assertTrue(file_exists($path_2) === false);
        $this->assertTrue(file_exists($dir) === false);
        helper_fs::delete_dir($dir);
    }

}
