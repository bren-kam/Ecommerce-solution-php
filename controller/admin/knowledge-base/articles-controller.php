<?php
class ArticlesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'knowledge-base/articles/';
        $this->section = _('Knowledge Base');
    }

    /**
     * List
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'articles', 'view' );
    }
}