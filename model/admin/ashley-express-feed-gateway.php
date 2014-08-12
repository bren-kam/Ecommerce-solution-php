<?php
/**
 * Handles ashley import
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class AshleyExpressFeedGateway extends ActiveRecordBase {
	const FTP_URL = 'ftp.ashleyfurniture.com';
    const USER_ID = 353; // Ashley
    const COMPLETE_CATALOG_MINIMUM = 10485760; // 10mb In bytes
    const SHIPPING_METHOD_ID = 1048576;

    protected $omit_sites = array( 161, 187, 296, 343, 341, 345, 371, 404, 456, 461, 464, 468, 492, 494, 501, 557, 572
        , 582, 588, 599, 606, 614, 641, 644, 649, 660, 667, 668, 702, 760, 928, 897, 911, 926, 972, 1011, 1016, 1032
        , 1034, 1071, 1088, 1091, 1105, 1112, 1117, 1118, 1119, 1152, 1156, 1204
    );

    /**
     * @var SimpleXMLElement
     */
    private $xml;

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
		// Get FTP

        $ftp = $this->get_ftp( $account );

        // Figure out what file we're getting
		if( empty( $file ) ) {
			// Get al ist of the files
			$files = array_reverse( $ftp->raw_list() );

            foreach ( $files as $f ) {
                if ( 'xml' != f::extension( $f['name'] ) )
                    continue;

                $file_name = f::name( $f['name'] );
                if ( strpos( $file_name, '846-' ) === false )
                    continue;

                $size = f::size2bytes( $f['size'] );
                if ( $size < self::COMPLETE_CATALOG_MINIMUM )
                    continue;

                $file = $f['name'];
            }
		}

        // Can't do anything without a file
        if ( empty( $file ) )
            return;

        // Make sure the folder has been created
		$local_folder = sys_get_temp_dir() . '/';

		// Grab the latest file
		if( !file_exists( $local_folder . $file ) )
			$ftp->get( $file, '', $local_folder );

		$this->xml = simplexml_load_file( $local_folder . $file );

        // Now remove the file
        unlink( $local_folder . $file );

        // Check #1 - Stop mass deletion
        if ( 0 == count( $this->xml->items->itemAdvice ) ) {
            // We want to skip this account
            $ticket = new Ticket();
            $ticket->user_id = self::USER_ID;
            $ticket->assigned_to_user_id = User::KERRY;
            $ticket->website_id = $account->id;
            $ticket->priority = Ticket::PRIORITY_HIGH;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->summary = 'Ashley Express Feed w/ No Products';
            $ticket->message = 'This account needs to be investigated';
            $ticket->create();
            return;
        }

        // Declare array
        $ashley_express_skus = array();

        $ns = $this->xml->getDocNamespaces();
        if ( isset( $this->xml->inquiry->potentialBuyer ) ) {
            $account->set_settings( array(
                'ashley-express-buyer-id' => (string)$this->xml->inquiry->potentialBuyer->children( $ns['fnParty'] )->attributes()->partyIdentifierCode
            ) );
        }

        // Generate array of our items
        /**
         * @var SimpleXMLElement $item
         */
        foreach ( $this->xml->items->itemAdvice as $item ) {

            $sku = $item->itemId->itemIdentifier['itemNumber'];

            foreach ( $item->itemAvailability as $availability ) {
                // Item is Ashley Express only if stock for current availability is greater than 5
                if ( $availability['availability'] == 'current' ) {
                    if ( $availability->availQty['value'] > 5 ) {
                        $ashley_express_skus[] = $sku;
                    }
                    break;
                }
            }

		}

        $this->add_bulk( $account, $ashley_express_skus );

	}

    /**
     * Get Feed Accounts
     *
     * @return mixed
     */
    protected function get_feed_accounts() {
        return $this->get_results( "SELECT ws.`website_id` FROM `website_settings` AS ws LEFT JOIN `websites` AS w ON ( w.`website_id` = ws.`website_id` ) LEFT JOIN `website_settings` AS ws2 ON ( ws2.`website_id` = w.`website_id` AND ws2.`key` = 'feed-last-run' ) WHERE ws.`key` = 'ashley-ftp-password' AND ws.`value` <> '' AND w.`status` = 1 ORDER BY ws2.`value`", PDO::FETCH_CLASS, 'Account' );
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
     * Set Bulk Ashley Express
     * @param Account $account
     * @param string[] $skus array of skus
     */
    private function add_bulk( $account, $skus ) {

        $this->prepare("
                DELETE wpsm
                FROM `website_product_shipping_method` wpsm
                INNER JOIN `products` p ON ( p.`product_id` = wpsm.`product_id` )
                WHERE wpsm.`website_id` = :website_id
                  AND wpsm.`website_shipping_method_id` = :shipping_method_id
                  AND p.`user_id_created` = :user_id_created
                  AND p.`sku` NOT IN ('". implode("','", $skus) ."')"
            , 'iii'
            , array(
                ':website_id' => $account->website_id
                , ':shipping_method_id' => self::SHIPPING_METHOD_ID
                , ':user_id_created' => self::USER_ID
            )
        )->query();

        $this->prepare("
                INSERT IGNORE INTO `website_product_shipping_method` ( website_id, product_id, website_shipping_method_id )
                SELECT :website_id, p.product_id, :shipping_method_id
                FROM `products` p
                WHERE p.`user_id_created` = :user_id_created
                  AND p.`sku` IN ('". implode("','", $skus) ."')"
            , 'iii'
            , array(
                ':website_id' => $account->website_id
                , ':shipping_method_id' => self::SHIPPING_METHOD_ID
                , ':user_id_created' => self::USER_ID
            )
        )->query();

    }

}
