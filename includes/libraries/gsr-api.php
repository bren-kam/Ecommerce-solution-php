<?php
/**
 * Grey Suit Retail - API Class
 *
 * This handles all API Calls
 * @version 1.0.0
 */
class GSR_API {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'https://www.imagineretailer.com/api/';
	const DEBUG = false;
	
	/**
	 * A few variables that will determine the basic status
	 */
    private $api_key;
    private $message = NULL;
    private $success = FALSE;
	private $raw_request = NULL;
	private $request = NULL;
	private $raw_response = NULL;
	private $response = NULL;
    private $error = NULL;
    
	/**
	 * Construct class will initiate and run everything
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );

		$this->api_key = $api_key;
	}
	
	/**************************/
	/* Start: GSR API Methods */
	/**************************/
	
	/**
	 * Add Order Item
	 *
	 * @param int $order_id
	 * @param string $item The item name
	 * @param int $quantity
	 * @param float $amount the setup cost
	 * @param float $monthly the monthly cost
	 * @return bool
	 */
	public function add_order_item( $order_id, $item, $quantity, $amount, $monthly ) {
		// Execute the command
		$this->_execute( 'add_order_item', compact( 'order_id', 'item', 'quantity', 'amount', 'monthly' ) );
		
		// Return the success of the method
		return $this->success;
	}

    /**
	 * Craigslist Error
	 *
	 * @param int $product_id
	 * @param string $message
	 * @return bool
	 */
	public function craigslist_error( $product_id, $message ) {
		// Execute the command
		$this->_execute( 'craigslist_error', compact( 'product_id', 'message' ) );

		// Return the user id successful
		return $this->success;
	}

	/**
	 * Create Order
	 *
	 * @param int $user_id
	 * @param float $setup
	 * @param float $monthly
	 * @return int|bool
	 */
	public function create_order( $user_id, $setup, $monthly ) {
		// Execute the command
		$this->_execute( 'create_order', compact( 'user_id', 'setup', 'monthly' ) );

		// Return the order id if successful
		return ( $this->success ) ? $this->response->order_id : false;
	}

	/**
	 * Create User
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
	 * @return int|bool
	 */
	public function create_user( $email, $password, $contact_name, $store_name, $work_phone, $cell_phone, $billing_first_name, $billing_last_name, $billing_address1, $billing_city, $billing_state, $billing_zip ) {
		// Execute the command
		$this->_execute( 'create_user', compact( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip' ) );

		// Return the user id if successful
		return ( $this->success ) ? $this->response->user_id : false;
	}
	
	/**
	 * Create Authorized Users
	 *
	 * @param int $website_id
	 * @param array $emails
	 * @return bool
	 */
	public function create_authorized_users( $website_id, $emails ) {
		// Execute the command
		$this->_execute( 'create_authorized_users', compact( 'website_id', 'emails' ) );
		
		// Return the success of the method
		return $this->success;
	}

	/**
	 * Create Website
	 *
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
	 * @param int $products (optional|126)
	 * @return int|bool
	 */
	public function create_website( $user_id, $domain, $title, $plan_name, $plan_description, $type, $pages, $product_catalog, $blog, $email_marketing, $shopping_cart, $seo, $room_planner, $craigslist, $social_media, $domain_registration, $additional_email_addresses, $products = 126 ) {
		// We need to make sure there is a number there
		if( !$additional_email_addresses )
			$additional_email_addresses = 0;
		
		// Execute the command
		$this->_execute( 'create_website', compact( 'user_id', 'domain', 'title', 'plan_name', 'plan_description', 'type', 'pages', 'product_catalog', 'blog', 'email_marketing', 'shopping_cart', 'seo', 'room_planner', 'craigslist', 'social_media', 'domain_registration', 'additional_email_addresses', 'products' ) );
		
		// Return the website id if successful
		return ( $this->success ) ? $this->response->website_id : false;
	}

    /**
     * Install Package
     *
     * @param int $website_id
     * @param int $company_package_id
     * @return bool
     */
    public function install_package( $website_id, $company_package_id ) {
        // Execute the command
		$this->_execute( 'install_package', compact( 'website_id', 'company_package_id' ) );

        return $this->success;
    }

    /**
	 * Add Note
	 *
	 * @param int $website_id
	 * @param int $user_id
	 * @param string $message
	 * @return bool
	 */
	public function add_note( $website_id, $user_id, $message ) {
		// Execute the command
		$this->_execute( 'add_note', compact( 'website_id', 'user_id', 'message' ) );

		return $this->success;
	}

	/**
	 * Update Social media
	 *
	 * @param int $website_id
	 * @param array $social_media_add_ons
	 * @return bool
	 */
	public function update_social_media( $website_id, $social_media_add_ons ) {
		// Execute the command
		$this->_execute( 'update_social_media', compact( 'website_id', 'social_media_add_ons' ) );

		// Return the success of the method
		return $this->success;
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
	 * @return bool
	 */
	public function update_user( $email, $password, $contact_name, $store_name, $work_phone, $cell_phone, $billing_first_name, $billing_last_name, $billing_address1, $billing_city, $billing_state, $billing_zip, $user_id ) {
		// Execute the command
		$this->_execute( 'update_user', compact( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip', 'user_id' ) );

		// Return the success of the method
		return $this->success;
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
	public function set_arb_subscription( $arb_subscription_id, $website_id ) {
		// Execute the command
		$this->_execute( 'set_arb_subscription', compact( 'arb_subscription_id', 'website_id' ) );

		// Return the success of the method
		return $this->success;
	}
	
	/************************/
	/* END: GSR API Methods */
	/************************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->success;
    }

    /**
     * Get private raw_request variable
     *
     * @return string
     */
    public function raw_request() {
        return $this->raw_request();
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->request;
    }

    /**
     * Get private raw_response variable
     *
     * @return string
     */

    public function raw_response() {
        return $this->raw_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->response;
    }

    /**
     * Get private error variable
     *
     * @return string
     */
    public function error() {
        return $this->error;
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
	 * @param string $method The method being called
	 * @param array $params an array of the parameters to be sent
     * @return string
	 */
	private function _execute( $method, $params ) {
		if( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
		}

        $this->request = array_merge( array( 'auth_key' => $this->api_key, 'method' => $method ), $params );
        $this->raw_request = http_build_query( $this->request );
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::URL_API );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->raw_request );
		curl_setopt( $ch, CURLOPT_POST, 1 );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

		$this->response = json_decode( $this->raw_response );

		curl_close($ch);
		if( $this->response->success ) {
			$this->success = true;
        } else {
            $this->error = $this->response->message;
        }

		$this->message = $this->response->message;

        // If we're debugging lets give as much info as possible
        if( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", self::URL_API, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}