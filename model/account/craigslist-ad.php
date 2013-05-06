<?php
class CraigslistAd extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $craigslist_ad_id, $website_id, $product_id, $text, $price, $error, $active, $date_posted
        , $date_created, $date_updated;

    // Artificial fields
    public $headlines, $craigslist_markets;

    // Fields from other tables
    public $headline, $product_name, $sku;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'craigslist_ads' );

        // We want to make sure they match
        if ( isset( $this->craigslist_ad_id ) )
            $this->id = $this->craigslist_ad_id;
    }

    /**
     * Get
     *
     * @param int $craigslist_ad_id
     * @param int $account_id
     */
    public function get( $craigslist_ad_id, $account_id ) {
        $sql = "SELECT ca.`craigslist_ad_id`, ca.`website_id`, ca.`product_id`, ca.`text`, ca.`price`, ca.`active`, GROUP_CONCAT( cah.`headline` SEPARATOR '`' ) AS headlines, w.`title` AS store_name, p.`name` AS product_name,";
        $sql .= " p.`sku`, ca.`date_created`, ca.`date_posted`";
        $sql .= " FROM `craigslist_ads` AS ca";
        $sql .= " LEFT JOIN `craigslist_ad_headlines` AS cah ON ( cah.`craigslist_ad_id` = ca.`craigslist_ad_id` )";
        $sql .= " LEFT JOIN `websites` AS w ON ( w.`website_id` = ca.`website_id` )";
        $sql .= " LEFT JOIN `products` AS p ON ( p.`product_id` = ca.`product_id` )";
        $sql .= " WHERE ca.`craigslist_ad_id` = :craigslist_ad_id AND ca.`website_id` = :account_id GROUP BY ca.`craigslist_ad_id`";

        $this->prepare(
            $sql
            , 'ii'
            , array( ':craigslist_ad_id' => $craigslist_ad_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->craigslist_ad_id;
        $this->headlines = explode( '`', $this->headlines );
    }

    /**
     * Get Market Ids
     *
     * @return array
     */
    protected function get_markets() {
        return $this->prepare(
            'SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = :craigslist_ad_id'
            , 'i'
            , array( ':craigslist_ad_id' => $this->id )
        )->get_col();
    }

    /**
     * Get Complete
     *
     * @param int $craigslist_ad_id
     * @param int $account_id
     */
    public function get_complete( $craigslist_ad_id, $account_id ) {
        $this->get( $craigslist_ad_id, $account_id );
        $this->craigslist_markets = $this->get_markets();
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
            , 'text' => $this->text
            , 'price' => $this->price
            , 'date_created' => $this->date_created
        ), 'iisis' );

        $this->id = $this->craigslist_ad_id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        $this->update( array(
            'product_id' => $this->product_id
            , 'text' => $this->text
            , 'price' => $this->price
            , 'date_posted' => $this->date_posted
            , 'active' => $this->active
        ), array(
            'craigslist_ad_id' => $this->craigslist_ad_id )
        , 'isisi', 'i' );
    }

    /**
     * Add Headlines
     *
     * @param array $headlines
     */
    public function add_headlines( array $headlines ) {
        // Insert headlines
        $values = array();

        foreach ( $headlines as $h ) {
            // We don't want blank values
            if ( empty( $h ) )
                continue;

            $values[] = $h;
        }

        $value_count = count( $values );

        // Make sure we have something to do
        if ( 0 == $value_count )
            return;

        $value_string = substr( str_repeat( ',( ' . (int) $this->id . ', ? )', $value_count ), 1 );

        // Add them!
        $this->prepare(
            "INSERT INTO `craigslist_ad_headlines` VALUES $value_string"
            , str_repeat( 's', $value_count )
            , $values
        )->query();
    }

    /**
     * Delete Headlines
     */
    public function delete_headlines() {
        $this->prepare(
            'DELETE FROM `craigslist_ad_headlines` WHERE `craigslist_ad_id` = :craigslist_ad_id'
            , 'i'
            , array( ':craigslist_ad_id' => $this->id )
        )->query();
    }

    /**
     * Delete Market IDs
     *
     * @param array $craigslist_market_ids
     */
    protected function add_markets( $craigslist_market_ids ) {
        // Type Juggling
        $craigslist_ad_id = (int) $this->id;

        $value_string = '';

        foreach ( $craigslist_market_ids as &$cmid ) {
            if ( !empty( $value_string ) )
                $value_string .= ',';

            $value_string .= "( $craigslist_ad_id, " . (int) $cmid . ' )';
        }

        $this->query( "INSERT INTO `craigslist_ad_markets` ( `craigslist_ad_id`, `craigslist_market_id` ) VALUES $value_string ON DUPLICATE KEY UPDATE `craigslist_ad_id` = $craigslist_ad_id" );
    }

    /**
     * Delete Market IDs
     *
     * @param array $craigslist_market_id_exceptions
     */
    protected function delete_markets( array $craigslist_market_id_exceptions ) {
        foreach ( $craigslist_market_id_exceptions as &$cmid ) {
            $cmid = (int) $cmid;
        }

        $this->prepare(
            'DELETE FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = :craigslist_ad_id AND `craigslist_market_id` NOT IN (' . implode( ',', $craigslist_market_id_exceptions ) . ') '
            , 'i'
            , array( ':craigslist_ad_id' => $this->id )
        )->query();
    }

    /**
     * Add Craigslist Markets
     *
     * @param array $craigslist_market_ids
     */
    public function set_markets( array $craigslist_market_ids ) {
        // Insert headlines
        $add_craiglist_market_ids = array();

        // Find out which ones we need to add
        foreach ( $craigslist_market_ids as $cmid ) {
            // We only want to add the ones that we don't have
            if ( is_array( $this->craigslist_markets ) && in_array( $cmid, $this->craigslist_markets ) )
                continue;

            $add_craiglist_market_ids[] = $cmid;
        }

        // Delete the market ids
        $this->delete_markets( $craigslist_market_ids );

        // If there are no values to add, we're done
        if ( !empty( $add_craiglist_market_ids ) )
            $this->add_markets( $add_craiglist_market_ids );
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return CraigslistAd[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT ca.`craigslist_ad_id`, ca.`text`, cah.`headline`, p.`name` AS `product_name`, p.`sku`, ca.`date_created`, ca.`date_posted` FROM `craigslist_ads` AS ca LEFT JOIN `craigslist_ad_headlines` AS cah ON ( cah.`craigslist_ad_id` = ca.`craigslist_ad_id` ) LEFT JOIN `products` AS p ON( p.`product_id` = ca.`product_id` ) WHERE ca.`active` = 1 $where GROUP BY ca.`craigslist_ad_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'CraigslistAd' );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( DISTINCT ca.`craigslist_ad_id` ) FROM `craigslist_ads` AS ca LEFT JOIN `craigslist_ad_headlines` AS cah ON ( cah.`craigslist_ad_id` = ca.`craigslist_ad_id` ) LEFT JOIN `products` AS p ON( p.`product_id` = ca.`product_id` ) WHERE ca.`active` = 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /**
     * Get Primus Product IDs
     *
     * @return array
     */
    protected function get_primus_product_ids() {
        return $this->prepare(
            'SELECT `primus_product_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = :craigslist_ad_id'
            , 'i'
            , array( ':craigslist_ad_id' => $this->id )
        )->get_col();
    }

    /**
     * Remove Primus Product IDs
     */
    protected function remove_primus_product_ids() {
        $this->prepare(
            'UPDATE `craigslist_ad_markets` SET `primus_product_id` = 0 WHERE `craigslist_ad_id` = :craigslist_ad_id'
            , 'i'
            , array( ':craigslist_ad_id' => $this->id )
        )->query();
    }

    /**
     * Delete primus
     *
     * @throws ModelException
     *
     * @param Craigslist_API $craigslist
     */
    public function delete_from_primus( Craigslist_API $craigslist ) {
        // Delete old ads and upate the status so that
        $old_primus_product_ids = $this->get_primus_product_ids();

        if ( is_array( $old_primus_product_ids ) )
        foreach ( $old_primus_product_ids as $key => $oppid ) {
            if ( empty( $oppid ) || '0' == $oppid )
                unset( $old_primus_product_ids[$key] );
        }

        // See if we have anything to do
        if ( empty( $old_primus_product_ids ) )
            return;

        // Make sure we successfully remove the old IDs
        if ( !$craigslist->delete_ad_product( $old_primus_product_ids ) )
            throw new ModelException( _('Failed to delete Primus Ad') );

        // Now update the database
        $this->date_posted = '0000-00-00 00:00:00';
        $this->save();

        // Now remove the old primus product_ids
        $this->remove_primus_product_ids();
    }

    /**
     * Set Craigslist Ad Markets
     *
     * @param array $primus_product_ids
     */
    protected function set_craigslist_ad_markets( array $primus_product_ids ) {
        // Update primus product links
        $statement = $this->prepare_raw( "UPDATE `craigslist_ad_markets` SET `primus_product_id` = :primus_product_id WHERE `craigslist_ad_id` = :craigslist_ad_id AND `craigslist_market_id` = :craigslist_market_id" );
        $statement->bind_param( ':primus_product_id', $primus_product_id, 'i' )
            ->bind_value( ':craigslist_ad_id', $this->id, 'i' )
            ->bind_param( ':craigslist_market_id', $craigslist_market_id, 'i' );

        foreach ( $primus_product_ids as $craigslist_market_id => $primus_product_id ) {
            $statement->query();
        }
    }

    /**
     * Post Craigslist Ad
     *
     * @throws ModelException
     */
    public function post() {
        $market = new CraigslistMarket();

        // Get craigslist markets
        $markets = $market->get_by_ad( $this->id, $this->website_id );

        // If we don't have markets, then we can't post
        if ( empty( $markets ) )
            throw new ModelException( _('There are no markets selected') );

        // Make sure the headlines aren't empty
        foreach ( $this->headlines as $hl ) {
            if ( empty( $hl ) )
                throw new ModelException( _('Cannot have empty headlines') );
        }

        $product = new Product();
        $category = $parent_category = new Category();

        // Get the product
        $product->get( $this->product_id );
        $category->get( $product->category_id );
        $parent_category->get_top( $category->parent_category_id );

        // Make sure we have the product
        if ( !$product )
            throw new ModelException( _('Unable to get product') );

        $craigslist_tag = new CraigslistTag;
        $craigslist_tags = $craigslist_tag->get_by_all( $product->id, $category->id, $parent_category->id );

        // Declare variables
        $product_tag_id = $category_tag_id = $parent_category_tag_id = $tags = false;

        /**
         * @var CraigslistTag $ct
         */
        if ( is_array( $craigslist_tags ) )
        foreach ( $craigslist_tags as $ct ) {
            switch ( $ct->type ) {
                case 'category':
                    if ( $ct->object == $category->id ) {
                        $category_tag_id = $ct->craigslist_tag_id;
                    } elseif ( $ct->object == $parent_category->id ) {
                        $parent_category_tag_id = $ct->craigslist_tag_id;
                    }
                break;

                case 'product':
                    $product_tag_id = $ct->craigslist_tag_id;
                break;
            }
        }

        $tags = array();

        // Create product tag
        if ( !$product_tag_id )
        $tags[$this->product_id] = array(
            'type' => 'item'
            , 'name' => $product->sku
        );

        // Create category tag
        if ( !$category_tag_id )
        // Add it to the tags array
        $tags[$category->id] = array(
            'type' => 'category'
            , 'name' => $category->name
        );

        // Create parent category tag
        if ( !$parent_category_tag_id )
        // Add it to the tags array
        $tags[$parent_category->id] = array(
            'type' => 'category'
            , 'name' => $parent_category->name
        );

        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        // If it's an array
        if ( !empty( $tags ) ) {
            // To insert into our database once done
            $tag_response = $craigslist->add_tags( $tags );

            if ( is_array( $tag_response ) || is_object( $tag_response ) )
            foreach ( $tag_response as $object_id => $tr ) {
                $tag = new CraigslistTag();
                $tag->craigslist_tag_id = $tr->id;
                $tag->object_id = $object_id;

                switch ( $tr->type ) {
                    case 'item':
                        // Get the product tag ID
                        $product_tag_id = $tr->id;

                        $tag->type = 'product';
                    break;

                    case 'category':
                        if ( $object_id == $category->id ) {
                            // Get the category tag ID
                            $category_tag_id = $tr->id;
                        } elseif( $object_id == $parent_category->id ) {
                            // Get the parent category tag ID
                            $parent_category_tag_id = $tr->id;
                        }

                        $tag->type = 'category';
                    break;
                }

                // Create the tags
                $tag->create();
            }
        }

        // Set post tags
        $post_tags = array( $product_tag_id, $category_tag_id, $parent_category_tag_id );


        $account_product = new AccountProduct();
        $account_product->get( $product->id, $this->website_id );

        // Get product URL
        if ( $account_product->product_id ) {
            // Make Product URL
            $product_url = $category->get_url( $category->id ) . $product->slug . '/';
        } else {
            // We don't have a product URL -- it's not hosted on our site
            $product_url = '';
        }

        // Get the product image URL
        $images = $product->get_images();
        $product_image_url = 'http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/large/' . current( $images );

        $primus_product_ids = array();

        // Delete add from Primus
        $this->delete_from_primus( $craigslist );

        // Post the ad in each market
        foreach ( $markets as $market ) {
            $response = (object) array( 'status' => 'RETRY' );
            $i = 0;

            while ( 'RETRY' == $response->status && $i < 10 ) {
                $response = $craigslist->add_ad_product( $market->market_id, $post_tags, $product_url, $product_image_url, $this->price, $this->headlines, $this->text );

                if ( 'SUCCESS' == $response->status ) {
                    $primus_product_ids[$market->id] = $response->product_id;
                } elseif ( 'RETRY' != $response->status ) {
                    throw new ModelException( _('Status: ' . $response->status . '. Failed to add craigslist product') );
                }

                $i++;
            }
        }

        // Get the date
        $this->date_posted = dt::now();
        $this->save();

        $this->set_craigslist_ad_markets( $primus_product_ids );
    }
}
