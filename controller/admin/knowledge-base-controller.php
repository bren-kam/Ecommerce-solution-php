<?php
class KnowledgeBaseController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'knowledge-base/';
        $this->section = _('Knowledge Base');
    }

    /**
     * Reports Search Page
     *
     * @return RedirectResponse
     */
    protected function index() {
        return new RedirectResponse('/knowledge-base/articles/');
    }
}