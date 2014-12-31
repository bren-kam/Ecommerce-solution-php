<?php
class ApiRequest {
	/**
	 * Constant paths to include files
	 */
	const DEBUG = false;

	/**
	 * Set of messages used throughout the script for easy access
	 * @var array $messages
	 */
	protected $messages = array(
		'error' => 'An unknown error has occurred. This has been reported to the Database Administrator. Please try again later.'
		, 'failed-add-order-item' => 'Failed to add the order item. Please verify you have the correct parameters.'
		, 'failed-authentication' => 'Authentication failed. Please verify you have the correct Authorization Key.'
		, 'failed-create-order' => 'Create Order failed. Please verify you have sent the correct parameters.'
		, 'failed-create-user' => 'Create User failed. Please verify you have sent the correct parameters.'
		, 'failed-create-authorized-users' => 'Create Authorized Users failed. Please verify you have sent the correct parameters.'
		, 'failed-create-website' => 'Create Website failed. Please verify you have sent the correct parameters.'
		, 'failed-install-package' => 'Failed to install a package. Please verify you have sent the correct parameters.'
        , 'failed-add-note' => 'Add Note failed. Please verify you have sent the correct parameters.'
        , 'failed-update-social-media' => 'Update Social media failed. Please verify you have sent the correct parameters.'
		, 'failed-update-user' => 'Update User failed. Please verify you have sent the correct parameters.'
		, 'failed-set-arb-subscription' => 'Update User ARB Subscription failed. Please verify you have sent the correct parameters.'
		, 'no-authentication-key' => 'Authentication failed. No Authorization Key was sent.'
		, 'ssl-required' => 'You must make the call to the secured version of our website.'
		, 'success-add-order-item' => 'Add Order Item succeeded!'
		, 'success-create-order' => 'Create Order succeeded!'
		, 'success-create-user' => 'Create User succeeded!'
		, 'success-create-authorized-users' => 'Create Authorized Users succeeded!'
		, 'success-create-website' => 'Create Website succeeded! The checklist and checklist items have also been created.'
		, 'success-install-package' => 'Successfully installed package!'
        , 'success-add-note' => 'Add Note succeeded! You can see the information in the dashboard.'
        , 'success-update-social-media' => 'Update Social Media succeeded!'
		, 'success-update-user' => 'Update User succeeded!'
		 ,'success-set-arb-subscription' => 'Update User ARB Subscription succeeded!'
	);
	
	/**
	 * Set of valid methods
	 * @var array $messages
	 */
	protected $methods = array(
		'add_order_item'
		, 'create_order'
		, 'create_user'
		, 'create_authorized_users'
		, 'create_website'
		, 'install_package'
        , 'add_note'
        , 'update-social-media'
		, 'update_user'
		, 'set_arb_subscription'
        , 'sendgrid_event_callback'
	);
	
	/**
	 * Pieces of data accrued throughout processing
	 */
	protected $company_id = 0;
    protected $method = '';
    protected $error_message = '';
    protected $response = array();
	
	/**
	 * Statuses of different stages of processing
	 */
    protected $statuses = array( 
		'init' => false,
		'auth' => false,
		'method_called' => false
	);
    protected $logged = false;
    protected $error = false;

    /**
     * Password Manager Group IDs
     */
    protected $group_ids = array(
        'A' => 342
        , 'B' => 343
        , 'C' => 345
        , 'D' => 346
        , 'E' => 347
        , 'F' => 348
        , 'G' => 349
        , 'H' => 350
        , 'I' => 364
        , 'J' => 351
        , 'K' => 352
        , 'L' => 353
        , 'M' => 354
        , 'N' => 355
        , 'O' => 356
        , 'P' => 357
        , 'Q' => 606
        , 'R' => 358
        , 'S' => 359
        , 'T' => 360
        , 'U' => 468
        , 'V' => 405
        , 'W' => 361
        , 'X' => 669
        , 'Z' => 829
    );
	
	/**
	 * Hold WHM object
     *
     * @var WHM_API
	 */
    protected $whm;
	
	/**
	 * Construct class will initiate and run everything
	 *
	 * This class simply needs to be initiated for it run to the data on $_POST variables
	 */
	public function __construct() {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );

		// Load everything that needs to be loaded
		$this->init();

        if ( $this->error )
            return;
		
		// Authenticate & load company id
		$this->authenticate();
		
        if ( $this->error )
            return;

		// Parse method
        $this->parse();
	}

    /**
     * Get response
     *
     * @return array
     */
    public function get_response() {
        return $this->response;
    }

    /**************************/
    /* START: GSR API Methods */
    /**************************/

    /**
	 * Add Order Item
	 */
	protected function add_order_item() {
		/**
         * @param int $order_id
         * @param string $item The item name
         * @param int $quantity
         * @param float $amount the setup cost
         * @param float $monthly the monthly cost
         * @return bool
         */
        extract( $this->get_parameters( 'order_id', 'item', 'quantity', 'amount', 'monthly' ) );

        $order_item = new OrderItem();
        $order_item->order_id = $order_id;
        $order_item->item = $item;
        $order_item->quantity = $quantity;
        $order_item->amount = $amount;
        $order_item->monthly = $monthly;
        $order_item->create();
		
		$this->add_response( array( 'success' => true, 'message' => 'success-add-order-item' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nOrder ID: " . $order_item->id, true );
	}
	
	/**
	 * Create Order
	 */
	protected function create_order() {
        /**
         * @param int $user_id
         * @param float $setup
         * @param float $monthly
         */
        extract( $this->get_parameters( 'user_id', 'setup', 'monthly' ) );

        $order = new Order();
        $order->user_id = $user_id;
        $order->total_amount = $setup;
        $order->total_monthly = $monthly;
        $order->type = 'new-website';
        $order->status = 0;
        $order->create();

		$this->add_response( array( 'success' => true, 'message' => 'success-create-order', 'order_id' => $order->id ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nUser ID: " . $order->user_id . "\nOrder ID:" . $order->id, true );
	}
	
	/**
	 * Create User
	 */
	protected function create_user() {
        /**
         * @param string $email
         * @param string $password
         * @param string $contact_name
         * @param string $store_name
         * @param string $work_phone
         * @param string $cell_phone
         * @param string $billing_first_name
         * @param string $billing_last_name
         * @param string $billing_address1
         * @param string $billing_city
         * @param string $billing_state
         * @param string $billing_zip
         */
        extract( $this->get_parameters( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip' ) );

        // Setup user
        $user = new User();
        $user->get_by_email( $email, false );
        $user->status = 1;

        // See if already exists
        if ( $user->id ) {
            $user->save();
        } else {
            $user->email = $email;
            $user->contact_name = $contact_name;
            $user->store_name = $store_name;
            $user->work_phone = $work_phone;
            $user->cell_phone = $cell_phone;
            $user->billing_first_name = $billing_first_name;
            $user->billing_last_name = $billing_last_name;
            $user->billing_address1 = $billing_address1;
            $user->billing_city = $billing_city;
            $user->billing_state = $billing_state;
            $user->billing_zip = $billing_zip;
            $user->role = User::ROLE_STORE_OWNER;
            $user->company_id = $this->company_id;

            // Create user
            $user->create();

            // Set password
            $user->set_password( $password );
        }

		$this->add_response( array( 'success' => true, 'message' => 'success-create-user', 'user_id' => (int) $user->id ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nUser ID: $user->id", true );
	}

    /**
	 * Create Authorized Users
	 */
	protected function create_authorized_users() {
		/**
         *
         * @param int $website_id
         * @param array $emails
         * @return bool
         */
		extract( $this->get_parameters( 'website_id', 'emails' ) );

        if ( !$this->verify_website( $website_id ) )
            return;
		
        if ( is_array( $emails ) )
        // Create each authorized user
        foreach ( $emails as $email ) {
            $pieces = explode( '@', $email );

            $auth_user_website = new AuthUserWebsite();
            $auth_user_website->add( $pieces[0], $email, $website_id, 1, 1, 1, 0, 0, 0 );
        }

		$this->add_response( array( 'success' => true, 'message' => 'success-authorized-users' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}
	
	/**
	 * Create Website
	 */
	protected function create_website() {
        /**
         * @param int $user_id
         * @param string $domain
         * @param string $title
         * @param string $plan_name
         * @param string $plan_description
         * @param string $type
         * @param bool $pages
         * @param bool $product_catalog
         * @param bool $blog
         * @param bool $email_marketing
         * @param bool $shopping_cart
         * @param bool $room_planner
         * @param bool $craigslist
         * @param bool $social_media
         * @param bool $geo_marketing
         * @param bool $domain_registration
         * @param bool $additional_email_addresses
         * @param int $products
         */

        try {

            // Gets parameters and errors out if something is missing
            extract($this->get_parameters('user_id', 'domain', 'title', 'plan_name', 'plan_description', 'type', 'pages', 'product_catalog', 'blog', 'email_marketing', 'shopping_cart', 'room_planner', 'craigslist', 'social_media', 'geo_marketing', 'domain_registration', 'additional_email_addresses', 'products'));

            // Create account
            $account = new Account();

            $server = new Server();
            $server->get($title{0} < 'i' ? 1 : 2);

            $this->log('method', '"' . $this->method . '": Title: ' . $title . '.Server: ' . json_encode($server), true);

            // Create
            $account->user_id = $user_id;
            $account->domain = $domain;
            $account->title = $title;
            $account->plan_name = $plan_name;
            $account->plan_description = $plan_description;
            $account->type = $type;
            $account->pages = $pages;
            $account->status = 1;
            $account->product_catalog = $product_catalog;
            $account->blog = $blog;
            $account->email_marketing = $email_marketing;
            $account->geo_marketing = $geo_marketing;
            $account->shopping_cart = $shopping_cart;
            $account->room_planner = $room_planner;
            $account->craigslist = $craigslist;
            $account->social_media = $social_media;
            $account->domain_registration = $domain_registration;
            $account->additional_email_Addresses = $additional_email_addresses;
            $account->products = $products;
            $account->server_id = $server->id;

            // Create and update
            $account->create(); // Doesn't add them all

            $account->user_id_updated = $user_id;
            $account->save();

            // Needs to create a checklist
            $checklist = new Checklist();
            $checklist->website_id = $account->id;
            $checklist->type = 'Website Setup';
            $checklist->create();

            // Add checklist website items
            $checklist_website_item = new ChecklistWebsiteItem();
            $checklist_website_item->add_all_to_checklist($checklist->id);

            // If they had social media, add all the plugins, they get update this later
            if ('1' == $account->social_media)
                $account->set_settings(array(
                    'social-media-add-ons' => 'a:10:{i:0;s:13:"email-sign-up";i:1;s:9:"fan-offer";i:2;s:11:"sweepstakes";i:3;s:14:"share-and-save";i:4;s:13:"facebook-site";i:5;s:10:"contact-us";i:6;s:8:"about-us";i:7;s:8:"products";i:8;s:10:"current-ad";i:9;s:7:"posting";}'
                ));

            // Set Industries if they got craigslist
            if ('1' == $account->craigslist)
                $account->add_industries(array(1, 2, 3));

            // Create WHM account and setup Password
            if ('1' == $account->pages) {
                library('pm-api');

                // First we need to create the group and the password
                $pm = new PM_API(Config::key('s98-pm-key'));

                $this->log('method', '"' . $this->method . '": About to Create Group', true);

                // Get the group ID
                $group_id = $pm->create_group($account->title, $this->group_ids[strtoupper($account->title[0])]);

                $this->log('method', '"' . $this->method . '": Group: ' . json_encode($pm), true);

                if ($group_id) {
                    library('whm-api');
                    $this->whm = new WHM_API($server);
                    $company = new Company();

                    // Make sure it's a unique username
                    $company->get($this->company_id);
                    $email = 'serveradmin@' . url::domain($company->domain, false);
                    $domain = $this->unique_domain($account->title);
                    $username = $this->unique_username($account->title);
                    $password = security::generate_password();

                    $this->log('method', '"' . $this->method . '": Trying to create user: ' . $username, true);

                    // Create the password
                    $password_id = $pm->create_password($group_id, 'cPanel/FTP', $username, $password, $server->ip);

                    if (!$password_id) {
                        $this->add_response(array('success' => false, 'message' => 'failed-create-website'));
                        return;
                    }

                    // Now, create the WHM API accounts
                    if (!$this->whm->create_account($username, $domain, 'Basic No Shopping Cart', 'serveradmin@imagineretailer.com', $password)) {
                        $this->add_response(array('success' => false, 'message' => 'failed-create-website'));
                        return;
                    }

                    // Update the domain field
                    $account->ftp_username = security::encrypt($username, ENCRYPTION_KEY, true);
                    $account->domain = $domain;
                    $account->user_id_updated = $user_id;
                    $account->save();

                    // Get user
                    $user = new User();
                    $user->get($account->user_id);

                    // Set address settings
                    $account->set_settings(array(
                        'address' => $user->billing_address1
                    , 'city' => $user->billing_city
                    , 'state' => $user->billing_state
                    , 'zip' => $user->billing_zip
                    ));

                    // Now need to install the service
                    $install_service = new InstallService();
                    $install_service->install_website($account, $user_id);

                    // Setup DNS
                    library('r53');

                    $r53 = new Route53(Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key'));

                    // Add to domain.blinkyblinky.me
                    $r53->changeResourceRecordSets('hostedzone/Z20FV3IPLIV928', array($r53->prepareChange('CREATE', $domain . '.', 'A', '14400', $server->ip)));
                }

            }

            // Everything was successful
            $this->add_response(array('success' => true, 'message' => 'success-create-website', 'website_id' => $account->id));
            $this->log('method', 'The method "' . $this->method . '" has been successfully called.' . "\nUser ID: " . $account->user_id . "\nWebsite ID: {$account->id}", true);
        } catch ( Exception $e ) {
            $this->log('method', 'The method "' . $this->method . '" failed: ' . $e->getMessage(), true);
        }
	}

    /**
     * Install Package
     */
    protected function install_package() {
        /**
         *  @param int $website_id
         * @param int $company_package_id
         */
        extract( $this->get_parameters( 'website_id', 'company_package_id' ) );

        if ( !$this->verify_website( $website_id ) )
            return;

        // Get the account and update the package
        $account = new Account;
        $account->get( $website_id );
        $account->company_package_id = $company_package_id;
        $account->user_id_updated = -1;  // This service does not asks for a user id
        $account->save();

        try {
            // Install the package
            $install_service = new InstallService();
            $user_id = -1; // This service doesn't asks for a user
            $install_service->install_package( $account, $user_id );
        } catch (Exception $e) {
            $this->log( 'error', "Error installing package: \n " . $e->getMessage() . " on " . $e->getFile() . " at line " . $e->getLine() . "\n" . $e->getTraceAsString() . "\n" . json_encode($account) , false );
        }

        // Everything was successful
        $this->add_response( array( 'success' => true, 'message' => 'success-install-package', 'website_id' => $website_id ) );
        $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nWebsite ID: $website_id\nCompany Package ID: $company_package_id", true );
    }

    /**
	 * Add Note
	 */
	protected function add_note() {
        /**
         * @param int $website_id
         * @param int $user_id
         * @param string $message
         */
        extract( $this->get_parameters( 'website_id', 'user_id', 'message' ) );

        if ( !$this->verify_website( $website_id ) )
            return;

        // Create account note
        $account_note = new AccountNote();
        $account_note->website_id = $website_id;
        $account_note->user_id = $user_id;
        $account_note->message = $message;
        $account_note->create();

		$this->add_response( array( 'success' => true, 'message' => 'success-add-note' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nWebsite ID: $website_id\nUser ID: $user_id", true );
	}

    /**
	 * Update Social Media
	 */
	protected function update_social_media() {
        /**
         * @param int $website_id
         * @param array $website_social_media_add_ons
         */
        extract( $this->get_parameters( 'website_id', 'website_social_media_add_ons' ) );

        // Make sure we can edit this website
        if ( !$this->verify_website( $website_id ) )
            return;

        if ( !is_array( $website_social_media_add_ons ) ) {
            $this->add_response( array( 'success' => false, 'message' => 'failed-update-social-media' ) );
            return;
        }

        // Master list of social media add ons
        $social_media_add_ons = array(
            'email-sign-up'
            , 'fan-offer'
            , 'sweepstakes'
            , 'share-and-save'
            , 'facebook-site'
            , 'contact-us'
            , 'about-us'
            , 'products'
            , 'current-ad'
            , 'posting'
        );

        // Make sure we only have valid arguments
        foreach ( $website_social_media_add_ons as &$value ) {
            if ( !in_array( $value, $social_media_add_ons ) )
                unset( $value );
        }

        // Check again to make sure it is an array
        if ( !is_array( $website_social_media_add_ons ) ) {
            $this->add_response( array( 'success' => false, 'message' => 'failed-update-social-media' ) );
            return;
        }

        // Type Juggling
        $account = new Account;
        $account->get( $website_id );
        $account->set_settings( array(
            'social-media-add-ons' => serialize( $website_social_media_add_ons )
        ) );

		$this->add_response( array( 'success' => true, 'message' => 'success-update-social-media' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. Website ID: ' . $website_id, true );
	}

	/**
	 * Update User
	 */
	protected function update_user() {
        /**
         * @param string $email
         * @param string $password
         * @param string $contact_name
         * @param string $store_name
         * @param string $work_phone
         * @param string $cell_phone
         * @param string $billing_first_name
         * @param string $billing_last_name
         * @param string $billing_address1
         * @param string $billing_city
         * @param string $billing_state
         * @param string $billing_zip
         * @param int $user_id
         */
        $personal_information = $this->get_parameters( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip', 'user_id' );
		
		// Get the user_id, but we don't want it in the update data
		$user_id = $personal_information['user_id'];
		unset( $personal_information['user_id'] );
		
		// Make sure he exists, if not, create user
        $user = new User();
        $user->get( $user_id );

        $user->email = $email;
        $user->contact_name = $contact_name;
        $user->store_name = $store_name;
        $user->work_phone = $work_phone;
        $user->cell_phone = $cell_phone;
        $user->billing_first_name = $billing_first_name;
        $user->billing_last_name = $billing_last_name;
        $user->billing_address1 = $billing_address1;
        $user->billing_city = $billing_city;
        $user->billing_state = $billing_state;
        $user->billing_zip = $billing_zip;

        if ( $user->id ) {
            $user->save();
        } else {
            $user->company_id = $this->company_id;
            $user->role = User::ROLE_STORE_OWNER;
            $user->status = 1;
            $user->create();
        }

        $user->set_password( $password );

		$this->add_response( array( 'success' => true, 'message' => 'success-update-user' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. User ID: ' . $user_id, true );
	}
	
	/**
	 * Set ARB Subscription
	 *
	 * ARB is Automatic Recurring Billing (part of Authorize.net)
	 */
	protected function set_arb_subscription() {
        /**
         * @param int $arb_subscription_id
         * @param int $website_id
         */
        extract( $this->get_parameters( 'arb_subscription_id', 'website_id' ) );

        // Make sure we can edit this website
        $this->verify_website( $website_id );

        $account = new Account();
        $account->get( $website_id );

        // Protection
		$account->set_settings( array(
            'arb-subscription-id' => $arb_subscription_id
        ) );

		$this->add_response( array( 'success' => true, 'message' => 'success-set-arb-subscription' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. Website ID: ' . $website_id, true );
	}


    /**
     * Sendgrid Event Callback
     */
    protected function sendgrid_event_callback() {
        $account_id = $_GET['aid'];
        $input = file_get_contents( 'php://input' );

        $this->log( 'method', "Event a#{$account_id} INPUT: {$input}", true );

        $account = new Account();
        $account->get( $account_id );

        if ( !$account_id ) {
            $this->log( 'method', "Bad account id #{$account_id}", false );
            $this->add_response( array( 'success' => false, 'message' => 'Bad Account ID' ) );
            return;
        }

        $events = json_decode( $input );
        if ( !$events ) {
            $this->log( 'method', "Bad json input", false );
            $this->add_response( array( 'success' => false, 'message' => 'Bad Input' ) );
            return;
        }

        // See https://sendgrid.com/docs/API_Reference/Webhooks/event.html#-Marketing-Email-Unsubscribes
        foreach ( $events as $event ) {
            if ( $event->event != 'unsubscribe' )
                continue;

            $email = new Email();
            $email->get_by_email( $account_id, $event->email );

            if ( !$email->email_id ) {
                $this->log( 'method', "Email {$event->email} not found", true );
                continue;
            }

            $email->remove_all( $account );
            $this->log( 'method', "Disabled email {$event->email}", true );
        }

        $this->add_response( array( 'success' => true ) );
    }

	
	/***********************/
	/* END: IR API Methods */
	/***********************/
	
	/**
	 * Unique Username
	 *
	 * Runs a loop and returns a unique username
	 *
	 * @param string $title
	 * @return string
	 */
	protected function unique_username( $title ) {
		$username = $this->generate_username( $title );
		$available = $this->whm->account_summary( $username );
		
		while ( false != $available ) {
			$username = $this->generate_username( $title, true );
			$available = $this->whm->account_summary( $username );
			
			if ( false != $available )
				break;
		}
		
		return $username;
	}
	
    /**
     * Generate Username
     *
     * Generates a username for WHM/cPanel based off the title of a website
     *
     * @param string $title
     * @param bool $complicated [optional]
     * @return string
     */
    protected function generate_username( $title, $complicated = false ) {
        $pieces = explode( ' ', preg_replace( '/[^a-z0-9 ]/', '', strtolower( $title ) ) );
        $increment = ( $complicated ) ? 0 : 2;

        if ( is_array( $pieces ) && count( $pieces ) > 1 ) {
            $username = substr( $pieces[0], 0, 4 );
            $username .= substr( $pieces[1], 0, 2 + $increment );
        } else {
            $username = substr( $pieces[0], 0, 6 + $increment );
        }

        if ( $complicated )
            $username .= rand( 1, 99 );

        return str_replace( 'test', 'tset', $username );
    }
	
	/**
	 * Unique Domain
	 *
	 * Runs a loop and returns a unique domain
	 *
	 * @param string $title
	 * @return string
	 */
	protected function unique_domain( $title ) {
		$domain = $this->generate_domain( $title );
		
		$available = $this->whm->domain_user_data( $domain );
		
		while ( false != $available ) {
			$domain = $this->generate_domain( $title, true );
			$available = $this->whm->domain_user_data( $domain );
			
			if ( false != $available )
				break;
		}
		
		return $domain;
	}
	
	/**
	 * Generate Domain
	 *
	 * Generates a unique domain for WHM/cPnale
	 *
	 * @param string $title
     * @param bool $complicated [optional
     * @return string
	 */
	protected function generate_domain( $title, $complicated = false ) {
		$domain = preg_replace( '/[^a-z]/', '', strtolower( $title ) );
		
		if ( $complicated )
            $domain .= rand( 1, 999 );
		
		return $domain . '.blinkyblinky.me';
	}
	
    /**
     * Check to make sure a website belongs to the company
     *
     * @param int $website_id
     * @return bool
     */
    protected function verify_website( $website_id ) {
        // Type Juggling
        $website_id = (int) $website_id;
        $company_id = (int) $this->company_id;

        // See if we can grab the website ID
        $account = new Account;
        $account->get( $website_id );

        // Verify that it exists
        if ( $account->company_id != $company_id ) {
            $this->add_response( array( 'success' => false, 'message' => 'failed-website-verification' ) );
            return false;
        }

        return true;
    }
    
    /**
	 * This loads all the variables that we need
	 *
	 * @access protected
	 */
	protected function init() {
		// Make sure it's ssl
		if( !security::is_ssl() ) {
			$this->add_response( array( 'success' => false, 'message' => 'ssl-required' ) );

			$this->error = true;
			$this->error_message = 'The request was made without SSL';
			return;
		}

		$this->statuses['init'] = true;
	}
    
    /**
	 * This authenticates the request and loads the company data
	 *
	 * @access protected
	 */
	protected function authenticate() {
		// They didn't send an authorization key
		if( !isset( $_REQUEST['auth_key'] ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'no-authentication-key' ) );
			
			$this->error = true;
			$this->error_message = 'There was no authentication key';
			return;
		}

        $api_key = new ApiKey;
        $api_key->get_by_key( $_REQUEST['auth_key'] );

        $this->company_id = (int) $api_key->company_id;

		// If failed to grab any company id
		if( !$this->company_id ) {
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			
			$this->error = true;
			$this->error_message = 'There was no company to match API key';
			return;
		}

        define( 'DOMAIN', $api_key->domain );

		$this->statuses['auth'] = true;
	}
	
	/**
	 * This parses the request and calls the correct functions
	 *
	 * @access protected
	 */
	protected function parse() {
		if( in_array( $_REQUEST['method'], $this->methods ) ) {
			$this->method = $_REQUEST['method'];
			$this->statuses['method_called'] = true;
			
			call_user_func( array( 'ApiRequest', $_REQUEST['method'] ) );

            // used to be destruct
            // Make sure we haven't already logged something
            if( !$this->logged )
            if( $this->error ) {
                $message = '';

                foreach( $this->statuses as $status => $value ) {
                    // Set the message status name
                    $message_status = ucwords( str_replace( '_', ' ', $status ) );

                    $message .= ( $this->statuses[$status] ) ? "$message_status: True" : "$message_status: False";
                    $message .= "\n";
                }

                $this->log( 'error', 'Error: ' . $this->error_message . "\n\n" . rtrim( $message, "\n" ), false );
            } else {
                $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
            }
		} else {
			$this->add_response( array( 'success' => false, 'message' => 'The method, "' . $_REQUEST['method'] . '", is not a valid method.' ) );
			
			$this->error = true;
			$this->error_message = 'The method, "' . $_REQUEST['method'] . '", is not a valid method.';
		}
	}
	
	/**
	 * Add a response to be sent
	 *
	 * Adds data to the response that will be sent back to the client
	 *
	 * @param string|array $key this can contain the key OR an array of key => value pairs
	 * @param string $value (optional) $value of the $key. Only optional if $key is an array
	 */
	protected function add_response( $key, $value = '' ) {
		if( empty( $value ) && !is_array( $key ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'error' ) );
		}
		
		// Set the response
		if( is_array( $key ) ) {
			foreach( $key as $k => $v ) {
				// Makes sure there isn't a premade message
				$this->response[$k] = ( ( is_string( $v ) || is_int( $v ) ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
			}
		} else {
			// Makes sure there isn't a premade message
			$this->response[$key] = ( !is_array( $value ) && array_key_exists( $value, $this->messages ) ) ? $this->messages[$value] : $value;
		}
	}
	
	/**
	 * Gets parameters from the post variable and returns and associative array with those values
	 *
	 * @param mixed $arg1,$arg2,$arg3... the args that contain the parameters to get
	 * @return array $parameters
	 */
	protected function get_parameters() {
		$args = func_get_args();
		
		// Make sure the arguments are correct
		if( !is_array( $args ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'error' ) );
			return array();
		}

        $parameters = array();

		// Go through each argument
		foreach( $args as $a ) {
			// Make sure the argument is set
			if( !isset( $_POST[$a] ) ) {
				$message = 'Required parameter "' . $a . '" was not set for the method "' . $this->method . '".';
				$this->add_response( array( 'success' => false, 'message' => $message ) );
				
				$this->error = true;
				$this->error_message = $message;
				return array();
			}
			
			$parameters[$a] = $_POST[$a];
		}
		
		// Return arguments
		return $parameters;
	}
	
	/**
	 * Adds an log entry to the API log table
	 *
	 * @param string $type the type of log entry
	 * @param string $message message to be put into the log
 	 * @param bool $success whether the call was successful
	 * @param bool $set_logged (optional) whether to set the logged variable as true
	 */
	protected function log( $type, $message, $success, $set_logged = true ) {
		// Set before hand so that a loop isn't caught in the destructor
		if( $set_logged )
			$this->logged = true;

        // Create log entry
        $api_log = new ApiLog();
        $api_log->company_id = $this->company_id;
        $api_log->type = $type;
        $api_log->method = $this->method;
        $api_log->message = $message;
        $api_log->success = $success;
        $api_log->create();
	}
}