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
        // Get Feed Websites
        $website_ids = $this->get_feed_websites();

		// Get the file if htere is one
		$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;
		
        if ( is_array( $website_ids ) )
        foreach( $website_ids as $wid ) {
            $this->run( $wid, $file );
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
		$ftp = new FTP( "/CustEDI/$folder/$subfolder/" );

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
        $skus = $remove_products = $new_products = array();

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
			
			if ( !array_key_exists( $sku, $products ) )
				$new_products[] = $sku;

			$skus[] = $sku;
		}

		if ( is_array( $products ) )
		foreach ( $products as $sku => $product_id ) {
			if ( !in_array( $sku, $skus ) )
				$remove_products[] = (int) $product_id;
		}

		// Add new products
        $account_product = new AccountProduct();
		$account_product->add_bulk( $account->id, $account->get_industries(), $new_products );

		// Deactivate old products
		$account_product->remove_bulk( $account->id, $remove_products );
		
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
            , array( ':account_id' => $account_id, ':user_id_created' => SELF::USER_ID )
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
     * Get Feed Websites
     */
    protected function get_feed_websites() {
        return $this->get_col( "SELECT `website_id` FROM `website_settings` WHERE `key` = 'ashley-ftp-password' AND `value` <> ''" );
    }
}
