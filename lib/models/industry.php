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
		return $this->get_results( 'SELECT `industry_id`, `name` FROM `industries` ORDER BY `name` ASC', PDO::FETCH_CLASS, 'Industry' );
	}

    /**
	 * Get all industries by an account
	 *
     * @param int $account_id
	 * @return array
	 */
	public function get_by_account( $account_id ) {
		return $this->prepare( 'SELECT `industry_id` FROM `industries` ORDER BY `name` ASC', PDO::FETCH_CLASS, 'Industry' );
	}
}
