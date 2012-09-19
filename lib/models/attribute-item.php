<?php
class AttributeItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $attribute_item_id, $attribute_id, $name, $sequence;

    // Columns from other tables
    public $title;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'attribute_items' );

        if ( isset( $this->attribute_item_id ) )
            $this->id = $this->attribute_item_id;
    }

    /**
     * Add Relations
     *
     * @param int $product_id
     * @param array $attribute_items
     */
    public function add_relations( $product_id, array $attribute_items ) {
        // Don't want to add no attribute_items
        if ( 0 == count( $attribute_items ) )
            return;

        // Declare variable
        $values = '';
        $product_id = (int) $product_id;

        // Create the array for all the values
        foreach ( $attribute_items as $attribute_item_id ) {
            if ( !empty( $values ) )
                $values .= ',';

            $attribute_item_id = (int) $attribute_item_id;

            $values .= "( $attribute_item_id, $product_id )";
        }

        // Insert the values
        $this->query( "INSERT INTO `attribute_item_relations` VALUES $values");
    }

    /**
     * Get an attribute item
     *
     * @param int $attribute_item_id
     */
    public function get( $attribute_item_id ) {
        $this->prepare(
            'SELECT `attribute_item_id`, `attribute_id`, `name`, `sequence` FROM `attribute_items` WHERE `attribute_item_id` = :attribute_item_id'
            , 'i'
            , array( ':attribute_item_id' => $attribute_item_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->attribute_item_id;
    }

    /**
     * Get all attribute items by a category id
     *
     * @param int $category_id
     * @return array
     */
    public function get_by_category( $category_id ) {
        return $this->prepare(
            'SELECT ai.`attribute_item_id`, ai.`name`, a.`title` FROM `attribute_items` AS ai LEFT JOIN `attributes` AS a ON ( ai.`attribute_id` = a.`attribute_id` ) LEFT JOIN `attribute_relations` AS ar ON ( ar.`attribute_id` = a.`attribute_id` ) WHERE ar.`category_id` = :category_id'
            , 'i'
            , array( ':category_id' => $category_id )
        )->get_results( PDO::FETCH_CLASS , 'AttributeItem' );
    }

    /**
     * Get all attribute items for an attribute
     *
     * @param int $attribute_id
     * @return array
     */
    public function get_by_attribute( $attribute_id ) {
        return $this->prepare(
            'SELECT `attribute_item_id`, `name` FROM `attribute_items` WHERE `attribute_id` = :attribute_id ORDER BY `sequence` ASC'
            , 'i'
            , array( ':attribute_id' => $attribute_id )
        )->get_results( PDO::FETCH_CLASS, 'AttributeItem' );
    }

    /**
     * Get all attribute items by a product_id
     *
     * @param int $product_id
     * @return array
     */
    public function get_by_product( $product_id ) {
        return $this->prepare(
            'SELECT ai.`attribute_item_id`, ai.`attribute_id`, ai.`name`, a.`title` FROM `attribute_items` AS ai LEFT JOIN `attribute_item_relations` AS air ON ( ai.`attribute_item_id` = air.`attribute_item_id` ) INNER JOIN `attributes` AS a ON ( ai.`attribute_id` = a.`attribute_id` ) WHERE air.`product_id` = :product_id'
            , 's'
            , array( ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'AttributeItem' );
    }

    /**
     * Create Attribute item
     */
    public function create() {
        $this->insert( array(
            'attribute_id' => $this->attribute_id
            , 'name' => $this->name
            , 'sequence' => $this->sequence
        ), 'isi' );

        $this->attribute_item_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an attribute item
     */
    public function update() {
        parent::update( array(
            'name' => $this->name
            , 'sequence' => $this->sequence
        ), array(
            'attribute_item_id' => $this->id
        ), 'si', 'i' );
    }

    /**
     * Delete attribute item
     */
    public function delete() {
        parent::delete( array( 'attribute_item_id' => $this->id ), 'i' );
    }

    /**
     * Delete relations by product
     *
     * @param int $product_id
     */
    public function delete_relations( $product_id ) {
        $this->prepare(
            'DELETE FROM `attribute_item_relations` WHERE `product_id` = :product_id'
            , 'i'
            , array( ':product_id' => $product_id )
        )->query();
    }
}
