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
     * @return CssResponse
     */
    protected function css() {
        return new CssResponse( $_GET['f'] );
    }

    /**
     * Handle CSS Single File
     *
     * @return CssResponse
     */
    protected function css_single() {
        return new CssResponse( $this->resources->get_css_file( $_GET['f'] ) );
    }

    /**
     * Handle JS
     *
     * @return JavascriptResponse
     */
    protected function js() {
        return new JavascriptResponse( $_GET['f'] );
    }

    /**
     * Handle single JS
     *
     * @return JavascriptResponse
     */
    protected function js_single() {
        return new JavascriptResponse( $this->resources->get_javascript_file( $_GET['f'] ) );
    }

    /**
     * Handle single JSON
     *
     * @return JavascriptResponse
     */
    protected function json_single() {
        return new JavascriptResponse( $this->resources->get_json_file( $_GET['f'] ) );
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
     * Need different things for media
     *
     * @return bool
     */
    protected function media() {
        return new MediaResponse( $_SERVER['REQUEST_URI'] );
    }

    protected function fonts(){
        return new ExternalResponse( $this->resources->get_font_file($_GET['f']));
    }

    /**
     * Need different things for external files (i.e. ckeditor)
     *
     * @return bool
     */
    protected function external() {
        return new ExternalResponse( $_SERVER['REQUEST_URI'] );
    }

    /**
     * Override login function
     * @return bool
     */
    protected function get_logged_in_user() {
        return true;
    }

    /**
     * Company CSS
     * @return HtmlResponse
     */
    protected function company_css() {
        header::css();
        return new HtmlResponse( Company::get_current_company()->css );
    }
}


