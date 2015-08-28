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
     * Delete Old Action Log
     */
    public function cleanup_action_log() {
        $action_log = new ActionLog();
        $action_log->cleanup();
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

            $ticket = new Ticket();
            $ticket->user_id = User::TECHNICAL;
            $ticket->assigned_to_user_id = User::TECHNICAL;
            $ticket->website_id = null;
            $ticket->priority = Ticket::PRIORITY_HIGH;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->summary = 'Disabled Sites since ' . $yesterday->format('Y-m-d');
            $ticket->message = $message;
            $ticket->create();
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

    /**
     * YEXT Download Reports
     */
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
     * YEXT Download Reviews
     */
    public function yext_download_reviews() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        global $argv;

        $account = new Account();
        $account->get(1352);

        library('yext');
        $yext = new YEXT( $account );

        $start_date = (new DateTime($argv[2]));
        echo "Downloading reviews since " . $start_date->format('Y-m-d') . "...\n";

        $reviews = $yext->get(
            'reviews'
            , [ 'dateStart' => $start_date->format('Y-m-d') ]
        )->reviews;

        if ( empty( $reviews ) ) {
            echo "No Reviews. Finished. \n";
            return;
        }

        $yext_location = new WebsiteYextLocation();
        $locations = $yext_location->list_all( [ '', '', '', 999999 ] );
        foreach ( $locations as $location ) {
            $locations[$location->id] = $location;
        }

        foreach ( $reviews as $review ) {
            try {

                $review_location = isset( $locations[$review->locationId] ) ? $locations[ $review->locationId] : null;
                if ( !$review_location ) {
                    throw new Exception( "Location ID '{$review->locationId}' not found." );
                }

                $website_yext_review = new WebsiteYextReview();
                $website_yext_review->id = $review->id;
                $website_yext_review->location_id = $review->locationId;
                $website_yext_review->site_id = $review->siteId;
                $website_yext_review->rating = isset($review->rating) ? $review->rating : null;
                $website_yext_review->title = isset($review->title) ? $review->title : null;
                $website_yext_review->content = $review->content;
                $website_yext_review->author_name = isset($review->authorName) ? $review->authorName : null;
                $website_yext_review->url = $review->url;
                $website_yext_review->date_created = $review->reviewDate;
                $website_yext_review->create();

                echo "Saved: {$review->id}|{$review->locationId}|{$review->siteId}\n";

                $review_account = new Account();
                $review_account->website_id = $review_location->website_id;
                $review_account->get( $review_account->website_id );
                $email_settings = $review_account->get_settings('yext-customer-reviews', 'yext-review-disable-email', 'yext-review-email-address');
                if ( $email_settings['yext-customer-reviews'] && !$email_settings['yext-review-disable-email'] ) {

                    $to = $email_settings['yext-review-email-address'];
                    if ( !$to ) {
                        $user = new User();
                        $user->get( $review_account->user_id );
                        $to = $user->email;
                    }

                    $os = new User();
                    $os->get( $review_account->os_user_id );
                    if ( $os->email ) {
                        $to .= "," . $os->email;
                    }

                    echo "    Sending email TO {$to} ...\n";

                    $website_yext_review->get( $website_yext_review->id, $review_location->website_id );
                    $site_name = ucfirst( strtolower( $website_yext_review->site_id ) );
                    $review_score = floor($website_yext_review->rating * 2);
                    $review_date = new DateTime( $website_yext_review->date_created );
                    $author_name = $website_yext_review->author_name ? ('by ' . $website_yext_review->author_name) : '';

                    $content = file_get_contents( VIEW_PATH . '../account/geo-marketing/reviews/_email.php' );

                    $content = str_replace(
                        ['[site_name]', '[site_id]', '[review_author]', '[review_text]', '[review_date]', '[review_time]', '[review_url]', '[review_score]', '[location_name]', '[location_address]']
                        , [$site_name, $website_yext_review->site_id, $author_name, $website_yext_review->content, $review_date->format('D, n/j/y'), $review_date->format('g:i A'), $website_yext_review->url, $review_score, $website_yext_review->location_name, $website_yext_review->location_address]
                        , $content
                    );

                    $subject = "Review on $site_name";
                    if ( $website_yext_review->author_name ) {
                        $subject .= " by {$website_yext_review->author_name}";
                    }
                    $subject .= " on " . $review_date->format('D, n/j/y');

                    echo "\n $to...\n";
                    $success = fn::mail( $to, $subject, $content, 'noreply@' . url::domain($user->domain, false), 'noreply@' . url::domain($review_account->domain, false), false);
                    // Copy to Us
                    $success = fn::mail( 'jack@greysuitretail.com', $subject, $content, 'noreply@' . url::domain($user->domain, false), 'noreply@' . url::domain($review_account->domain, false), false);
                    var_dump($success);

                } else {
                    echo "    Will not send email...\n";
                }
            } catch( Exception $e ) {
                echo "Failed: {$review->id}|{$review->locationId}|{$review->siteId}: " . $e->getMessage() . "\n";
            }
        }
        echo "Finished.\n";
    }

    public function sm_post_scheduled() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        $website_sm_post = new WebsiteSmPost();
        $posts = $website_sm_post->get_all_unposted();

        foreach ( $posts as $post ) {
            echo "{$post->id} {$post->post_at} {$post->timezone}...";
            if ( $post->can_post() ) {
                echo "posting";
                $success = $post->post();
                if ( !$success ) {
                    echo "..." . $post->sm_message;
                }
            } else {
                echo "don't post yet";
            }

            echo "\n";
        }

        echo "Finished\n";

    }

    /**
     * Reorganize Categories ALL
     *
     *
     * @return HtmlResponse
     */
    protected function reorganize_categories_all() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        $account = new Account();
        $accounts = $account->list_all( array(' AND a.status=1 ', '', '', 10000 ) );
        $account_category = new AccountCategory();
        $category = new Category();

        foreach ( $accounts as $a ) {
            echo "#{$a->id}...<br>\n";
            flush();
            $account_category->reorganize_categories( $a->website_id, $category );
        }

        return new HtmlResponse( 'Finished' );
    }

    /**
     * Jira get Updates
     */
    protected function jira_get_updates() {
        // Set it as a background job
        if ( extension_loaded('newrelic') )
            newrelic_background_job();

        library('jira');
        $jira = new Jira();

        $ticket = new Ticket();
        $ticket_comment = new TicketComment();

        $tickets_in_jira = $ticket->list_all( [ ' AND a.`jira_id` IS NOT NULL AND a.`status` = 0 ', '', '', 9999 ] );
        foreach ( $tickets_in_jira as $ticket ) {

            $ticket->get( $ticket->ticket_id );

            // Pull Comments from Jira
            echo "Getting comments for ticket {$ticket->ticket_id} {$ticket->jira_key}\n";
            $jira_comments = $jira->get_comments_by_issue( $ticket->jira_id );
            if ( isset($jira_comments->comments) ) {
                // Get GSR Ticket Comment by Jira ID
                $ticket_comment_list = $ticket_comment->get_by_ticket( $ticket->ticket_id );
                $ticket_comments = [];
                foreach ( $ticket_comment_list as $tc ) {
                    $ticket_comments[$tc->jira_id] = $tc;
                }

                foreach ( $jira_comments->comments as $jira_comment ) {
                    // Create Comment if not found
                    if ( !isset($ticket_comments[(int)$jira_comment->id]) ) {
                        echo "Creating comment Jira#{$jira_comment->id}\n";
                        $tc = new TicketComment();
                        $tc->ticket_id = $ticket->ticket_id;
                        $tc->user_id = User::DEVELOPMENT;
                        $tc->comment = $jira_comment->body;
                        $tc->jira_id = $jira_comment->id;
                        $tc->private = isset($jira_comment->visibility);
                        $tc->create();
                    }
                }
            }

            // Get Issue information from Jira
            echo "Updating Ticket {$ticket->ticket_id} information from Jira issue {$ticket->jira_key}\n";
            $jira_issue = $jira->get_issue( $ticket->jira_id );
            if ( isset( $jira_issue->fields->status ) ) {
                if ( $jira_issue->fields->status->name == 'Done' ) {
                    $ticket->status = Ticket::STATUS_CLOSED;
                }

                if ( $jira_issue->fields->status->name == 'Waiting for OS' ) {
                    $ticket->assigned_to_user_id = $ticket->user_id;
                }

                $ticket->save();
            }

        }

        echo "Finished\n";

    }

    /**
     * Add Image Dimensions
     */
    public function addImageDimensions() {
        ini_set( 'memory_limit', '512M' );
        $product = new Product();
        $images = $product->get_results("SELECT i.name industry, pi.product_id, pi.product_image_id, pi.image
                                FROM products p
                                INNER JOIN industries i ON p.industry_id = i.industry_id
                                INNER JOIN product_images pi ON p.product_id = pi.product_id
                                WHERE pi.width IS NULL AND p.publish_visibility = 'public'"
            , PDO::FETCH_ASSOC);

        $i = 0;
        $total = count($images);
        foreach ( $images as &$image ) {
            try {
                if ( strpos($image['image'], 'http') === 0 )
                   $url = $image['image'];
                else
                    $url = "http://{$image['industry']}.retailcatalog.us/products/{$image['product_id']}/large/{$image['image']}";

                list( $width, $height ) = getimagesize($url);
                $sql = "UPDATE product_images SET width = " .  (int) $width . ", height = " . (int) $height . " WHERE product_image_id = {$image['product_image_id']}";
                //echo "{$sql}\n";
                $product->query($sql);

            } catch( Exception $e ) {
                echo "Failed {$image['product_image_id']} " . $e->getMessage() . "\n";
            }
            if ( $i++ % 1000 === 0 ) {
                echo "$i/$total...\n";
            }
        }
        echo "Finished\n";
    }

    /**
     * Discontinue Orphan Packages
     */
    public function discontinue_orphan_packages() {
        $product = new Product();

        $product->discontinue_orphan_packages( true );
    }

    /**
     * Send Remarketing Emails
     */
    public function send_remarketing_emails() {
        $account = new Account();
        $website_ids = $account->get_col("SELECT website_id FROM website_settings WHERE `key` = 'remarketing-enabled' AND `value` = 1");

        foreach ( $website_ids as $website_id ) {
            echo "Looking for pending emails for website $website_id\n";

            $website_carts = $account->get_results("SELECT wc.website_cart_id, wc.name, wc.email, wc.last_remarketing_email, wc.timestamp FROM website_carts wc LEFT JOIN website_orders wo ON wc.website_cart_id = wo.website_cart_id WHERE wo.website_cart_id IS NULL and wc.email IS NOT NULL AND last_remarketing_email < 3 AND wc.website_id = {$website_id}", PDO::FETCH_ASSOC);
            $account->get($website_id);

            $reply_to = new User();
            $reply_to->get($account->user_id);

            $settings = $account->get_settings([
                'remarketing-email1-enabled'
                , 'remarketing-email1-delay'
                , 'remarketing-email2-enabled'
                , 'remarketing-email2-delay'
                , 'remarketing-email3-enabled'
                , 'remarketing-email3-delay'
            ]);

            foreach ( $website_carts as $website_cart ) {
                echo "> Cart {$website_cart['website_cart_id']} '{$website_cart['name']}' <{$website_cart['email']}>.\n> Emails sent so far: {$website_cart['last_remarketing_email']}\n";

                $email_number = $website_cart['last_remarketing_email'] + 1;
                if ( $email_number < 1 ) {
                    $email_number = 1;
                }

                $email_body_url = "http://{$account->domain}/shopping-cart/remarketing-email/?wcid={$website_cart['website_cart_id']}&email_number={$email_number}";
                $email_body = file_get_contents($email_body_url);

                //    $email = fn::build_html_with_attachments($email_body, "{$account->title} <noreply@{$account->domain}>", $account->domain);
y

                if ( strpos($email_body, '<img src="" alt="" border="0"/>') !== FALSE ) {
                    echo "> > Could not get email for cart {$website_cart['website_cart_id']}, trying again later\n";
                    echo "> > Please visit {$email_body_url} for more information\n";
                    continue;
                }

                $is_enabled = (bool) $settings["remarketing-email{$email_number}-enabled"];


                if ( !$is_enabled ) {
                    echo "> > Email #{$email_number} disabled. Skipping\n";
                    continue;
                }

                $email_delay = (int) $settings["remarketing-email{$email_number}-delay"];
                echo "> > Email Delay: {$email_delay} seconds.\n";

                $time_elapsed = (new DateTime())->getTimestamp() - (new DateTime($website_cart['timestamp']))->getTimestamp();

                if ( $time_elapsed < $email_delay && $website_cart['email'] != 'juanfgs@gmail.com' ) {
                    echo "> > We need to wait " . ( $email_delay - $time_elapsed ) . " seconds. Skipping\n";
                    continue;
                }
                
                $email_sent = fn::mail(
                    $website_cart['email']
                    , "Your shopping cart is saved!"
                    , $email_body
                    , "noreply@{$account->domain}"
                    , $reply_to->email
                    , false
                    , false

                );

                $email_sent = fn::mail(
                    $website_cart['email']
                    , "Your shopping cart is saved!"
                    , $email['multipart']
                    , "no-reply@blinkyblinky.me"
                    , $reply_to->email
                    , false
                    , false
                );

                if ( $email_sent ) {
                    echo "> > Email #{$email_number} sent!\n";
                    $account->query("UPDATE website_carts SET last_remarketing_email = {$email_number} WHERE website_cart_id = {$website_cart['website_cart_id']}");
                } else {
                    echo "> > There was an error sending email #{$email_number}. Trying again later\n";
                }

            }
            echo "Processed " . count($website_carts) . " carts for {$website_id}\n\n";
        }

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
