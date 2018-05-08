<?php

// Intended as helper class for UNIT TESTING
class helper_bash {

    static public function skip_abstract($ut_method) {
        $method  = str_replace('Test::test_', '::', $ut_method);
        $label   = helper_bash::style('bold', '!! %s !!', array($method));
        $keyword = helper_bash::style('bold', 'abstract method');
        $end     = helper_bash::style('cyan', '.');
        $msg     = sprintf("%s cannot test %s%s", $label, $keyword, $end);
        helper_bash::output_message($msg);
    }

    static public function output_message($msg) {
        echo sprintf('%s%s', $msg, PHP_EOL);
    }

    // --- utility ---
    static public function exclamation($type) {
        $dot_dot_dot = helper_bash::style('light_red', '...');
        $guy         = helper_bash::style('bold', '(╯°□°）╯');
        $swoosh      = array(
            'flip'       => helper_bash::style('light_cyan:blink', '︵'),
            'kamehameha' => helper_bash::style('light_cyan:blink', '=====)'),
        );
        $table       = helper_bash::style('yellow', '┻━┻');

        return sprintf("%s %s%s %s", $dot_dot_dot, $guy, $swoosh[$type], $table);
    }

    static public function style($rules, $format, $list_arg = null) {
        $list_rule          = explode(':', $rules);
        $common_color_codes = array(
            'bold'          => '1',
            'blink'         => '5',
            'inverted'      => '7',
            //
            'yellow'        => '33',
            'blue'          => '34',
            'cyan'          => '36',
            'light_red'     => '91',
            'light_cyan'    => '96',
            //
            'light_gray_bg' => '47',
        );

        $return = "";
        $tail   = "";
        foreach ($list_rule as $rule) {
            if (isset($common_color_codes[$rule]) === true) {
                $return .= sprintf("\e[%sm", $common_color_codes[$rule]);
                $tail   .= "\e[0m";
            }
        }
        $string = $format;
        if (is_array($list_arg) === true) {
            $string = vsprintf($format, $list_arg);
        }
        return sprintf("%s%s%s", $return, $string, $tail);
    }

}
