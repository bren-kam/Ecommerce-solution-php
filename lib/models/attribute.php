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
     * Get All
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT * FROM `attributes` ORDER BY `title`', PDO::FETCH_CLASS, 'Attribute' );
    }

    /**
     * Delete Attribute
     */
    public function delete() {
        if ( isset( $this->id ) )
            parent::delete( array( 'attribute_id' => $this->id ), 'i' );
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
