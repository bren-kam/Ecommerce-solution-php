<?php
/**
 * Handles all the social media
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Social_Media extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}

    /***** FACEBOOK PAGES *****/

    /**
	 * List Facebook Pages
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_facebook_pages( $variables ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$facebook_pages = $this->db->get_results( "SELECT `id`, `name`, `date_created` FROM `sm_facebook_page` WHERE `website_id` = $website_id AND `status` = 1 $where $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list facebook pages.', __LINE__, __METHOD__ );
			return false;
		}

		return $facebook_pages;
	}

	/**
	 * Count Posting posts
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_facebook_pages( $where ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        // Get the count
		$count = $this->db->get_var( "SELECT COUNT( `id` ) FROM `sm_facebook_page` WHERE `website_id` = $website_id AND `status` = 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count posting posts.', __LINE__, __METHOD__ );
			return false;
		}

		return $count;
	}

    /**
     * Get Facebook Page
     *
     * @param int $id
     * @return array
     */
    public function get_facebook_page( $id ) {
        global $user;

        $facebook_page = $this->db->prepare( 'SELECT `id`, `name` FROM `sm_facebook_page` WHERE `id` = ? AND `website_id` = ?', 'ii', $id, $user['website']['website_id'] )->get_row('');

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count posting posts.', __LINE__, __METHOD__ );
			return false;
		}

        return $facebook_page;
    }

    /**
     * Create Facebook Page
     *
     * @param string $name
     * @return int
     */
    public function create_facebook_page( $name ) {
        global $user;

        $this->db->insert( 'sm_facebook_page', array( 'website_id' => $user['website']['website_id'], 'name' => $name, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to create facebook page.', __LINE__, __METHOD__ );
            return false;
        }

        return $this->db->insert_id;
    }

    /**
     * Update Facebook Page
     *
     * @param int $id
     * @param string $name
     * @return int
     */
    public function update_facebook_page( $id, $name ) {
        global $user;

        $this->db->update( 'sm_facebook_page', array( 'name' => $name ), array( 'id' => $id, 'website_id' => $user['website']['website_id'] ), 's', 'ii' );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to upadte facebook page.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
    }

    /**
     * Delete Facebook Page
     *
     * @param int $id
     * @return bool
     */
    public function delete_facebook_page( $id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $id = (int) $id;

        $this->db->update( 'sm_facebook_page', array( 'status' => 0 ), array( 'id' => $id, 'website_id' => $website_id ), 'i', 'ii' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete facebook page.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

	/***** EMAIL SIGN UP *****/
	
	/**
	 * Create Email Sign Up
	 *
	 * @return string
	 */
	public function create_email_sign_up() {
		global $user;

		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_email_sign_up', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Email Sign Up
	 *
	 * @return array
	 */
	public function get_email_sign_up() {
		// Get the email sign up
		$email_sign_up = $this->db->prepare( 'SELECT `fb_page_id`, `email_list_id`, `key`, `tab` FROM `sm_email_sign_up` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $email_sign_up;
	}
	
	/**
	 * Update Email Sign Up
	 *
	 * @param string $tab
	 * @param int $email_list_id
	 * @return bool
	 */
	public function update_email_sign_up( $tab, $email_list_id ) {
		// Update the email sign up
		$this->db->update( 'sm_email_sign_up', array( 'tab' => $tab, 'email_list_id' => $email_list_id ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 'si', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update email sign up.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** FAN OFFER *****/
	
	/**
	 * Create Fan Offer
	 *
	 * @return string
	 */
	public function create_fan_offer() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_fan_offer', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Fan Offer
	 *
	 * @return array
	 */
	public function get_fan_offer() {
		// Get the fan offer
		$fan_offer = $this->db->prepare( 'SELECT `fb_page_id`, `email_list_id`, `key`, `before`, `after`, UNIX_TIMESTAMP( `start_date` ) AS start_date, UNIX_TIMESTAMP( `end_date` ) AS end_date, `share_title`, `share_image_url`, `share_text` FROM `sm_fan_offer` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $fan_offer;
	}
	
	/**
	 * Update Fan Offer
	 *
	 * @param int $email_list_id
	 * @param string $before
	 * @param string $after
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_fan_offer( $email_list_id, $before, $after, $start_date, $end_date, $share_title, $share_image_url, $share_text ) {
		// Update the fan offer
		$this->db->update( 'sm_fan_offer', array( 'email_list_id' => $email_list_id, 'before' => $before, 'after' => $after, 'start_date' => $start_date, 'end_date' => $end_date, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 'isssssss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update fan offer.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** SWEEPSTAKES *****/
	
	/**
	 * Create Sweepstakes
	 *
	 * @return string
	 */
	public function create_sweepstakes() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_sweepstakes', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}

		return $key;
	}
	
	/**
	 * Get Sweepstakes
	 *
	 * @return array
	 */
	public function get_sweepstakes() {
		// Get the sweepstakes
		$sweepstakes = $this->db->prepare( 'SELECT `fb_page_id`, `email_list_id`, `key`, `before`, `after`, UNIX_TIMESTAMP( `start_date` ) AS start_date, UNIX_TIMESTAMP( `end_date` ) AS end_date, `contest_rules_url`, `share_title`, `share_image_url`, `share_text` FROM `sm_sweepstakes` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $sweepstakes;
	}
	
	/**
	 * Update Sweepstakes
	 *
	 * @param int $email_list_id
	 * @param string $before
	 * @param string $after
	 * @param string $start_date
	 * @param string $end_date
	 * @param string $contest_rules_url
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_sweepstakes( $email_list_id, $before, $after, $start_date, $end_date, $contest_rules_url, $share_title, $share_image_url, $share_text ) {
		// Update the sweepstakes
		$this->db->update( 'sm_sweepstakes', array( 'email_list_id' => $email_list_id, 'before' => $before, 'after' => $after, 'start_date' => $start_date, 'end_date' => $end_date, 'contest_rules_url' => $contest_rules_url, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 'issssssss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update sweepstakes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** SHARE AND SAVE *****/
	
	/**
	 * Create Share and Save
	 *
	 * @return string
	 */
	public function create_share_and_save() {
		global $user;

		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_share_and_save', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Share and Save
	 *
	 * @return array
	 */
	public function get_share_and_save() {
		// Get the fan offer
		$share_and_save = $this->db->prepare( 'SELECT `fb_page_id`, `email_list_id`, `maximum_email_list_id`, `key`, `before`, `after`, `minimum`, `maximum`, `share_title`, `share_image_url`, `share_text` FROM `sm_share_and_save` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $share_and_save;
	}
	
	/**
	 * Update Share and Save
	 *
	 * @param int $email_list_id
	 * @param int $maximum_email_list_id
	 * @param string $before
	 * @param string $after
	 * @param int $minimum
	 * @param int $maximum
	 * @param string $share_title
	 * @param string $share_image_url
	 * @param string $share_text
	 * @return bool
	 */
	public function update_share_and_save( $email_list_id, $maximum_email_list_id, $before, $after, $minimum, $maximum, $share_title, $share_image_url, $share_text ) {
		// Update the share and save
		$this->db->update( 'sm_share_and_save', array( 'email_list_id' => $email_list_id, 'maximum_email_list_id' => $maximum_email_list_id, 'before' => $before, 'after' => $after, 'minimum' => $minimum, 'maximum' => $maximum, 'share_title' => $share_title, 'share_image_url' => $share_image_url, 'share_text' => $share_text ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 'iissiisss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update share and save.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** FACEBOOK SITE *****/
	
	/**
	 * Create Facebook Site
	 *
	 * @return string
	 */
	public function create_facebook_site() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_facebook_site', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Facebook Site
	 *
	 * @return array
	 */
	public function get_facebook_site() {
		// Get the facebook site
		$facebook_site = $this->db->prepare( 'SELECT `fb_page_id`, `key`, `content` FROM `sm_facebook_site` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $facebook_site;
	}
	
	/**
	 * Update The Facebook Site Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_facebook_site( $content ) {
		// Update the facebook site
		$this->db->update( 'sm_facebook_site', array( 'content' => $content ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update the facebook site.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** CONTACT US *****/
	
	/**
	 * Create Contact Us
	 *
	 * @return string
	 */
	public function create_contact_us() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		// Get the page id for their contact us page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('contact-us');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_contact_us', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create contact us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Contact Us
	 *
	 * @return array
	 */
	public function get_contact_us() {
		// Get the contact us
		$contact_us = $this->db->prepare( 'SELECT `fb_page_id`, `key`, `content` FROM `sm_contact_us` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the contact us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $contact_us;
	}
	
	/**
	 * Update The Contact Us Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_contact_us( $content ) {
		// Update the contact us page
		$this->db->update( 'sm_contact_us', array( 'content' => $content ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update the contact us page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** ABOUT US *****/
	
	/**
	 * Create About Us
	 *
	 * @return string
	 */
	public function create_about_us() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		// Get the page id for their About Us page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('about-us');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_about_us', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create about us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get About Us
	 *
	 * @return array
	 */
	public function get_about_us() {
		// Get the About Us
		$about_us = $this->db->prepare( 'SELECT `fb_page_id`, `key`, `content` FROM `sm_about_us` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the about us.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $about_us;
	}
	
	/**
	 * Update The About Us Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_about_us( $content ) {
		// Update the About Us page
		$this->db->update( 'sm_about_us', array( 'content' => $content ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update the about us page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** PRODUCTS *****/
	
	/**
	 * Create Products
	 *
	 * @return string
	 */
	public function create_products() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_products', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Products
	 *
	 * @return array
	 */
	public function get_products() {
		// Get the products page
		$products = $this->db->prepare( 'SELECT `fb_page_id`, `key`, `content` FROM `sm_products` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the products.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $products;
	}
	
	/**
	 * Update The Products Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_products( $content ) {
		// Update the products page
		$this->db->update( 'sm_products', array( 'content' => $content ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update the products page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** CURRENT AD *****/
	
	/**
	 * Create Current Ad
	 *
	 * @return string
	 */
	public function create_current_ad() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		// Get the page id for their Current Ad page if they have it
		if ( $user['website']['pages'] ) {
			// @Fix should a custom method be used or query string to get JUST the website page id?
			$w = new Websites;
			$page = $w->get_page_by_slug('sidebar');
			
			$website_page_id = $page['website_page_id'];
		} else {
			$website_page_id = 0;
		}
		
		$this->db->insert( 'sm_current_ad', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'website_page_id' => $website_page_id, 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create current ad.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Current Ad
	 *
	 * @return array
	 */
	public function get_current_ad() {
		// Get the Current Ad
		$current_ad = $this->db->prepare( 'SELECT `fb_page_id`, `key`, `content` FROM `sm_current_ad` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the current ad.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $current_ad;
	}
	
	/**
	 * Update The Current Ad Page
	 *
	 * @param string $content
	 * @return bool
	 */
	public function update_current_ad( $content ) {
		// Update the Current Ad page
		$this->db->update( 'sm_current_ad', array( 'content' => $content ), array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'] ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to update the current ad page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/***** POSTING *****/
	
	/**
	 * Create Posting
	 *
	 * @return string
	 */
	public function create_posting() {
		global $user;
		
		$key = md5( $user['user_id'] . microtime() . $_SESSION['sm_facebook_page_id'] );
		
		$this->db->insert( 'sm_posting', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create posting.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $key;
	}
	
	/**
	 * Get Posting
	 *
	 * @return array
	 */
	public function get_posting() {
		// Get the posting
		$posting = $this->db->prepare( 'SELECT `fb_user_id`, `fb_page_id`, `key`, `access_token` FROM `sm_posting` WHERE `sm_facebook_page_id` = ?', 'i', $_SESSION['sm_facebook_page_id'] )->get_row('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get the posting.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $posting;
	}

	/**
	 * Get Posting
	 *
	 * @param string $access_token
	 * @param string $post
	 * @param string $link
	 * @param string $date_posted
	 * @param int $status (optional|0)
	 * @return bool
	 */
	public function create_posting_post( $access_token, $post, $link, $date_posted, $status = 0 ) {
        if ( empty( $access_token ) || is_null( $access_token ) )
            return false;

		global $user;
		
		// Create the posting post
		$this->db->insert( 'sm_posting_posts', array( 'sm_facebook_page_id' => $_SESSION['sm_facebook_page_id'], 'access_token' => $access_token, 'post' => $post, 'link' => $link, 'status' => $status, 'date_posted' => $date_posted, 'date_created' => dt::date('Y-m-d H:i:s') ), 'isssiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create the posting post.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}

    /**
	 * List Posting posts
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_posting_posts( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;

		$posts = $this->db->get_results( "SELECT `sm_posting_post_id`, `post`, `error`, `status`, `date_posted` FROM `sm_posting_posts` WHERE 1 $where $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to list posts.', __LINE__, __METHOD__ );
			return false;
		}

		return $posts;
	}

	/**
	 * Count Posting posts
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_posting_posts( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( `sm_posting_post_id` )  FROM `sm_posting_posts` WHERE 1 $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to count posting posts.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $count;
	}

    /**
     * Delete posting post
     * 
     * @param $sm_posting_post_id
     * @return bool
     */
    public function delete_posting_post( $sm_posting_post_id ) {
        // Type Juggling
        $sm_facebook_page_id = (int) $_SESSION['sm_facebook_page_id'];
        $sm_posting_post_id = (int) $sm_posting_post_id;

        // Delete the post
        $this->db->query( "DELETE FROM `sm_posting_posts` WHERE  `sm_posting_post_id` = $sm_posting_post_id AND `sm_facebook_page_id` = $sm_facebook_page_id AND `status` <> 1" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete posting post.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}