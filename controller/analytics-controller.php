<?php
class AnalyticsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'analytics/';
        $this->title = 'Analytics';
    }

    /**
     * Get dashboard
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->add_title( _('Dashboard') )
            ->select( 'dashboard' );
    }
}


