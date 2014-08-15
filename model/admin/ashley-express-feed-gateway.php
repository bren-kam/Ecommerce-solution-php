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

        require_once MODEL_PATH . '../account/website-order.php';
        require_once MODEL_PATH . '../account/website-shipping-method.php';
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
     * Get XML
     *
     * @param Account $account
     * @param string $prefix
     * @param bool $archive
     * @return SimpleXMLElement
     */
    private function get_xml( $account, $prefix = null, $archive = false ) {
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
                if ( $prefix && strpos( $file_name, $prefix ) === false )
                    continue;

                $file = $f['name'];
            }
        }

        // Can't do anything without a file
        if ( empty( $file ) )
            return null;

        // Make sure the folder has been created
        $local_folder = sys_get_temp_dir() . '/';

        // Grab the latest file
        if( !file_exists( $local_folder . $file ) )
            $ftp->get( $file, '', $local_folder );

        $this->xml = simplexml_load_file( $local_folder . $file );

        // Now remove the file
        unlink( $local_folder . $file );

        if ( $archive ) {
            $dir_parts = explode( '/', trim( $ftp->cwd, '/' ) );
            array_pop( $dir_parts );
            $dir_parts[] = 'Archive';
            $archive_folder = '/' . implode( '/', $dir_parts ) . '/';

            @$ftp->mkdir( $archive_folder );
            $ftp->rename( $file, $archive_folder . $file );
        }

        return $this->xml;

    }

	/**
     *  Run Flag Products (all accounts)
     */
    public function run_flag_products_all() {
        // Get Feed Accounts
        $accounts = $this->get_feed_accounts();

        if ( is_array( $accounts ) )
        foreach( $accounts as $account ) {
            $this->run_flag_products( $account );
        }
    }

	/**
	 * Run Flag Products
     * This will flag all Ashley Express products so they can enter the Ashley Express program.
	 *
	 * @param Account $account
	 * @return bool
	 */
	public function run_flag_products( Account $account ) {

        $this->get_xml( $account, '846-' );

        // Declare array
        $ashley_express_skus = array();

        // Set Settings: Ashley Express Buyer ID from XML
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

        $this->flag_bulk( $account, $ashley_express_skus );

	}

    /**
     * Flag a Bulk of Products as Ashley Express
     * Removes Flag for products that are no in $skus
     *
     * @param Account $account
     * @param string[] $skus array of skus
     */
    private function flag_bulk( $account, $skus ) {

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
                , ':shipping_method_id' => WebsiteOrder::get_ashley_express_shipping_method()->id
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
                , ':shipping_method_id' => WebsiteOrder::get_ashley_express_shipping_method()->id
                , ':user_id_created' => self::USER_ID
            )
        )->query();

    }

    /**
     *  Run Order Acknowledgement (all accounts)
     */
    public function run_order_acknowledgement_all() {
        // Get Feed Accounts
        $accounts = $this->get_feed_accounts();

        if ( is_array( $accounts ) )
            foreach( $accounts as $account ) {
                $this->run_order_acknowledgement( $account );
            }
    }

    /**
     * Run Order Acknowledgement
     * This will check for Orders response after they are created
     *
     * @param Account $account
     */
    public function run_order_acknowledgement( Account $account ) {

        echo "Working with Account {$account->id}\n";

        while( $this->get_xml( $account, '855-', true ) !== null ) {

            $order_id = (string)$this->xml->ackOrder->orderDocument['id'];
            echo "Order $order_id \n";

            $order = new WebsiteOrder();
            $order->get( $order_id, $account->id );

            echo "Order: ". json_encode($order) ." \n";

            if ( !$order->id )
                continue;

            if ( $order->website_shipping_method_id != WebsiteOrder::get_ashley_express_shipping_method()->id )
                continue;

            if ( $order->status != WebsiteOrder::STATUS_PURCHASED )
                continue;

            echo "Order Updated\n";

            $order->status = WebsiteOrder::STATUS_RECEIVED;
            $order->save();
        }

        echo "Finished with Account\n----\n";

    }

    /**
     * Run Order ASN (Advanced Ship Notice) (all accounts)
     */
    public function run_order_asn_all() {
        // Get Feed Accounts
        $accounts = $this->get_feed_accounts();

        if ( is_array( $accounts ) )
            foreach( $accounts as $account ) {
                $this->run_order_asn( $account );
            }
    }


    /**
     * Run Order ASN (Advanced Ship Notice)
     * This will check for Orders response after they are marked at Received by run_order_acknowledgement()
     *
     * @param Account $account
     */
    public function run_order_asn( Account $account ) {

        echo "Working with Account {$account->id}\n";

        while( $this->get_xml( $account, '856-', true ) !== null ) {

            $order_id = (string)$this->xml->shipment->order->orderReferenceNumber['referenceNumberValue'];
            echo "Order $order_id \n";

            $order = new WebsiteOrder();
            $order->get( $order_id, $account->id );

            echo "Order: ". json_encode($order) ." \n";

            if ( !$order->id )
                continue;

            if ( $order->website_shipping_method_id != WebsiteOrder::get_ashley_express_shipping_method()->id )
                continue;

            if ( $order->status != WebsiteOrder::STATUS_RECEIVED )
                continue;

            echo "Order Updated\n";

            $shipping_track_numbers = array();
            try {
                foreach ( $this->xml->shipment->order->item as $item ) {
                    foreach ( $item->itemQuantity->unitsShipped->pieceIdentification->pieceIdentificationNumber as $identification ) {
                        $shipping_track_numbers[] = (string)$identification;
                    }
                }
            } catch ( Exception $e ) { }

            $order->shipping_track_number = implode( ',', $shipping_track_numbers );
            $order->status = WebsiteOrder::STATUS_SHIPPED;
            $order->save();

        }

        echo "Finished with Account\n----\n";

    }


}
