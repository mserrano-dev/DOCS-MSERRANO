<?php

class input {

    const get = 'get';
    const post = 'post';

    protected $data;

    public function __construct($get, $post) {
        $this->data = array(
            input::get => $get,
            input::post => $post,
        );
    }

    // --- functions ---
    public function data($type) {
        $return = null;
        if (isset($this->data[$type]) === true) {
            $return = $this->data[$type];
        }
        return $return;
    }

}
