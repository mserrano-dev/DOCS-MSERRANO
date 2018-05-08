<?php

class routerTest extends UnitTestCase {

    protected $rnd;

    function setUp() {
        $this->rnd = random_int(0, 200);
    }

    function tearDown() {
        
    }

    function test_get_page() {
        $tests = array(
            // CASE: empty sring
            array(__LINE__,
                '', // redirect url
                null, // exp
            ),
            // CASE: valid page
            array(__LINE__,
                '/checker',
                'checker',
            ),
            // CASE: invalid page
            array(__LINE__,
                '/DNE-page',
                null,
            ),
            // CASE: bad page
            array(__LINE__,
                '/../../../../../../../etc/paswd',
                null,
            ),
        );
        foreach ($tests as $vars) {
            list($line, $redirect_url, $exp) = $vars;
            $err_msg = "$line - %s";

            $root                    = helper_fs::project_root();
            $_SERVER['REDIRECT_URL'] = $redirect_url;
            $obj                     = router::get_page($root);

            if (is_null($exp) === true) {
                $this->assertNull($obj, $err_msg);
            } else {
                $res = (is_a($obj, $exp) === true);
                $this->assertTrue($res, $err_msg);
            }
        }
    }

}
