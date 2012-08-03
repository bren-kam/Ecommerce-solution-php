<?php
class Industry extends ActiveRecordBase {
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
	 * Get all industries
	 *
	 * @return array
	 */
	public function get_all() {
		$industries = $this->get_results( 'SELECT `industry_id`, `name` FROM `industries` ORDER BY `name` ASC', PDO::FETCH_CLASS, 'Industry' );

		return $industries;
	}
}
