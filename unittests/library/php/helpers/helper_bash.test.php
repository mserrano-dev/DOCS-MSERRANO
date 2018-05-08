<?php

class helper_bashTest extends UnitTestCase {

    public $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
    }

    function tearDown() {
        
    }

    function test_skip_abstract() {
        ob_start();
        helper_bash::skip_abstract(__METHOD__);

        $res = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(preg_match('/helper_bash::skip_abstract/', $res));
        $this->assertNotEqual('', $res);
    }

    function test_output_message() {
        ob_start();
        helper_bash::output_message($this->rnd + 0);

        $res = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(preg_match('/' . $this->rnd . '/', $res));
        $this->assertNotEqual('', $res);
    }

    function test_exclamation() {
        // flip
        ob_start();
        helper_bash::output_message(helper_bash::exclamation('flip'));

        $res = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(preg_match('/︵/', $res));
        $this->assertNotEqual('', $res);

        // kamehameha
        ob_start();
        helper_bash::output_message(helper_bash::exclamation('kamehameha'));

        $res = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(preg_match('/=====\)/', $res));
        $this->assertNotEqual('', $res);
    }

    function test_style() {
        $tests = array(
            // CASE: empty string
            array(__LINE__,
                '', // rules
                '', // format
                array(), // list_arg
                "", // exp
            ),
            // CASE: single style
            array(__LINE__,
                'cyan',
                'a',
                array(),
                "\e[36ma\e[0m",
            ),
            // CASE: chained style
            array(__LINE__,
                'cyan:blink',
                ($this->rnd + 0),
                array(),
                "\e[36m\e[5m" . ($this->rnd + 0) . "\e[0m\e[0m",
            ),
            // CASE: string with args
            array(__LINE__,
                'cyan:blink:inverted',
                "%s wat %s",
                array($this->rnd + 1, $this->rnd + 2),
                "\e[36m\e[5m\e[7m" . sprintf("%s wat %s", $this->rnd + 1, $this->rnd + 2) . "\e[0m\e[0m\e[0m",
            ),
        );
        foreach ($tests as $vars) {
            list($line, $rules, $format, $list_arg, $exp) = $vars;
            $err_msg = "{$line} - %s";

            $res = helper_bash::style($rules, $format, $list_arg);
            $this->assertEqual($exp, $res, $err_msg);
        }
    }

}
