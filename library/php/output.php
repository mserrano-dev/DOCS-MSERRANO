<?php

class output {

    const raw     = 'raw';
    const json    = 'json';
    const angular = 'angular';
    const pretty  = 'pretty';

    protected $headers;
    protected $content;

    public function __construct() {
        $this->headers = array();
    }

    public function print_it() {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->content;
    }

    // --- functions ---
    public function add_header($string) {
        $this->headers[] = $string;
    }

    public function format($content, $type) {
        switch ($type) {
            case output::json:
                $data = json_encode($content);
                $this->add_header('Content-Type: application/json');
                break;
            case output::angular:
                $data = sprintf(")]}',\n%s", json_encode($content));
                $this->add_header('Content-Type: application/json');
                break;
            case output::pretty:
                $data = json_encode($content, JSON_PRETTY_PRINT);
                $this->add_header('Content-Type: application/json');
                break;
            case output::raw:
                $data = print_r($content, true);
                $this->add_header('Content-Type: text/plain');
                break;
        }
        if (isset($data) === true) {
            $this->content = $data;
        }
    }

}
