<?php
class CronsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

    /**
     * Hourly
     */
    public function remove_unused_ticket_uploads() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        /** Remove uploads that were never used */

        // Instantiate classes
        $ticket = new Ticket;
        $ticket_upload = new TicketUpload();
        $file = new File( 'retailcatalog.us' );

        // Get data
        $keys = $ticket_upload->get_keys_by_uncreated_tickets();

        if ( !empty( $keys ) ) {
            // Remove uploads
            foreach ( $keys as $key ) {
                $file->delete_file( "attachments/{$key}" );
            }

            // Delete everything relating to them
            $ticket->deleted_uncreated_tickets();
        }
    }

    /**
     * Hourly
     */
    public function facebook_posts() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        /** Have Social media send out facebook posts */
        $social_media_posting_post = new SocialMediaPostingPost();
        $posts = $social_media_posting_post->get_unposted_posts();

        if ( is_array( $posts ) ) {
            // Get facebook class to interacting with our posting app
            $fb = new Fb( 'posting' );

            $sm_errors = array();

            /**
             * @var SocialMediaPostingPost $post
             */
            foreach ( $posts as $post ) {
                $fb->setAccessToken( $post->access_token );

                // Information:
                // http://developers.facebook.com/docs/reference/api/page/#posts
                try {
                    $fb->api( $post->fb_page_id . '/feed', 'POST', array( 'message' => $post->post, 'link' => $post->link ) );
                } catch ( Exception $e ) {
                    $error_message = $e->getMessage();

                    $sm_errors[$post->id] = $error_message;

                    fn::mail( $post->email, $post->company . ' - Unable to Post to Facebook', "We were unable to send the following post to Facebook:\n\n" . $post->post . "\n\nFor the following reason(s):\n\n" . $error_message . "\n\nTo fix this, please login to the dashboard, go to Social Media > Posting, then delete this post and recreate it following the rules above.\n\n" . $post->account . "\nhttp://admin." . $post->domain . "/accounts/control/?aid=" . $post->website_id . "\n\nHave a great day!", $post->company . ' <noreply@' . $post->domain . '>' );
                    continue;
                }

                $post->status = 1;
                $post->save();
            }

            // Mark post errors
            if ( !empty( $sm_error_ids ) )
                $social_media_posting_post->mark_errors( $sm_error_ids );
        }
    }

    /**
     * Hourly
     */
    public function email_marketing_mark_sent() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Mark emails as sent
        $email_marketing = new EmailMarketing();
        $email_marketing->mark_sent();
    }

    /**
     * Hourly
     */
    public function ashley_express_order_status() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Ashley Express Feed - Order Acknowledgement
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_order_acknowledgement_all();
        unset( $ashley_express_feed );
        gc_collect_cycles();

        // Ashley Express Feed - Order ASN
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_order_asn_all();
        unset( $ashley_express_feed );
        gc_collect_cycles();
    }

    /**
     * Daily
     */
    public function ashley_express_get_products() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Ashley Express Feed
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_flag_products_all();
        unset( $ashley_express_feed );
        gc_collect_cycles();
    }

    /**
     * Daily
     */
    public function ashley_package() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Ashley Package Gateway
        $ashley_package_gateway = new AshleyPackageProductFeedGateway();
        $ashley_package_gateway->run();
        gc_collect_cycles();
    }

    /**
     * Daily
     */
    public function site_on_time() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        /** Run Site On Time Feed */
        $site_on_time = new SiteOnTimeProductFeedGateway();
        $site_on_time->run();
        unset( $site_on_time );
        gc_collect_cycles();
    }

    /**
     * Daily
     */
    public function remove_discontinued_products() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Remove Discontinued products
        $account_product = new AccountProduct();
        $account_product->remove_all_discontinued();
    }

    /**
     * Daily
     */
    public function purge_website_product_view() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Remove Old Product View Data
        $website_product_view = new WebsiteProductView();
        $website_product_view->purge_old_data();
        unset( $website_product_view );
        gc_collect_cycles();
    }

    /**
     * Daily
     */
    public function disabled_accounts_report() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Email Accounts Disabled Yesterday
        $account = new Account();
        $accounts = $account->list_all( array( ' AND a.`status` = 0 AND a.`date_updated` >= CURDATE() - INTERVAL 1 DAY ', '', '', 500 ) );
        if ( count( $accounts ) ) {
            $message = 'Accounts Disabled:<br><br>';
            foreach ($accounts as $account) {
                $message .= "{$account->domain} <a href=\"http://admin.greysuitretail.com/accounts/edit/?aid={$account->website_id}\">Edit Site</a> - Disabled At {$account->date_updated} <br>";
            }
            $yesterday = new DateTime();
            $yesterday->sub(new DateInterval('P1D'));
            fn::mail('technical@greysuitretail.com, gabriel@greysuitretail.com', 'Sites disabled since ' . $yesterday->format('Y-m-d'), $message, 'noreply@greysuitretail.com', 'noreply@greysuitretail.com', false);
        }
    }

    /**
     * Daily
     */
    public function new_products_report() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Email Products created yesterday
        $product = new Product();
        $new_product_list = $product->list_all( array( ' AND p.`date_created` >= CURDATE() - INTERVAL 1 DAY ', '', '', 10000 ) );
        if ( count( $new_product_list ) ) {
            $email_message = "New Products: " . count( $new_product_list ) . PHP_EOL . PHP_EOL;
            $i = 0;
            foreach( $new_product_list as $p ) {
                $i++;
                $email_message .= "[{$i}] {$p->sku} - {$p->name} - Created By {$p->created_by} - http://admin.greysuitretail.com/products/add-edit/?pid={$p->id}" . PHP_EOL;
            }
            $yesterday = new DateTime();
            $yesterday->sub( new DateInterval('P1D') );
            fn::mail( 'kerry@greysuitretail.com, david@greysuitretail.com, rafferty@greysuitretail.com, productmanager@greysuitretail.com, gabriel@greysuitretail.com', 'Ashley Products - ' . $yesterday->format('Y-m-d'), $email_message );
        }
    }

    /**
     * Weekly
     */
    public function ashley_specific_feed() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Run Ashley Feed
        $ashley = new AshleySpecificFeedGateway();
        $ashley->run_all();
    }

    /**
     * Weekly
     */
    public function purge_api_ext_log() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Clean up old API logs (1 month)
        $api_ext_log = new ApiExtLog();
        $api_ext_log->purge();
    }

    /**
     * Weekly
     */
    public function butler_feed() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Run Butler Feed
        $butler = new ButlerFeedGateway();
        $butler->run();
    }

    /**
     * Daily
     */
    public function yext_synchronize_location_products() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        $yext_location = new WebsiteYextLocation();
        $locations = $yext_location->list_all( [ ' AND `synchronize_products` = 1  ', '', '', 999999 ] );
        foreach ( $locations as $location ) {
            echo "Updating top product list for location {$location->id}\n";
            $yext_location->do_synchronize_products( $location );
        }
    }

    /**
     * Daily
     */
    public function yext_upload_location_images() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        $yext_location = new WebsiteYextLocation();
        $locations = $yext_location->list_all( [ '', '', '', 999999 ] );
        foreach ( $locations as $location ) {
            echo "Updating images for location {$location->id}\n";
            $yext_location->do_upload_photos( $location );
        }
    }

    public function yext_download_reports() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        global $argv;
        $start_date = new DateTime( $argv[2] );
        $end_date = new DateTime( $argv[3] );

        $account = new Account();
        $account->get( 1352 );

        echo "Downloading reports from " . $start_date->format('Y-m-d') . " to " . $end_date->format('Y-m-d') . "...\n";

        $analytics = new WebsiteYextAnalytics( $account );
        $analytics->fetch_analytics( $start_date, $end_date );

        echo "Finished\n";
    }

    /**
     * Login
     *
     * @return bool
     */
    protected function get_logged_in_user() {
        if ( defined('CLI') && true == CLI ) {
            $this->user = new User();
            return true;
        }

        return false;
    }
}