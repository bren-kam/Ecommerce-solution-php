<?php
/**
 * Handles all the companies
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Companies extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

    /**
     * Get a company by ID
     *
     * @param int $company_id
     * @return array
     */
    public function get( $company_id ) {
        // Type Juggling
        $company_id= (int) $company_id;

        $company = $this->db->get_row( "SELECT `name`, `domain` FROM `companies` WHERE `company_id` = $company_id", ARRAY_A );

		// Handle errors
		if ( mysql_errno() ) {
			$this->err( 'Failed to get company', __LINE__, __METHOD__ );
			return false;
		}

        return $company;
    }
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}