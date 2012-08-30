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
        $template_response = $this->get_template_response( 'index' )
            ->select( 'search' );

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
            ->set( compact( 'form' ) )
            ->add_title( _('Custom Reports') )
            ->select( 'custom' );

        return $template_response;
    }
}