<?php
class FeedApiRequest {
	/**
	 * Constant paths to include files
	 */
	const DEBUG = false;

	/**
	 * Set of messages used throughout the script for easy access
	 * @var array $messages
	 */
	protected $messages = array(
		'error' => 'An unknown error has occured. This has been reported to the Database Administrator. Please try again later.',
		'failed-to-get-feed' => 'Failed to get feed. Please report this to a system administrator.',
		'failed-to-get-categories' => 'Failed to get feed. Please report this to a system administrator.',
		'failed-to-get-brands' => 'Failed to get feed. Please report this to a system administrator.',
		'failed-to-get-industries' => 'Failed to get industries. Please report this to a system administrator.',
		'failed-to-get-product-groups' => 'Failed to get product groups. Please report this to a system administrator.',
		'no-authentication-key' => 'Authentication failed. No Authorization Key was sent.',
		'ssl-required' => 'You must make the call to the secured version of our website.',
		'success-get-products' => 'Get Products succeeded!',
		'success-get-categories' => 'Get Categories succeeded!',
		'success-get-industries' => 'Get Industries succeeded!',
		'success-get-brands' => 'Get Brands succeeded!',
		'success-get-product-groups' => 'Get Product Groups succeeded!'
	);
	
	/**
	 * Set of valid methods
	 * @var array $methods
	 */
	protected $methods = array(
        'get_products'
        , 'get_categories'
        , 'get_attributes'
        , 'get_brands'
        , 'get_industries'
        , 'get_product_groups'
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
    protected $feed;

	/**
	 * Construct class will initiate and run everything
	 *
	 * This class simply needs to be initiated for it run to the data on $_POST variables
	 */
	public function __construct() {
        ini_set( 'memory_limit', '512M' );

		// Do we need to debug
		if( self::DEBUG )
			error_reporting( E_ALL );

        $this->feed = new Feed();

		// Load everything that needs to be loaded
		$this->statuses['init'] = true;

		// Authenticate & load company id
		$this->authenticate();
		
		// Parse method
		if ( !$this->error )
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
			return;
		}

        $api_key = new ApiKey;
        $api_key->get_by_key( $_POST['auth_key'] );

        $this->company_id = (int) $api_key->company_id;

		// If failed to grab any company id
		if( !$this->company_id ) {
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );

			$this->error = true;
			$this->error_message = 'There was no company to match API key';
			return;
		}

        $this->feed->api_key = $api_key;
		$this->statuses['auth'] = true;
	}
	
	/**
	 * This parses the request and calls the correct functions
	 *
	 * @access protected
	 */
	protected function parse() {
		$method = ( isset( $_POST['method'] ) ) ? $_POST['method'] : '';
		
		if( in_array( $method, $this->methods ) ) {
			$this->method = $method;
			$this->statuses['method_called'] = true;
			
			call_user_func( array( 'FeedAPIRequest', $method ) );

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
			$this->add_response( array( 'success' => false, 'message' => 'The method, "' . $method . '", is not a valid method.' ) );

			$this->error = true;
			$this->error_message = 'The method, "' . $method . '", is not a valid method.';
		}
	}
	
	/*******************************/
	/* START: GSR Feed API Methods */
	/*******************************/

    /**
	 * Get Products
	 */
	protected function get_products() {
        // Give them a value no matter what
        $start_date = $end_date = $starting_point = $limit = $ashley_id = NULL;

        /**
         * @param string $start_date (optional)
         * @param string $end_date (optional)
         * @param int $starting_point (optional)
         * @param int $limit (optional)
         * @param int $ashley_id (optional)
         * @return bool
         */
        extract( $this->get_parameters( 'start_date', 'end_date', 'starting_point', 'limit', 'ashley_id' ) );

        $product = new Product();
    	$products = $this->feed->get_products( $start_date, $end_date, $starting_point, $limit, $ashley_id );

		if ( is_array( $products ) )
        foreach ( $products as &$p ) {
            $product->id = $p['product_id'];
            $p['product_specifications'] = $product->get_specifications();

            if ( empty( $p['categories'] ) ) {
                unset( $p['categories'] );
            } else {
                $p['categories'] = explode( ',', $p['categories'] );
            }

            if ( empty( $p['images'] ) ) {
                unset( $p['images'] );
            } else {
                $p['images'] = explode( ',', $p['images'] );
            }

            if ( empty( $p['attributes'] ) ) {
                unset( $p['attributes'] );
            } else {
                $p['attributes'] = explode( ',', $p['attributes'] );
            }

            if ( empty( $p['product_groups'] ) ) {
                unset( $p['product_groups'] );
            } else {
                $p['product_groups'] = explode( ',', $p['product_groups'] );
            }

            if ( is_array( $p['images'] ) )
            foreach( $p['images'] as &$i ) {
                if ( !stristr( $i, 'http' ) )
                    $i = 'http://' . $p['industry'] . '.retailcatalog.us/products/' . $p['product_id'] . '/large/' . $i;
            }

            unset( $p['industry'] );
        }

		if ( !is_array( $products ) )
			$products = array();

        $this->add_response( array( 'success' => true, 'message' => 'success-get-products', 'products' => $products ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

    /**
     * Get Brands
     */
    protected function get_brands() {
        $this->add_response( array( 'success' => true, 'message' => 'success-get-brands', 'brands' => $this->feed->get_brands() ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/**
     * Get Categories
     */
    protected function get_categories() {
        $this->add_response( array( 'success' => true, 'message' => 'success-get-categories', 'categories' => $this->feed->get_categories() ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/**
     * Get Industries
     */
    protected function get_industries() {
        $this->add_response( array( 'success' => true, 'message' => 'success-get-industries', 'industries' => $this->feed->get_industries() ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

    /**
     * Get Attributes
     */
    protected function get_attributes() {
		$attributes = ar::assign_key( $this->feed->get_attributes(), 'attribute_id' );
        $attribute_items = $this->feed->get_attribute_items();

        foreach ( $attribute_items as $ai ) {
            $attributes[$ai['attribute_id']]['items'][] = $ai;
        }

		// We need to properly break up the category IDs
		foreach ( $attributes as &$a ) {
			if ( empty( $a['categories'] ) ) {
				unset( $a['categories'] );
			} else {
				$a['categories'] = explode( ',', $a['categories'] );
			}
		}

        $this->add_response( array( 'success' => true, 'message' => 'success-get-attributes', 'attributes' => array_values( $attributes ) ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

    /**
     * Get Product Groups
     */
    protected function get_product_groups() {
        $this->add_response( array( 'success' => true, 'message' => 'success-get-product-groups', 'product_groups' => $this->feed->get_product_groups() ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/*****************************/
	/* END: GSR Feed API Methods */
	/*****************************/

	/**
	 * Add a response to be sent
	 *
	 * Adds data to the response that will be sent back to the client
	 *
	 * @param string|array $key this can contain the key OR an array of key => value pairs
	 * @param string $value (optional) $value of the $key. Only optional if $key is an array
	 */
	protected function add_response( $key, $value = '' ) {
		if( empty( $value ) && !is_array( $key ) )
			$this->add_response( array( 'success' => false, 'message' => 'error' ) );

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
	 * @param mixed $args the args that contain the parameters to get
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
//				$message = 'Required parameter "' . $a . '" was not set for the method "' . $this->method . '".';
//				$this->add_response( array( 'success' => false, 'message' => $message ) );
//
//				$this->error = true;
//				$this->error_message = $message;
//				return array();
                
                $parameters[$a] = '';
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