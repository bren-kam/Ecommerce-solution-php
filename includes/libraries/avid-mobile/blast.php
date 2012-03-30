<?php
/**
 * Avid Mobile (Mobile Marketing) - Blast API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
library('avid-mobile-api');

class AM_Blast extends Avid_Mobile_API {
    /**
	 * Construct class will initiate and run everything
	 *
	 * @param int $customer_id
	 * @param string $username
	 * @param string $password
	 */
	public function __construct( $customer_id, $username, $password ) {
        if ( !parent::__construct( $customer_id, $username, $password ) )
            return false;
	}

    /**
	 * Create Blast
	 *
     * @param string $name
     * @param string $blast_text
     * @param string $blast_type [optional]
	 * @return int
	 */
	public function create( $name, $blast_text, $blast_type = 'text' ) {
        // Return the Blast ID
		return $this->_execute( self::OPERATION_PUT, 'blast.create', compact( 'name', 'blast_text', 'blast_type' ) );
	}
	
	/**
	 * Update Blast
	 *
	 * @param int $id
     * @param string $name
     * @param string $blast_text
     * @param string $blast_type [optional]
	 * @return bool
	 */
	public function update( $id, $name, $blast_text, $blast_type = 'text' ) {
        // Return the Blast ID
		return $this->_execute( self::OPERATION_PUT, 'blast.update', compact( 'id', 'name', 'blast_text', 'blast_type' ) );
	}
	
	/**
	 * Delete Blast
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete( $id ) {
        // Return the Blast ID
		return $this->_execute( self::OPERATION_PUT, 'blast.delete', compact( 'id' ) );
	}

    /**
	 * Link Dynamic Group to Keyword Campaign
	 *
     * @param string $id the Blast ID
     * @param string $month
     * @param string $day_of_month
     * @param string $day_of_week
     * @param string $year
     * @param string $hour
     * @param string $minute
     * @param string $timezone
	 * @return bool
	 */
	public function schedule( $id, $month, $day_of_month, $day_of_week, $year, $hour, $minute, $timezone ) {
        // Schedule blast
		return $this->_execute( self::OPERATION_PUT, 'blast.schedule', compact( 'id', 'month', 'day_of_month', 'day_of_week', 'year', 'hour', 'minute', 'timezone' ) );
	}

    /**
	 * Add Group to Blast
	 *
     * @param int $id The Blast ID
     * @param int $group_id
	 * @return int
	 */
	public function add_group( $id, $group_id ) {
        // Return the Group ID
		return $this->_execute( self::OPERATION_PUT, 'blast.addgroup', compact( 'id', 'group_id' ) );
	}
}