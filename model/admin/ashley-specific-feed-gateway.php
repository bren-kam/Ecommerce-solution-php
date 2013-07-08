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
    const COMPLETE_CATALOG_MINIMUM = 10485760; // 10mb In bytes

    // Not used
    protected $testing_sites = array( 78, 123, 124, 134, 158, 168, 175, 186, 190, 218, 228, 243, 291, 292, 293, 317, 318
        , 327, 335, 337, 354, 357, 377, 378, 403, 457, 458, 476, 477, 479, 527, 535, 559, 571, 573, 587, 590, 593, 596, 600
        , 601, 605, 610, 612, 613, 638, 642, 645, 650, 659, 663, 664, 665, 666, 674, 681, 682, 684, 686, 689, 692, 700, 704, 720
        , 743, 805, 806, 807, 809, 829, 878, 882, 883, 895, 902, 904, 912, 915, 929, 932, 936, 939, 942, 975, 978, 980
        , 991, 1014, 1017, 1022, 1037, 1042, 1058, 1066, 1067, 1068, 1077, 1078, 1099, 1100, 1101, 1113, 1116, 1120, 1126, 1129, 1133
        , 1134, 1137, 1140, 1141, 1147, 1148, 1184, 1186, 1197, 1198, 1199, 1206, 1218, 1221, 1223
    );

    protected $deleted_sites = array( 70, 365, 412, 497, 506, 606, 611, 647, 680, 1015, 1049, 1093, 1138, 1205 );

    protected $omit_sites = array( 161, 187, 296, 343, 341, 345, 371, 404, 456, 461, 464, 468, 492, 494, 501, 557, 572
        , 582, 588, 599, 606, 614, 641, 644, 649, 660, 667, 668, 702, 760, 928, 897, 911, 926, 972, 1011, 1016, 1032
        , 1034, 1071, 1088, 1091, 1105, 1112, 1117, 1118, 1119, 1152, 1156, 1204
    );

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

		// Get the file if there is one
		$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;
		
        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Delete all files
        ssh2_exec( $ssh_connection, "rm -Rf /gsr/systems/backend/admin/media/downloads/ashley/*" );

        if ( is_array( $accounts ) )
        foreach( $accounts as $account ) {
            // Need to make this not timeout and remove half the products first
            // @fix
            $this->run( $account, $file );
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
		$products = ar::assign_key( $this->get_website_product_skus( $account->id ), 'sku', true );

		if ( !is_array( $products ) )
			$products = array();

        // Get FTP
        $ftp = $this->get_ftp( $account );
        $delete_files = array();

        // Figure out what file we're getting
		if( empty( $file ) ) {
			// Get al ist of the files
			$files = array_reverse( $ftp->raw_list() );

            foreach ( $files as $f ) {
                if ( 'xml' != f::extension( $f['name'] ) )
                    continue;

                $size = f::size2bytes( $f['size'] );

                if ( empty( $file ) && $size >= self::COMPLETE_CATALOG_MINIMUM ) {
                    $file = $f['name'];
                } else {
                    $delete_files[] = $f['name'];
                }
            }
		}

        // Can't do anything without a file
        if ( empty( $file ) )
            return;

        // Delete all the other files
        if ( !empty( $delete_files ) )
        foreach ( $delete_files as $df ) {
            $ftp->delete( $df );
        }

        // Make sure the folder has been created
		$local_folder = "/gsr/systems/backend/admin/media/downloads/ashley/$ftp->username/";
        
		if ( !file_exists( $local_folder ) ) {
            // @fix MkDir isnt' changing the permissions, so we have to do the second call too.
			mkdir( $local_folder, 0777 );
            chmod( $local_folder, 0777 );
        }

		// Grab the latest file
		if( !file_exists( $local_folder . $file ) )
			$ftp->get( $file, '', $local_folder );
			
		$this->xml = simplexml_load_file( $local_folder . $file );

        // Now remove the file
        unlink( $local_folder . $file );
		
        // Declare array
        $packages = $this->get_ashley_packages();
        $skus = $remove_products = $new_product_skus = $all_skus = array();

        // Check #1 - Stop mass deletion
        if ( 0 == count( $this->xml->items->item ) ) {
            // We want to skip this account
            $ticket = new Ticket();
            $ticket->user_id = self::USER_ID; // Ashley
            $ticket->assigned_to_user_id = User::KERRY;
            $ticket->website_id = $account->id;
            $ticket->priority = Ticket::PRIORITY_HIGH;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->summary = 'Ashley Feed w/ No Products';
            $ticket->message = 'This account needs to be investigated';
            $ticket->create();
            return;
        }


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

            // Add any products they don't have
            if ( !array_key_exists( $sku, $products ) )
                $new_product_skus[] = $sku;

            // Setup packages
			if ( !stristr( $sku, '-' ) )
				continue;

			list( $series, $item ) = explode( '-', $sku, 2 );

			$skus[$series][] = $item;
		}
        
        $new_product_ids = $remove_skus = $group_items = array();
		
        // Add packages if they have all the pieces
		foreach ( $packages as $series => $items ) {
            // Go through each item
			foreach ( $items as $product_id => $package_pieces ) {
				// See if they have all the items necessary
				foreach ( $package_pieces as $item ) {
                    // Check if it is a series such as "W123-45" or "W12345"
					if ( is_array( $skus[$series] ) && in_array( $item, $skus[$series] ) ) {
                        $group_items[$series] = true;
						continue;
                    }
                    
					if ( in_array( $series . $item, $all_skus ) ) {
                        $group_items[$series] = true;
						continue;
                    }

                    //$remove_skus[] = "$series-$item";
                    //$remove_skus[] = $series . $item;

                    // If they don't have both, then stop this item
                    unset ( $group_items[$series] );
					continue 2; // Drop out of both
				}

                // Add to packages list
				$new_product_ids[] = $product_id;
			}
		}
		
        // Only need one of each
		//$remove_skus = array_unique( $remove_skus );

		// Now remove skus
		/* if ( !empty( $remove_skus ) )
		foreach ( $remove_skus as $sku ) {
			unset( $new_product_skus[array_search( $sku, $new_product_skus )] );
		}*/

		if ( is_array( $products ) )
		foreach ( $products as $sku => $product_id ) {
			if ( !in_array( $sku, $all_skus ) )
				$remove_products[] = (int) $product_id;
		}

        // Check #2 - Stop mass deletion
        $remove_product_count = count( $remove_products );

        if ( $remove_product_count > 500 && !isset( $_GET['override'] ) ) {
            // We want to skip this account
            $ticket = new Ticket();
            $ticket->user_id = self::USER_ID; // Ashley
            $ticket->assigned_to_user_id = User::KERRY;
            $ticket->website_id = $account->id;
            $ticket->priority = Ticket::PRIORITY_HIGH;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->summary = 'Ashley Feed Removing Too Many Products';
            $ticket->message = 'Trying to remove ' . $remove_product_count . ' products';
            $ticket->create();
            return;
        }

		// Add new products
        $industries = $account->get_industries();
		$this->add_bulk( $account->id, $industries, $new_product_skus );

        // Check testing sites

        if ( !in_array( $account->id, $this->omit_sites ) ) {
            $this->add_bulk_packages_by_ids( $account->id, $industries, $new_product_ids );
            $this->add_product_groups( $account->id, array_keys( $group_items ) );
        }

		// Deactivate old products
        $account_product = new AccountProduct();

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
            , array( ':account_id' => $account_id, ':user_id_created' => self::USER_ID )
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
     * Get Feed Accounts
     *
     * @return mixed
     */
    protected function get_feed_accounts() {
        return $this->get_results( "SELECT ws.`website_id` FROM `website_settings` AS ws LEFT JOIN `websites` AS w ON ( w.`website_id` = ws.`website_id` ) WHERE ws.`key` = 'ashley-ftp-password' AND ws.`value` <> '' AND w.`status` = 1", PDO::FETCH_CLASS, 'Account' );
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
	 * @param array $product_skus
	 */
	public function add_bulk( $account_id, array $industry_ids, array $product_skus ) {
        // Make sure they entered in SKUs
        if ( 0 == count( $product_skus ) || 0 == $industry_ids )
            return;

        // Make account id safe
        $account_id = (int) $account_id;

        // Make industry IDs safe
        foreach ( $industry_ids as &$iid ) {
            $iid = (int) $iid;
        }

        $industry_ids_sql = implode( ',', $industry_ids );

        // Split into chunks so we can do queries one at a time
		$product_sku_chunks = array_chunk( $product_skus, 500 );

		foreach ( $product_sku_chunks as $product_skus ) {
            // Get the count
            $product_sku_count = count( $product_skus );

			// Turn it into a string
			$product_skus_sql = '?' . str_repeat( ',?', $product_sku_count - 1 );

			// Magical Query
			// Insert website products
			$this->prepare(
                "INSERT INTO `website_products` ( `website_id`, `product_id` ) SELECT DISTINCT $account_id, `product_id` FROM `products` WHERE `industry_id` IN( $industry_ids_sql ) AND `user_id_created` = 353 AND `publish_visibility` = 'public' AND `status` <> 'discontinued' AND `sku` IN ( $product_skus_sql ) GROUP BY `sku` ON DUPLICATE KEY UPDATE `active` = 1"
                , str_repeat( 's', $product_sku_count )
                , $product_skus
            )->query();
		}
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

    /**
     * Add Product Groups
     *
     * @param int $account_id
     * @param array $group_items
     */
    protected function add_product_groups( $account_id, array $group_items ) {
        // Delete all the relations created in the past
        $this->delete_by_account( $account_id );

        // If they have nothing to add, go home!
        if ( empty( $group_items ) )
            return;

        $account_product_group = new AccountProductGroup();
        $account_product_group->website_id = $account_id;

        foreach ( $group_items as $series ) {
            $account_product_group->name = "Ashley Feed ($series)";
            $account_product_group->create();

            $account_product_group->add_relations_by_series( $series );

            // If it didn't add anything, that means they were blocked products or belonged to hidden categories -- let's remove the group
            if ( $account_product_group->get_row_count() <= 0 )
                $account_product_group->remove();
        }
    }

    /**
     * Delete relations
     *
     * @param int $account_id
     */
    protected function delete_by_account( $account_id ) {
        $this->prepare(
            "DELETE wpg.*, wpgr.* FROM `website_product_groups` AS wpg LEFT JOIN `website_product_group_relations` AS wpgr ON( wpg.`website_product_group_id` = wpgr.`website_product_group_id` ) WHERE wpg.`website_id` = :account_id AND wpg.`name` LIKE 'Ashley Feed (%)'"
            , 'i'
            , array( ':account_id' => $account_id )
        )->query();
    }

    /**
     * Get FTP
     *
     * @param Account $account
     * @return Ftp
     */
    public function get_ftp( Account $account ) {
        // Initialize variables
        $settings = $account->get_settings( 'ashley-ftp-username', 'ashley-ftp-password', 'ashley-alternate-folder' );
        $username = security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY );
        $password = security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY );

        $folder = str_replace( 'CE_', '', $username );

          // Modify variables as necessary
        if ( '-' != substr( $folder, -1 ) )
            $folder .= '-';

          $subfolder = ( '1' == $settings['ashley-alternate-folder'] ) ? 'Outbound/Items' : 'Outbound';

        // Setup FTP
        $ftp = new Ftp( "/CustEDI/$folder/$subfolder/" );

        // Set login information
        $ftp->host     = self::FTP_URL;
        $ftp->username = $username;
        $ftp->password = $password;
        $ftp->port     = 21;

        // Connect
        $ftp->connect();

        return $ftp;
    }
}
