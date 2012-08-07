<?php
class User extends ActiveRecordBase {
    /**
     * Hold whether admin is active or not
     * @var int
     */
    private  $_admin;

    // The columsn we will have access to
    public $id, $user_id, $company_id, $email, $contact_name, $store_name, $products, $role;
    private $_columns = array( 'user_id', 'company_id', 'email', 'contact_name', 'store_name', 'products', 'role' );

    /**
     * Setup the account initial data
     */
    public function __construct( $admin = 0 ) {
        parent::__construct( 'users' );
        $this->_admin = $admin;

        // We want to make sure they match
        if ( isset( $this->user_id ) )
            $this->id = $this->user_id;
    }

    /**
     * Login
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login( $email, $password ) {
        $role_requirement = ( 1 == $this->_admin ) ? 6 : 1;

		// Prepare the statement
		$columns = $this->prepare( 'SELECT ' . $this->get_columns() . " FROM `users` WHERE `role` >= $role_requirement AND `status` = 1 AND `email` = :email AND `password` = MD5(:password)",
            'ss',
            array(
                ':email' => $email
                , ':password' => $password
            )
        )->get_row();

		// If no user was found, return false
		if ( !$columns ) {
            $this->unset_values();
			return false;
        }

        // Assign values to this user
        $this->assign_values( $columns );

        return true;
	}

    /**
     * Get By Email
     *
     * @param string $email
     */
    public function get_by_email( $email ) {
        $columns = $this->prepare( 'SELECT ' . $this->get_columns() . ' FROM `users` WHERE `status` = 1 AND `email` = :email', 's', array( ':email' => $email ) )->get_row();

        $this->assign_values( $columns );
    }


    /**
     * Check if the user has permissions
     *
     * @param int $permission
     * @return bool
     */
    public function has_permission( $permission ) {
        if ( $this->role >= $permission )
            return true;

        return false;
    }

    /**
     * Record login
     */
    public function record_login() {
        if ( $this->id )
            $this->update( array( 'last_login' => dt::date('Y-m-d H:i:s') ), array( 'user_id' => $this->id ), 's', 'i' );
    }

    /**
	 * Get all information of the users
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        $users = $this->prepare( "SELECT a.`user_id`, a.`email`, a.`contact_name`, COALESCE( a.`work_phone`, a.`cell_phone`, b.`phone`, '') AS phone, a.`role`, COALESCE( b.`domain`, '' ) AS domain FROM `users` AS a LEFT JOIN `websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`status` <> 0 $where GROUP BY a.`user_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'User' );

		return $users;
	}

	/**
	 * Count all the websites
	 *
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

		// Get the website count
        $count = $this->prepare( "SELECT COUNT( a.`user_id` ) FROM `users` AS a LEFT JOIN ( SELECT `domain`, `user_id` FROM `websites` ) AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`status` <> 0 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();

		return $count;
	}

    /**
     * Assign values
     *
     * @param stdClass|array $columns
     */
    protected function assign_values( $columns ) {
        if ( !is_array( $columns ) && !$columns instanceof stdClass )
            return;

        foreach ( $columns as $col => $value ) {
            $this->{$col} = $value;
        }

        $this->id = $this->user_id;
    }

    /**
     * Unset Values
     */
    protected function unset_values() {
        $this->assign_values( array_fill_keys( $this->_columns, NULL ) );
    }

    /**
     * Gets the columns
     *
     * @param string $prefix [optional]
     * @return string
     */
    protected function get_columns( $prefix = '' ) {
        if ( !empty( $prefix ) )
            $prefix .= '.';

        return "{$prefix}`" . implode( "`, {$prefix}`", $this->_columns ) . '`';
    }
}
