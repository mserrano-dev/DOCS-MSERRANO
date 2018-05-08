<?php

abstract class page_base {

    protected $get;
    protected $post;
    //
    protected $response;

    public function __construct() {
        $this->response = array();
        $this->response[cst_infrastructure::result] = cst_infrastructure::fail;
    }

    abstract public function build(output $output);

    // --- functions ---
    public function register_input(input $input) {
        $this->get = $input->data(input::get);
        $this->post = $input->data(input::post);
    }

    public function set_response_pass() {
        $this->response[cst_infrastructure::result] = cst_infrastructure::pass;
    }

}
