<?php
/**
 * Grey Suit Retail - API Class
 *
 * @requires Studio98 Framework
 *
 * This handles all API Calls
 * @version 1.0.0
 */
class GSRAPI {
	/**
	 * Constant paths to include files
	 */
	const PATH_S98_LIB = '/path/to/s98lib/init.php';
	const URL_API = 'https://www.imagineretailer.com/api/';
	const DEBUG = false;
	
	/**
	 * A few variables that will determine the basic status
	 */
	public $message = NULL;
	public $success = false;
	public $response = NULL;
	
	/**
	 * Construct class will initiate and run everything
	 *
	 * @param string $api_key
	 */
	public function __construct( $api_key ) {
		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );
		
		// Include S98 Framework
		require_once( self::PATH_S98_LIB );
		
		$this->api_key = $api_key;
	}
	
	/*************************/
	/* Start: IR API Methods */
	/*************************/	
	
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
		$this->execute( 'add_order_item', compact( 'order_id', 'item', 'quantity', 'amount', 'monthly' ) );
		
		// Return the user id successful
		return ( $this->success ) ? true : false;
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
		$this->execute( 'create_order', compact( 'user_id', 'setup', 'monthly' ) );
		
		// Return the user id successful
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
		$this->execute( 'create_user', compact( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip' ) );
		
		// Return the user id successful
		return ( $this->success ) ? $this->response->user_id : false;
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
		$this->execute( 'create_website', compact( 'user_id', 'domain', 'title', 'plan_name', 'plan_description', 'type', 'pages', 'product_catalog', 'blog', 'email_marketing', 'shopping_cart', 'seo', 'room_planner', 'craigslist', 'social_media', 'domain_registration', 'additional_email_addresses', 'products' ) );
		
		// Return the user id successful
		return ( $this->success ) ? $this->response->website_id : false;
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
		$this->execute( 'update_social_media', compact( 'website_id', 'social_media_add_ons' ) );
		
		// Return the user id successful
		return ( $this->success ) ? true : false;
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
		$this->execute( 'update_user', compact( 'email', 'password', 'contact_name', 'store_name', 'work_phone', 'cell_phone', 'billing_first_name', 'billing_last_name', 'billing_address1', 'billing_city', 'billing_state', 'billing_zip', 'user_id' ) );
		
		// Return the user id successful
		return ( $this->success ) ? true : false;
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
		$this->execute( 'set_arb_subscription', compact( 'arb_subscription_id', 'website_id' ) );
		
		// Return the user id successful
		return ( $this->success ) ? true : false;
	}
	
	/***********************/
	/* END: IR API Methods */
	/***********************/
	
	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
	 * @param string $method The method being called
	 * @param array $params an array of the parameters to be sent
	 */
	private function execute( $method, $params ) {
		if( empty( $this->api_key ) ) {
			$this->error = 'Cannot send request without an API Key.';
			$this->success = false;
		}
		
		$post_vars = http_build_query( array_merge( array( 'auth_key' => $this->api_key, 'method' => $method ), $params ) );
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::URL_API );
		//curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 20 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_vars );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		$this->response = json_decode( curl_exec( $ch ) );
		curl_close($ch);
		
		if( $this->response->success )
			$this->success = true;
		
		$this->message = $this->response->message;
		
		return $this->response;
	}
}