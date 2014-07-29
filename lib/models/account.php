<?php
class Account extends ActiveRecordBase {
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const LIVE = 1;
    const UNALIVE = 0;

    // Template unlocked
    const TEMPLATE_UNLOCKED = 1352;
    
    // The columns we will have access to
    public $id, $website_id, $company_package_id, $user_id, $os_user_id, $title, $domain, $plan_name
        , $plan_description, $theme, $logo,  $phone, $products, $pages, $shopping_cart, $product_catalog, $link_brands
        , $room_planner, $blog, $craigslist, $email_marketing, $auth_user_email_marketing, $domain_registration, $mobile_marketing
        , $additional_email_Addresses, $social_media, $ftp_username, $ga_profile_id, $ga_tracking_key
        , $wordpress_username, $wordpress_password, $mc_list_id, $version, $live, $type, $status, $date_created
        , $user_id_updated;

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
            "SELECT w.`website_id`, w.`company_package_id`, w.`user_id`, w.`os_user_id`, w.`domain`, w.`subdomain`, w.`title`, w.`plan_name`, w.`plan_description`, w.`theme`, w.`logo`, w.`phone`, w.`pages`, w.`products`, w.`product_catalog`, w.`link_brands`, w.`blog`, w.`email_marketing`, w.`mobile_marketing`, w.`shopping_cart`, w.`room_planner`, w.`craigslist`, w.`social_media`, w.`domain_registration`, w.`additional_email_addresses`, w.`ftp_username`, w.`ga_profile_id`, w.`ga_tracking_key`, w.`wordpress_username`, w.`wordpress_password`, w.`type`, w.`version`, w.`live`, w.`date_created`, w.`date_updated`, w.`status`, w.`user_id_updated`, u.`status` AS user_status, c.`company_id`, c.`name` AS company  FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) WHERE w.`website_id` = :account_id"
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
     * @return  Account[]
     */
    public function get_by_user( $user_id ) {
        return $this->prepare(
            'SELECT `website_id`, `os_user_id`, `domain`, `phone`, `logo`, `title`, `pages`, `products`, `product_catalog`, `link_brands`, `blog`, `email_marketing`, `mobile_marketing`, `shopping_cart`, `room_planner`, `craigslist`, `social_media`, `wordpress_username`, `wordpress_password`, `ga_profile_id`, `live`, `user_id_updated`, `company_package_id` FROM `websites` WHERE `user_id` = :user_id AND `status` = :status'
            , 'ii'
            , array( ':user_id' => $user_id, ':status' => self::STATUS_ACTIVE )
        )->get_results( PDO::FETCH_CLASS, 'Account' );
    }

    /**
     * Get Accounts by Authorized User
     *
     * @param int $user_id
     * @return  Account[]
     */
    public function get_by_authorized_user( $user_id ) {
        return $this->prepare(
            'SELECT w.`website_id`, w.`os_user_id`, w.`domain`, w.`title`, w.`products`, w.`product_catalog`, w.`link_brands`, w.`room_planner`, w.`craigslist`, w.`social_media`, w.`wordpress_username`, w.`wordpress_password`, IF ( w.`live`, auw.`analytics`, 0 ) AS live, w.`pages`, ( auw.`products` * w.`products` * w.`product_catalog` ) AS product_catalog, w.`ga_profile_id`, IF ( 1 = w.`blog`, auw.`blog`, 0 ) AS blog, IF( 1 = w.`email_marketing`, auw.`email_marketing`, 0 ) AS email_marketing, auw.`email_marketing` AS auth_user_email_marketing, IF( 1 = w.`shopping_cart`, auw.`shopping_cart`, 0 ) AS shopping_cart, w.`user_id_updated`, w.`company_package_id` FROM `websites` AS w LEFT JOIN `auth_user_websites` AS auw ON ( auw.`website_id` = w.`website_id` ) WHERE auw.`user_id` = :user_id AND w.`status` = 1 ORDER BY w.`title` ASC'
            , 'i'
            , array( ':user_id' => $user_id )
        )->get_results( PDO::FETCH_CLASS, 'Account' );
    }

    /**
     * Get Accounts by Product
     *
     * @param int $product_id
     * @return Account[]
     */
    public function get_by_product( $product_id ) {
        return $this->prepare( "SELECT w.`website_id`, w.`title`, `domain` FROM `websites` AS w LEFT JOIN `website_products` AS wp ON ( wp.`website_id` = w.`website_id` ) WHERE w.`status` = 1 AND wp.`product_id` = :product_id AND wp.`blocked` = 0 AND wp.`active` = 1 ORDER BY w.`title`"
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'Account' );
    }

    /**
     * Get Account by domain
     *
     * @param string $domain
     */
    public function get_by_domain( $domain ) {
        // Get the account
        $this->prepare(
            "SELECT w.`website_id`, w.`company_package_id`, w.`user_id`, w.`os_user_id`, w.`domain`, w.`subdomain`, w.`title`, w.`plan_name`, w.`plan_description`, w.`theme`, w.`logo`, w.`phone`, w.`pages`, w.`products`, w.`product_catalog`, w.`link_brands`, w.`blog`, w.`email_marketing`, w.`mobile_marketing`, w.`shopping_cart`, w.`room_planner`, w.`craigslist`, w.`social_media`, w.`domain_registration`, w.`additional_email_addresses`, w.`ftp_username`, w.`ga_profile_id`, w.`ga_tracking_key`, w.`wordpress_username`, w.`wordpress_password`, w.`type`, w.`version`, w.`live`, w.`date_created`, w.`date_updated`, w.`status`, w.`user_id_updated`, u.`status` AS user_status, c.`company_id`, c.`name` AS company  FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) WHERE w.`domain` LIKE :domain"
            , 's'
            , array( ':domain' => '%' . $domain . '%' )
        )->get_row( PDO::FETCH_INTO, $this );

        // Set the ID
        $this->id = $this->website_id;
    }

    /**
     * Get Accounts by the setting LESS (CSS)
     *
     * @return array
     */
    public function get_less_sites() {
        return $this->get_results(
            "SELECT w.`website_id`, w.`title`, `domain` FROM `websites` AS w LEFT JOIN `website_settings` AS ws ON ( ws.`website_id` = w.`website_id` ) WHERE w.`status` = 1 AND ws.`key` = 'less' AND ws.`value` <> '' ORDER BY w.`title`"
            , PDO::FETCH_CLASS, 'Account'
        );
    }

    /**
     * Create an account
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'user_id' => $this->user_id
            , 'os_user_id' => $this->os_user_id
            , 'domain' => strip_tags($this->domain)
            , 'title' => strip_tags($this->title)
            , 'type' => strip_tags($this->type)
            , 'status' => 1
            , 'date_created' => $this->date_created
        ), 'iisssis' );

        $this->website_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update an account
     */
    public function save() {
        parent::update( array(
            'company_package_id' => $this->company_package_id
            , 'user_id' => $this->user_id
            , 'os_user_id' => $this->os_user_id
            , 'domain' => strip_tags($this->domain)
            , 'title' => strip_tags($this->title)
            , 'plan_name' => strip_tags($this->plan_name)
            , 'plan_description' => strip_tags($this->plan_description)
            , 'theme' => strip_tags($this->theme)
            , 'logo' => strip_tags($this->logo)
            , 'phone' => $this->phone
            , 'products' => $this->products
            , 'pages' => $this->pages
            , 'product_catalog' => $this->product_catalog
            , 'link_brands' => $this->link_brands
            , 'blog' => $this->blog
            , 'email_marketing' => $this->email_marketing
            , 'mobile_marketing' => $this->mobile_marketing
            , 'shopping_cart' => $this->shopping_cart
            , 'room_planner' => $this->room_planner
            , 'craigslist' => $this->craigslist
            , 'social_media' => $this->social_media
            , 'domain_registration' => $this->domain_registration
            , 'additional_email_addresses' => $this->additional_email_Addresses
            , 'ftp_username' => strip_tags($this->ftp_username)
            , 'ga_profile_id' => strip_tags($this->ga_profile_id)
            , 'ga_tracking_key' => strip_tags($this->ga_tracking_key)
            , 'wordpress_username' => strip_tags($this->wordpress_username)
            , 'wordpress_password' => strip_tags($this->wordpress_password)
            , 'version' => strip_tags($this->version)
            , 'live' => $this->live
            , 'status' => $this->status
            , 'user_id_updated' => $this->user_id_updated
        ), array( 'website_id' => $this->id )
        , 'iiisssssssiiiiiiiiiiiisissssiii', 'i' );
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
		if ( !$user->has_permission( User::ROLE_ADMIN ) )
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
     * @param string|array $key1, $key2
     * @return mixed
     */
    public function get_settings() {
        $arguments = func_get_args();

        if ( 0 == count( $arguments ) )
            return false;

        // Determine the keys
        if ( 1 == count( $arguments ) && !is_array( $arguments[0] ) ) {
            // Getting one value -- return it
            return $this->get_setting( $arguments[0] );
        } else {
            $keys = ( is_array( $arguments[0] ) ) ? $arguments[0] : $arguments;
        }

        $settings = ar::assign_key( $this->get_settings_array( $keys ), 'key', true );

        foreach ( $keys as $key ) {
            if ( !isset( $settings[$key] ) )
                $settings[$key] = '';
        }

        return $settings;
    }

    /**
     * Get setting
     *
     * @param string $key
     * @return string
     */
    protected function get_setting( $key ) {
        return $this->prepare( 'SELECT `value` FROM `website_settings` WHERE `website_id` = :account_id AND `key` = :key'
            , 'is'
            , array(
                ':account_id' => $this->id
                , ':key' => $key
            )
        )->get_var();
    }

    /**
     * Get Settings as Array
     *
     * @param array $keys
     * @return array
     */
    protected function get_settings_array( array $keys ) {
        $count = count( $keys );

        // Getting multiple values, return them
        return $this->prepare( 'SELECT `key`, `value` FROM `website_settings` WHERE `website_id` = ? AND `key` IN( ?' . str_repeat( ', ?', $count - 1 ) . ')'
            , 'i' . str_repeat( 's', $count )
            , array_merge( array( $this->id ), $keys )
        )->get_results( PDO::FETCH_ASSOC );
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
            $setting_values[] = strip_tags($k);
            $setting_values[] = strip_tags( $v, '<p><font><ul><li><a>' );
        }

		// Insert it or update it
		$this->prepare(
            'INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES ' . substr( str_repeat( ', ( ?, ?, ? )', $settings_count ), 2 ) . ' ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )'
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

        $this->query( "INSERT INTO `website_industries` VALUES " . implode( ',', $values ) . ' ON DUPLICATE KEY UPDATE `industry_id` = VALUES( `industry_id` )' );
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
    
    /**
     * Is New Template
     * 
     * Checks if the frontend site is a New Template (GSR Site) 
     * or a legacy template (GSR Platform)
     * 
     * @return boolean TRUE if it's GSR Site
     */
    public function is_new_template() {
        $new_template_company_package_ids = array(
            26      // Butler
            , 27    // GSR - Unlocked
            , 28    // Signature Design - A (Updated)
            , 29    // Signature Design - B (Updated)
            , 30    // Signature Design - C (Updated)
            , 31    // Signature Design - D (Updated)
            , 32    // IR - Theme 4 (updated)
            , 33    // GSR - Theme 1 (unlocked)
            , 34    // GSR - Theme 2 (unlocked)
            , 35    // GSR - Theme 3 (unlocked)
            , 36    // GSR - Theme 4 (unlocked)
            , 37    // IMR - Theme 1 (Unlocked)
            , 38    // IMR - Theme 2 (Unlocked)
            , 39    // IMR - Theme 3 (Unlocked)
            , 40    // IMR - Theme 4 (Unlocked)
            , 41    // RTO 1 (Unlocked)
            , 42    // RTO 2 (Unlocked)
            , 43    // RTO 3 (Unlocked)
            , 44    // RTO 4 (Unlocked)
        );
        
        return in_array($this->company_package_id, $new_template_company_package_ids);
    }

    /**
     * Get Online Specialist
     * @return User
     */
    public function get_online_specialist() {
        $user = new User();
        $user->get( $this->os_user_id );
        return $user;
    }

    /**
     * Purge Varnish Cache
     */
    public function purge_varnish_cache() {
        try {
            exec("varnishadm -T localhost:6082 ban req.http.host == {$this->domain}");
            exec("varnishadm -T localhost:6082 ban req.http.host == www.{$this->domain}");
        } catch (Exception $e) { /* Probably Varnish is not installed */ }
    }
    
}
