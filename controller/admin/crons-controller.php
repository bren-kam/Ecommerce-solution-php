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

        /** Run Ashley Feed */
        //$ashley = new AshleyMasterProductFeedGateway();
        //$ashley->run();

        /** Run Site On Time Feed */
        $site_on_time = new SiteOnTimeProductFeedGateway();
        $site_on_time->run();

        // Remove Discontinued products
        $account_product = new AccountProduct();
        $account_product->remove_all_discontinued();

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

        return new HtmlResponse( 'Weekly Jobs Completed' );
    }
}