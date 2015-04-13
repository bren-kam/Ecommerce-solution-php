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
            , 'geo_marketing' => 'GeoMarketing'
            , 'shopping_cart' => _('Shopping Cart')
            , 'room_planner' => _('Room Planner')
            , 'craigslist' => _('Craigslist')
            , 'domain_registration' => _('Domain')
            , 'additional_email_addresses' => _('Additional Email Addresses')
        );

        $this->resources
            ->javascript('reports/index')
            ->javascript_url( Config::resource('typeahead-js') )
            ->css('reports/index');

        return $this->get_template_response( 'index' )
            ->kb( 26 )
            ->select( 'reports', 'reports/index' )
            ->set( compact( 'services' ) );
    }

    /**
     * Custom Reports
     *
     * @return TemplateResponse|CsvResponse
     */
    protected function custom() {
        $form_reports = new BootstrapForm( 'fCustomReports' );
        $form_reports->submit( _('Download') );

        $reports_array = array(
            '' => '-- ' . _('Select Report') . ' --'
            , 'Ashley' => _('Ashley')
            , 'AllAccounts' => _('All Accounts')
            , 'AccountsAndServices' => _('Accounts and Services')
            , 'AshleyFeedSites' => _('Ashley Feed Sites')
            , 'AshleyHandbuiltProducts' => _('Ashley Handbuilt Products')
            , 'WebsiteCancellations' => _('Website Cancellations')
        );

        if ( $this->user->has_permission( User::ROLE_SUPER_ADMIN ) )
            $reports_array['ApiExtLog'] = _('External API Log');

        if ( $this->user->has_permission( User::ROLE_SUPER_ADMIN ) || $this->user->has_permission( User::ROLE_ADMIN )){
            $reports_array['MyAccounts'] = _('My Accounts');
        }

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

        return $this->get_template_response( 'custom' )
            ->kb( 27 )
            ->select( 'reports', 'reports/custom' )
            ->add_title( _('Custom Reports') )
            ->set( compact( 'form' ) );
    }

    /***** AJAX *****/

    /**
     * AutoComplete
     *
     * @return AjaxResponse
     */
    protected function autocomplete() {
        // Make sure it's verified
        $ajax_response = new AjaxResponse( $this->verified() );

        $report = new Report;

        // Setup array
        $results = array();

        // Get the right suggestions for the right type
        switch ( $_GET['type'] ) {
            case 'brand':
                $results = $report->autocomplete_brands( $_GET['term'] );
            break;

            case 'online_specialist':
                $where = ( $this->user->has_permission( User::ROLE_ADMIN ) ) ? '' : ' AND a.`company_id` = ' . (int) $this->user->company_id;

                $results = $report->autocomplete_online_specialists( $_GET['term'], $where );
            break;

            case 'marketing_specialist':
                $results = $report->autocomplete_marketing_specialists( $_GET['term'] );
            break;

            case 'company':
                if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) {
                    $where = ( $this->user->has_permission( User::ROLE_ADMIN ) ) ? '' : ' AND `company_id` = ' . (int) $this->user->company_id;
                    $results = $report->autocomplete_companies( $_GET['term'], $where );
                } else {
                    $results = array();
                }
            break;

            case 'billing_state':
                $results = $this->user->autocomplete( $_GET['term'], 'billing_state' );

                if ( is_array( $results ) )
                foreach ( $results as &$r ) {
                    // Adjust for autocomplete
                    $r['object_id'] = $r['billing_state'];
                }
            break;

            case 'package':
                $where = ( $this->user->has_permission( User::ROLE_ADMIN ) ) ? '' : ' AND `company_id` = ' . (int) $this->user->company_id;
                $results = $report->autocomplete_company_packages( $_GET['term'], $where );
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
    protected function search() {
        if ( !$this->verified() )
            return new HtmlResponse('');

        $report = new Report();

        $where = $joins = '';

        // Define available services
        $services = array(
            'product_catalog'
            , 'blog'
            , 'email_marketing'
            , 'geo_marketing'
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
                    $where .= ' AND ( p.`brand_id` IN( ';

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
                            $where .= " AND w.`$service` = 1";
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
                    $where .= ' AND ( w.`os_user_id` IN( ';

                    $online_specialist_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $online_specialist_where ) )
                            $online_specialist_where .= ',';

                        $online_specialist_where .= (int) $object_id;
                    }

                    $where .= "$online_specialist_where ) )";
                break;

                case 'marketing_specialist':
                    $joins = 'LEFT JOIN `auth_user_websites` AS auw ON ( auw.`website_id` = w.`website_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = auw.`user_id` )';
                    $where .= ' AND ( u2.`role` = 6 AND auw.`user_id` IN( ';

                    $marketing_specialist_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $marketing_specialist_where ) )
                            $marketing_specialist_where .= ',';

                        $marketing_specialist_where .= (int) $object_id;
                    }

                    $where .= "$marketing_specialist_where ) )";
                break;


                case 'billing_state':
                    $where .= ' AND u.`billing_state` IN( ';

                    $state_where = '';

                    foreach ( $criterion as $object_id => $value ) {
                        if ( !empty( $state_where ) )
                            $state_where .= ',';

                        $state_where .= $report->quote( $object_id );
                    }

                    $where .= "$state_where )";
                break;

                case 'package':
                    $where .= ' AND ( w.`company_package_id` IN( ';

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

        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            $where .= " AND u.`company_id` = " . (int) $this->user->company_id . " ";

        // Do the search
        $accounts = $report->search( $where, $joins );

        // Form HTML
        $html = '';
        $csv = [[
            'account',
            'company',
            'products',
            'date',
            'edit_account'
        ]];

        foreach ( $accounts as $account ) {
            if ( $_POST['download'] ) {
                $date = new DateTime( $account->date_created );
                $csv[] = [
                    $account->title,
                    $account->company,
                    $account->products,
                    $date->format('F j, Y'),
                    "http//admin.greysuitretail.com/accounts/edit/?aid={$account->id}"
                ];
            } else {
                $html .= '<tr>';
                $html.= '<td><a href="/accounts/edit/?aid=' . $account->id . '" title="' . _('Edit Account') . '" target="_blank">' . $account->title . '</a></td>';
                $html.= '<td>' . $account->company . '</td>';
                $html.= '<td>' . $account->products . '</td>';
                $date = new DateTime( $account->date_created );
                $html.= '<td>' . $date->format('F j, Y') . '</td>';
                $html .= '</tr>';
            }
        }

        if ( $_POST['download'] ) {
            return new CsvResponse($csv, 'report-' . date('YmdHis') . '.csv');
        }

        return new HtmlResponse( $html );
    }
}