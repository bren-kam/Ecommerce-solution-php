<?php

require_once 'response.php';

class TemplateResponse extends Response {

    private $file_to_render = NULL;
    private $error_message = false;

    public function __construct( $file_to_render, $error_message = NULL ) {
        $this->error_message = $error_message;
    }

    protected function has_error() {
        return is_null( $this->error_message );
    }

    public function respond() {
        require_once ( $this->file_to_render );
    }

}
