<?php
class User extends ActiveRecordBase {
    /**
     * Hold whether admin is active or not
     * @var int
     */
    private  $_admin;

    // The columns we will have access to
    public $id, $user_id, $company_id, $email, $contact_name, $store_name, $products, $role;

    // Columns available in getting a complete user
    public $work_phone, $cell_phone, $status, $billing_first_name, $billing_last_name, $billing_address1, $billing_city, $billing_state, $billing_zip;

    // These columns belong to another table but might be available from the user
    public $company, $domain;

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
     * Create a company
     */
    public function create() {
        $this->insert( array(
            'company_id' => $this->company_id
            , 'email' => $this->email
            , 'contact_name' => $this->contact_name
            , 'work_phone' => $this->work_phone
            , 'cell_phone' => $this->cell_phone
            , 'store_name' => $this->store_name
            , 'products' => $this->products
            , 'status' => $this->status
            , 'role' => $this->role
            , 'billing_first_name' => $this->billing_first_name
            , 'billing_last_name' => $this->billing_last_name
            , 'billing_address1' => $this->billing_address1
            , 'billing_state' => $this->billing_state
            , 'billing_zip' => $this->billing_zip
            , 'date_created' => dt::date( 'Y-m-d H:i:s' )
        ), 'isssssiiissssss' );

        $this->user_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update the user
     */
    public function update() {
        parent::update( array(
            'company_id' => $this->company_id
            , 'email' => $this->email
            , 'contact_name' => $this->contact_name
            , 'work_phone' => $this->work_phone
            , 'cell_phone' => $this->cell_phone
            , 'store_name' => $this->store_name
            , 'products' => $this->products
            , 'status' => $this->status
            , 'role' => $this->role
            , 'billing_first_name' => $this->billing_first_name
            , 'billing_last_name' => $this->billing_last_name
            , 'billing_address1' => $this->billing_address1
            , 'billing_state' => $this->billing_state
            , 'billing_zip' => $this->billing_zip
        ), array( 'user_id' => $this->id )
            , 'isssssiiisssss', 'i' );
    }

    /**
     * Set Password
     *
     * @param string $password
     */
    public function set_password( $password ) {
        parent::update( array( 'password' => md5( $password ) ), array( 'user_id' => $this->id ), 's', 'i' );
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
		$this->prepare( 'SELECT ' . $this->get_columns() . " FROM `users` WHERE `role` >= $role_requirement AND `status` = 1 AND `email` = :email AND `password` = MD5(:password)",
            'ss',
            array(
                ':email' => $email
                , ':password' => $password
            )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->user_id;

        return 1 === $this->get_row_count();
	}

    /**
	 * Gets a user by their id
	 *
	 * @param int $user_id
	 * @return User
	 */
	public function get( $user_id ) {
        // Prepare the statement
        $this->prepare( 'SELECT a.`user_id`, a.`company_id`, a.`email`, a.`contact_name`, a.`store_name`, a.`work_phone`, a.`cell_phone`, a.`billing_first_name`, a.`billing_last_name`, a.`billing_address1`, a.`billing_city`, a.`billing_state`, a.`billing_zip`, a.`products`, a.`role`, a.`status`, a.`date_created`, b.`name` AS company, b.`domain` FROM `users` AS a LEFT JOIN `companies` AS b ON ( a.`company_id` = b.`company_id` ) WHERE a.`user_id` = :user_id'
            , 'i'
            , array( ':user_id' => $user_id )
        )->get_row( PDO::FETCH_INTO, $this );

		$this->id = $this->user_id;
	}

    /**
     * Gets all users
     *
     * @return array
     */
    public function get_all() {
        $where = ( $this->role < 8 ) ? ' AND ( `company_id` = ' . $this->company_id . ' OR `user_id` = 493 )' : '';

		$users = $this->get_results( "SELECT `user_id`, `company_id`, `contact_name`, `email`, `role` FROM `users` WHERE `status` = 1 AND `contact_name` <> '' $where ORDER BY `contact_name`", PDO::FETCH_CLASS, 'User' );

        return $users;
    }

    /**
     * Get By Email
     *
     * @param string $email
     */
    public function get_by_email( $email ) {
        $this->prepare(
            'SELECT ' . $this->get_columns() . ' FROM `users` WHERE `status` = 1 AND `email` = :email'
            , 's'
            , array( ':email' => $email )
        )->get_row(  PDO::FETCH_INTO, $this );

        $this->id = $this->user_id;
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
            parent::update( array( 'last_login' => dt::date('Y-m-d H:i:s') ), array( 'user_id' => $this->id ), 's', 'i' );
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
