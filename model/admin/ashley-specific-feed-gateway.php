<?php
/**
 * Handles ashley import
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class AshleySpecificFeedGateway extends ActiveRecordBase {
	const FTP_URL = 'ftp.ashleyfurniture.com';
    const USER_ID = 353; // Ashley

	/**
	 * Creates new Database instance
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct('');

        // Set specs to last longer
        ini_set( 'max_execution_time', 600 ); // 10 minutes
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 600 );	}

	/**
     *  Get websites to run
     */
    public function run_all() {
        // Get Feed Accounts
        $accounts = $this->get_feed_accounts();

		// Get the file if htere is one
		$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;
		
        if ( is_array( $accounts ) )
        foreach( $accounts as $account ) {
            // Need to make this not timeout and remove half the products first
            // @fix
            // $this->run( $account, $file );
        }
    }

	/**
	 * Main function, goes to page and grabs everything needed and does required actions.
	 * 
	 * @param Account $account
	 * @param string $file (optional|)
	 * @return bool
	 */
	public function run( Account $account, $file = '' ) {
		// Initialize variables
		$settings = $account->get_settings( 'ashley-ftp-username', 'ashley-ftp-password', 'ashley-alternate-folder' );
		$username = security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY );
		$password = security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY );
		$products = ar::assign_key( $this->get_website_product_skus( $account->id ), 'sku', true );
		$folder = str_replace( 'CE_', '', $username );

        // Modify variables as necessary
		if ( '-' != substr( $folder, -1 ) )
			$folder .= '-';
		
        $subfolder = ( '1' == $settings['ashley-alternate-folder'] ) ? 'Items' : 'Outbound';

		if ( !is_array( $products ) )
			$products = array();

        // Setup FTP
		$ftp = new Ftp( "/CustEDI/$folder/$subfolder/" );

		// Set login information
		$ftp->host     = self::FTP_URL;
		$ftp->username = $username;
		$ftp->password = $password;
		$ftp->port     = 21;
		
		// Connect
		$ftp->connect();

        // Figure out what file we're getting
		if( empty( $file ) ) {
			// Get al ist of the files
			$files = $ftp->dir_list();
			
			$file = $files[count($files)-1];
		}

        // Can't do anything without a file
        if ( empty( $file ) )
            return;

        // Make sure the folder has been created
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
			$this->xml = simplexml_load_string( $ftp->ftp_get_contents( $file ) );
		}

        // Declare array
        $packages = $this->get_ashley_packages();
        $skus = $remove_products = $new_product_skus = $all_skus = array();

        /**
         * @var SimpleXMLElement $item
         */
        // Generate array of our items
        foreach ( $this->xml->items->item as $item ) {
            if ( 'Discontinued' == trim( $item->attributes()->itemStatus ) )
                continue;

			$sku = trim( $item->itemIdentification->itemIdentifier[0]->attributes()->itemNumber );

            // Prevent SKUs not sold in America or only in containers
			if ( preg_match( '/[a-zA-Z]?[0-9-]+[a-zA-Z][0-9-]+/', $sku ) )
				continue;

            $all_skus[] = $sku;

			if ( !stristr( $sku, '-' ) ) {
				if ( !array_key_exists( $sku, $products ) )
					$new_product_skus[] = $sku;

				continue;
			}

			list( $series, $item ) = explode( '-', $sku, 2 );

			$skus[$series][] = $item;
		}
        
        $new_product_ids = $remove_skus = array();
		
        // Add packages if they have all the pieces
		foreach ( $packages as $series => $items ) {
            // Go through each item
			foreach ( $items as $product_id => $package_pieces ) {
				// See if they have all the items necessary
				foreach ( $package_pieces as $item ) { 
					if ( in_array( $item, $skus[$series] ) ) { // Check if it is a series such as "W123-45"
						$remove_skus[] = "$series-$item";
						continue;
					} elseif( in_array( $series . $item, $all_skus ) ) { // Check if it is straight like "W12345"
						$remove_skus[] = $series . $item;
						continue;
					}

                    // If they don't have both, then stop this item
					continue 2; // Drop out of both
				}

                // Add to packages list
				$new_product_ids[] = $product_id;
			}
		}

        // Only need one of each
		$remove_skus = array_unique( $remove_skus );
		
		// Now remove skus
		if ( !empty( $remove_skus ) )
		foreach ( $remove_skus as $sku ) {
			unset( $new_product_skus[array_search( $sku, $new_product_skus )] );
		}

		if ( is_array( $products ) )
		foreach ( $products as $sku => $product_id ) {
			if ( !in_array( $sku, $skus ) )
				$remove_products[] = (int) $product_id;
		}

		// Add new products
        $industries = $account->get_industries();
        $account_product = new AccountProduct();
		$account_product->add_bulk( $account->id, $industries, $new_product_skus );
        $this->add_bulk_packages_by_ids( $account->id, $industries, $new_product_ids );

		// Deactivate old products
		//$account_product->remove_bulk( $account->id, $remove_products );
		
		// Reorganize Categories
        $account_category = new AccountCategory();
		$account_category->reorganize_categories( $account->id, new Category() );
	}
	
	/**
	 * Gets the products SKUs of a website to determine what products they have
	 *
	 * @param int $account_id
	 * @return array
	 */
	protected function get_website_product_skus( $account_id ) {
		return $this->prepare(
            'SELECT wp.`product_id`, p.`sku` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( wp.`product_id` = p.`product_id` ) WHERE wp.`website_id` = :account_id AND wp.`blocked` = 0 AND wp.`active` = 1 AND p.`user_id_created` = :user_id_created'
            , 'ii'
            , array( ':account_id' => $account_id, ':user_id_created' => self::USER_ID )
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
     * Get Feed Accounts
     *
     * @return mixed
     */
    protected function get_feed_accounts() {
        return $this->get_results( "SELECT `website_id` FROM `website_settings` WHERE `key` = 'ashley-ftp-password' AND `value` <> ''", PDO::FETCH_CLASS, 'Account' );
    }

    /**
	 * Get Ashley Packages
	 *
	 * @return array
	 */
	protected function get_ashley_packages() {
		$products = ar::assign_key( $this->get_results( 'SELECT `product_id`, `sku` FROM `products` WHERE `user_id_created` = 1477', PDO::FETCH_ASSOC ), 'sku', true );

		$ashley_packages = array();

		foreach ( $products as $sku => $product_id ) {
			$sku_pieces = explode( '/', $sku );

			$series = array_shift( $sku_pieces );

			$ashley_packages[$series][$product_id] = $sku_pieces;
		}

		return $ashley_packages;
	}

    /**
	 * Add Bulk
	 *
	 * @param int $account_id
     * @param array $industry_ids
	 * @param array $product_ids
	 */
	protected function add_bulk_packages_by_ids( $account_id, array $industry_ids, array $product_ids ) {
        // Make sure they entered in SKUs
        if ( empty( $industry_ids ) || empty( $product_ids ) )
            return;

        // Make account id safe
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Split into chunks so we can do queries one at a time
		$product_id_chunks = array_chunk( $product_ids, 500 );

		foreach ( $product_id_chunks as $product_ids ) {
            // Escape all the SKUs
			foreach ( $product_ids as &$pid ) {
				$pid = (int) $pid;
			}

            // Turn it into a string
			$product_ids = implode( ",", $product_ids );

			// Magical Query
			// Insert website products
			$this->query( "INSERT INTO `website_products` ( `website_id`, `product_id`, `sequence` ) SELECT DISTINCT $account_id, `product_id`, 10000 FROM `products` WHERE `industry_id` IN( $industry_ids_sql ) AND `user_id_created` = 1477 AND `product_id` IN ( $product_ids ) GROUP BY `sku` ON DUPLICATE KEY UPDATE `active` = 1" );
		}
	}
}
