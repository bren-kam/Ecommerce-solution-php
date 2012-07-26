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
        $this->_admin = $admin;
        parent::__construct( 'users' );
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
     * Assign values
     *
     * @param stdClass|array $columns
     */
    protected function assign_values( $columns ) {
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

    /**
     * Record login
     */
    public function record_login() {
        if ( $this->id )
            $this->update( array( 'last_login' => dt::date('Y-m-d H:i:s') ), array( 'user_id' => $this->id ), 's', 'i' );
    }
}
