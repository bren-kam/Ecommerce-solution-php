<?php
/**
 * Avid Mobile (Mobile Marketing) - Groups API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
library('avid-mobile-api');

class AM_Groups extends Avid_Mobile_API {
    /**
     * Group Constants
     */
    const GROUP_STATIC = 'STATIC';
    const GROUP_DYNAMIC = 'DYNAMIC_KEYWORD';

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
	 * Create Group
	 *
     * @param string $name
     * @param string $type [optional] ('STATIC' or 'DYNAMIC_KEYWORD')
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
	 * Link Dynamic Group to Keyword Campaign
	 *
     * @param string $group_id
     * @param string $keyword_campaign_id
	 * @return int
	 */
	public function link_dynamic_group( $group_id, $keyword_campaign_id ) {
        // Return the Group ID
		return $this->_execute( self::OPERATION_PUT, 'group.createdynamicgroup', array( 'group_id' => $group_id, 'param1' => $keyword_campaign_id ) );
	}

    /**
	 * Update Group
	 *
     * @param int $id The Group ID
     * @param string $name
     * @param string $type
	 * @return int
	 */
	public function update_group( $id, $name, $type ) {
        // Return the Group ID
		return $this->_execute( self::OPERATION_PUT, 'group.update', compact( 'id', 'name', 'type' ) );
	}

    /**
	 * Delete Group
	 *
     * @param int $id The Group ID
	 * @return int
	 */
	public function delete_group( $id ) {
        // Return the Group ID
		return $this->_execute( self::OPERATION_PUT, 'group.delete', compact( 'id' ) );
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