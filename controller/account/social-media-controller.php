<?php
class SocialMediaController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'social-media/';
        $this->title = 'Social Media';
    }

    /**
     * Redirect to Facebook
     *
     * @return RedirectResponse
     */
    protected function index() {
        return new RedirectResponse('/social-media/facebook/');
    }
}


