<?php

class top_secret {

    private $class_property;
    static private $static_property;

    public function __construct() {
        $this->class_property        = sprintf('private_property: %s', date('Y-m-d H:i:s'));
        top_secret::$static_property = sprintf('static_private_property: %s', date('Y-m-d H:i:s'));
    }

    private function class_method($arg = '') {
        return sprintf('private_class_method: %s%s', date('Y-m-d H:i:s'), $arg);
    }

    static private function static_method($arg = '') {
        return sprintf('private_static_method: %s%s', date('Y-m-d H:i:s'), $arg);
    }

}

class helper_utTest extends UnitTestCase {

    public $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
    }

    function tearDown() {
        
    }

    function test_val() {
        // CASE: object instance
        $obj = new top_secret();
        $exp = sprintf('private_property: %s', date('Y-m-d H:i:s'));
        $res = helper_ut::val($obj, 'class_property');
        $this->assertEqual($exp, $res);

        // CASE: static class
        $exp = sprintf('static_private_property: %s', date('Y-m-d H:i:s'));
        $res = helper_ut::val('top_secret', 'static_property');
        $this->assertEqual($exp, $res);
    }

    function test_run() {
        // CASE: object instance
        $obj = new top_secret();
        $exp = sprintf('private_class_method: %s', date('Y-m-d H:i:s'));
        $res = helper_ut::run($obj, 'class_method');
        $this->assertEqual($exp, $res);

        // CASE: instance w/ args
        $obj = new top_secret();
        $exp = sprintf('private_class_method: %s%s', date('Y-m-d H:i:s'), $this->rnd + 0);
        $res = helper_ut::run($obj, 'class_method', $this->rnd + 0);
        $this->assertEqual($exp, $res);

        // CASE: static class
        $exp = sprintf('private_static_method: %s', date('Y-m-d H:i:s'));
        $res = helper_ut::run('top_secret', 'static_method');
        $this->assertEqual($exp, $res);

        // CASE: static w/ args
        $exp = sprintf('private_static_method: %s%s', date('Y-m-d H:i:s'), $this->rnd + 1);
        $res = helper_ut::run('top_secret', 'static_method', $this->rnd + 1);
        $this->assertEqual($exp, $res);
    }

}
