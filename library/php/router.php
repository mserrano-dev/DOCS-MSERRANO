<?php

class router {

    const valid_page = '/^(\/[a-z_]+)+$/';

    static public function get_page($root) {
        $return = null;

        $path = $_SERVER['REDIRECT_URL'];
        if (preg_match(router::valid_page, $path, $matches) && isset($matches[0]) === true) {
            $full_path = explode('/', $path);
            $class_name = end($full_path);
            $class_filepath = sprintf('%s/midtier%s.php', $root, $path);
            $return = router::get_class_instance($class_filepath, $class_name);
        }
        return $return;
    }

    // --- helpers ---
    static private function get_class_instance($class_filepath, $class_name) {
        $return = null;
        if (file_exists($class_filepath) === true) {
            require($class_filepath);

            if (class_exists($class_name) === true) {
                $instance = new $class_name();
                if (is_a($instance, 'page_base') === true) {
                    $return = $instance;
                }
            }
        }
        return $return;
    }

}
