<?php
class AccountProductGroup extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_product_group_id, $website_id, $name;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_product_groups' );

        // We want to make sure they match
        if ( isset( $this->website_product_group_id ) )
            $this->id = $this->website_product_group_id;
    }

    /**
     * Get by name
     *
     * @param int $account_id
     * @param string $name
     */
    public function get_by_name( $account_id, $name ) {
        $this->prepare(
            'SELECT * FROM `website_product_groups` WHERE `website_id` = :account_id AND `name` = :name'
            , 'is'
            , array(
                ':account_id' => $account_id
                , ':name' => $name
            )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_product_group_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_id' => $this->website_id
            , 'name' => strip_tags($this->name)
        ), 'is' );

        $this->id = $this->website_product_group_id = $this->get_insert_id();
    }

    /**
     * Add relational items by series
     *
     * @param string $series
     */
    public function add_relations_by_series( $series ) {
        // Insert the values
        $this->prepare(
            "INSERT INTO `website_product_group_relations` ( `website_product_group_id`, `product_id` ) SELECT :website_product_group_id, wp.`product_id` FROM `website_products` AS wp LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_blocked_category` AS wbc ON ( wbc.`website_id` = wp.`website_id` AND wbc.`category_id` = p.`category_id` ) WHERE wp.`website_id` = :account_id AND wp.`active` = 1 AND wp.`blocked` = 0 AND p.`sku` LIKE :series AND wbc.`category_id` IS NULL GROUP BY wp.`product_id`"
            , 'iis'
            , array(
                ':website_product_group_id' => $this->id
                , ':account_id' => $this->website_id
                , ':series' => $series . '%'
            )
        )->query();
    }

    /**
     * Add Images
     *
     * @param array $product_ids
     */
    public function add_relations( array $product_ids ) {
        $values = '';

        foreach ( $product_ids as $pid ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= '( ' . $this->id . ', ' . (int) $pid . ')';
        }

        // Insert the values
        $this->query( "INSERT INTO `website_product_group_relations` ( `website_product_group_id`, `product_id` ) VALUES $values" );
    }

    /**
     * Delete
     */
    public function remove() {
        parent::delete( array( 'website_product_group_id' => $this->id ), 'i' );
    }
}