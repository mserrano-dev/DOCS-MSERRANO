<?php

class autoloader {

    const max_depth = 1;

    static public function library($root) {
        $lib_dir = sprintf('%s/library/php/*', $root);
        static::load_dir($lib_dir);
    }

    // --- helpers ---
    static private function load_dir($to_glob, $depth = 0) {
        $files = glob($to_glob);
        foreach ($files as $lib_file) {
            if (is_dir($lib_file) === true) {
                $subdir_pattern = static::get_subdir_pattern($lib_file);
                static::load_dir($subdir_pattern, ($depth + 1));
            } else if ($depth <= static::max_depth) {
                require_once($lib_file);
            }
        }
    }

    static private function get_subdir_pattern($lib_file) {
        $path           = explode('/', $lib_file);
        $tag            = substr(end($path), 0, -1); // strip 's'
        $subdir_pattern = sprintf('%s/%s_*', $lib_file, $tag);

        return $subdir_pattern;
    }

}
