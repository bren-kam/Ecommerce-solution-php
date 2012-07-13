<?php

class RedirectResponse extends Response {

    /**
     * The place to which the user will be redirected
     * @var string
     */
    private $to;

    public function __construct( $to ) {
        if ( empty ( $to ) )
            throw new LibraryException( 'You need to inform a destination to redirect the user' );
        $this->to = $to;
    }

    protected function has_error() {
        return false;
    }

    protected function respond() {
        url::redirect( $this->to );
    }

}
