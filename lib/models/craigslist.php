<?php
class Craigslist extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
     * Update Stats
     */
    public function update_stats() {
        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        // Get data
        $yesterday = new DateTime();
        $yesterday->sub( new DateInterval('P1D') );

        // Add stats
        $this->add_stats( $craigslist->get_stats( $yesterday->format('Y-m-d') ), $this->get_customers(), $this->get_all_market_links() );
    }

    /**
     * Update Tags
     *
     * @return bool
     */
    public function update_tags() {
        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        // Get data
        $craigslist_tags = $craigslist->get_tags( $this->get_unlinked_tags() );

        // Make sure we have a reason to go on
        if ( empty( $craigslist_tags ) )
            return;

        // Set the tags
        $tags = array();

        // Form the correct array of tags
        if ( is_array( $craigslist_tags ) )
        foreach ( $craigslist_tags as $ct ) {
            if ( 'item' != $ct->type )
                continue;

            $tags[] = $ct;
        }

        // Make sure we have a reason to go on
        if ( empty( $tags ) )
            return;

        // Get tags that need to be updated
        $this->add_tags( $tags );
    }

    /**
     * Add Tags
     *
     * @param array $tags
     */
    public function add_tags( array $tags ) {
        // Declare variables
        $values = $product_skus = $craigslist_tags = array();

        foreach ( $tags as $object_id => $tag ) {
            if ( 'item' == $tag->type ) {
                $product_skus[] = $tag->name;
                $craigslist_tags[$tag->id] = $tag->name;
            } else {
                $values[] = '( ' . (int) $tag->id . ", " . (int) $object_id . ", 'category' )";
            }
        }

        // Add categories
        if ( !empty( $values ) )
            $this->add_bulk_tags( $values, 'category' );

        // Now Product SKUs
        if ( !is_array( $product_skus ) )
            return;

        // Get the Product IDs
        $product_ids = $this->get_product_ids_by_sku( $product_skus );

        // Make sure we can go on
        if ( !is_array( $product_ids ) )
            return;

        // Declare variables
        $values = array();

        foreach ( $craigslist_tags as $craigslist_tag_id => $sku ) {
            $values[] = '( ' . (int) $craigslist_tag_id . ", " . (int) $product_ids[$sku] . ", 'product' )";
        }

        // Add the products
        $this->add_bulk_tags( $values, 'product' );
    }

    /***** PROTECTED FUNCTIONS *****/

    /**
     * Add Stats
     *
     * @param array $stats
     * @param array $customers
     * @param array $market_links
     */
    protected function add_stats( array $stats, array $customers, array $market_links ) {
        $values = $tag_ids = $dates = array();

        foreach ( $stats as $stat ) {
            $account_id = (int) $customers[$stat->customer_id];
            $craigslist_market_id = (int) $market_links[$stat->market_id];
            $dates[] = $stat->date;

            // Add Marketing
            $values[] = "( $account_id, $craigslist_market_id, 0, " . (int) $stat->overall->unique . ', ' . (int) $stat->overall->views . ', ' . $stat->overall->posts . ", ? )";

            if ( is_array( $stat->tags ) )
            foreach ( $stat->tags as $tag ) {
                $dates[] = $stat->date;
                $tag_ids[] = $tag->tag_id;

                // Add Marketing
                $values[] = "( $account_id, $craigslist_market_id, " . (int) $tag->tag_id . ", " . (int) $tag->unique . ', ' . (int) $tag->views . ', ' . $t->posts . ", ? )";
            }
        }

        // Add at up to 500 at a time
        $value_chunks = array_chunk( $values, 500 );
        $date_chunks = array_chunk( $dates, 500 );

        foreach ( $value_chunks as $index => $vc ) {
            $date_chunk = $date_chunks[$index];

            $this->prepare(
                "INSERT INTO `analytics_craigslist` VALUES " . implode( ',', $vc )
                , str_repeat( 's', count( $date_chunk ) )
                , $date_chunk
            )->query();
        }
    }

    /**
     * Get All Craigslist Market Links
     *
     * @return array
     */
    protected function get_all_market_links() {
        $market_links = $this->db->get_results( "SELECT cml.`craigslist_market_id`, cml.`market_id` FROM `craigslist_market_links` AS cml LEFT JOIN `craigslist_markets` AS cm ON ( cm.`craigslist_market_id` = cml.`craigslist_market_id` ) WHERE cm.`status` = 1", PDO::FETCH_ASSOC );

        return ( $market_links ) ? ar::assign_key( $market_links, 'market_id', true ) : array();
    }

    /**
     * Get Customers
     *
     * @return array
     */
    protected function get_customers() {
        return ar::assign_key( $this->db->get_results( "SELECT `website_id`, `value` FROM `website_settings` WHERE `key` = 'craigslist-customer-id'", PDO::FETCH_ASSOC ), 'value', true );
    }

    /**
     * Get Unlinked Tags
     *
     * @return array
     */
    protected function get_unlinked_tags() {
        return $this->db->get_col( "SELECT ac.`craigslist_tag_id` FROM `analytics_craigslist` AS ac LEFT JOIN `craigslist_tags` AS ct ON ( ct.`craigslist_tag_id` = ac.`craigslist_tag_id` ) WHERE ac.`date` > DATE_SUB( ac.`date`, INTERVAL 30 DAY ) AND ct.`craigslist_tag_id` IS NULL" );
    }

    /**
     * Add Bulk Tags
     *
     * @param array $tags
     * @param string $type
     */
    protected function add_bulk_tags( array $tags, $type ) {
        if ( empty( $tags ) )
            return;

        // Determine what should be updated on duplicate key
        $update = ( 'product' == $type ) ? '`object_id` = VALUES(`object_id`),' : '';

        // Add at up to 500 at a time
        $tag_chunks = array_chunk( $tags, 500 );

        foreach ( $tag_chunks as $chunk ) {
            $this->query( "INSERT INTO `craigslist_tags` ( `craigslist_tag_id`, `object_id`, `type` ) VALUES " . implode( ',', $chunk ) . " ON DUPLICATE KEY UPDATE {$update} `type` = VALUES(`type`)" );
        }
    }

    /**
     * Get Product Ids by SKU
     *
     * @param array $skus
     * @return array
     */
    public function get_product_ids_by_sku( array $skus ) {
        $sku_count = count( $skus );
        $sku_string = '?' . str_repeat( ',?', $sku_count - 1 );

        return ar::assign_key( $this->prepare(
            "SELECT `product_id`, `sku` FROM `products` WHERE `publish_visibility` = 'public' AND `sku` IN ( $sku_string ) GROUP BY `product_id`"
            , $sku_count
            , $skus
        )->get_results( PDO::FETCH_ASSOC ), 'sku', true );
    }
}
