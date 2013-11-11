<?php
class TestController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'test/';
    }

    /**
     * List Accounts
     *
     *
     * @return TemplateResponse
     */
    protected function index() {
        $account = new Account();

        library('sendgrid-api');

        // Get accounts with email marketing
        $accounts = $account->get_results('SELECT w.*, u.`email`, u.`contact_name`, COALESCE( u.`work_phone`, u.`cell_phone` ) AS phone FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) WHERE w.`status` = 1 AND w.`email_marketing` = 1 AND `website_id` <> 96', PDO::FETCH_CLASS, 'Account' );

        /**
         * @var Account $account
         */
        foreach ( $accounts as $account ) {
            $settings = $account->get_settings( 'sendgrid-username', 'sendgrid-password' );
            $sendgrid = new SendGridAPI( $account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
            $sendgrid->setup_email();
            $sendgrid->setup_list();

            $email_list = new EmailList();
            $email_lists = $email_list->get_by_account( $account->website_id );

            $email = new Email();

            foreach ( $email_lists as $email_list ) {
                $sendgrid->list->add( $email_list->name );

                $emails = $email->get_by_email_list( $email_list->id );

                $email_chunks = array_chunk( $emails, 1000 );

                foreach ( $email_chunks as $email_set ) {
                    $sendgrid->email->add( $email_list->name, array( $email_set ) );
                }
            }
            break;
        }

        /*
        library('Excel_Reader/Excel_Reader');
        $er = new Excel_Reader();
        // Set the basics and then read in the rows
        $er->setOutputEncoding('ASCII');
        $er->read( ABS_PATH . 'temp/map-price-list.xls' );

        $rows = array_slice( $er->sheets[0]['cells'], 3 );

        foreach ( $rows as $row ) {
            break;
            $product = new Product();
            $product->get_by_sku( $row[3] );
            if ( $product->id ) {
                $product->price = $row[15];
                fn::info( $product );exit;
                $product->save();
            }
        }*/

        return new HtmlResponse( 'heh' );
    }
}