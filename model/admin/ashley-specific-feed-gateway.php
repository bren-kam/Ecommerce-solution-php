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
    const ASHLEY_EXPRESS_FLAG = 'ashley-express';

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

    // We need to skip these products, they came from an XLS, should be replaced by a feed soon
    protected $ashley_express_carton = array('2530113','8170013','A1000029','A1000030','A1000031','A1000032','A1000041','A1000043','A1000044','A1000046','A1000047','A1000066','A1000069','A1000070','A1000072','A1000074','A1000077','A1000078','A1000087','A1000091','A1000092','A1000094','A1000146','A1000157','A1000159','A1000160','A1000161','A1000168','A1000172','A1000179','A1000180','A1000181','A1000183','A1000188','A1000189','A1000190','A1000191','A1000192','A1000193','A1000194','A1000195','A1000196','A1000197','A1000198','A1000199','A1000200','A1000201','A1000203','A1000204','A1000205','A1000207','A1000209','A1000210','A1000211','A1000212','A1000213','A1000214','A1000215','A1000216','A1000217','A1000223','A1000225','A1000227','A1000228','A1000229','A1000231','A1000232','A1000239','A1000240','A1000241','A1000242','A1000243','A1000247','A1000248','A1000249','A1000250','A1000251','A1000264','A1000265','A1000271','A1000272','A1000273','A1000275','A1000283','A1000286','A1000287','A1000288','A1000289','A1000291','A1000293','A1000294','A1000296','A1000297','A1000301','A1000302','A1000303','A1000305','A1000307','A1000308','A1000309','A1000310','A1000311','A1000312','A1000316','A1000317','A1000318','A1000321','A1000325','A1000326','A1000329','A1000332','A1000334','A1000336','A1000337','A1000339','A1000340','A1000341','A1000342','A1000343','A1000344','A1000346','A1000347','A1000349','A1000350','A1000353','A1000355','A1000361','A1000362','A1000363','A1000371','A1000374','A1000377','A1000378','A1000379','A1000380','A1000381','A1000391','A1000395','A1000396','A1000397','A1000414','A2C00006','A2C00015','A2C00016','A2C00019','A2C00026','A2C00029','A2C00030','A2C00037','A2C00039','A2C00046','A2C00048','A2C00049','A2C00069','A2C00071','A2C00073','A2C00074','A2C00075','A2C00077','A2C00078','A2C00088','A2C00089','A2C00093','A2C00095','A2C00096','A2C00097','A2C00098','A2C00100','A2C00101','A2C00102','A2C00103','A2C00104','A2C00105','A2C00106','A2C00107','A2C00108','A2C00109','A2C00110','A2C00111','A2C00114','A8000009','A8000012','A8000031','A8000043','A8000047','A8000061','A8000076','A8000077','A8000085','A8000089','A8000090','A8000098','A8000111','D293-223','D316-225','L115244','L116084','L117914','L118824','L119304','L119514','L120094','L121844','L123884','L124064','L125264','L126084','L128044','L129914','L136534','L141714','L142084','L150214','L151304','L200114','L200934','L201934','L201944','L202904','L203784','L204124','L205254','L206914','L207944','L210304','L211894','L213134','L235334','L243014','L243074','L245374','L246084','L247014','L248514','L276334','L277334','L278144','L279304','L280334','L281714','L282974','L283514','L287904','L289904','L292154','L292184','L293084','L304514','L304894','L307164','L311154','L312974','L313334','L316984','L318924','L319834','L320964','L321894','L324934','L328984','L329584','L347784','L369934','L370974','L372944','L405284','L406894','L408914','L409294','L409914','L410124','L411124','L411904','L413124','L414264','L415044','L415124','L416124','L417294','L419994','L420284','L422294','L428354','L429354','L431044','L431264','L431354','L432054','L433294','L434674','L436504','L438084','L439564','L440234','L442234','L443784','L481654','L507944','L508574','L509904','L511934','L512434','L513934','L521904','L530944','L531914','L603186','L610131','M82501','M82502','M82503','M82504','M82505','M82506','M82507','M82508','M82509','M89601','M89602','M89619','M89620','M89621','M89622','M89623','M89701','M89702','M89719','M89720','M89721','M89722','M89723','M89724','M97001','M97002','M97019','M97020','M97021','M97022','M97023','M97024','Q380004Q','Q393013K','Q395013Q','T113-13','T126-13','T131-13','T133-13','T134-13','T140-13','T141-13','T143-13','T158-13','T164-13','T165-13','T176-13','T180-13','T204-13','T210-13','T211-13','T225-13','T227-13','T228-13','T230-6','T230-8','T231-13','T238-13','T252-13','T258-13','T265-13','T269-13','T277-13','T281-13','T286-13','T291-13','T292-6','T303-13','T309-13','T317-13','T352-13','T362-13','T369-13','T392-13','T401-13','T406-13','T407-13','T409-13','T428-13','T470-13','T473-13','T477-8','T500-716','T533-13','T593-13','T838-1','T838-4');

    /**
     * Determine what was not identical
     * @var array
     */
    protected $not_identical = array();

    /**
     * Hold the brand code translation
     */
    protected $codes = array(
        'AB' => 8
        , 'AD' => 8
        , 'AS' => 8
        , 'AT' => 8
        , 'MB' => 171
        , 'MD' => 171
        , 'BF' => 8
        , 'BL' => 8
        , 'BV' => 8
        , 'DB' => 170
        , 'DD' => 170
        , 'DT' => 170
        , 'SB' => 170
        , 'SD' => 170
        , 'DH' => 170
        , 'DM' => 170
        , 'DS' => 170
        , 'DC' => 170
        , 'SS' => 170
        , 'SH' => 170
        , 'SM' => 170
        , 'SC' => 170
        , 'AH' => 8
        , 'AM' => 8
        , 'AO' => 8
        , 'AC' => 8
        , 'MH' => 171
        , 'MM' => 171
        , 'MS' => 171
        , 'MC' => 171
        , 'UA' => 8
        , 'UU' => 8
        , 'UO' => 8
        , 'MO' => 171
        , 'MU' => 171
        , 'DA' => 170
        , 'DO' => 170
        , 'DU' => 170
        , 'SO' => 170
        , 'SU' => 170
        , 'ZZ' => 8
    );

    /**
     * All products in System
     * @var Product[]
     */
    protected $existing_products;

	/**
	 * Creates new Database instance
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct('');

        // Set specs to last longer
        ini_set( 'max_execution_time', 3600 ); // 1 hour
		ini_set( 'memory_limit', '512M' );
		set_time_limit( 3600 );

        // Get all existing products in the system
        $this->get_existing_products();
    }

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
            echo "Running: " . $account->title . "\n";
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

                $file_name = f::name( $f['name'] );
                if ( strpos( $file_name, '888-' ) === false )
                    continue;

                if ( $size < self::COMPLETE_CATALOG_MINIMUM )
                    continue;

                $file = $f['name'];
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
        $skus = $remove_products = $new_product_skus = $all_skus = $new_products = $ashley_express_skus = array();

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

            // Create the products if we don't have it in the system
            // Lets run this on all product
            // So we can ensure they are up to date
            // if ( !array_key_exists( $sku, $this->existing_products ) ) {
                $new_product = $this->get_product_info( $item );
                if ( $new_product )
                    $new_products[$sku] = $new_product;
            // }

            // Add to Account any products they don't have
            if ( !array_key_exists( $sku, $products ) )
                $new_product_skus[] = $sku;

            // Ashley Express detection
            if ( $item->attributes()->itemIsAvailable == "true" ) {
                $ashley_express_skus[$sku] = true;
            } else {
                $ashley_express_skus[$sku] = false;
            }

            // Setup packages
			if ( stristr( $sku, '-' ) ) {
                list( $series, $item ) = explode( '-', $sku, 2 );
            } else if ( strlen( $sku ) == 7 && is_numeric( $sku{0} ) ) {
                $series = substr( $sku, 0, 5 );
                $item = substr( $sku, 5 );
            } else if ( strlen( $sku ) == 8 && ctype_alpha( $sku{0} ) ) {
                $series = substr( $sku, 0, 6 );
                $item = substr( $sku, 6 );
            } else {
                continue;
            }


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

                    // If they don't have both, then stop this item
                    unset ( $group_items[$series] );
					continue 2; // Drop out of both
				}

                // Add to packages list
				$new_product_ids[] = $product_id;
			}
		}

        // Delete products that are not in the feed
		if ( is_array( $products ) )
		foreach ( $products as $sku => $product_id ) {
			if ( !in_array( $sku, $all_skus ) )
				$remove_products[] = (int) $product_id;

            // Skip Ashley Carton/Case products
            // Came from an Ashley Express spreadsheet given by Ashley
            if ( in_array( $sku, $this->ashley_express_carton ) )
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

        // Add missing products to Master Catalog
        if ( !empty( $new_products ) ) {
            // Get groups
            $groups = $this->get_groups();
            // Add new products to System
            $this->add_products( $new_products, $groups );
        }

        // set/unset Ashley Express flag
        // Update 2014-07-30 this was changed to flag manually, NOT via xml
        // if ( !empty( $ashley_express_skus ) ) {
        //     $this->set_bulk_ashley_express( $ashley_express_skus );
        // }

		// Add new products to Account
        $industries = $account->get_industries();
		$this->add_bulk( $account->id, $industries, $new_product_skus );

        // Check testing sites

        if ( !in_array( $account->id, $this->omit_sites ) ) {
            $this->add_bulk_packages_by_ids( $account->id, $industries, $new_product_ids );

            $series = array_merge( array_keys( $group_items ), array_keys( $skus ) );
            $this->add_product_groups( $account->id, array_unique( $series ) );
        }

		// Deactivate old products
        $account_product = new AccountProduct();

        $account_product->remove_bulk( $account->id, $remove_products );

		// Reorganize Categories
        $account_category = new AccountCategory();
		$account_category->reorganize_categories( $account->id, new Category() );
        $account->set_settings( array( 'feed-last-run' => dt::now() ) );

        // Set prices
        $auto_price = new WebsiteAutoPrice();
        $category = new Category();
        $auto_prices = $auto_price->get_all( $account->id );

        // Make sure we have something to work with
        if ( empty( Category::$categories ) )
            $category->get_all();

        if ( is_array( $auto_prices ) )
        foreach ( $auto_prices as $auto_price ) {
            $child_categories = $category->get_all_children( $auto_price->category_id );
            $category_ids = array( $auto_price->category_id );

            foreach ( $child_categories as $child_cat ) {
                $category_ids[] = $child_cat->id;
            }

            // Auto price for these categories
            $account_product->auto_price( $category_ids, $auto_price->brand_id, $auto_price->price, $auto_price->sale_price, $auto_price->alternate_price, $auto_price->ending, $account->id );
        }

        // If they haven't disabled it
        if ( '1' != $account->get_settings('disable-map-pricing') )
            // Make sure they didn't go below a minimum price
            $account_product->adjust_to_minimum_price( $account->id );
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
    public function get_feed_accounts() {
        return $this->get_results( "SELECT ws.`website_id` FROM `website_settings` AS ws LEFT JOIN `websites` AS w ON ( w.`website_id` = ws.`website_id` ) LEFT JOIN `website_settings` AS ws2 ON ( ws2.`website_id` = w.`website_id` AND ws2.`key` = 'feed-last-run' ) WHERE ws.`key` = 'ashley-ftp-password' AND ws.`value` <> '' AND w.`status` = 1 ORDER BY ws2.`value`", PDO::FETCH_CLASS, 'Account' );
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

            // Remove anything within parenthesis on SKU Pieces
            $regex = '/\(([^)]*)\)/';
            foreach ( $sku_pieces as $k => $sp ) {
                $sku_pieces[$k] = preg_replace($regex, '', $sp);
            }

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
			$this->query( "INSERT INTO `website_products` ( `website_id`, `product_id`, `sequence` ) SELECT DISTINCT $account_id, `product_id`, 10000 FROM `products` WHERE `industry_id` IN( $industry_ids_sql ) AND `user_id_created` = 1477 AND `product_id` IN ( $product_ids ) AND publish_visibility = 'public' GROUP BY `sku` ON DUPLICATE KEY UPDATE `active` = 1" );
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

        $website_product_group = new WebsiteProductGroup();
        $website_product_group->website_id = $account_id;

        foreach ( $group_items as $series ) {
            $website_product_group->name = "Ashley Feed ($series)";
            $website_product_group->create();

            $website_product_group->add_relations_by_series( $series );

            // If it didn't add anything, that means they were blocked products or belonged to hidden categories -- let's remove the group
            if ( $website_product_group->get_row_count() <= 0 )
                $website_product_group->remove();
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

    /**
     * Get the existing products
     */
    protected function get_existing_products() {
        $products = $this->prepare(
            "SELECT p.`product_id`, p.`brand_id`, p.`industry_id`, p.`name`, p.`slug`, p.`description`, p.`status`, p.`sku`, p.`price`, p.`weight`, p.`volume`, p.`product_specifications`, p.`publish_visibility`, p.`publish_date`, i.`name` AS industry, GROUP_CONCAT( `image` ORDER BY `sequence` ASC SEPARATOR '|' ) AS images, p.`category_id`, p.`timestamp` FROM `products` AS p LEFT JOIN `industries` AS i ON ( i.`industry_id` = p.`industry_id`) LEFT JOIN `product_images` AS pi ON ( pi.`product_id` = p.`product_id` ) WHERE p.`user_id_created` = :user_id_created GROUP BY p.`sku` ORDER BY `publish_visibility` DESC"
            , 'i'
            , array( ':user_id_created' => self::USER_ID )
        )->get_results( PDO::FETCH_CLASS, 'Product' );

        /**
         * @var Product $product
         */
        foreach ( $products as $product ) {
            $this->existing_products[$product->sku] = $product;
        }
    }

    /**
     * Get Brand
     *
     * @param string $retail_sales_category_code
     * @return int
     */
    protected function get_brand( $retail_sales_category_code ) {
        return ( array_key_exists( $retail_sales_category_code, $this->codes ) ) ? $this->codes[$retail_sales_category_code] : '';
    }

    /**
     * See if something exists and return product id if it does
     *
     * @param mixed $key
     * @return Product
     */
    protected function get_existing_product( $key ) {
        $key = (string) $key;

        return ( array_key_exists( $key, $this->existing_products ) ) ? $this->existing_products[$key] : false;
    }

    /**
     * Get Groups from XML
     * @return array
     */
    protected function get_groups() {

        $groups = array();

        foreach ( $this->xml->groups->groupInformation as $group ) {
            $group_id = trim( $group['groupID'] );
            $groups[ $group_id ] = array(
                'name' => trim( $group['groupName'] )
            , 'description' => trim( $group['groupDescription'] )
            , 'features' => trim( $group['groupFeatures'] )
            );
        }

        return $groups;

    }

    /**
     * Reset identical
     */
    public function reset_identical() {
        $this->not_identical = array();
    }

    /**
     * Checks if something is identical, and returns it new one if it's empty
     *
     * @param string $variable
     * @param string $original
     * @param string $type
     * @return mixed
     */
    public function identical( $variable, $original, $type ) {
        // Nothing there, need original
        if ( empty( $variable ) )
            return $original;

        // They're not equal, so we need to mark it down
        if ( $variable != $original ) {
            if ( 'slug' == $type ) {
                $variable = $this->unique_slug( $variable );

                if ( $variable != $original )
                    $this->not_identical[] = $type;
            } else {
                $this->not_identical[] = $type;
            }
        }

        // Return the variable
        return $variable;
    }

    /**
     * Is identical -- checks if there any not identical parts
     *
     * @return bool
     */
    public function is_identical() {
        return 0 == count( $this->not_identical );
    }

    /**
     * Check to see if a Slug is already being used
     *
     * @param string $slug
     * @return string
     */
    protected function unique_slug( $slug ) {
        $existing_slug = $this->prepare( "SELECT `slug` FROM `products` WHERE `user_id_created` = :user_id_created AND `publish_visibility` <> 'deleted' AND `slug` = :slug"
            , 'is'
            , array( ':user_id_created' => self::USER_ID, ':slug' => $slug )
        )->get_var();

        // See if the slug already exists
        if ( $slug == $existing_slug ) {
            // Check to see if it has been incremented before
            if ( preg_match( '/-([0-9]+)$/', $slug, $matches ) > 0 ) {
                // The number to increment it by
                $increment = $matches[1] * 1 + 1;

                // Give it the new increment
                $slug = preg_replace( '/-[0-9]+$/', "-$increment", $slug );

                // Make sure it's unique
                $slug = $this->unique_slug( $slug );
            } else {
                // It has not been incremented before, start with 2
                $slug .= '-2';
            }
        }

        // Return the unique slug
        return $slug;
    }

    /**
     * Get Category
     *
     * @param string $sku
     * @param string $name
     * @return int
     */
    protected function get_category( $sku, $name ) {
        // Setup
        $category_id = 0;
        $length = strlen( $sku );
        $first_character = $sku[0];
        $last_character = substr( $sku, -1 );

        if ( 7 == $length && is_numeric( $first_character ) ) {
            // Living Room & Leather
            $relevant_sku = substr( $sku, 5, 2 );

            // Living Room
            switch ( $relevant_sku ) {
                case 60:
                case 23:
                case 21:
                case 20:
                case 46: // Chairs
                    $category_id = 221;
                    break;

                case 76:
                case 70:
                case 69:
                case 04:
                case 01:
                case 49:
                case 48:
                case 77:
                case 17:
                case 16:
                case 34:
                case 67:
                case 56:
                case 66:
                case 55: // Sectional Pieces
                    $category_id = 695;
                    break;

                case 14:
                case 13:
                case '08': // Ottomans
                    $category_id = 229;
                    break;

                case 35: // Loveseat
                    $category_id = 220;
                    break;

                case 73: // Chair w/ Ottoman
                    $category_id = 692;
                    break;

                case 37:
                case 36:
                case 39: // Sleeper Sofa
                    $category_id = 425;
                    break;

                case 38: // Sofa
                    $category_id = 219;
                    break;

                case 15:
                case 18: // Chaise
                    $category_id = 249;
                    break;

                case 31:
                case 29:
                case 61:
                case 26:
                case 30:
                case 25: // Recliners
                    $category_id = 222;
                    break;

                case 86:
                case 94:
                case 43: // Reclining Furniture > Reclining Love Seats
                    $category_id = 227;
                    break;

                case 74:
                case 96:
                case 91: // Reclining Power Loveseat
                    $category_id = 671;
                    break;

                case 98: // Power Recliner
                    $category_id = 672;
                    break;

                case 47:
                case 87: // Reclining Power Sofa
                    $category_id = 670;
                    break;

                case 81:
                case 88: // Reclining Furniture > Reclining Sofas
                    $category_id = 224;
                    break;
            }
        } elseif ( 'T' == $first_character ) {
            // Living Room > Occasional
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case 407:
                case 202:
                case 102:
                case 3:
                case 7:
                case 699:
                case 684:
                case 668:
                case 477:
                case 371: // Chair Side Tables
                    $category_id = 237;
                    break;

                case 13: // Three Pack Table Sets
                    $category_id = 251;
                    break;

                case '8T':
                case '8B':
                case 9;
                case 20:
                case 8:
                case 1:
                case 0: // Cocktail Table
                    $category_id = 231;
                    break;

                case 232:
                case 632:
                case 442:
                case 430:
                case 360:
                case 142:
                case 40: // Accent Cabinet
                    $category_id = 736;
                    break;

                case 12:
                case 306:
                case 106: // Accent Table
                    $category_id = 1165;
                    break;

                case 615: // Magazine Racks
                    $category_id = 738;
                    break;

                case 4: // Sofa Tables
                    $category_id = 234;
                    break;

                case 17:
                case 6:
                case 2: // End Tables
                    $category_id = 233;
                    break;

                case 705:
                case 804:
                case 504: // Console
                    $category_id = 235;
                    break;

                case 11: // Cabinet
                    $category_id = 657;
                    break;
            }
        } elseif ( 'W' == $first_character ) {
            // Home Entertainment
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '01': // Entertainment Accessories
                    $category_id = 1166;
                    break;

                case 78:
                case 22:
                case 68:
                case 50:
                case 12:
                case '23H':
                case '21H':
                case 11:
                case 20:
                case 80:
                case 58:
                case '60H':
                case 48:
                case 22:
                case 31:
                case 21:
                case 38:
                case 28:
                case 18:
                case 17:
                case 10:
                case 60: // Consoles
                    $category_id = 333;
                    break;

                case 27:
                case 35:
                case 33:
                case 25:
                case 24:
                case 34:
                case '23B':
                case 23:
                case 26: // Wall Systems
                    $category_id = 336;
                    break;

                case 36: // Media Storage Cabinets
                    $category_id = 622;
                    break;

                case 400:
                case 40: // Corner Cabinets
                    $category_id = 334;
                    break;
            }
        } elseif ( 'D' == $first_character ) {
            // Dining Room
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '13T':
                case '13B':
                case '15T':
                case '15B':
                case 32:
                case 325:
                case 125:
                case '50T':
                case '50B':
                case 13:
                case 21:
                case 26:
                case 25:
                case 15: // Tables
                    $category_id = 130;
                    break;

                case 223:
                case 65: // Pub Tables
                    $category_id = 144;
                    break;

                case 225: // Dining Room Groups
                    $category_id = 347;
                    break;

                case 524:
                case 424:
                case 224:
                case 323:
                case 320:
                case 324:
                case 130:
                case 124:
                case 230: // Bar Stools
                    $category_id = 142;
                    break;

                case 76:
                case 360:
                case 160:
                case 59:
                case 60: // Servers
                    $category_id = 726;
                    break;

                case '05':
                case 102:
                case 202:
                case 101:
                case '06':
                case '09':
                case '07':
                case '04':
                case '03':
                case '01': // Side Chairs
                    $category_id = 132;
                    break;

                case 80: // Buffets
                    $category_id = 133;
                    break;

                case '00': // Benches
                    $category_id = 141;
                    break;

                case '03A':
                case '02A':
                case '01A': // Arm Chairs
                    $category_id = 131;
                    break;

                case '65T':
                case '65B':
                case 65: // Side Boards
                    $category_id = 134;
                    break;

                case 76: // Bakers Racks
                    $category_id = 135;
                    break;

                case 61:
                case 81: // China Cabinets
                    $category_id = 212;
                    break;
            }
        } elseif ( 'B' == $first_character ) {
            // Bedroom / Kid's Furniture
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                /***** Kid's Furniture *****/

                case '01': // Chairs
                    $category_id = 279;
                    break;

                case '13R':
                case '13L';
                case '68T':
                case '68B': // Beds > Loft
                    $category_id = 688;
                    break;

                case '59B': // Beds > Beds
                    $category_id = 273;
                    break;

                case 83:
                case 82:
                case 86:
                case '59S':
                case '59R':
                case '59P':
                case '50T':
                case '50D':
                case '20R': // Beds > Bed Frame
                    $category_id = 700;
                    break;

                case 87:
                case 53:
                case 52: // Beds > Headboard
                    $category_id = 698;
                    break;

                case 84:
                case 51: // Beds > Footboard
                    $category_id = 699;
                    break;

                case '20L';
                case 22:
                case 52: // Desks
                    $category_id = 277;
                    break;

                case 20;
                case 17:
                case 16: // Bookcase
                    $category_id = 417;
                    break;

                case 19:
                case 18: // Chests
                    $category_id = 272;
                    break;

                case 21: // Dressers
                    $category_id = 270;
                    break;

                case 23: // Hutch
                    $category_id = 278;
                    break;

                case 26: // Mirrors
                    $category_id = 269;
                    break;

                case 38: // Media Chests
                    $category_id = 624;
                    break;

                case '68B':
                case 60:
                case 59:
                case '58S':
                case '57S':
                case '57P':
                case '058':
                case '008': // Bunk Beds
                    $category_id = 617;
                    break;

                case 80: // Daybed
                    $category_id = 282;
                    break;

                /***** Bedroom *****/

                case '09':
                case '00': // Benches
                    $category_id = 569;
                    break;

                case 36:
                case 35:
                case 28: // Mirrors
                    $category_id = 102;
                    break;

                case 31: // Dressers
                    $category_id = 101;
                    break;

                case 39:
                case 38:
                case 41:
                case 40: // Media Chests
                    $category_id = 107;
                    break;

                case 47:
                case 46:
                case 45:
                case 43: // Chests
                    $category_id = 103;
                    break;

                case '50R':
                case '50L': // Storage Cabinet
                    $category_id = 626;
                    break;

                case '91R':
                case '91L':
                case 193:
                case 91:
                case 92:
                case 93: // Nightstand
                    $category_id = 105;
                    break;

                case '49T':
                case '49B': // Armoire
                    $category_id = 104;
                    break;

                case 68:
                case 58:
                case 57:
                case 82:
                case 81:
                case 55:
                case 67:
                case 394:
                case 357:
                case 357:
                case 258:
                case 257:
                case 150:
                case 158:
                case 157:
                case 78:
                case 77:
                case '71N': // Headboards
                    $category_id = 125;
                    break;

                case '166S':
                case 166:
                case '164S':
                case 164:
                case 151:
                case 356:
                case 354:
                case 294:
                case 256:
                case 254:
                case 194:
                case 154:
                case 156:
                case '66N':
                case '64N':
                case '64S':
                case 76:
                case 74:
                case 64:
                case 54:
                case 50:
                case '66S':
                case 66:
                case 56:
                case 70: // Footboards
                    $category_id = 428;
                    break;

                case '97S':
                case '96S':
                case 95:
                case '94S':
                case 94:
                case 256:
                case 254:
                case 199:
                case 197:
                case 196:
                case 195:
                case 194:
                case 97:
                case '99N':
                case '98N':
                case 96:
                case 98:
                case 99: // Bed Frames
                    $category_id = 126;
                    break;
            }
        } elseif ( 'H' == $first_character ) {
            // Home Furniture
            list( $series, $relevant_sku ) = explode( '-', $sku );

            switch ( $relevant_sku ) {
                case '19H':
                case 49:
                case 48: // Hutch
                    $category_id = 583;
                    break;

                case '23H':
                case 47:
                case 23:
                case 24:
                case 45:
                case '27R':
                case 46:
                case 29:
                case 27:
                case 44:
                case 26:
                case 10:
                case 19: // Home Office Desks
                    $category_id = 329;
                    break;

                case 25:
                case 40:
                case 42:
                case 12: // Home Office File Cabinets and Carts
                    $category_id = 330;
                    break;

                case '01A': // Home Office Desk Chair
                    $category_id = 437;
                    break;

                case 47:
                case 34:
                case '70T':
                case '70B':
                case 18:
                case 17:
                case 16:
                case 15: // Bookcases
                    $category_id = 452;
                    break;
            }
        } elseif ( 'M' == $first_character ) {
            // Mattresses

            if ( stristr( $name, 'Pillowtop' ) ) {
                // Pillowtop mattresses
                $category_id = 167;
            } elseif ( stristr( $name, 'Plush' ) ) {
                // Plush Mattresses
                $category_id = 166;
            } elseif ( stristr( $name, 'Latex' ) ) {
                // Latex Mattresses
                $category_id = 169;
            } elseif ( stristr( $name, 'Gel' ) ) {
                // Gel mattresses
                $category_id = 564;
            } else {
                // Memory Foam Mattresses
                $category_id = 168;
            }
        } elseif ( 'Q' == $first_character && 8 == $length && !is_numeric( $last_character ) ) {
            // Bedding > Bedding Ensembles
            $category_id = 179;
        }  elseif ( 'L' == $first_character && 7 == $length ) {
            // Accessories > Lamps
            $category_id = 194;
        } elseif ( 'R' == $first_character && 7 == $length ) {
            // Accessories > Rugs
            $category_id = 338;
        } elseif ( 'AC2' == substr( $sku, 0, 3 ) && 8 == $length ) {
            // Accessories > Table Tops
            $category_id = 341;
        } elseif ( 'M89' == substr( $sku, 0, 3 ) && 6 == $length ) {
            // Bedding > Pillows
            $category_id = 597;
        } elseif ( stristr( $name, 'Throw' ) ) {
            // Accessories > Throws
            $category_id = 342;
        } elseif ( stristr( $name, 'Wall Art' ) ) {
            // Accessories > Wall Art
            $category_id = 339;
        } elseif ( 'A' == $first_character && stristr( $name, 'Pillow' ) ) {
            // Bedding > Pillows
            $category_id = 597;
        }

        return $category_id;
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
        $curl = new Curl();
        $file = new File();

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
        $curl->save_file( $image_url, $fp );

        // Close file
        fclose( $fp );

        $file->upload_image( $image_path, $new_image_name, 350, 350, $industry, "products/{$product_id}/", false, true );
        $file->upload_image( $image_path, $new_image_name, 64, 64, $industry, "products/{$product_id}/thumbnail/", false, true );
        $file->upload_image( $image_path, $new_image_name, 200, 200, $industry, "products/{$product_id}/small/", false, true );
        $full_image_name = $file->upload_image( $image_path, $new_image_name, 1000, 1000, $industry, "products/{$product_id}/large/" );

        if( file_exists( $image_path ) )
            @unlink( $image_path );

        return $full_image_name;
    }


    /**
     *  Add products System wide
     *
     * @param array $products
     * @param array $groups
     */
    protected function add_products( array $products, array $groups ) {

        // Generate array of our items
        foreach( $products as $item_key => $item ) {
            /***** SETUP OF PRODUCT *****/

            // Trick to make sure the page doesn't timeout or segfault
            set_time_limit(3600);

            /***** CHECK PRODUCT *****/

            // Setup the variables to see if we should continue
            $sku = $item['sku'];

            // We can't have a SKU like B457B532 -- it means it is international and comes in a container
            if ( preg_match( '/^[lL]?[0-9-]+[a-zA-Z][0-9-]+/', $sku ) )
                continue;

            if ( !isset( $groups[$item['group'] ] ) ) {
                $item['group'] = preg_replace( '/([^-]+)-.*/', '$1', $item['group'] );

                if ( !isset( $groups[$item['group']] ) )
                    $groups[$item['group']] = array('name' => '', 'description' => '', 'features' => '');
            }

            /***** GET PRODUCT *****/

            // Get Product
            $product = $this->get_existing_product( $sku );

            $two_days_ago = new DateTime();
            $two_days_ago->sub( new DateInterval("P2D"));
            $last_update = new DateTime($product->timestamp);
            if ( $last_update > $two_days_ago )
                continue;

            if ( 'deleted' == $product->publish_visibility )
                continue;

            // Now we have the product
            if ( !$product instanceof Product ) {
                $new_product = true;
                $product = new Product();
                $product->website_id = 0;
                $product->user_id_created = self::USER_ID;
                $product->publish_visibility = 'private';

                $product->create();

                // Set publish date
                $product->publish_date = dt::now();
            } else {
                $new_product = false;
                $product->user_id_modified = self::USER_ID;
            }

            $product->get_specifications();

            /***** PREPARE PRODUCT DATA *****/

            $group = $groups[$item['group']];
            $group_name = $group['name'] ? ( $group['name'] . ' - ' ) : '';

            $group_description = $group['description'] ? ('<p>' . $group['description'] . '</p>') : '';
            $group_features = $group['features'] ? ('<p>' . $group['features'] . '</p>') : '';

            $name = format::convert_characters( $group_name . $item['description'] );

            /***** ADD PRODUCT DATA *****/

            // Reset the product to being "not" identical
            $this->reset_identical();

            $product->industry_id = 1;

            // Ticket 17005 said to no longer change these.
            if ( $new_product || empty( $product->slug ) ) {
                $product->name = $name;
                $product->slug = str_replace( '---', '-', format::slug( $name ) );

                // Check if slug already exists
                $duplicated_slug = new Product();
                $duplicated_slug->get_by_slug( $product->slug );
                // If slug exists, append random number and check again
                while ( $duplicated_slug->id != null ) {
                    $product->slug = str_replace( '---', '-', format::slug( $name ) ) . '-' . rand( 1000, 9999 );
                    $duplicated_slug = new Product();
                    $duplicated_slug->get_by_slug( $product->slug );
                }
            }

            $product->sku = $this->identical( $sku, $product->sku, 'sku' );
            $product->status = $this->identical( $item['status'], $product->status, 'status' );
            $product->price = $this->identical( $item['price'], $product->price, 'price' );
            $product->weight = $this->identical( $item['weight'], $product->weight, 'weight' );
            $product->brand_id = $this->identical( $item['brand_id'], $product->brand_id, 'brand' );
            $product->description = $this->identical( format::convert_characters( format::autop( format::unautop( '<p>' . $item['description'] . "</p>{$group_description}{$group_features}" ) ) ), format::autop( format::unautop( $product->description ) ), 'description' );

            // Handle categories
            if ( $new_product || empty( $product->category_id ) ) {
                // Get category
                $product->category_id = $this->get_category( $product->sku, $product->name );
            }

            // Save image to load later in AshleySpecificFeedGateway::getImages();
            // It's taking ages as many images timeout, so we are getting them in a separate cron job.
            $image = $item['image'];
            $tag = new Tag();
            $already_queued = $tag->get_value_by_type( 'ashley_product', $product->id );
            if ( !$already_queued ) {
                $tag->add_bulk( 'ashley_product_image', $product->id, array( $image ) );
            }

            $publish_visibility = ( 'discontinued' == $item['status'] ) ? 'deleted' : $product->publish_visibility;
            $product->publish_visibility = $this->identical( $publish_visibility, $product->publish_visibility, 'publish_visibility' );

            /***** SKIP PRODUCT IF IDENTICAL *****/

            // If everything is identical, we don't want to do anything
            if ( $this->is_identical() ) {
                // -- Skips for report --
                // $this->skip( $name );
                $this->items[$item_key] = NULL;
                continue;
            }

            /***** UPDATE PRODUCT *****/

            if ( $product->category_id && !empty( $images ) )
                $product->publish_visibility = 'public';

            $product->save();

            // Add specs
            $product->delete_specifications();

            // if ( !empty( $item['specs'] ) )
                $product->add_specifications( $item['specs'] );

            // Add on to lists
            $this->existing_products[$product->sku] = $product;
            $products[$item_key] = NULL;

        }

    }

    /**
     * Get product info from SimpleXMLElement
     * @param SimpleXMLElement $item
     * @return array|null
     */
    protected function get_product_info( $item ) {

        $new_product = array(
            'sku' => trim( $item->itemIdentification->itemIdentifier[0]->attributes()->itemNumber )
            , 'description' => trim( $item->itemIdentification->itemDescription['itemFriendlyDescription'] )
            , 'status' => ( 'Discontinued' == trim( $item['itemStatus'] ) ) ? 'discontinued' : 'in-stock'
            , 'group' => trim( $item['itemGroupCode'] )
            , 'image' => trim( $item['image'] )
            , 'brand_id' => $this->get_brand( trim( $item['retailSalesCategory'] ) )
            , 'weight' => trim( $item->itemIdentification->packageCharacteristics->packageDimensions->weight['value'] )
            , 'price' => (float) trim( $item->itemPricing->unitPrice )
            , 'volume' => 0
            , 'specs' => array()
        );

        if ( empty( $new_product['image'] ) )
            return null;

        // we will search for the <itemDimensions> in Inches
        foreach ( $item->itemIdentification->itemCharacteristics as $ic ) {
            if ( $ic->itemDimensions->depth['unitOfMeasure'] == "Inches" ) {
                $new_product['specs'][] = array( 'Depth', trim( $ic->itemDimensions->depth['value'] ) . ' Inches' ) ;
                $new_product['specs'][] = array( 'Height', trim( $ic->itemDimensions->height['value'] ) . ' Inches' ) ;
                $new_product['specs'][] = array( 'Length', trim( $ic->itemDimensions->length['value'] ) . ' Inches' ) ;
                break;
            }
        }

        return $new_product;

    }

    public function get_product_images() {

        $tag = new Tag();
        $this->get_existing_products();

        foreach( $this->existing_products as $product ) {

            if ( $product->publish_visibility == 'deleted' )
                continue;

            $images = $tag->get_value_by_type( 'ashley_product_image', $product->id );
            foreach ( $images as $image ) {
                echo "#{$product->id} - {$image}\n";

                /***** ADD PRODUCT IMAGES *****/
                $image_urls = array();
                $image_urls[] = 'https://www.ashleydirect.com/graphics/ad_images/' . str_replace( '_BIG', '', $image );
                $image_urls[] = 'https://www.ashleydirect.com/graphics/Presentation_Images/' . str_replace( '_BIG', '', $image );
                $image_urls[] = 'https://www.ashleydirect.com/graphics/' . $image;

                // Setup images array
                $images = explode( '|', $product->images );
                $last_character = substr( $images[0], -1 );

                foreach ( $image_urls as $image_url ) {
                    if ( ( 0 == count( $images ) || empty( $images[0] ) || '.' == $last_character ) && !empty( $image ) && !in_array( $image, array( 'Blank.gif', 'NOIMAGEAVAILABLE_BIG.jpg' ) ) && curl::check_file( $image_url, 5 ) ) {
                        try {
                            echo "...uploading...\n";
                            $image_name = $this->upload_image( $image_url, $product->slug, $product->id, 'furniture' );
                        } catch( InvalidParametersException $e ) {
                            fn::info( $product );
                            exit;
                        }

                        if ( !is_array( $images ) || !in_array( $image_name, $images ) ) {
                            echo "...uploaded. Saving Image & Product.\n";
                            $images[] = $image_name;

                            $product->add_images( $images );
                            $product->publish_visibility = 'public';
                            $product->save();
                        }
                    }
                }

            }

            $tag->delete_by_type( 'ashley_product_image', $product->id );
        }

        echo "Finished\n";

    }

}
