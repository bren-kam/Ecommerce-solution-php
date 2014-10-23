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
     *
     * @return HtmlResponse
     */
    protected function hourly() {
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

        // Mark emails as sent
        $email_marketing = new EmailMarketing();
        $email_marketing->mark_sent();

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


        return new HtmlResponse( 'Hourly Jobs Completed');
    }

    /**
     * Daily
     *
     * @return HtmlResponse
     */
    protected function daily() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Ashley Express Feed
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_flag_products_all();
        unset( $ashley_express_feed );
        gc_collect_cycles();

        /** Run Ashley Feed */
        //$ashley = new AshleyMasterProductFeedGateway();
        //$ashley->run();
        //unset( $ashley );
        //gc_collect_cycles();

        // Ashley Package Gateway
        $ashley_package_gateway = new AshleyPackageProductFeedGateway();
        $ashley_package_gateway->run();
        gc_collect_cycles();

        /** Run Site On Time Feed */
        $site_on_time = new SiteOnTimeProductFeedGateway();
        $site_on_time->run();
        unset( $site_on_time );
        gc_collect_cycles();

        // Remove Discontinued products
        $account_product = new AccountProduct();
        $account_product->remove_all_discontinued();

        // Remove Old Product View Data
        $website_product_view = new WebsiteProductView();
        $website_product_view->purge_old_data();
        unset( $website_product_view );
        gc_collect_cycles();

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

        // Email Products created yesterday
        $product = new Product();
        $new_product_list = $product->list_all( array( ' AND p.`date_created` >= CURDATE() - INTERVAL 1 DAY ', '', '', 10000 ) );
        if ( count( $new_product_list ) ) {
            $email_message = "New Products: " . count( $new_product_list ) . PHP_EOL . PHP_EOL;
            foreach( $new_product_list as $p ) {
                $email_message .= "{$p->sku} - {$p->name} - http://admin.greysuitretail.com/products/add-edit/pid={$p->id}" . PHP_EOL;
            }
            $yesterday = new DateTime();
            $yesterday->sub( new DateInterval('P1D') );
            fn::mail( 'kerry@greysuitretail.com, david@greysuitretail.com, rafferty@greysuitretail.com, productmanager@greysuitretail.com, gabriel@greysuitretail.com', 'Ashley Products - ' . $yesterday->format('Y-m-d'), $email_message );
        }


        return new HtmlResponse( 'Daily Jobs Completed' );
    }

    /**
     * Weekly
     *
     * @return HtmlResponse
     */
    protected function weekly() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        // Run Ashley Feed
        $ashley = new AshleySpecificFeedGateway();
        $ashley->run_all();

        // Clean up old API logs (1 month)
        $api_ext_log = new ApiExtLog();
        $api_ext_log->purge();

        // Run Butler Feed
        $butler = new ButlerFeedGateway();
        $butler->run();

        return new HtmlResponse( 'Weekly Jobs Completed' );
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