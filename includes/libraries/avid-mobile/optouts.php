<?php
/**
 * Avid Mobile (Mobile Marketing) - Optouts API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
class AM_Optouts extends Avid_Mobile_API {
     /**
	 * Construct class will initiate and run everything
	 *
	 * @param int $customer_id
	 */
	public function __construct( $customer_id = NULL ) {
        if ( !is_null( $customer_id ) && !parent::__construct( $customer_id ) )
            return false;
	}

    /**
	 * List Optouts
	 *
	 * @return array of phone numbers
	 */
	public function list_optouts() {
        // Return the Optouts
		$optouts_object = $this->_execute( self::OPERATION_GET, 'optout.list', array( 'optout_list.mobile' => 'select' ) );

        // Make sure we're dealing with the right object
        if ( !is_object( $optouts_object ) )
            return false;

        // Declare optouts array
        $optouts = array();

        foreach ( $optouts_object as $oo ) {
            $optouts[] = $oo->item->Value;
        }

        return $optouts;
	}
}