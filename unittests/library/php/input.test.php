<?php

class inputTest extends UnitTestCase {

    protected $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
    }

    function tearDown() {
        
    }

    function test___construct() {
        $obj = new input($this->rnd + 0, $this->rnd + 1);
        $res = is_a($obj, 'input') === true;
        $this->assertTrue($res);

        $res = $obj->data(input::get);
        $this->assertNotNull($res);
    }

    function test_data() {
        $obj = new input($this->rnd + 0, $this->rnd + 1);
        $res = $obj->data(input::get);
        $this->assertEqual($this->rnd + 0, $res);

        $res = $obj->data(input::post);
        $this->assertEqual($this->rnd + 1, $res);
    }

}
