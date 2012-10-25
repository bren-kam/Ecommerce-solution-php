<?php
class APIRequest {
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
		
		// Authenticate & load company id
		$this->authenticate();
		
		// Parse method
		$this->parse();
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
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nOrder ID: " . $order_item['order_id'], true );
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
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nUser ID: " . $order['user_id'] . "\nOrder ID:" . $order->id, true );
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
            $user->update();
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
            $user->billing_zip = $billing_zip;

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
         * @param string $emails
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
         * @param bool $seo
         * @param bool $room_planner
         * @param bool $craigslist
         * @param bool $social_media
         * @param bool $domain_registration
         * @param bool $additional_email_addresses
         * @param int $pages
         */

		// Gets parameters and errors out if something is missing
		extract( $this->get_parameters( 'user_id', 'domain', 'title', 'plan_name', 'plan_description', 'type', 'pages', 'product_catalog', 'blog', 'email_marketing', 'shopping_cart', 'seo', 'room_planner', 'craigslist', 'social_media', 'domain_registration', 'additional_email_addresses', 'products' ) );

        // Create account
        $account = new Account();

        // @CONTINUE HERE

        $website['title'] = stripslashes( $website['title'] );
		$website['status'] = 1;
        $website['date_created'] = dt::date('Y-m-d H:i:s');
		
		// Insert website
		$this->db->insert( 'websites', $website, 'isssssiiiiiiiiiiiiis' );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( "Failed to create website.\n\nUser ID: " . $website['user_id'], __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
			exit;
		}
		
		// Get the website ID
		$website_id = (int) $this->db->insert_id;
		
		// Now we have to insert checklists
		$this->db->insert( 'checklists', array( 'website_id' => $website_id, 'type' => 'Website Setup', 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( "Failed to insert checklist.\n\nWebsite ID: $website_id", __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
			exit;
		}
		
		// Get checklist ID
		$checklist_id = (int) $this->db->insert_id;

        // Insert all the checklist items
        $this->db->query( "INSERT INTO `checklist_website_items` ( `checklist_id`, `checklist_item_id` ) SELECT $checklist_id, `checklist_item_id` FROM `checklist_items` WHERE `status` = 1" );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( "Failed to insert checklist.\n\Checklist ID: $checklist_id", __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
			exit;
		}

        // If they had social media, add all the plugins, they get update this later
        if ( '1' == $website['social_media'] ) {
            $this->db->insert( 'website_settings', array( 'website_id' => $website_id, 'key' => 'social-media-add-ons', 'value' => 'a:10:{i:0;s:13:"email-sign-up";i:1;s:9:"fan-offer";i:2;s:11:"sweepstakes";i:3;s:14:"share-and-save";i:4;s:13:"facebook-site";i:5;s:10:"contact-us";i:6;s:8:"about-us";i:7;s:8:"products";i:8;s:10:"current-ad";i:9;s:7:"posting";}' ), 'iss' );

            // If there was a MySQL error
            if( $this->db->errno() ) {
                $this->_err( "Failed to create website settings.\n\Website ID: $website_id", __LINE__, __METHOD__ );
                $this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
                exit;
            }
        }

        // Set Industries if they got craigslist
        if ( '1' == $website['craigslist'] ) {
            $this->db->query( 'INSERT INTO `website_industries` ( `website_id`, `industry_id` ) VALUES ( $website_id, 1 ), ( $website_id, 2 ), ( $website_id, 3 )' );

            // If there was a MySQL error
            if( $this->db->errno() ) {
                $this->_err( "Failed to create website industries.\n\Website ID: $website_id", __LINE__, __METHOD__ );
                $this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
                exit;
            }
        }

        // Create WHM account and setup Password
        if ( '1' == $website['pages'] ) {
            library('pm-api');

            // First we need to create the group and the password
            $pm = new PM_API( config::key('s98-pm-key') );

            // Get the group ID
            $group_id = $pm->create_group( $website['title'], $this->group_ids[strtoupper( $website['title'][0] )] );
			
            if ( $group_id ) {
                library('whm-api');
                $this->whm = new WHM_API();
                $c = new Companies();
				
                // Make sure it's a unique username
                $company = $c->get( $this->company_id );
                $email = 'serveradmin@' . url::domain( $company['domain'], false );
                $domain = $this->_unique_domain( $website['title'] );
                $username = $this->_unique_username( $website['title'] );
                $password = security::generate_password();
				
				// Create the password
                $password_id = $pm->create_password( $group_id, 'cPanel/FTP', $username, $password, '199.79.48.137' );

                if ( !$password_id ) {
                    $this->_err( "Failed to create password:\n" . $pm->error(), __LINE__, __METHOD__ );
                    $this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
                    exit;
                }
				
                // Now, create the WHM API accounts
                if ( !$this->whm->create_account( $username, $domain, 'Basic No Shopping Cart', 'serveradmin@imagineretailer.com', $password ) ) {
                    $this->_err( "Failed to create WHM/cPanel Account:\n$username\n" . $this->whm->message(), __LINE__, __METHOD__ );
                    $this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
					exit;
                }
				
                // Update the domain field
                $this->db->update( 'websites', array( 'domain' => $domain ), array( 'website_id' => $website_id ), 's', 'i' );

                // If there was a MySQL error -- don't stop the intallation
                if( $this->db->errno() )
                    $this->_err( "Failed to update website domain.\n\Website ID: $website_id", __LINE__, __METHOD__ );

                $w = new Websites();
                // Now install

                // Now, create the WHM API accounts
                if ( !$w->install( $website_id, $username ) ) {
                    $this->_err( "Failed to install website", __LINE__, __METHOD__ );
                    $this->add_response( array( 'success' => false, 'message' => 'failed-create-website' ) );
                    exit;
                }

				// Setup DNS
				library('r53');

				$r53 = new Route53( config::key('aws_iam-access-key'), config::key('aws_iam-secret-key') );
				
				// Add to domain.blinkyblinky.me
		        $r53->changeResourceRecordSets( 'hostedzone/Z20FV3IPLIV928', array( $r53->prepareChange( 'CREATE', $domain . '.', 'CNAME', '14400', 'blinkyblinky.me.' ) ) );
            }

        }

		// Everything was successful
		$this->add_response( array( 'success' => true, 'message' => 'success-create-website', 'website_id' => $website_id ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nUser ID: " . $website['user_id'] . "\nWebsite ID: $website_id", true );
	}

    /**
     * Install Package
     *
     * @param int $website_id
     * @param int $company_package_id
     * @return bool
     */
    protected function install_package() {
        // Gets parameters and errors out if something is missing
		extract( $this->get_parameters( 'website_id', 'company_package_id' ) );
		
        // Include Classes
        inc('classes/admin/websites');
        inc('classes/admin/products');
        inc('classes/admin/files');
        inc('classes/admin/categories');

        // Generate fake user
        global $user;
        $user['role'] = 7;
        $user['company_id'] = $this->company_id;

        $w = new Websites();

        $success = $w->install_package( $website_id, $company_package_id );

        if ( !$success ) {
            $this->_err( "Failed to install package", __LINE__, __METHOD__ );
            $this->add_response( array( 'success' => false, 'message' => 'failed-install-package' ) );
        }

        // Everything was successful
        $this->add_response( array( 'success' => true, 'message' => 'success-install-package', 'website_id' => $website_id ) );
        $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nWebsite ID: $website_id\nCompany Package ID: $company_package_id", true );
    }

    /**
	 * Add Note
	 *
	 * @param int $website_id
     * @param int $user_id
	 * @param string $message
	 * @return int|bool
	 */
	protected function add_note() {
		// Gets parameters and errors out if something is missing
		extract( $this->get_parameters( 'website_id', 'user_id', 'message' ) );

		$this->db->insert( 'website_notes', array( 'website_id' => $website_id, 'user_id' => $user_id, 'message' => $message, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to add website note', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-add-note' ) );
			exit;
		}

		$this->add_response( array( 'success' => true, 'message' => 'success-add-note' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.' . "\nWebsite ID: $website_id\nUser ID: $user_id", true );
	}

    /**
	 * Update Social Media
	 *
	 * @param int $website_id
	 * @param array $social_media_add_ons
	 */
	protected function update_social_media() {
		// Gets parameters and errors out if something is missing
		extract( $this->get_parameters( 'website_id', 'website_social_media_add_ons' ) );

        // Make sure we can edit this website
        $this->verify_website( $website_id );

        if ( !is_array( $website_social_media_add_ons ) ) {
            $this->add_response( array( 'success' => false, 'message' => 'failed-update-social-media' ) );
            exit;
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
            exit;
        }

        // Type Juggling
        $website_id = (int) $website_id;

        // Make the variable
        $db_website_social_media_add_ons = $this->db->escape( serialize( $website_social_media_add_ons ) );

        // Insert/update website settings
        $this->db->query( "INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES ( $website_id, 'social-media-add-ons', '$db_website_social_media_add_ons' ) ON DUPLICATE KEY UPDATE `value` = '$db_website_social_media_add_ons'" );

        // If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( "Failed to update website settings.\n\Website ID: $website_id", __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-update-social-media' ) );
			exit;
		}

		$this->add_response( array( 'success' => true, 'message' => 'success-update-social-media' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. Website ID: ' . $website_id, true );
	}

	/**
	 * Update User
	 *
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
	protected function update_user() {
		// Gets parameters and errors out if something is missing
		$personal_information = $this->get_parameters( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip', 'user_id' );
		
		// Get the user_id, but we don't want it in the update data
		$user_id = $personal_information['user_id'];
		unset( $personal_information['user_id'] );
		
		// Make sure he exists, if not, create user
		if( !$this->user_exists( $user_id ) ) {
			$this->create_user();
			return;
		}
		
		$personal_information['password'] = md5( $personal_information['password'] );
		$personal_information['date_created'] = dt::date('Y-m-d H:i:s');
		
		// Update the user
		$this->db->update( 'users', $personal_information, array( 'user_id' => $user_id, 'company_id' => $this->company_id ), str_repeat( 's', count( $personal_information ) ), 'ii' );
		
		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to update user', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-update-user' ) );
			exit;
		}
		
		$this->add_response( array( 'success' => true, 'message' => 'success-update-user' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. User ID: ' . $user_id, true );
	}
	
	/**
	 * Set ARB Subscription
	 *
	 * ARB is Automatic Recurring Billing (part of Authorize.net)
	 *
	 * @param int $arb_subscription_id
	 * @param int $website_id
	 * @return bool
	 */
	protected function set_arb_subscription() {
		// Gets parameters and errors out if something is missing
		extract( $this->get_parameters( 'arb_subscription_id', 'website_id' ) );

        // Make sure we can edit this website
        $this->verify_website( $website_id );

        // Protection
		$website_id = (int) $website_id;
        $arb_subscription_id = $this->db->escape( $arb_subscription_id );

		$this->db->query( "INSERT INTO `website_settings` (`website_id`, `key`, `value`) VALUES ( $website_id, 'arb-subscription-id', '$arb_subscription_id' ) ON DUPLICATE KEY UPDATE `value` = '$arb_subscription_id' " );
		
		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( "Failed to set ARB subscription id.\n\nWebsite ID: $website_id\nARB Subscription ID:$arb_subscription_id", __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-set-arb-subscription' ) );
			exit;
		}

		$this->add_response( array( 'success' => true, 'message' => 'success-set-arb-subscription' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. Website ID: ' . $website_id, true );
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
	public function _unique_username( $title ) {
		$username = $this->_generate_username( $title );
		$available = $this->whm->account_summary( $username );
		
		while ( false != $available ) {
			$username = $this->_generate_username( $title, true );
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
    protected function _generate_username( $title, $complicated = false ) {
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
	public function _unique_domain( $title ) {
		$domain = $this->_generate_domain( $title );
		
		$available = $this->whm->domain_user_data( $domain );
		
		while ( false != $available ) {
			$domain = $this->_generate_domain( $title, true );
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
	public function _generate_domain( $title, $complicated = false ) {
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
        if ( !$account->company_id != $company_id ) {
            $this->add_response( array( 'success' => false, 'message' => 'failed-website-verification' ) );
            return false;
        }

        return true;
    }

	/**
	 * Checks to see if a user exists
	 *
	 * @param int $user_id
	 * @return bool
	 */
	protected function user_exists( $user_id ) {
        // Type Juggling
        $user_id = (int) $user_id;
        $company_id = (int) $this->company_id;

		$email = $this->db->get_var( "SELECT `email` FROM `users` WHERE `user_id` = $user_id AND `company_id` = $company_id" );
		
		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to check if user exists', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-update-user' ) );
			exit;
		}
		
		return ( $email ) ? true : false;
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
			exit;
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
		if( !isset( $_POST['auth_key'] ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'no-authentication-key' ) );
			
			$this->error = true;
			$this->error_message = 'There was no authentication key';
			exit;
		}

        $auth_key = $this->db->escape( $_POST['auth_key'] );
		$this->company_id = (int) $this->db->get_var( "SELECT `company_id` FROM `api_keys` WHERE `status` = 1 AND `key` = '$auth_key'" );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to retrieve company id', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			exit;
		}
		
		// If failed to grab any company id
		if( !$this->company_id ) {
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			
			$this->error = true;
			$this->error_message = 'There was no company to match API key';
			exit;
		}

        // Need to set domain
        $domain = $this->db->get_var( 'SELECT `domain` FROM `companies` WHERE `company_id` = ' . $this->company_id );

        // If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to get domain from companies', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			exit;
		}

        define( 'DOMAIN', $domain );

		$this->statuses['auth'] = true;
	}
	
	/**
	 * This parses the request and calls the correct functions
	 *
	 * @access protected
	 */
	protected function parse() {
		if( in_array( $_POST['method'], $this->methods ) ) {
			$this->method = $_POST['method'];
			$this->statuses['method_called'] = true;
			
			call_user_func( array( 'Requests', $_POST['method'] ) );
		} else {
			$this->add_response( array( 'success' => false, 'message' => 'The method, "' . $_POST['method'] . '", is not a valid method.' ) );
			
			$this->error = true;
			$this->error_message = 'The method, "' . $_POST['method'] . '", is not a valid method.';
			exit;
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
				$this->response[$k] = ( is_string( $v ) || is_int( $v ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
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
			exit;
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
				exit;
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
	 * @param bool $setlogged (optional) whether to set the logged variable as true
	 */
	protected function log( $type, $message, $success, $setlogged = true ) { 
		// Set before hand so that a loop isn't caught in the destructor
		if( $setlogged )
			$this->logged = true;
		
		// If it fails to insert, send an email with the information
		$this->db->insert( 'apilog', array( 'company_id' => $this->company_id, 'type' => $type, 'method' => $this->method, 'message' => $message, 'success' => $success, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssis' );

        if( $this->db->errno() ) {
			$this->_err( "Failed to add entry to log\n\nType: $type\nMessage:\n$message", __LINE__, __METHOD__ );
			
			// Let the client know that something broke
			$this->add_response( array( 'success' => false, 'message' => 'error' ) );
		}
	}
	
	/**
	 * Destructor which creates the log and any information that we should know about it
	 */
	public function __destruct() {
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
		
		// Respond in JSON
		echo json_encode( $this->response );
	}
}