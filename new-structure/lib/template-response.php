<?php

require_once 'response.php';

class TemplateResponse extends Response {

    private $file_to_render = NULL;
    private $error = false;

    public function __construct( $file_to_render, $error = false ) {
        $this->error = $error;
    }

    protected function has_error() {
        return $this->error;
    }

    public function respond() {
        require_once ( $this->file_to_render );
    }

}
