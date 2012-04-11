<?php
/**
 * Handles all the user information
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Users extends Base_Class {
	/**
	 * Hold the user_id
	 *
	 * @since 1.0.0
	 * @var int
	 * @access public
	 */
	public $user_id = 0;

	/**
	 * Construct initializes data
	 */
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;

		// Find out if the user has a cookie set, if so, sign him or her in
		if ( get_cookie( SECURE_AUTH_COOKIE ) ) {
			$this->encrypted_email = get_cookie( SECURE_AUTH_COOKIE );
		} elseif ( get_cookie( AUTH_COOKIE ) ) {
			$this->encrypted_email = get_cookie( AUTH_COOKIE );
		}

		if ( !empty( $this->encrypted_email ) ) {
            global $user;

            $user = $this->get_user_by_email( security::decrypt( base64_decode( $this->encrypted_email ), security::hash( COOKIE_KEY, 'secure-auth' ) ), security::hash( COOKIE_KEY, 'secure-auth' ) );

            // Get website
            $user['websites'] = ar::assign_key( $this->get_websites( $user['user_id'], $user['role'] ), 'website_id' );

            // Don't send them into an infinite loop if you can avoid it
            if ( ( !is_array( $user['websites'] ) || 0 == count( $user['websites'] ) ) && !get_cookie('wid') ) {
                $this->logout();
                url::redirect('/');
            }

            if ( get_cookie('action') && 'bypass' == security::decrypt( base64_decode( get_cookie('action') ), ENCRYPTION_KEY ) ) {
                $w = new Websites;

                $user['website'] = $w->get_website( get_cookie('wid') );
            } else {
                $user['website'] = ( isset( $user['websites'][get_cookie('wid')] ) ) ? $user['websites'][get_cookie('wid')] : '';
            }

            // They must have a website
            if ( empty( $user['website'] ) ) {
                // Need to get the website ID
                $website = current( $user['websites'] );

                // Set the cookie so everything can load
                set_cookie( 'wid', $website['website_id'], 172800 ); // 2 days

                // Ask them to select a website
                url::redirect('/select-website/');
            }
		}
	}

	/**********************************/
	/********** USER SECTION **********/
	/**********************************/

	/**
	 * Creates a new user
	 *
	 * @since 1.0.0
	 *
	 * @param int $company_id
	 * @param string $email
	 * @param string $password
	 * @param string $contact_name
	 * @param string $store_name
	 * @param int $role
	 * @return bool|int
	 */
	public function create( $company_id, $email, $password, $contact_name, $store_name, $role ) {
		$this->db->insert( 'users', array( 'company_id' => $company_id, 'email' => $email, 'password' => md5( $password ), 'contact_name' => $contact_name, 'store_name' => $store_name, 'role' => $role, 'date_created' => dt::now() ), 'issssis' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create user.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
	}

	/**
	 * Create authorized user
	 *
	 * @since 1.0.0
	 *
	 * @param string $email
	 * @param int $role (optional|1)
	 * @return bool|int
	 */
	public function create_authorized_user( $email, $role = 1 ) {
		global $user;

		$this->db->insert( 'users', array( 'email' => $email, 'company_id' => $user['company_id'], 'role' => $role, 'status' => 1, 'date_created' => ( dt::date('Y-m-d H:i:s') ) ), 'siiis' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create authorized user.', __LINE__, __METHOD__ );
			return false;
		}

		return $this->db->insert_id;
	}

	/**
	 * Updates a user's arbitrary information (whatever is in the array)
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id used to identify the user
	 * @param array $information (assumes all to be strings)
	 * @return bool|int
	 */
	public function update_information( $user_id, $information ) {
		global $user;

		if ( isset( $information['password'] ) )
			$information['password'] = md5( $information['password'] );

		$this->db->update( 'users', $information, array( 'user_id' => $user_id ), str_repeat( 's', count( $information ) ), 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update information for user.', __LINE__, __METHOD__ );
			return false;
		}

		// If it was this user, update it
		if ( $user['user_id'] == $user_id )
			$user = array_merge( $user, $information );

		return $user_id;
	}

	/**
	 * Activates a user
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id used to identify the user
	 * @return bool|int
	 */
	public function activate( $user_id ) {
		$this->db->update( 'users', array( 'status' => 1 ), array( 'user_id' => $user_id, 'status' => -1 ), 'i', 'ii' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to activate user.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

	/**
	 * Signs in a user and sets cookie
	 *
	 * @param string $email
	 * @return bool
	 */
	public function login( $email, $password, $remember_me ) {
		// Prepare the statement
		$user = $this->db->prepare( 'SELECT `user_id`, `company_id`, `email`, `contact_name`, `store_name`, `products`, `role` FROM `users` WHERE `status` = 1 AND `email` = ? AND `password` = MD5(?)', 'ss', $email, $password )->get_row( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to sign in user.', __LINE__, __METHOD__ );
			return false;
		}

		// If no user was found, return false
		if ( !$user )
			return false;

		$expiration = ( $remember_me ) ? 1209600 : 172800; // Two Weeks : Two Days
		$auth_cookie = ( security::is_ssl() ) ? AUTH_COOKIE : SECURE_AUTH_COOKIE;
		set_cookie( $auth_cookie, base64_encode( security::encrypt( $email, security::hash( COOKIE_KEY, 'secure-auth' ) ) ), $expiration );

		// Record the login
		$this->record_login( $user['user_id'] );

		return $user;
	}

	/**
	 * Logs out
	 */
	public function logout() {
		global $mc;

		// Removing both of these cookies will destroy everything
		remove_cookie( AUTH_COOKIE );
		remove_cookie( SECURE_AUTH_COOKIE );
		remove_cookie( 'action' );

		$mc->delete( $this->encrypted_email );
	}

	/**
	 * Get websites
	 *
	 * @param int $user_id
	 * @param int $role
	 * @return array
	 */
	public function get_websites( $user_id, $role ) {
        // Type Juggling
        $user_id = (int) $user_id;

		if ( $role > 1 ) {
			// @Fix should `phone` and `logo` be removed and put in the websites:get_website function (meaning theme/website/top needs to change)
			$websites = $this->db->get_results( "SELECT `website_id`, `os_user_id`, IF( '' = `subdomain`, `domain`, CONCAT( `subdomain`, '.', `domain` ) ) AS domain, `phone`, `logo`, `title`, `pages`, `products`, `product_catalog`, `link_brands`, `blog`, `email_marketing`, `shopping_cart`, `seo`, `room_planner`, `craigslist`, `social_media`, `wordpress_username`, `wordpress_password`, `mc_list_id`,  `ga_profile_id`, `mc_list_id`, `live`, `type` FROM `websites` WHERE `user_id` = $user_id AND `status` = 1", ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to predetermine website.', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			// @Fix -- look off `products` or `product_catalog`
			$websites = $this->db->get_results( "SELECT a.`website_id`, a.`os_user_id`, a.`domain`, a.`subdomain`, a.`title`, a.`product_catalog`, a.`link_brands`, a.`seo`, a.`room_planner`, a.`craigslist`, a.`social_media`, a.`wordpress_username`, a.`wordpress_password`, a.`mc_list_id`, a.`live`, a.`type`, a.`pages`, ( b.`products` * a.`products` * a.`product_catalog` ) AS products, a.`ga_profile_id`, b.`blog`, b.`email_marketing`, b.`shopping_cart` FROM `websites` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`user_id` = $user_id AND `status` = 1", ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to predetermine authorized user website.', __LINE__, __METHOD__ );
				return false;
			}
		}

		return $websites;
	}

	/**
	 * Record Login Date/Time
	 *
	 * @param int $user_id
	 * @return bool
	 */
	private function record_login( $user_id ) {
		// Set the last login date to now
		$this->db->update( 'users', array( 'last_login' => dt::date('Y-m-d H:i:s') ), array( 'user_id' => $user_id ), 's', 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to record login.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

	/**
	 * Forgot Your Password
	 *
	 * @param string $email user email
	 * @return int
	 */
	public function forgot_password( $email ) {
		// Get the user
		$user = $this->db->prepare( "SELECT `user_id`, `account_type_id`, CONCAT( `first_name`, ' ', `last_name` ) AS name, `status` FROM `users` WHERE `email` = ?", 's', $email )->get_row( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get user data.', __LINE__, __METHOD__ );
			return false;
		}

		if ( $user ) {
			$e = new Emails();

			if ( -1 == $user['status'] ) {
				// This means their account was never activated, so send the same email
				$e->send_confirmation( $user['user_id'], $email );

				return 1;
			} else {
				// This means it is a legitimate forgot password request
				$e->reset_password( $user['user_id'], $user['name'], $email );

				return 2;
			}
		} else {
			return 0;
		}
	}

	/**
	 * Gets a user by their id
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 * @return object
	 */
	public function get_user( $user_id ) {
		global $mc;

		$user = $mc->get( 'get_user > ' . $user_id );

		if ( empty( $user ) ) {
			// Prepare the statement
			$user = $this->db->prepare( 'SELECT a.`user_id`, a.`company_id`, a.`email`, a.`contact_name`, a.`store_name`, a.`products`, a.`role`, b.`name`, b.`domain` AS company FROM `users` AS a LEFT JOIN `companies` AS b ON ( a.`company_id` = b.`company_id` ) WHERE a.`user_id` = ? AND a.`status` = 1', 'i', $user_id )->get_row( '', ARRAY_A );

			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get user.', __LINE__, __METHOD__ );
				return false;
			}

			$mc->add( 'get_user > ' . $user_id, $user, 7200 );
		}

		return $user;
	}
	
	
	
	/**
	 * Gets a bunch of users
	 *
	 * @param string $where
	 * @return array
	 */
	public function get_website_users( $where = '' ) {
		global $user;
		
		// Make sure they can only see what they're supposed to
		if ( $user['role'] < 8 )
			$where .= ' AND `company_id` = ' . $user['company_id'];
		
		$users = $this->db->get_results( "SELECT a.`user_id`, a.`contact_name`, a.`email`, a.`role` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON a.`user_id` = b.`user_id` WHERE 1 $where ORDER BY `contact_name`", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get users.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $users;
	}
	

	/**
	 * Gets a user by their email address
	 *
	 * @since 1.0.0
	 *
	 * @param string $email the user's email address
	 * @param bool $assign_user_id (optional|true)
	 * @return object|bool $user (object) if user is logged in, false if not logged in.
	 */
	public function get_user_by_email( $email, $assign_user_id = true ) {
		// Prepare the statement
		$user = $this->db->prepare( 'SELECT `user_id`, `company_id`, `email`, `contact_name`, `store_name`, `products`, `role` FROM `users` WHERE `status` = 1 AND `email` = ?', 's', $email )->get_row( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get user by email.', __LINE__, __METHOD__ );
			return false;
		}

		if ( $assign_user_id )
			$this->user_id = (int) $user['user_id'];

		return $user;
	}

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}