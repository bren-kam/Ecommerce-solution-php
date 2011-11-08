<?php
/**
 * Handles all the authorized users
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Authorized_Users extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * List Authorized users
	 *
	 * @param array( $where, $order_by, $limit )
	 * @return array
	 */
	public function list_authorized_users( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$authorized_users = $this->db->get_results( "SELECT a.`user_id`, a.`email`, b.`pages`, b.`products`, b.`analytics`, b.`blog`, b.`email_marketing`, b.`shopping_cart` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`role` = 1 $where $order_by LIMIT $limit", ARRAY_A );
		// $authorized_users = $this->db->get_results( "SELECT a.`user_id`, a.`email`, b.`pages`, b.`products`, b.`analytics`, b.`blog`, b.`email_marketing`, b.`shopping_cart` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`role` > 0 $where $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to list authorized users.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $authorized_users;
	}
	
	/**
	 * List Authorized users
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_authorized_users( $where ) {
		$count = $this->db->get_var( "SELECT COUNT( a.`user_id` ) FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`role` = 1 $where" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to count authorized users.', __LINE__, __METHOD__ );
			return false;
		}
			
		return $count;
	}
	
	/**
	 * Create an authorized user
	 *
	 * @param string $email
	 * @param bool $pages
	 * @param bool $products
	 * @param bool $analytics
	 * @param bool $blog
	 * @param bool $email_marketing
	 * @param bool $shopping_cart
	 * @param int $role (optional|1)
	 * @return array
	 */
	public function create( $email, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart, $role = 1 ) {
		global $user, $u;
		
		if ( $au = $u->get_user_by_email( $email, false ) ) {
			if ( $au['role'] > 1 ) return false;
			
			// If they are already authorized, nothing else to do
			if ( $this->is_authorized( $au['user_id'], $user['website']['website_id'] ) )
				return true;
			
			// Add the link
			$this->add_link( $au['user_id'], $user['website']['website_id'], $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart );
			
			// @Translate
			// @Fix very poorly translated email
			// Send them an email letting them know they have a new authorized user
			$from    = $user['website']['title'] . '<' . $user['email'] . '>';
			$headers = 'CC: ' . $user['website']['title'] . '<' . $user['email'] . '> \r\n';
			$subject = $user['website']['title'] . ' has added you as an Authorized User at ' . DOMAIN . '.';
			$message = '<br /><strong>' . $user['website']['title'] . '</strong> is using ' . DOMAIN . ' to build and manage a website. You have been added as an Authorized User to their account.<br /><br />Please click this link to login:<br /><br />';
			$message .= '<a href="http://account.' . DOMAIN . '/login/" title="Login">http://account.' . DOMAIN . '/login/</a>';
			$message .= '<br /><br />Please contact ' . DOMAIN . ' if you have any questions. Thank you for your time.<br /><br />';
			$message .= '<strong>Email:</strong> info@' . DOMAIN . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';
			
			return Emails::template( $email, $subject, $message , $from , $headers );
		} else {
			// Create the user
			$user_id = $u->create_authorized_user( $email, $role );
			
			// Add the link
			$this->add_link( $user_id, $user['website']['website_id'], $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart );
			
			/* Send him an intro email */
			// Create a token
			$t = new Tokens();
			$token = $t->create( $user_id, 'activate-account', 720 );
			
			// Send new email askign them to confirm their account
			$from    = $user['website']['title'] . '<' . $user['email'] . '>';
			$headers = 'CC: ' . $user['website']['title'] . '<' . $user['email'] . '> \r\n';
			$subject = $user['website']['title'] . ' has added you as an Authorized User at ' . DOMAIN . '.';
			$message = '<br /><strong>' . $user['website']['title'] . '</strong> is using ' . DOMAIN . ' to build and manage a website. You have been added as an Authorized User to their account.<br /><br />Please click this link to create your own password:<br /><br />';
			$message .= 'http://account.' . DOMAIN . "/activate-account/?t=$token";
			$message .= '<br /><br />Please contact ' . DOMAIN . ' if you have any questions. Thank you for your time.<br /><br />';
			$message .= '<strong>Email:</strong> info@' . DOMAIN . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';
			
			if ( !Emails::template( $email, $subject, $message, $from, $headers ) )
				$t->delete( $token );
			
			return true;
		}
		
		$authorized_user = $this->db->prepare( 'SELECT a.`user_id`, a.`email`, b.`pages`, b.`products`, b.`analytics`, b.`blog`, b.`email_marketing`, b.`shopping_cart` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`website_id` = ? a.`role` = 1 AND a.`user_id` = ?', 'ii', $user['website']['website_id'], $user_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get authorized user.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $authorized_user;
	}
	
	/**
	 * Update Authorized User
	 *
	 * @param int $user_id
	 * @param bool $pages
	 * @param bool $products
	 * @param bool $analytics
	 * @param bool $blog
	 * @param bool $email_marketing
	 * @param bool $shopping_cart 
	 * @return bool
	 */
	public function update( $user_id, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart ) {
		global $user;
		
		$this->db->update( 'auth_user_websites', array( 'pages' => $pages, 'products' => $products, 'analytics' => $analytics, 'blog' => $blog, 'email_marketing' => $email_marketing, 'shopping_cart' => $shopping_cart ), array( 'user_id' => $user_id, 'website_id' => $user['website']['website_id'] ), 'iiiiii', 'ii' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update authorized user link.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get an Authorized User by ID
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function get( $user_id ) {
		global $user;
		
		$authorized_user = $this->db->prepare( 'SELECT a.`user_id`, a.`email`, b.`pages`, b.`products`, b.`analytics`, b.`blog`, b.`email_marketing`, b.`shopping_cart` FROM `users` AS a LEFT JOIN `auth_user_websites` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`role` = 1 AND a.`user_id` = ? AND b.`website_id` = ?', 'ii', $user_id, $user['website']['website_id'] )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get authorized user.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $authorized_user;
	}
	
	/**
	 * Get the stores belonging to an authorized user
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function get_stores( $user_id ) {
		global $user;
		
		$stores = $this->db->get_col( 'SELECT a.`store_name` FROM `users` AS a LEFT JOIN `websites` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `auth_user_websites` AS c ON ( b.`website_id` = c.`website_id` ) WHERE c.`user_id` = ' . (int) $user_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get stores.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $stores;
	}
	
	/**
	 * Find out if someone is an authorized to a company
	 *
	 * @param int $user_id
	 * @param int $website_id
	 * @return bool
	 */
	private function is_authorized( $user_id, $website_id ) {
		$count = $this->db->prepare( 'SELECT COUNT(`user_id`) FROM `auth_user_websites` WHERE `user_id` = ? AND `website_id` = ?', 'ii', $user_id, $website_id )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to check if user is authorized.', __LINE__, __METHOD__ );
			return false;
		}
		
		return ( $count ) ? true : false;
	}
	
	/**
	 * Add a link
	 * 
	 * @param int $user_id
	 * @param int $website_id
	 * @param bool $pages
	 * @param bool $products
	 * @param bool $analytics
	 * @param bool $blog
	 * @param bool $email_marketing
	 * @param bool $shopping_cart
	 * @return bool
	 */
	private function add_link( $user_id, $website_id, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart ) {
		$this->db->insert( 'auth_user_websites', array( 'user_id' => $user_id, 'website_id' => $website_id, 'pages' => (int)$pages, 'products' => (int)$products, 'analytics' => (int)$analytics, 'blog' => (int)$blog, 'email_marketing' => (int)$email_marketing, 'shopping_cart' => (int)$shopping_cart ), 'iiiiiiii' );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add authorized user link.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete Authorized User
	 *
	 * @param int $user_id
	 * @param int $website_id
	 * @return bool
	 */
	public function delete( $user_id, $website_id ) {
		$this->db->prepare( 'DELETE FROM `auth_user_websites` WHERE `user_id` = ? AND `website_id` = ?', 'ii', $user_id, $website_id )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete authorized user.', __LINE__, __METHOD__ );
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
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}