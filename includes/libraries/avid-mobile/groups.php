<?php
/**
 * Avid Mobile (Mobile Marketing) - Groups API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
class AM_Groups extends Avid_Mobile_API {
    /**
     * Group Constants
     */
    const GROUP_STATIC = 'STATIC';
    const GROUP_DYNAMIC = 'DYNAMIC';

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
	 * Create Group
	 *
     * @param string $name
     * @param string $type optional ('STATIC' or 'DYNAMIC')
	 * @return int
	 */
	public function create_group( $name, $type = self::GROUP_STATIC ) {
		// Must contain a valid type
        if ( !in_array( $type, array( self::GROUP_STATIC, self::GROUP_DYNAMIC ) ) )
            return false;

        // Return the Group ID
		return $this->_execute( self::OPERATION_PUT, 'group.create', compact( 'name', 'type' ) );
	}

    /**
	 * Get Groups
	 *
	 * @return array ( array( 'id' => int, 'name' => string, 'type' => enum['STATIC', 'DYNAMIC'] ) )
	 */
	public function get_groups() {
        // Return the Groups
		$groups_object = $this->_execute( self::OPERATION_GET, 'group.list', array( 'id' => 'select', 'name' => 'select', 'type' => 'select' ) );

        // Make sure we're dealing with the right object
        if ( !is_object( $groups_object ) )
            return false;

        // Declare groups array
        $groups = array();

        foreach ( $groups_object as $go ) {
            $group = array();

            if ( is_object( $go ) )
            foreach ( $go as $group_value ) {
                $group[$group_value->Key] = $group_value->Value;
            }

            $groups[] = $group;
        }

        return $groups;
	}
}