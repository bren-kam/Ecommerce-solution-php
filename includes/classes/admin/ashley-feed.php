<?php
/**
 * Handles ashley import
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Ashley_Feed extends Base_Class {
	const FTP_URL = 'ftp.ashleyfurniture.com';
	
	/**
	 * Creates new Database instance
	 *
	 * @return  void
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct();
		
		// Time how long we've been on this page
		$this->timer_start();
		$this->curl = new curl();
		$this->w = new Websites();
		$this->file = new Files();
        $this->p = new Products();
	}

	/**
     *  Get websites to run
     *
     * @return bool
     */
    public function run_all() {
        $website_ids = $this->db->get_col( "SELECT `website_id` FROM `website_settings` WHERE `key` = 'ashley-ftp-password' AND `value` <> ''" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website_ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get the file if htere is one
		$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;
		
        if ( is_array( $website_ids ) )
        foreach( $website_ids as $wid ) {
			echo "<h1>$wid</h1>";
            //$this->run( $wid, $file );
        }

        return true;
    }

	/**
	 * Main function, goes to page and grabs everything needed and does required actions.
	 * 
	 * @param int $website_id
	 * @param string $file (optional|)
	 * @return bool
	 */
	public function run( $website_id, $file = '' ) {
		$this->timer_start();
		
        // Get the settings
		$settings = $this->w->get_settings( $website_id, array( 'ashley-ftp-username', 'ashley-ftp-password', 'ashley-alternate-folder' ) );
		
		$username = security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY );
		$password = security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY );
		
		// Initialize variables
		$folder = str_replace( 'CE_', '', $username );
		
		if ( '-' != substr( $folder, -1 ) )
			$folder .= '-';
		
        $subfolder = ( '1' == $settings['ashley-alternate-folder'] ) ? 'Items' : 'Outbound';
        
		$products = $this->get_website_product_skus( $website_id );
		
		if ( !is_array( $products ) )
			$products = array();
		
		$ftp = new FTP( 0, "/CustEDI/$folder/$subfolder/", true );
		ini_set( 'max_execution_time', 600 ); // 10 minutes
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 600 );
		$start = time();

		// Set login information
		$ftp->host     = self::FTP_URL;
		$ftp->username = $username;
		$ftp->password = $password;
		$ftp->port     = 21;
		
		// Connect
		$ftp->connect();
		
		if( empty( $file ) ) {
			// Get al ist of the files
			$files = $ftp->dir_list();
			
			$file = $files[count($files)-1];
		}
		
		$local_folder = "/gsr/systems/backend/admin/media/downloads/ashley/$username/";

		if ( !file_exists( $local_folder ) ) {
            // @fix MkDir isnt' changing the permissions, so we have to do the second call too.
			mkdir( $local_folder, 0777 );
            chmod( $local_folder, 0777 );
        }
		
		// Grab the latest file
		if( file_exists( $local_folder . $file ) ) {
			$this->xml = simplexml_load_file( $local_folder . $file );
		} else {
		    $count_spaces = 0; 
            while($count_spaces < 500) { 
              print('          '); 
              $count_spaces++; 
            } 
			$this->xml = simplexml_load_string( $ftp->ftp_get_contents( $file ) );
		}
		
		// Generate array of our items
		foreach ( $this->xml->items->item as $item ) {
            if ( 'Discontinued' == trim( $item->attributes()->itemStatus ) )
                continue;

			$sku = trim( $item->itemIdentification->itemIdentifier[0]->attributes()->itemNumber );

            // Prevent SKUs not sold in America or only in containers
			if ( preg_match( '/[a-zA-Z]?[0-9-]+[a-zA-Z][0-9-]+/', $sku ) )
				continue;
			
			if ( !array_key_exists( $sku, $products ) ) {
				$new_products[] = $sku;
			}
			
			$skus[] = $sku;
		}
		
		$remove_products = array();
		
		if ( is_array( $products ) )
		foreach ( $products as $sku => $product_id ) {
			if ( !in_array( $sku, $skus ) )
				$remove_products[] = (int) $product_id;
		}

		// Add new products
		$product_count = $this->add_bulk( $website_id, $new_products );

		echo "<p><strong>New Products:</strong> $product_count</p>";

		echo '<p><strong>Old Products:</strong> ' . count( $remove_products ) . '</p>';
		
		// Deactivate old products
		//$this->deactivate_old_products( $website_id, $remove_products );
		
		// Reorganize Categories
		$this->p->reorganize_categories( $website_id );
		
		echo $this->scratchy_time();
	}

    /**
     * Email Online Specialists
     *
     * @param int $website_id
     * @return bool
     */
    public function email_online_specialists( $website_id ) {
        $w = new Websites;

        $website_id = (int) $website_id;
        $website = $w->get_website( $website_id );

        $title = $website['title'];
        $ashley_feed_started = $w->get_setting( $website_id, 'ashley-feed-started' );

        if ( '1' == $ashley_feed_started )
            return true;

        // Grab any authorized users that have ashley in their email
        $emails = $this->db->get_col( "SELECT a.`email` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`user_id` = c.`user_id` OR a.`user_id` = c.`os_user_id` ) WHERE a.`status` = 1 AND ( b.`website_id` IS NULL AND c.`website_id` = $website_id OR a.`email` LIKE '%@ashleyfurniture.com' AND b.`website_id` = $website_id )" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get emails.', __LINE__, __METHOD__ );
			return false;
		}

        // Get the company domain
        $domain = $this->db->get_var( "SELECT a.`domain` FROM `companies` AS a LEFT JOIN `users` AS b ON ( a.`company_id` = b.`company_id` ) LEFT JOIN `websites` AS c ON ( b.`user_id` = c.`user_id` ) WHERE c.`website_id` = $website_id" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get domain.', __LINE__, __METHOD__ );
			return false;
		}

        $message = "This email is a notification that the Ashley Dealer Specific Feed has been run for $title and products have been added.";

        // Send out the email
        if ( fn::mail( implode( ',', $emails ), 'Ashley Dealer Specific Feed - Started', $message, "noreply@$domain" ) )
            $w->update_settings( $website_id, array( 'ashley-feed-started' => 1 ) );

        return true;
    }
	
	/**
	 * Gets the products SKUs of a website to determine what products they have
	 *
	 * @param int $website_id
	 * @return array
	 */
	private function get_website_product_skus( $website_id ) {
		// Type Juggling
		$website_id = (int) $website_id;
		
		// Get Products
		$products = $this->db->get_results( "SELECT a.`product_id`, b.`sku` FROM `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`website_id` = $website_id AND a.`blocked` = 0 AND a.`active` = 1 AND b.`user_id_created` = 353", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get products.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Reform the array
		return ar::assign_key( $products, 'sku', true );
	}
	
	/**
	 * Add Bulk
	 *
	 * @param int $website_id
	 * @param string $product_skus
	 * @return bool
	 */
	private function add_bulk( $website_id, $product_skus ) {
        // Make sure they entered in SKUs
        if ( !is_array( $product_skus ) || empty( $product_skus ) )
            return false;
		
		$product_sku_chunks = array_chunk( $product_skus, 500 );
		
		foreach ( $product_sku_chunks as $product_skus ) {
			// Escape all the SKUs
			foreach ( $product_skus as &$ps ) {
				$ps = "'" . $this->db->escape( trim( $ps ) ) . "'";
			}
			
			// Turn it into a string
			$product_skus = implode( ",", $product_skus );
			
			// Get industries
			$industries = preg_replace( '/[^0-9,]/', '', implode( ',', $this->get_website_industries( $website_id ) ) );
	
			if ( $industries == '' )
				return array( false, 0, true );
	
			// Type Juggling
			$website_id = (int) $website_id;
			
			// Magical Query #1
			// Insert website products and make sure not to add blocked products
			$this->db->query( "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $website_id, p.`product_id` FROM `products` AS p LEFT JOIN `website_products` AS wp ON ( wp.`product_id` = p.`product_id` AND wp.`website_id` = $website_id ) WHERE p.`industry_id` IN($industries) AND p.`user_id_created` = 353 AND p.`website_id` = 0 AND p.`publish_visibility` = 'public' AND p.`status` <> 'discontinued' AND p.`sku` IN ( $product_skus ) AND ( wp.`blocked` IS NULL OR wp.`blocked` = 0 ) ON DUPLICATE KEY UPDATE `active` = 1" );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to dump website products.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return $this->db->rows_affected;
	}
	
	/**
	 * Deactivate old products
	 *
	 * @param int $website_id
	 * @param array $product_ids
	 * @return bool
	 */
	private function deactivate_old_products( $website_id, $product_ids ) {
		if ( !is_array( $product_ids ) || 0 == count( $product_ids ) )
			return;
		
		// Type Juggling
		$website_id = (int) $website_id;
		
		// Deactivate in chunks of 500
		$product_id_chunks = array_chunk( $product_ids, 500 );
		
		foreach ( $product_id_chunks as $product_ids_array ) {
			// Make sure the product_ids are valid
			foreach ( $product_ids_array as &$pid ) {
				$pid = (int) $pid;
			}
			
			$this->db->query( "UPDATE `website_products` SET `active` = 0 WHERE `website_id` = $website_id AND `product_id` IN(" . implode( ',', $product_ids_array ) . ')' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to deactivate products.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Get website industries
	 *
	 * @param int $website_id
	 * @return array
	 */
	private function get_website_industries( $website_id ) {
		// Type Juggling
		$website_id = (int) $website_id;
		
		$industry_ids = $this->db->get_col( "SELECT `industry_id` FROM `website_industries` WHERE `website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get industry ids.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry_ids;
	}
	
	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @since 1.0.0
	 */
	private function timer_start() {
		$this->time_start = microtime( true );
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.0.0
	 *
	 * @return int Total time spent on the query, in seconds
	 */
	private function scratchy_time() {
		return microtime( true ) - $this->time_start;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}
