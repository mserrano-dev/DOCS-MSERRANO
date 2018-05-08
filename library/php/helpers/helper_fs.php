<?php

// Intended as helper class for UNIT TESTING
class helper_fs {

    static public function blacklist() {
        return ['silent_autorun.php', 'test_runner.php', 'test_coverage.php'];
    }

    static public function project_root() {
        $path = explode('/library', __DIR__);
        return $path[0];
    }

    static public function prefix_root($path) {
        return sprintf('%s/%s', helper_fs::project_root(), ltrim($path, '/'));
    }

    static public function create_file($path, $content = null) {
        if (file_exists($path) === false) {
            if (empty($content) === true) {
                touch($path);
            } else {
                file_put_contents($path, $content);
            }
        }
    }

    static public function create_dir($path) {
        if ((empty($path) === false) && (file_exists($path) === false)) {
            mkdir($path, 0777, true); // mkdir -p
        }
    }

    static public function delete_file($to_delete) {
        if (file_exists($to_delete) === true) {
            unlink($to_delete);
        }
    }

    static public function delete_dir($to_delete) {
        if (file_exists($to_delete) === true) {
            $traversal = new RecursiveDirectoryIterator($to_delete, FilesystemIterator::SKIP_DOTS); // path traversal
            $flatten   = new RecursiveIteratorIterator($traversal, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($flatten as $path) {
                $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }
            rmdir($to_delete);
        }
    }

    static public function delete_all($to_delete) {
        if (is_dir($to_delete) === true) {
            helper_fs::delete_dir($to_delete);
        } else {
            helper_fs::delete_file($to_delete);
        }
    }

}
