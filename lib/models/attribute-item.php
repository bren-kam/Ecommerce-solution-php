<?php
class AttributeItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $attribute_item_id, $attribute_id, $name, $sequence;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'attribute_items' );

        if ( isset( $this->attribute_item_id ) )
            $this->id = $this->attribute_item_id;
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
     * Get all attribute items for an attribute
     *
     * @param int $attribute_id
     * @return array
     */
    public function get_all( $attribute_id ) {
        return $this->prepare(
            'SELECT `attribute_item_id`, `name` FROM `attribute_items` WHERE `attribute_id` = :attribute_id ORDER BY `sequence` ASC'
            , 's'
            , array( ':attribute_id' => $attribute_id )
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
}
