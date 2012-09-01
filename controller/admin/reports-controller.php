<?php
class ReportsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'reports/';
        $this->section = 'Reports';
    }

    /**
     * Reports Search Page
     *
     * @return TemplateResponse
     */
    protected function index() {
        $services = array(
            'product_catalog' => _('Product Catalog')
            , 'blog' => _('Blog')
            , 'email_marketing' => _('Email Marketing')
            , 'mobile_marketing' => _('Mobile Marketing')
            , 'shopping_cart' => _('Shopping Cart')
            , 'room_planner' => _('Room Planner')
            , 'craigslist' => _('Craigslist')
            , 'domain_registration' => _('Domain')
            , 'additional_email_addresses' => _('Additional Email Addresses')
        );

        $template_response = $this->get_template_response( 'index' )
            ->select( 'search' )
            ->set( compact( 'services' ) );

        $this->resources
            ->javascript('reports/index')
            ->css('reports/index')
            ->css_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css' );

        return $template_response;
    }

    /**
     * Custom Reports
     *
     * @return TemplateResponse|CsvResponse
     */
    protected function custom() {
        $form_reports = new FormTable( 'fCustomReports' );
        $form_reports->submit( _('Download') );

        $reports_array = array(
            '' => '-- ' . _('Select Report') . ' --'
            , 'Ashley' => _('Ashley')
            , 'AllAccounts' => _('All Accounts')
            , 'AccountsAndServices' => _('Accounts and Services')
        );

        $form_reports->add_field( 'select', _('Report'), 'sReport' )
            ->add_validation( 'req', _('You must select a report to download') )
            ->options( $reports_array );

        if ( $form_reports->posted() ) {
            // Generate report
            if ( !empty( $_POST['sReport'] ) && array_key_exists( $_POST['sReport'], $reports_array ) ) {
                // Declare variables
                $report_name = $_POST['sReport'] . 'Report';
                $report_file_name = str_replace( '_', '-', format::camel_case_to_underscore( $report_name ) ) . '.php';
                $file_name = format::slug( $reports_array[$_POST['sReport']] ) . '-report.csv';

                // Get the report
                require_once MODEL_PATH . "reports/$report_file_name";

                // Pass in the user
                $report = new $report_name( $this->user );

                // Send it as a CSV File
                return new CsvResponse( $report->report(), $file_name );
            }
        }

        $form = $form_reports->generate_form();

        $template_response = $this->get_template_response( 'custom' )
            ->select( 'custom' )
            ->add_title( _('Custom Reports') )
            ->set( compact( 'form' ) );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * AutoComplete
     *
     * @return AjaxResponse
     */
    public function autocomplete() {
        // Make sure it's verified
        $ajax_response = new AjaxResponse( $this->verified() );

        $report = new Report;

        // Setup array
        $results = array();

        // Get the right suggestions for the right type
        switch ( $_POST['type'] ) {
            case 'brand':
                $results = $report->autocomplete_brands( $_POST['term'] );
            break;

            case 'online_specialist':
                $where = ( $this->user->has_permission(8) ) ? '' : ' AND a.`company_id` = ' . (int) $this->user->company_id;

                $results = $report->autocomplete_online_specialists( $_POST['term'], $where );
            break;

            case 'marketing_specialist':
                $results = $report->autocomplete_marketing_specialists( $_POST['term'] );
            break;

            case 'company':
                if ( $this->user->has_permission( 7 ) ) {
                    $where = ( $this->user->has_permission(8) ) ? '' : ' AND `company_id` = ' . (int) $this->user->company_id;
                    $results = $report->autocomplete_companies( $_POST['term'], $where );
                } else {
                    $results = array();
                }
            break;

            case 'billing_state':
                $results = $this->user->autocomplete( $_POST['term'], 'billing_state' );

                if ( is_array( $results ) )
                foreach ( $results as &$r ) {
                    // Adjust for autocomplete
                    $r['object_id'] = $r['billing_state'];
                }
            break;

            case 'package':
                $where = ( $this->user->has_permission(8) ) ? '' : ' AND `company_id` = ' . (int) $this->user->company_id;
                $results = $report->autocomplete_company_packages( $_POST['term'], $where );
            break;
        }

        $ajax_response->add_response( 'objects', $results );

        return $ajax_response;
    }

    /**
     * Search response
     *
     * @return HtmlResponse
     */
    public function search() {
        if ( !$this->verified() )
            return new HtmlResponse('');

        $report = new Report();

        $where = '';

        // Define available services
        $services = array(
            'product_catalog'
            , 'blog'
            , 'email_marketing'
            , 'mobile_marketing'
            , 'shopping_cart'
            , 'room_planner'
            , 'craigslist'
            , 'domain_registration'
            , 'additional_email_addresses'
        );

        if ( isset( $_POST['c'] ) )
        foreach ( $_POST['c'] as $type => $criterion ) {
            switch ( $type ) {
                case 'brand':
                    $where .= ' AND ( e.`brand_id` IN( ';

                    $brand_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $brand_where ) )
                            $brand_where .= ',';

                        $brand_where .= (int) $object_id;
                    }

                    $where .= "$brand_where ) )";
                break;

                case 'services':
                    $where .= ' AND ( 1';

                    foreach ( $criterion as $service => $value ) {
                        if ( in_array( $service, $services ) )
                            $where .= " AND a.`$service` = 1";
                    }

                    $where .= " )";
                break;

                case 'company':
                    $where .= ' AND ( c.`company_id` IN( ';

                    $company_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $company_where ) )
                            $company_where .= ',';

                        $company_where .= (int) $object_id;
                    }

                    $where .= "$company_where ) )";
                break;

                case 'online_specialist':
                    $where .= ' AND ( a.`os_user_id` IN( ';

                    $online_specialist_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $online_specialist_where ) )
                            $online_specialist_where .= ',';

                        $online_specialist_where .= (int) $object_id;
                    }

                    $where .= "$online_specialist_where ) )";
                break;

                case 'marketing_specialist':
                    $where .= ' AND ( b.`role` = 6 AND b.`user_id` IN( ';

                    $marketing_specialist_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $marketing_specialist_where ) )
                            $marketing_specialist_where .= ',';

                        $marketing_specialist_where .= (int) $object_id;
                    }

                    $where .= "$marketing_specialist_where ) )";
                break;


                case 'billing_state':
                    $where .= ' AND b.`billing_state` IN( ';

                    $state_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $state_where ) )
                            $state_where .= ',';

                        $state_where .= "'" . $this->db->escape( $object_id ) . "'";
                    }

                    $where .= "$state_where )";
                break;

                case 'package':
                    $where .= ' AND ( a.`company_package_id` IN( ';

                    $company_package_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $company_package_where ) )
                            $company_package_where .= ',';

                        $company_package_where .= (int) $object_id;
                    }

                    $where .= "$company_package_where ) )";
                break;
            }
        }

        if ( !$this->user->has_permission(8) )
            $where .= " AND b.`company_id` = " . (int) $this->user->company_id . " ";

        // Do the search
        $accounts = $report->search( $where );

        // Form HTML
        $html = '';

        foreach ( $accounts as $account ) {
            $html .= '<tr>';

            // Title
            $html.= '<td><a href="/accounts/edit/?aid=' . $account->id . '" title="' . _('Edit Account') . '" target="_blank">' . $account->title . '</a></td>';

            // Company
            $html.= '<td>' . $account->company . '</td>';

            // Products
            $html.= '<td>' . $account->products . '</td>';

            // Date Signed Up
            $date = new DateTime( $account->date_created );
            $html.= '<td>' . $date->format('F j, Y') . '</td>';

            $html .= '</tr>';
        }

        return new HtmlResponse( $html );
    }
}