<?php
/**
 * Avid Mobile (Mobile Marketing) - Members API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
library('avid-mobile-api');

class AM_Members extends Avid_Mobile_API {
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
	 * Create Member
	 *
     * @param string $phone_num
     * @param string $first_name optional
     * @param string $last_name optional
     * @param string $email optional
     * @param string $im optional - instant messenger
     * @param string $im2 optional - instant messenger 2
     * @param string $address optional
     * @param string $address2 optional
     * @param string $city optional
     * @param string $state optional
     * @param string $zip optional
	 * @return int
	 */
	public function create( $phone_num, $first_name = '', $last_name = '', $email = '', $im = '', $im2 = '', $address = '', $address2 = '', $city = '', $state = '', $zip = '' ) {
        // Return the Member ID
		return $this->_execute( self::OPERATION_PUT, 'member.create', compact( 'phone_num', 'first_name', 'last_name', 'email', 'im', 'im2', 'address', 'address2', 'city', 'state', 'zip' ) );
	}

    /**
	 * Update Member
	 *
	 * @param int $id
     * @param string $phone_num
     * @param string $first_name optional
     * @param string $last_name optional
     * @param string $email optional
     * @param string $im optional - instant messenger
     * @param string $im2 optional - instant messenger 2
     * @param string $address optional
     * @param string $address2 optional
     * @param string $city optional
     * @param string $state optional
     * @param string $zip optional
	 * @return int
	 */
	public function update( $id, $phone_num, $first_name = '', $last_name = '', $email = '', $im = '', $im2 = '', $address = '', $address2 = '', $city = '', $state = '', $zip = '' ) {
        // Return the Member ID
		return $this->_execute( self::OPERATION_PUT, 'member.update', compact( 'id', 'phone_num', 'first_name', 'last_name', 'email', 'im', 'im2', 'address', 'address2', 'city', 'state', 'zip' ) );
	}
	
	/**
	 * Delete Member
	 *
	 * @param int $id
	 */
	public function delete( $id ) {
        // Delete member
		$this->_execute( self::OPERATION_PUT, 'member.delete', compact( 'id' ) );
		
		// Return the success
		return $this->success();
	}
	
    /**
	 * Add Members To Group
	 *
     * @param array $member_ids
     * @param int $group_id
	 * @return bool
	 */
	public function add_members_to_group( array $member_ids, $group_id ) {
		// Must contain a valid type
        if ( !is_array( $member_ids ) )
            return false;

        // Define CSV member_ids
        $id = implode( ',', $member_ids );

        // Add the group
		$this->_execute( self::OPERATION_PUT, 'member.addtogroup', compact( 'id', 'group_id' ) );

        // Return their success
        return $this->success();
	}
}