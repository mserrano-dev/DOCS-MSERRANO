<?php

// !! ONLY FOR UNIT TESTING !!
class helper_ut {

    static public function val($obj, $property) {
        $reflection = new ReflectionProperty($obj, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($obj);
    }

    static public function run($instance_or_classname, $class_method) {
        $list_args = array_slice(func_get_args(), 2);

        $reflection = new ReflectionMethod($instance_or_classname, $class_method);
        $reflection->setAccessible(true);

        $obj = (is_string($instance_or_classname) === true ? null : $instance_or_classname);
        return $reflection->invokeArgs($obj, $list_args);
    }

}
