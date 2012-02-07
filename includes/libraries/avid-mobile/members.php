<?php
/**
 * Avid Mobile (Mobile Marketing) - Members API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
class AM_Members extends Avid_Mobile_API {
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
	public function create_member( $phone_num, $first_name = '', $last_name = '', $email = '', $im = '', $im2 = '', $address = '', $address2 = '', $city = '', $state = '', $zip = '') {
        // Return the Member ID
		return $this->_execute( self::OPERATION_PUT, 'member.create', compact( 'phone_num', 'first_name', 'last_name', 'email', 'im', 'im2', 'address', 'address2', 'city', 'state', 'zip' ) );
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