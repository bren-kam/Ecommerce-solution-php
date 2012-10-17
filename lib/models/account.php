<?php
class Account extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $company_package_id, $user_id, $os_user_id, $title, $domain, $plan_name
        , $plan_description, $theme, $logo,  $phone, $products, $pages, $shopping_cart, $product_catalog
        , $room_planner, $blog, $craigslist, $email_marketing, $domain_registration, $mobile_marketing
        , $additional_email_Addresses, $social_media, $ftp_username, $ga_profile_id, $ga_tracking_key
        , $wordpress_username, $wordpress_password, $mc_list_id, $version, $live, $type, $status;

    // Columns belonging to another table but which may reside here
    public $company_id;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'websites' );

        // We want to make sure they match
        if ( isset( $this->website_id ) )
            $this->id = $this->website_id;
    }

    /**
     * Get an account
     *
     * @param int $account_id
     */
    public function get( $account_id ) {
        // Get the account
		$this->prepare(
            "SELECT a.`website_id`, a.`company_package_id`, a.`user_id`, a.`os_user_id`, a.`domain`, a.`subdomain`, a.`title`, a.`plan_name`, a.`plan_description`, a.`theme`, a.`logo`, a.`phone`, a.`pages`, a.`products`, a.`product_catalog`, a.`link_brands`, a.`blog`, a.`email_marketing`, a.`mobile_marketing`, a.`shopping_cart`, a.`room_planner`, a.`craigslist`, a.`social_media`, a.`domain_registration`, a.`additional_email_addresses`, a.`ftp_username`, a.`ga_profile_id`, a.`ga_tracking_key`, a.`wordpress_username`, a.`wordpress_password`, a.`mc_list_id`, a.`type`, a.`version`, a.`live`, a.`date_created`, a.`date_updated`, a.`status`, b.`status` AS user_status, c.`company_id`, c.`name` AS company  FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) WHERE a.`website_id` = :account_id"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        // Set the ID
        $this->id = $this->website_id;
    }

    /**
     * Get Accounts by User
     *
     * @param int $user_id
     * @return array
     */
    public function get_by_user( $user_id ) {
        return $this->prepare( "SELECT `website_id`, `title`, `domain` FROM `websites` WHERE `user_id` = :user_id AND `status` = 1"
            , 'i'
            , array( ':user_id' => $user_id )
        )->get_results( PDO::FETCH_CLASS, 'Account' );
    }

    /**
     * Create an account
     */
    public function create() {
        $this->insert( array(
            'user_id' => $this->user_id
            , 'os_user_id' => $this->os_user_id
            , 'domain' => $this->domain
            , 'title' => $this->title
            , 'type' => $this->type
            , 'status' => 1
            , 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssis' );

        $this->website_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an account
     */
    public function update() {
        parent::update( array(
            'company_package_id' => $this->company_package_id
            , 'user_id' => $this->user_id
            , 'os_user_id' => $this->os_user_id
            , 'domain' => $this->domain
            , 'title' => $this->title
            , 'plan_name' => $this->plan_name
            , 'plan_description' => $this->plan_description
            , 'theme' => $this->theme
            , 'logo' => $this->logo
            , 'phone' => $this->phone
            , 'products' => $this->products
            , 'pages' => $this->pages
            , 'product_catalog' => $this->product_catalog
            , 'blog' => $this->blog
            , 'email_marketing' => $this->email_marketing
            , 'mobile_marketing' => $this->mobile_marketing
            , 'shopping_cart' => $this->shopping_cart
            , 'room_planner' => $this->room_planner
            , 'craigslist' => $this->craigslist
            , 'social_media' => $this->social_media
            , 'domain_registration' => $this->domain_registration
            , 'additional_email_addresses' => $this->additional_email_Addresses
            , 'ftp_username' => $this->ftp_username
            , 'ga_profile_id' => $this->ga_profile_id
            , 'ga_tracking_key' => $this->ga_tracking_key
            , 'wordpress_username' => $this->wordpress_username
            , 'wordpress_password' => $this->wordpress_password
            , 'mc_list_id' => $this->mc_list_id
            , 'version' => $this->version
            , 'live' => $this->live
            , 'status' => $this->status
        ), array( 'website_id' => $this->id )
        , 'iiisssssssiiiiiiiiiiisisssssii', 'i' );
    }

    /**
	 * Get all information of the websites
	 *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        $accounts = $this->prepare( "SELECT a.`website_id`, a.`domain`, a.`title`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, IF ( '' = b.`cell_phone`, b.`work_phone`, b.`cell_phone` ) AS phone, c.`contact_name` AS online_specialist FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) WHERE 1 $where GROUP BY a.`website_id` $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'Account' );

		return $accounts;
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
        $count = $this->prepare( "SELECT COUNT( DISTINCT a.`website_id` ) FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();

		return $count;
	}


    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
     * @param User $user
     * @param bool|null $status
	 * @return array
	 */
	public function autocomplete( $query, $field, $user, $status = null ) {
        $where = '';

		// Construct WHERE
		if ( !$user->has_permission(8) )
            $where .= ' AND b.`company_id` = ' . (int) $user->company_id;

        if ( is_null( $status ) ) {
            $where .= ' AND a.`status` = 1';
        } else {
            $where .= ( -1 == $status ) ? ' AND a.`status` = 0' : ' AND a.`status` = 1 AND a.`live` = ' . (int) $status;
        }

		// Get results
		return $this->prepare(
            "SELECT DISTINCT( a.`$field` ) FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`$field` LIKE :query $where AND a.`website_id` NOT IN ( 96, 114, 115, 116 ) ORDER BY a.`$field` LIMIT 10"
            , 's', array( ':query' => $query . '%' )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /***** Account Settings *****/

    /**
     * Get Settings
     *
     * @param string $key1, $key2
     * @return mixed
     */
    public function get_settings() {
        $arguments = func_get_args();

        if ( 0 == count( $arguments ) )
            return false;

        // Determine the keys
        if ( 1 == count( $arguments ) ) {
            // Getting one value -- return it
            return $this->prepare( 'SELECT `value` FROM `website_settings` WHERE `website_id` = :website_id AND `key` = :key'
                , 'is'
                , array(
                    ':website_id' => $this->id
                    , ':key' => $arguments[0]
                )
            )->get_var();
        } else {
            $keys = $arguments;
        }

        $count = count( $keys );

        // Getting multiple values, return them
        $values = $this->prepare( 'SELECT `key`, `value` FROM `website_settings` WHERE `website_id` = ? AND `key` IN( ?' . str_repeat( ', ?', $count - 1 ) . ')'
            , 'i' . str_repeat( 's', $count )
            , array_merge( array( $this->id ), $keys )
        )->get_results( PDO::FETCH_ASSOC );

        $settings = ar::assign_key( $values, 'key', true );

        foreach ( $arguments as $arg ) {
            if ( !isset( $settings[$arg] ) )
                $settings[$arg] = '';
        }

        return $settings;
    }

    /**
     * Set Settings
     *
     * @param array $settings
     */
    public function set_settings( array $settings ) {
        // How many settings are we dealing with?
        $settings_count = count( $settings );

        // Get the setting values
        $setting_values = array();

        foreach ( $settings as $k => $v ) {
            $setting_values[] = $this->id;
            $setting_values[] = $k;
            $setting_values[] = $v;
        }

		// Insert it or update it
		$this->prepare(
            'INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES ' . substr( str_repeat( ', ( ?, ?, ? )', $settings_count ), 2 ) . ' ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )'
            , str_repeat( 'iss', $settings_count )
            , $setting_values
        )->query();
    }

    /**
     * Set Email Settings
     *
     * @fix need to consolidate with the above function to remove this table
     *
     * @param array $settings
     */
    public function set_email_settings( array $settings ) {
        // How many settings are we dealing with?
        $settings_count = count( $settings );

        // Get the setting values
        $setting_values = array();

        foreach ( $settings as $k => $v ) {
            $setting_values[] = $this->id;
            $setting_values[] = $k;
            $setting_values[] = $v;
        }

		// Insert it or update it
		$this->prepare(
            'INSERT INTO `email_settings` ( `website_id`, `key`, `value` ) VALUES ' . substr( str_repeat( ', ( ?, ?, ? )', $settings_count ), 2 ) . ' ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )'
            , str_repeat( 'iss', $settings_count )
            , $setting_values
        )->query();
    }

    /**
     * Get Industries by account
     *
     * @return array
     */
    public function get_industries() {
        return $this->prepare(
            'SELECT `industry_id` FROM `website_industries` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $this->id )
        )->get_col();
    }

    /**
     * Copy industries
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_industries_by_account( $template_account_id, $account_id ) {
        $this->copy( 'website_industries', array(
                'website_id' => $account_id
                , 'industry_id' => NULL
            ), array( 'website_id' => $template_account_id )
        );
    }

    /**
     * Delete Industries
     */
    public function delete_industries() {
        $this->prepare( 'DELETE FROM `website_industries` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $this->id )
        )->query();
    }

    /**
     * Add Industries
     *
     * @param array $industry_ids
     */
    public function add_industries( array $industry_ids ) {
        if ( 0 == count( $industry_ids ) )
            return;

        $account_id = (int) $this->id;
        $values = array();

        foreach ( $industry_ids as $iid ) {
            $iid = (int) $iid;

            $values[] = "( $account_id, $iid )";
        }

        $this->query( "INSERT INTO `website_industries` VALUES " . implode( ',', $values ) );
    }


    /**
     * Copy top brands
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_top_brands_by_account( $template_account_id, $account_id ) {
        $this->copy( 'website_top_brands', array(
                'website_id' => $account_id
                , 'brand_id' => NULL
                , 'sequence' => NULL
            ), array( 'website_id' => $template_account_id )
        );
    }

    /**
     * Copy website_settings
     *
     * @param int $template_account_id
     * @param int $account_id
     * @param array $settings
     */
    public function copy_settings_by_account( $template_account_id, $account_id, array $settings ) {
        $this->copy( 'website_settings', array(
                'website_id' => $account_id
                , 'key' => NULL
                , 'value' => NULL
            ), array(
                'website_id' => $template_account_id
                , 'key' => $settings
            )
        );
    }
}
