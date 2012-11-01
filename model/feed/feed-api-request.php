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
		
		// Load everything that needs to be loaded
		$this->statuses['init'] = true;

		// Authenticate & load company id
		$this->authenticate();
		
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
			
			call_user_func( array( 'FeedAPIRequest', $_POST['method'] ) );

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
			$this->add_response( array( 'success' => false, 'message' => 'The method, "' . $_POST['method'] . '", is not a valid method.' ) );

			$this->error = true;
			$this->error_message = 'The method, "' . $_POST['method'] . '", is not a valid method.';
		}
	}
	
	/***************(***************/
	/* START: GSR Feed API Methods */
	/*******************************/

    /**
	 * Get Products
	 */
	protected function get_products() {
        /*
         * @param string $start_date (optional)
         * @param string $end_date (optional)
         * @param int $starting_point (optional)
         * @param int $limit (optional)
         * @return bool
         */
        extract( $this->_get_parameters( 'start_date', 'end_date', 'starting_point', 'limit' ) );

        // Use the variables if necessary
        $where = '';

        if ( isset( $start_date ) && !empty( $start_date ) )
            $where .= " AND a.`timestamp` >= '" . $this->db->escape( $start_date ) . "'";

        if ( isset( $end_date ) && !empty( $end_date ) )
            $where .= " AND a.`timestamp` < '" . $this->db->escape( $end_date ) . "'";

        $starting_point = ( isset( $starting_point ) && !empty( $starting_point ) ) ? (int) $starting_point : 0;
        $limit = ( isset( $limit ) && !empty( $limit ) ) ? (int) $limit : 10000;

        if ( $limit > 10000 )
            $limit = 10000;

    	$products = $this->db->get_results( "SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`weight`, a.`volume`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, a.`date_created`, a.`timestamp`, b.`name` AS industry, GROUP_CONCAT( DISTINCT c.`category_id` ) AS categories, GROUP_CONCAT( DISTINCT d.`image` ) AS images, GROUP_CONCAT( DISTINCT e.`attribute_item_id` ) AS attributes, GROUP_CONCAT( DISTINCT f.`product_group_id` ) AS product_groups FROM `products` AS a LEFT JOIN `industries` AS b ON ( a.`industry_id` = b.`industry_id` ) LEFT JOIN `product_categories` AS c ON ( a.`product_id` = c.`product_id` ) LEFT JOIN `product_images` AS d ON ( a.`product_id` = d.`product_id` ) LEFT JOIN `attribute_item_relations` AS e ON ( a.`product_id` = e.`product_id` ) LEFT JOIN `product_group_relations` AS f ON ( a.`product_id` = f.`product_id` ) WHERE a.`publish_visibility` <> 'deleted' $where GROUP BY a.`product_id` ORDER BY a.`product_id` LIMIT $starting_point, $limit", ARRAY_A );


		if ( is_array( $products ) )
        foreach ( $products as &$p ) {
            $p['product_specifications'] = unserialize( html_entity_decode( $p['product_specifications'], ENT_QUOTES ) );

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
                $i = 'http://' . $p['industry'] . '.retailcatalog.us/products/' . $p['product_id'] . '/large/' . $i;
            }

            unset( $p['industry'] );
        }
		
		if ( !is_array( $products ) )
			$products = array();
		
        $this->add_response( array( 'success' => true, 'message' => 'success-get-products', 'products' => $products ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called. User ID: ' . $user_id, true );
	}

    /**
     * Get Brands
     */
    protected function get_brands() {
		$brands = $this->db->get_results( "SELECT * FROM `brands`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Brands', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-brands' ) );
			exit;
		}

        $this->add_response( array( 'success' => true, 'message' => 'success-get-brands', 'brands' => $brands ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/**
     * Get Categories
     */
    protected function get_categories() {
		$categories = $this->db->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug`, `sequence`, `date_updated` FROM `categories`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Categories', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-categories' ) );
			exit;
		}

        $this->add_response( array( 'success' => true, 'message' => 'success-get-categories', 'categories' => $categories ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/**
     * Get Industries
     */
    protected function get_industries() {
		$industries = $this->db->get_results( "SELECT * FROM `industries`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Industries', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-industries' ) );
			exit;
		}

        $this->add_response( array( 'success' => true, 'message' => 'success-get-industries', 'industries' => $industries ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

    /**
     * Get Attributes
     */
    protected function get_attributes() {
		$attributes = $this->db->get_results( "SELECT a.*, GROUP_CONCAT( b.`category_id` ) AS categories FROM `attributes` AS a LEFT JOIN `attribute_relations` AS b ON ( a.`attribute_id` = b.`attribute_id` ) GROUP BY a.`attribute_id`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Attributes', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-attributes' ) );
			exit;
		}

        $attribute_items = $this->db->get_results( "SELECT * FROM `attribute_items`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Attributes Items', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-attributes' ) );
			exit;
		}

        $attributes = ar::assign_key( $attributes, 'attribute_id' );

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
		$product_groups = $this->db->get_results( "SELECT * FROM `product_groups`", ARRAY_A );

		// If there was a MySQL error
		if( $this->db->errno() ) {
			$this->_err( 'Failed to Get Product Groups', __LINE__, __METHOD__ );
			$this->add_response( array( 'success' => false, 'message' => 'failed-to-get-product-groups' ) );
			exit;
		}

        $this->add_response( array( 'success' => true, 'message' => 'success-get-product-groups', 'product_groups' => $product_groups ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

	/******************************/
	/* START: IR Feed API Methods */
	/******************************/

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
			
			$this->_err( "Tried to add a response without a valid key and value\nKey: \n----------\n" . fn::info( $key, false ) . "\n----------\n" . $value, __LINE__, __METHOD__ );
		}
		
		// Set the response
		if( is_array( $key ) ) {
			foreach( $key as $k => $v ) {
				// Makes sure there isn't a premade message
				$this->response[$k] = ( is_string( $v ) || is_int( $v ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
			}
		} else {
			// Makes sure there isn't a premade message
			$this->response[$key] = ( !is_array( $v ) && array_key_exists( $v, $this->messages ) ) ? $this->messages[$v] : $v;
		}
	}
	
	/**
	 * Gets parameters from the post variable and returns and associative array with those values
	 *
	 * @param mixed $args the args that contain the parameters to get
	 * @return array $parameters
	 */
	protected function _get_parameters() {
		$args = func_get_args();
		
		// Make sure the arguments are correct
		if( !is_array( $args ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'error' ) );
			$this->_err( "Call to get_parameters with incorrect arguments\nArguments:\n" . fn::info( $args ), __LINE__, __METHOD__ );
			exit;
		}
		
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
		if( !$this->db->insert( 'apilog', array( 'company_id' => $this->company_id, 'type' => $type, 'method' => $this->method, 'message' => $message, 'success' => $success, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssis' ) ) {
			$this->_err( "Failed to add entry to log\nType: $type\nMessage:\n$message", __LINE__, __METHOD__ );
			
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

        echo json_encode( $this->response );
	}
}