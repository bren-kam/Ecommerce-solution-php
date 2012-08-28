<?php
class Attribute extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $attribute_id, $brand_id, $title, $name;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'attributes' );

        if ( isset( $this->attribute_id ) )
            $this->id = $this->attribute_id;
    }

    /**
     * Get Attribute
     *
     * @param int $attribute_id
     */
    public function get( $attribute_id ) {
        $this->prepare(
            'SELECT * FROM `attributes` WHERE `attribute_id` = :attribute_id'
            , 's'
            , array( ':attribute_id' => $attribute_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->attribute_id;
    }

    /**
     * Create Attribute
     */
    public function create() {
        $this->insert( array(
            'brand_id' => $this->brand_id
            , 'title' => $this->title
            , 'name' => $this->name
        ), 'iss' );

        $this->attribute_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an attribute
     */
    public function update() {
        parent::update( array(
            'brand_id' => $this->brand_id
            , 'title' => $this->title
            , 'name' => $this->name
        ), array(
            'attribute_id' => $this->id
        ), 'iss', 'i' );
    }

    /**
     * Add Category Relations
     *
     * @param int $category_id
     * @param array $attribute_ids
     */
    public function add_category_relations( $category_id, array $attribute_ids ) {
        // Create all the values
        $values = '';

		foreach ( $attribute_ids as $attribute_id ) {
            // Make sure it's something
            if ( empty( $attribute_id ) )
                continue;

			if ( !empty( $values ) )
				$values .= ',';

			$values .= '(' . (int) $attribute_id . ', ' . (int) $category_id . ')';
		}

        // If there isn't anything, return
        if ( empty( $values ) )
            return;

        $this->query( "INSERT INTO `attribute_relations` ( `attribute_id`, `category_id` ) VALUES $values" );
    }

    /**
     * Get All
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT * FROM `attributes` ORDER BY `title`', PDO::FETCH_CLASS, 'Attribute' );
    }

    /**
     * Get Category Attribute IDs
     *
     * @param int $category_id
     * @return array
     */
    public function get_category_attribute_ids( $category_id ) {
        return $this->prepare(
            'SELECT a.`attribute_id` FROM `attributes` AS a LEFT JOIN `attribute_relations` AS b ON ( a.`attribute_id` = b.`attribute_id` ) WHERE b.`category_id` = :category_id ORDER BY a.`title`'
            , 'i'
            , array( ':category_id' => $category_id )
        )->get_col();
    }

    /**
     * Delete Attribute
     */
    public function delete() {
        if ( isset( $this->id ) )
            parent::delete( array( 'attribute_id' => $this->id ), 'i' );
    }

    /**
     * Delete Relations to a category
     *
     * @param int $category_id
     */
    public function delete_category_relations( $category_id ) {
        $this->prepare(
            'DELETE FROM `attribute_relations` WHERE `category_id` = :category_id'
            , 'i'
            , array( ':category_id' => $category_id )
        )->query();
    }

    /**
	 * Get listing information
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

		return $this->prepare( "SELECT `attribute_id`, `title` FROM `attributes` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Attribute' );
	}

	/**
	 * Count the listing items
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `attribute_id` ) FROM `attributes` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
