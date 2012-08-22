<?php
class Brand extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $brand_id, $name, $slug, $link, $image;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'brands' );

        if ( isset( $this->brand_id ) )
            $this->id = $this->brand_id;
    }

    /**
     * Get Brand
     *
     * @param int $brand_id
     */
    public function get( $brand_id ) {
        $this->prepare(
            'SELECT * FROM `brands` WHERE `brand_id` = :brand_id'
            , 's'
            , array( ':brand_id' => $brand_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->brand_id;
    }

    /**
     * Delete Brand
     */
    public function delete() {
        if ( isset( $this->id ) )
            parent::delete( array( 'brand_id' => $this->id ), 'i' );
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

		return $this->prepare( "SELECT `brand_id`, `name`, `link` FROM `brands` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Brand' );
	}

	/**
	 * Count all the checklists
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        return $this->prepare( "SELECT COUNT( `brand_id` ) FROM `brands` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
