<?php

class impl_page extends page_base {

    public function build(output $output) {
        
    }

    public function get() {
        return $this->get;
    }

    public function post() {
        return $this->post;
    }

    public function response() {
        return $this->response;
    }

}

class page_baseTest extends UnitTestCase {

    public $rnd;
    public $input;

    function setUp() {
        $this->rnd   = $rnd         = random_int(0, 200);
        $this->input = new input($rnd + 0, $rnd + 1);
    }

    function tearDown() {
        $this->input = null;
    }

    function test___construct() {
        $obj = new impl_page();
        $res = (is_a($obj, 'page_base') === true);
        $this->assertTrue($res);
    }

    function test_build() {
        helper_bash::skip_abstract(__METHOD__);
    }

    function test_register_input() {
        $obj = new impl_page();
        $obj->register_input($this->input);

        $res = $obj->get();
        $this->assertEqual($this->rnd + 0, $res);
        $res = $obj->post();
        $this->assertEqual($this->rnd + 1, $res);
    }

    function test_set_response_pass() {
        $obj = new impl_page();
        $obj->register_input($this->input);

        $res = $obj->response();
        $exp = array('result' => 'fail');
        $this->assertEqual($exp, $res);

        $obj->set_response_pass();

        $res = $obj->response();
        $exp = array('result' => 'pass');
        $this->assertEqual($exp, $res);
    }

}
