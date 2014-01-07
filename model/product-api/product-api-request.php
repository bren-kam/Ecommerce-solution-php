<?php
class ProductApiRequest {
	/**
	 * Constant paths to include files
	 */
	const DEBUG = false;

    /**
     * @var ApiKey
     */
    protected $api_key;

    /**
     * @var array
     */
    protected $categories, $categories_by_id;

    /**
     * @var array
     */
    protected $industries;

    /**
     * @var curl
     */
    protected $curl;

    /**
     * @var object
     */
    protected $parameters;

    /**
	 * Set of messages used throughout the script for easy access
	 * @var array $messages
	 */
	protected $messages = array(
		'error' => 'An unknown error has occurred. This has been reported to the Database Administrator. Please try again later.'
		, 'failed-set-product' => 'Failed to set the product. Please verify you have sent the correct parameters.'
		, 'failed-get-product' => 'Failed to get the product. Please verify you have sent the correct parameters.'
		, 'failed-list-products' => 'Failed to list products. Please verify you have sent the correct parameters.'
		, 'failed-get-categories' => 'Failed to get categories. Please verify you have sent the correct parameters.'
		, 'failed-get-industrues' => 'Failed to get industries. Please verify you have sent the correct parameters.'
		, 'failed-delete-product' => 'Failed to delete product. Please verify you have sent the correct parameters.'
		, 'no-authentication-key' => 'Authentication failed. No Authorization Key was sent.'
		, 'ssl-required' => 'You must make the call to the secured version of our website.'
		, 'success-set-product' => 'Set Product succeeded!'
		, 'success-delete-product' => 'Delete Product succeeded!'
		, 'success-get-product' => 'Get Product succeeded!'
		, 'success-get-categories' => 'Get Categories succeeded!'
		, 'success-get-industries' => 'Get Industries succeeded!'
		, 'success-list-products' => 'List Products succeeded!'
        , 'product-creation-requires-images' => 'Failed to set product. Images are required on product creation.'
	);
	
	/**
	 * Set of valid methods
	 * @var array $messages
	 */
	protected $methods = array(
        'get_product'
        , 'list_products'
        , 'get_categories'
        , 'get_industries'
		, 'set_product'
        , 'delete_product'
	);
	
	/**
	 * Pieces of data accrued throughout processing
	 */
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

    /**********************************/
    /* START: GSR Product API Methods */
    /**********************************/

    /**
	 * Set Product
	 */
	protected function set_product() {
        /**
         * @var object $product
         */

        extract( $this->get_parameters( 'product' ) );

        $set_product = new Product();
        $set_product->get_by_sku_by_brand( $product->sku, $this->api_key->brand_id );

        $set_product->category_id = $this->get_category_by_name( $product->category );
        $set_product->name = $product->name;
        $set_product->slug = format::slug( $product->name );
        $set_product->description = $product->description;
        $set_product->price = $product->price_wholesale;
        $set_product->price_min = $product->price_map;
        $set_product->status = ( 'discontinued' == $product->status ) ? 'discontinued' : 'in-stock';
        $set_product->product_specifications = serialize( $product->specifications );

        if ( $set_product->id ) {
            $set_product->save();
        } else {
            if ( empty( $product->images ) ) {
                $this->add_response( array( 'success' => false, 'message' => 'product-creation-requires-images' ) );
                $this->log( 'method', 'The method "' . $this->method . '" has incorrect parameters.', true );
                return;
            }

            $set_product->sku = $product->sku;
            $set_product->industry_id = $this->get_industry( $product->industry );
            $set_product->user_id_created = $this->api_key->user_id;
            $set_product->user_id_modified = $this->api_key->user_id;
            $set_product->publish_visibility = Product::PUBLISH_VISIBILITY_PUBLIC;
            $set_product->create();
            $set_product->save();
        }

        if ( !empty( $product->images ) ) {
            $set_product->delete_images();

            $images = array();

            foreach ( $product->images as $image ) {
                $image_name = $this->upload_image( $image, $set_product->slug, $set_product->id, strtolower( $product->industry ) );

                if ( $image_name )
                    $images[] = $image_name;
            }

            if ( !empty( $images ) )
                $set_product->add_images( $images );
        }

		$this->add_response( array( 'success' => true, 'message' => 'success-set-product' ) );
		$this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
	}

    /**
     * Get Product
     */
    protected function get_product() {
        /**
         * @var string $sku
         */
        extract( $this->get_parameters( 'sku' ) );

        // Get product
        $product = new Product();
        $product->get_by_sku_by_brand( $sku, $this->api_key->brand_id );

        // Get categories

        $get_product = array(
            'sku' => $product->sku
            , 'name' => $product->name
            , 'description' => $product->description
            , 'price_wholesale' => $product->price
            , 'price_map' => $product->price_min
            , 'specifications' => $product->get_specifications()
            , 'status' => $product->status
            , 'category' => $this->get_category_by_id( $product->category_id )
            , 'industry' => $product->industry
            , 'images' => $product->get_images()
        );

        $this->add_response( array( 'success' => true, 'product' => $get_product, 'message' => 'success-get-product' ) );
    }

    /**
     * List Products
     */
    protected function list_products() {
        // Get product
        $product = new Product();
        $products = $product->get_by_brand( $this->api_key->brand_id );

        $fetched_products = array();

        foreach ( $products as $product ) {
            $fetched_products[] = array(
                'sku' => $product->sku
                , 'name' => $product->name
                , 'description' => $product->description
                , 'price_wholesale' => $product->price
                , 'price_map' => $product->price_min
                , 'specifications' => $product->get_specifications()
                , 'status' => $product->status
                , 'category' => $this->get_category_by_id( $product->category_id )
                , 'industry' => $product->industry
                , 'images' => explode( ',', $product->images )
            );
        }

        $this->add_response( array( 'success' => true, 'products' => $fetched_products, 'message' => 'success-get-products' ) );
    }

    /**
	 * Get Categories
	 */
    protected function get_categories() {
        // Cause the categories to get loaded
        $this->load_categories();

        $this->add_response( array( 'success' => true, 'categories' => $this->categories, 'message' => 'success-get-categories' ) );
        $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
    }

    /**
	 * Get Industries
	 */
    protected function get_industries() {
        // Get the industries
        $industry = new Industry();
        $industries = $industry->get_all();

        $industry_array = array();

        /**
         * @var Industry $industry
         */
        foreach ( $industries as $industry ) {
            $industry_array[] = format::slug( $industry->name );
        }

        $this->add_response( array( 'success' => true, 'industries' => $industry_array, 'message' => 'success-get-industries' ) );
        $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
    }

    /**
	 * Delete Product
	 */
    protected function delete_product() {
        /**
         * @var string $sku
         */
        extract( $this->get_parameters( 'sku' ) );

        // Get product
        $product = new Product();
        $product->get_by_sku_by_brand( $sku, $this->api_key->brand_id );

        // Deactivate product
        if ( $product->id && 'deleted' != $product->publish_visibility ) {
            // We need to remove it from all user websites
            $account = new Account();
            $account_product = new AccountProduct();
            $account_category = new AccountCategory();
            $category = new Category();

            // Get variables
            $accounts = $account->get_by_product( $product->id );

            // Delete product from all accounts
            $account_product->delete_by_product( $product->id );

            // Recategorize them
            foreach ( $accounts as $account ) {
                $account_category->reorganize_categories( $account->id, $category );
            }

            // Delete the product
            $product->publish_visibility = 'deleted';
            $product->save();
        }

        $this->add_response( array( 'success' => true, 'message' => 'success-delete-product' ) );
        $this->log( 'method', 'The method "' . $this->method . '" has been successfully called.', true );
    }

	/********************************/
	/* END: GSR Product API Methods */
	/********************************/

    /**
     * Get Category based on string
     *
     * @param string $requested_category
     * @return int
     */
    protected function get_category_by_name( $requested_category ) {
        if ( empty( $this->categories ) )
            $this->load_categories();

        return $this->categories[$requested_category];
    }
    /**
     * Get Category based on string
     *
     * @param int $category_id
     * @return string
     */
    protected function get_category_by_id( $category_id ) {
        if ( empty( $this->categories ) )
            $this->load_categories();

        return $this->categories_by_id[$category_id];
    }

    /**
     * Load Categories
     */
    protected function load_categories() {
        $category = new Category();
        $categories = $category->get_all();

        /**
         * @var Category $category
         */
        foreach ( $categories as $category ) {
            if ( $category->has_children() )
                continue;

            $category_string = $category->name;
            $parents = $category->get_all_parents( $category->id );

            foreach ( $parents as $parent_category ) {
                $category_string = $parent_category->name . ' > ' . $category_string;
            }

            $this->categories[$category_string] = $category->id;
            $this->categories_by_id[$category->id] = $category_string;
        }
    }

    /**
     * Get industry based on name
     *
     * @param string $industry
     * @return int
     */
     protected function get_industry( $industry ) {
         if ( empty( $this->industries ) ) {
             $industry = new Industry();
             $industries = $industry->get_all();

             foreach ( $industries as $industry ) {
                 $this->industries[strtolower($industry->name)] = $industry->industry_id;
             }
         }

         return $this->industries[strtolower($industry)];
     }

    /**
     * Upload image
     *
     * @throws InvalidParametersException
     *
     * @param string $image_url
     * @param string $slug
     * @param int $product_id
     * @param string $industry
     * @return string
     */
    protected function upload_image( $image_url, $slug, $product_id, $industry ) {
        if ( is_null( $industry ) )
            throw new InvalidParametersException( _('Industry must not be null') );

        $new_image_name = $slug;
        $image_extension = strtolower( f::extension( $image_url ) );
        $full_image_name = "{$new_image_name}.{$image_extension}";
        $image_path = '/gsr/systems/backend/admin/media/downloads/scratchy/' . $full_image_name;

        // If it already exists, no reason to go on
        if( is_file( $image_path ) && curl::check_file( "http://{$industry}.retailcatalog.us/products/{$product_id}/thumbnail/{$full_image_name}" ) )
            return $full_image_name;

        // Open the file to write to it
        $fp = fopen( $image_path, 'wb' );

        // Save the file
        if ( !isset( $this->curl ) )
            $this->curl = new curl();

        $this->curl->save_file( $image_url, $fp );

        // Close file
        fclose( $fp );

        $file = new File();

        $file->upload_image( $image_path, $new_image_name, 350, 350, $industry, "products/{$product_id}/", false, true );
        $file->upload_image( $image_path, $new_image_name, 64, 64, $industry, "products/{$product_id}/thumbnail/", false, true );
        $file->upload_image( $image_path, $new_image_name, 200, 200, $industry, "products/{$product_id}/small/", false, true );
        $full_image_name = $file->upload_image( $image_path, $new_image_name, 1000, 1000, $industry, "products/{$product_id}/large/" );

        if( file_exists( $image_path ) )
            @unlink( $image_path );

        return $full_image_name;
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
		if( !isset( $_POST['auth_key'] ) ) {
			$this->add_response( array( 'success' => false, 'message' => 'no-authentication-key' ) );
			
			$this->error = true;
			$this->error_message = 'There was no authentication key';
			return;
		}

        $this->api_key = new ApiKey;
        $this->api_key->get_by_key( $_POST['auth_key'] );

		// If failed to grab any company id
		if( !$this->api_key->company_id ) {
			$this->add_response( array( 'success' => false, 'message' => 'failed-authentication' ) );
			
			$this->error = true;
			$this->error_message = 'There was no company to match API key';
			return;
		}

        define( 'DOMAIN', $this->api_key->domain );

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
            $this->parameters = json_decode( $_POST['data'] );
			
			call_user_func( array( 'ApiRequest', $_POST['method'] ) );

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
			if( !isset( $this->parameters->$a ) ) {
				$message = 'Required parameter "' . $a . '" was not set for the method "' . $this->method . '".';
				$this->add_response( array( 'success' => false, 'message' => $message ) );
				
				$this->error = true;
				$this->error_message = $message;
				return array();
			}
			
			$parameters[$a] = $this->parameters->$a;
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