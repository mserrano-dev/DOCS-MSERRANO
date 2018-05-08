<?php

class outputTest extends UnitTestCase {

    public $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
    }

    function tearDown() {
        
    }

    function test___construct() {
        $obj = new output();
        $res = helper_ut::val($obj, 'headers');

        $this->assertEqual(array(), $res);
    }

    function test_print_it() {
        $obj         = new output();
        $rand_header = 'test: javascript/' . ($this->rnd + 0);
        $obj->add_header($rand_header);
        $obj->format($this->rnd + 1, output::angular);

        ob_start();
        $obj->print_it();

        // CASE: headers
        $res = xdebug_get_headers();
        $exp = array($rand_header, 'Content-Type: application/json');
        $this->assertEqual($exp, $res);

        // CASE: content
        $res = ob_get_clean();
        $exp = ")]}',\n" . ($this->rnd + 1);
        $this->assertEqual($exp, $res);
    }

    function test_add_header() {
        $obj = new output();
        $exp = 'Content-type: javascript/' . ($this->rnd + 0);
        $obj->add_header($exp);
        $res = helper_ut::val($obj, 'headers');

        if ($this->assertEqual(count($res), 1, 'headers broken') === true) {
            $this->assertEqual($exp, $res[0]);
        }
    }

    function test_format() {
        $content = array(
            'unittest' => ($this->rnd + 0),
            'date'     => date('Y-m-d H:i:s'),
        );
        $tests   = array(
            // CASE: empty
            array(__LINE__,
                '', // type
                '', // expected
            ),
            // CASE: raw
            array(__LINE__,
                output::raw,
                print_r($content, true),
            ),
            // CASE: json
            array(__LINE__,
                output::json,
                json_encode($content),
            ),
            // CASE: angular
            array(__LINE__,
                output::angular,
                ")]}',\n" . json_encode($content),
            ),
            // CASE: pretty
            array(__LINE__,
                output::pretty,
                json_encode($content, 128),
            ),
        );
        foreach ($tests as $vars) {
            list($line, $type, $exp) = $vars;
            $err_msg = "{$line} - %s";

            $obj = new output();
            $obj->format($content, $type);

            $res = helper_ut::val($obj, 'content');
            $this->assertEqual($exp, $res, $err_msg);
        }
    }

}
