<?php
class Industry extends ActiveRecordBase {
    const FURNITURE = 1;

    // The columns we will have access to
    public $id, $industry_id, $name;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'industries' );

        // We want to make sure they match
        if ( isset( $this->industry_id ) )
            $this->id = $this->industry_id;
    }

    /**
     * Get Industry
     *
     * @param int $industry_id
     */
    public function get( $industry_id ) {
		$this->prepare(
            'SELECT `industry_id`, `name` FROM `industries` WHERE `industry_id` = :industry_id'
            , 'i'
            , array( ':industry_id' => $industry_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->industry_id;
    }

    /**
	 * Get all industries
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->get_results( 'SELECT `industry_id`, `name` FROM `industries` ORDER BY `name` ASC', PDO::FETCH_CLASS, 'Industry' );
	}
}
