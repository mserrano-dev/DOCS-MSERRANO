<?php

class checker extends page_base {

    public function build(output $output) {
        $this->set_response_pass();
        $output->format($this->response, output::json);
    }

}
