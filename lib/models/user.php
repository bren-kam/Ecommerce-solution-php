<?php
class User extends ActiveRecordBase {
    const ROLE_AUTHORIZED_USER = 1;
    const ROLE_MARKETING_SPECIALIST = 3;
    const ROLE_STORE_OWNER = 5;
    const ROLE_COMPANY_ADMIN = 6;
    const ROLE_ONLINE_SPECIALIST = 7;
    const ROLE_ADMIN = 8;
    const ROLE_SUPER_ADMIN = 10;

    const KERRY = 1;
    const JEFF = 214;
    const TECHNICAL = 493; // Jack
    const CHRIS = 73; // Products
    const CRAIG = 54; // Accounting
    const RODRIGO = 305; // Design
    const MANINDER = 85; // Head of Conversions
    const RAFFERTY = 19;
    const KEVIN_DORAN = 251;
    const CATALOG_MANAGER = 2734;
    const DAVID = 52;
    const DESIGN_TEAM = 1970;
    const DEVELOPMENT = 2696;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // The columns we will have access to
    public $id, $user_id, $company_id, $email, $contact_name, $store_name, $role, $email_signature, $date_created, $jira_username, $jira_password, $from_email_address;

    // Columns available in getting a complete user
    public $work_phone, $cell_phone, $status, $billing_first_name, $billing_last_name, $billing_address1, $billing_city, $billing_state, $billing_zip, $photo, $new_features_dismissed_at;

    // Artificial column
    public $phone;

    // These columns belong to another table but might be available from the user
    public $company, $domain, $accounts, $main_website;

    public static $support_team = [
        User::DESIGN_TEAM
        , User::DEVELOPMENT
        , User::TECHNICAL
    ];

    /**
     * Holds the account if it has it
     * @var Account
     */
    public $account;

    private $_columns = array( 'user_id', 'company_id', 'email', 'contact_name', 'store_name', 'role', 'status','jira_username','jira_password' );

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'users' );

        // We want to make sure they match
        if ( isset( $this->user_id ) )
            $this->id = $this->user_id;
    }

    /**
     * Create a user
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'company_id' => $this->company_id
            , 'email' => strip_tags($this->email)
            , 'contact_name' => strip_tags($this->contact_name)
            , 'work_phone' => strip_tags($this->work_phone)
            , 'cell_phone' => strip_tags($this->cell_phone)
            , 'store_name' => strip_tags($this->store_name)
            , 'photo' => $this->photo
            , 'status' => $this->status
            , 'role' => $this->role
            , 'jira_username' => $this->jira_username
            , 'jira_password' => $this->jira_password
            , 'email_signature' => strip_tags($this->email_signature)
            , 'billing_first_name' => strip_tags($this->billing_first_name)
            , 'billing_last_name' => strip_tags($this->billing_last_name)
            , 'billing_address1' => strip_tags($this->billing_address1)
            , 'billing_city' => strip_tags($this->billing_city)
            , 'billing_state' => strip_tags($this->billing_state)
            , 'billing_zip' => strip_tags($this->billing_zip)
            , 'date_created' => $this->date_created
        ), 'issssssiisssssssss' );

        $this->user_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update the user
     */
    public function save() {
        $this->update([
            'company_id' => $this->company_id
            , 'email' => strip_tags($this->email)
            , 'contact_name' => strip_tags($this->contact_name)
            , 'work_phone' => strip_tags($this->work_phone)
            , 'cell_phone' => strip_tags($this->cell_phone)
            , 'store_name' => strip_tags($this->store_name)
            , 'photo' => $this->photo
            , 'status' => $this->status
            , 'role' => $this->role
            , 'jira_username' => $this->jira_username
            , 'jira_password' => $this->jira_password
            , 'email_signature' => strip_tags($this->email_signature)
            , 'from_email_address' => strip_tags($this->from_email_address)            
            , 'billing_first_name' => strip_tags($this->billing_first_name)
            , 'billing_last_name' => strip_tags($this->billing_last_name)
            , 'billing_address1' => strip_tags($this->billing_address1)
            , 'billing_city' => strip_tags($this->billing_city)
            , 'billing_state' => strip_tags($this->billing_state)
            , 'billing_zip' => strip_tags($this->billing_zip)
            , 'new_features_dismissed_at' => $this->new_features_dismissed_at
        ], [
            'user_id' => $this->id
        ], 'issssssiissssssss', 'i' );
    }

    /**
     * Set Password
     *
     * @param string $password
     */
    public function set_password( $password ) {
        parent::update( array(
            'password' => md5( $password )
        ), array(
            'user_id' => $this->id ), 's', 'i' );
    }

    /**
     * Login
     *
     * @param string $email
     * @param string $password
     * @param bool $admin
     * @return bool
     */
    public function login( $email, $password, $admin = false ) {
        $role_requirement = ( $admin ) ? User::ROLE_COMPANY_ADMIN : User::ROLE_AUTHORIZED_USER;

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
        $this->prepare( 'SELECT u.`user_id`, u.`company_id`, u.`email`, u.`contact_name`, u.`store_name`, u.`work_phone`, u.`cell_phone`, u.`photo`, u.`billing_first_name`, u.`billing_last_name`, u.`billing_address1`, u.`billing_city`, u.`billing_state`, u.`billing_zip`, u.`role`, u.`status`, u.`date_created`, u.`jira_username`,u.`jira_password`, u.`from_email_address`, c.`name` AS company, c.`domain`, u.`email_signature`, u.`new_features_dismissed_at` FROM `users` AS u LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) WHERE u.`user_id` = :user_id'
            , 'i'
            , array( ':user_id' => $user_id )
        )->get_row( PDO::FETCH_INTO, $this );

		$this->id = $this->user_id;
	}

    /**
     * Gets all users
     *
     * @return User[]
     */
    public function get_all() {
        $where = ( !$this->has_permission( self::ROLE_ADMIN ) ) ? ' AND ( `company_id` = ' . $this->company_id . ' OR `user_id` = 493 )' : '';

		return $this->prepare(
            "SELECT `user_id`, `company_id`, `contact_name`, `email`, `role` FROM `users` WHERE `status` = :status AND `contact_name` <> '' $where ORDER BY `contact_name`"
            , 'i'
            , array( ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'User' );
    }

    /**
     * Get By Email
     *
     * @param string $email
     * @param bool $status [optional]
     */
     public function get_by_email( $email, $status = true ) {
        $status_where = ( $status ) ? ' AND u.`status` = ' . self::STATUS_ACTIVE : '';

        $this->prepare(
            'SELECT u.`user_id`, u.`company_id`, u.`email`, u.`contact_name`, u.`store_name`, u.`work_phone`, u.`cell_phone`, u.`photo`, u.`billing_first_name`, u.`billing_last_name`, u.`billing_address1`, u.`billing_city`, u.`billing_state`, u.`billing_zip`, u.`role`, u.`status`, u.`date_created`, c.`name` AS company, c.`domain`, u.`email_signature`, u.`new_features_dismissed_at` FROM `users` AS u LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) WHERE u.`email` = :email' . $status_where
            , 's'
            , array( ':email' => $email )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->user_id;
    }

    /**
	 * Gets all the "admin" users
	 *
	 * @param array $user_ids [optional] any additional user ids you want to be included
	 * @return User[]
	 */
	public function get_admin_users( $user_ids = array() ) {
        $user_ids[] = self::TECHNICAL;

        $where = '';

        // Type Juggline
        foreach ( $user_ids as &$uid ) {
            $uid = (int) $uid;
        }

        // Make sure they can only see what they're supposed to
        if ( !$this->has_permission( self::ROLE_ADMIN ) )
            $where .= ' AND ( `company_id` = ' . $this->company_id . ' OR `user_id` IN( ' . implode( ', ', $user_ids ) . ' ) ) ';

        return $this->prepare(
            "SELECT `user_id`, `contact_name`, `email`, `role` FROM `users` WHERE `status` = :status AND `role` >= " . self::ROLE_ONLINE_SPECIALIST . " AND '' <> `contact_name` $where ORDER BY `contact_name`"
            , 'i'
            , array( ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'User' );
    }

    /**
     * Get Product users
     *
     * @return array
     */
    public function get_product_users() {
        // Make sure they can only see what they're supposed to
        $where = ( !$this->has_permission( self::ROLE_ADMIN ) ) ? ' AND a.`company_id` = ' . (int) $this->company_id : '';

        return $this->get_results(
            "SELECT DISTINCT a.`user_id`, a.`contact_name` FROM `users` AS a INNER JOIN `products` AS b ON ( a.`user_id` = b.`user_id_created` || a.`user_id` = b.`user_id_modified` ) WHERE b.`publish_date` <> '0000-00-00 00:00:00' $where"
            , PDO::FETCH_CLASS
            , 'User'
        );
    }

    /**
	 * Autocomplete
	 *
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
	 * @return array
	 */
	public function autocomplete( $query, $field ) {
		// Construct WHERE
		$where = ( !$this->has_permission( self::ROLE_ADMIN ) ) ? ' AND `company_id` = ' . (int) $this->company_id : '';

		// Get results
		return $this->prepare(
            "SELECT DISTINCT( `$field` ) FROM `users` WHERE `$field` LIKE :query $where ORDER BY `$field` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%' )
        )->get_results( PDO::FETCH_ASSOC );
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
            parent::update( array( 'last_login' => dt::now() ), array( 'user_id' => $this->id ), 's', 'i' );
    }

    /**
     * Get all information of the users
     *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
     * @return User[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare( "SELECT u.`user_id`, u.`email`, u.`contact_name`, u.`role`, w.`title` as main_website FROM `users` u LEFT JOIN `websites` w ON u.`user_id` = w.`user_id` WHERE u.`status` <> " . self::STATUS_INACTIVE . " $where GROUP BY u.`user_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'User' );
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
        $count = $this->prepare( "SELECT COUNT( DISTINCT `user_id` ) FROM `users` WHERE `status` <> 0 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();

        return $count;
    }

    /**
     * Get Role Name
     *
     * @param int $role
     * @return string
     */
    public static function get_role_name( $role ) {
        $translations = array(
            self::ROLE_AUTHORIZED_USER => 'Authorized User'
            , self::ROLE_MARKETING_SPECIALIST => 'Marketing Specialist'
            , self::ROLE_STORE_OWNER => 'Store Owner'
            , self::ROLE_COMPANY_ADMIN =>'Company Admin'
            , self::ROLE_ONLINE_SPECIALIST => 'Online Specialist'
            , self::ROLE_ADMIN => 'Admin'
            , self::ROLE_SUPER_ADMIN => 'Super Admin'
        );

        return $translations[$role];
    }

    /***** PROTECTED *****/

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

    public function copyProperties(User $old_user) {
        $this->company_id = $old_user->company_id;
        $this->email = $old_user->email;
        $this->password = $old_user->password;
        $this->contact_name = $old_user->contact_name;
        $this->store_name = $old_user->store_name;
        $this->work_phone = $old_user->work_phone;
        $this->cell_phone = $old_user->cell_phone;
        $this->photo = $old_user->photo;
        $this->billing_first_name = $old_user->billing_first_name;
        $this->billing_last_name = $old_user->billing_last_name;
        $this->billing_city = $old_user->billing_city;
        $this->billing_state = $old_user->billing_state;
        $this->billing_zip = $old_user->billing_zip;
        $this->arb_subscription_id = $old_user->arb_subscription_id;
        $this->role = $old_user->role;
        $this->status = $old_user->status;
        $this->email_signature = $old_user->email_signature;
        $this->job_title = $old_user->job_title;
        $this->last_login = $old_user->last_login;
        $this->date_created = $old_user->date_created;
        $this->date_updated = $old_user->date_updated;
        $this->new_features_dismissed_at = $old_user->new_features_dismissed_at;
        $this->jira_username = $old_user->jira_username;
        $this->jira_password = $old_user->jira_password;

        return true;
    }
}
