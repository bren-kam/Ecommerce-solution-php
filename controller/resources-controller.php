<?php
class ResourcesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );
    }

    /**
     * Handle CSS
     *
     * @return TemplateResponse
     */
    protected function css() {
        return new CssResponse( $_GET['f'] );
    }

    /**
     * Handle JS
     *
     * @return bool
     */
    protected function js() {
        return new JavascriptResponse( $_GET['f'] );
    }

    /**
     * Need different things for an image
     *
     * @return bool
     */
    protected function image() {
        return new ImageResponse( $_SERVER['REQUEST_URI'] );
    }

    /**
     * Override login function
     * @return bool
     */
    protected function get_logged_in_user() {
        return true;
    }
}


