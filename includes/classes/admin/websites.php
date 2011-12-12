<?php

/**
 * Handles all the website information
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Websites extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create a website
	 *
	 * @param int $user_id
	 * @param int $os_user_id
	 * @param string $domain
	 * @param string $subdomain
	 * @param string $title
	 * @param string $type
	 * @return int|bool
	 */
    public function create( $user_id, $os_user_id, $domain, $subdomain, $title, $type ) {
		$this->db->insert( 'websites', array( 
			'user_id' => $user_id
			, 'os_user_id' => $os_user_id
			, 'domain' => $domain
			, 'subdomain' => $subdomain
			, 'title' => $title
			, 'type' => $type
            , 'status' => 1
			, 'date_created' => dt::now() ), 'iissssis');
		
		// Handle errors
		if ( $this->db->errno() ) {
			$this->err(  'Failed to create website.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Assign the website_id
		$website_id = $this->db->insert_id;
		
		// Create checklist
		$this->db->insert( 'checklists', array( 'website_id' => $website_id, 'type' => 'Website Setup', 'date_created' => dt::now() ), 'iss' );
		
		// Handle errors
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create checklist.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Assign the checklist_id
		$checklist_id = $this->db->insert_id;
		
		// Create checklist items
		$SQL = '';
		
		for ( $i = 1; $i <= 36; $i++ ) {
			if ( !empty( $SQL ) )
				$SQL .= ',';
			
			$SQL .= sprintf( "( %d, %d )", $checklist_id, $i );
		}
		
		// Create checklist items
		$this->db->query( "INSERT INTO `checklist_website_items` ( `checklist_id`, `checklist_item_id` ) VALUES " . $SQL );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to create checklist items', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_id;
    }
	
	/**
	 * Update Website
	 *
	 * @param $website_id
	 * @param array $fields
	 * @param string $fields_safety
	 * @return bool
	 */
	public function update( $website_id, $fields, $fields_safety ) {
		$this->db->update( 'websites', $fields, array( 'website_id' => $website_id ), $fields_safety, 'i' );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Updates a website's header information
	 *
	 * @param string $phone
	 * @param string $logo
	 * @param int $website_id
	 * @return bool
	 */
	public function update_header( $logo, $phone, $website_id ) {
		$this->db->update( 'websites', array( 'logo' => $logo, 'phone' => $phone ), array( 'website_id' => $website_id ), 'ss', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website header', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Get Website
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_website( $website_id ) {
        // Type Juggling
        $website_id = (int) $website_id;

		$website = $this->db->get_row( "SELECT `website_id`, `os_user_id`, `user_id`, `domain`, `subdomain`, `title`, `theme`, `logo`, `phone`, `pages`, `products`, `product_catalog`, `link_brands`, `blog`, `email_marketing`, `shopping_cart`, `seo`, `room_planner`, `craigslist`, `social_media`, `domain_registration`, `additional_email_addresses`, `ga_profile_id`, `ga_tracking_key`, `wordpress_username`, `wordpress_password`, `mc_list_id`, `type`, `version`, `live`, `date_created`, `date_updated`  FROM `websites` WHERE `website_id` = $website_id", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
	}
	
	/**
	 * Get User Websites
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function get_user_websites( $user_id ) {
        // Type Jugglin
        $user_id = (int) $user_id;

		$websites = $this->db->get_results( "SELECT * FROM `websites` WHERE `user_id` = $user_id AND `status` = 1", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get user websites.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
	}
	
	/**
	 * Get all information of the websites
	 *
	 * @param string $where
	 * @param string $order_by
	 * @param string $limit
	 * @return array
	 */
	public function list_websites( $where, $order_by, $limit ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user['company_id'] : $where . ' AND b.`company_id` = ' . $user['company_id'];
		
		// What other sites we might need to omit
		$omit_sites = ( $user['role'] < 8 ) ? ', 96, 114, 115, 116' : '';
		
		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";
				
		// Get the websites
		// $websites = $this->db->get_results( "SELECT a.`website_id`, a.`domain`, a.`title`, a.`products`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, SUM( IF( c.`active` = 1 OR c.`active` IS NULL, 1, 0 ) ) AS used_products FROM `websites` as a INNER JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `website_products` AS c ON ( a.`website_id` = c.`website_id` ) $where GROUP BY a.`website_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );
		// Original version ^, version below omits counting because it's difficult to get a 100% accurate count
		$websites = $this->db->get_results( "SELECT a.`website_id`, IF( '' = a.`subdomain`, a.`domain`, CONCAT( a.`subdomain`, '.', a.`domain` ) ) AS domain, a.`title`, a.`products`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, d.`contact_name` AS online_specialist FROM `websites` as a INNER JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `website_products` AS c ON ( a.`website_id` = c.`website_id` ) LEFT JOIN `users` AS d ON ( a.`os_user_id` = d.`user_id` ) $where AND a.`status` = 1 AND b.`status` = 1 GROUP BY a.`website_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );
		
		foreach ( $websites as &$website ){
			$website_id = $website['website_id'];
			$count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`product_id` ) AS count FROM `website_products` AS a LEFT JOIN `products` AS b ON ( a.`product_id` = b.`product_id` ) WHERE a.`active` = 1 AND b.`publish_visibility` <> 'deleted' AND a.`website_id` = $website_id" );
			$website['used_products'] = $count;
		}
		
		// Handle any error
		if ( $this->db->errno() ) {
			$user_info = '[ ' . implode( ",", $user ) . ' ]';
			$this->err( ( 'Failed to get all websites for user: ' . $user_info ), __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
	}
	
	/**
	 * Count all the websites
	 *
	 * @param string $where
	 * @return array
	 */
	public function count_websites( $where ) {
		global $user;
		
		// If they are below 8, that means they are a partner
		if ( $user['role'] < 8 )
			$where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user['company_id'] : $where . ' AND b.`company_id` = ' . $user['company_id'];
		
		// What other sites we might need to omit
		$omit_sites = ( $user['role'] < 8 ) ? ', 96, 114, 115, 116' : '';
		
		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";
		
		// @Fix -- shouldn't have to count the results
		// Get the website count
		$website_count = count( $this->db->get_results( "SELECT COUNT( a.`website_id` ) FROM `websites` as a INNER JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `website_products` AS c ON ( a.`website_id` = c.`website_id` ) $where AND a.`status` = 1 AND b.`status` = 1 GROUP BY a.`website_id`", ARRAY_A ) );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$user_info = '[ ' . implode( ",", $user ) . ' ]';
			$this->err( 'Failed to count websites.  User info: ' . $user_info, __LINE__, __METHOD__ );
			return false;
		}
		
		return $website_count;
	}
	
	/**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @param string $field
	 * @return bool
	 */
	public function autocomplete( $query, $field ) {
		global $user;
		
		// Construct WHERE
		$where = ( $user['role'] < 8 ) ? ' AND b.`company_id` = ' . $user['company_id'] : '';
		
		// Get results
		$results = $this->db->prepare( "SELECT DISTINCT( a.`$field` ) FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`$field` LIKE ? $where AND a.`website_id` NOT IN ( 96, 114, 115, 116 ) ORDER BY a.`$field`", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Gets the data for an autocomplete online specialists request
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_online_specialists( $query ) {
		// Get results
		$results = $this->db->prepare( "SELECT a.`user_id` AS object_id, a.`contact_name` AS online_specialist FROM `users` AS a LEFT JOIN `websites` AS b ON ( a.`user_id` = b.`os_user_id` ) WHERE a.`contact_name` LIKE ? GROUP BY a.`user_id` ORDER BY a.`contact_name`", 's', $query . '%' )->get_results( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get autocomplete online specialists entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}
	
	/**
	 * Gets a websites FTP data and unencrypts it
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_ftp_data( $website_id ) {
		$ftp_data = $this->db->get_row( 'SELECT `ftp_host`, `ftp_username`, `ftp_password` FROM `websites` WHERE `website_id` = ' . (int) $website_id, ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get FTP data.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $ftp_data;
	}
	
	/**
	 * Gets the Google Analytics data for a website.
	 *
	 * @param int $website_id
	 * @return var
	 */
	public function get_ga_profile_id( $website_id ){
		$website_id = (int) $website_id;	
		$ga_profile_id = $this->db->get_var( "SELECT `ga_profile_id` FROM `websites` WHERE `website_id` = $website_id LIMIT 1" );
		
		if ( !$ga_profile_id ) return false;
		
		return $ga_profile_id;
	}
	
	/**
	 * Gets all the industry ids for a website
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_industries( $website_id ) {
		$industry_ids = $this->db->get_col( 'SELECT `industry_id` FROM `website_industries` WHERE `website_id` = ' . (int) $website_id );
				
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get industries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $industry_ids;
	}
	
	/**
	 * Add industries for a website
	 *
	 * @param array $industry_ids
	 * @param int $website_id
	 * @return bool
	 */
	public function add_industries( $industry_ids, $website_id ) {
		// No nead in going on
		if ( !is_array( $industry_ids ) || 0 >= count( $industry_ids ) )
			return;
		
		// Create empty $SQL variable to be added to
		$SQL = '';
		
		// Create $SQL statement
		foreach ( $industry_ids as $i_id ) {
			if ( !empty( $SQL ) )
				$SQL .= ',';
			
			$SQL .= sprintf( "( %d, %d )", $website_id, $i_id );
		}
		
		// Add industries
		$this->db->query( 'INSERT INTO `website_industries` ( `website_id`, `industry_id` ) VALUES' . $SQL );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get industries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Removes all industries for a website
	 *
	 * @param int $website_id
	 * @return bool
	 */
	public function remove_industries( $website_id ) {
		$this->db->query( 'DELETE FROM `website_industries` WHERE `website_id` = ' . (int) $website_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website industries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Create a website note
	 *
	 * @param int $website_id
	 * @param int $user_id
	 * @param string $message
	 * @return int
	 */
	public function create_note( $website_id, $user_id, $message ) {
		$this->db->insert( 'website_notes', array( 'website_id' => $website_id, 'user_id' => $user_id, 'message' => $message, 'date_created' => dt::now() ), 'iiss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete create website note.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Update a website note
	 *
	 * @param int $website_note_id
	 * @param string $message
	 * @return bool
	 */
	public function update_note( $message, $website_note_id ) {
		global $user;
		
		$this->db->update( 'website_notes', array( 'message' => $message ), array( 'website_note_id' => $website_note_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website note.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete a website note
	 *
	 * @param int $website_note_id
	 * @return bool
	 */
	public function delete_note( $website_note_id ) {
		$this->db->query( 'DELETE FROM `website_notes` WHERE `website_note_id` = ' . (int) $website_note_id );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website note.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Get Notes
	 *
	 * @param int $website_id
	 * @return array
	 */
	public function get_notes( $website_id ) {
		// Typecast it as an int to make sure we have a number
		$website_id = (int) $website_id;
		
		$notes = $this->db->get_results( "SELECT a.`website_note_id`, a.`user_id`, a.`message`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, b.`email`, b.`contact_name`, b.`store_name`, c.`title` FROM `website_notes` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` ) WHERE a.`website_id` = $website_id ORDER BY date_created DESC", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website notes.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $notes;
	}
	
	/**
	 * Installs a website
	 *
	 * @param int $website_id
	 * @param int $industry_id (optional)
	 * @return bool
	 */
	public function install( $website_id, $industry_id = 1 ) {
		// Make sure it has enough memory to install
		ini_set('memory_limit', '256M'); 
		
		// Typecast
		$website_id = (int) $website_id;
		
		$web = $this->get_website( $website_id );
		
		// Add it to the subdomain
		$subdomain = ( !empty( $web['subdomain'] ) ) ? $web['subdomain'] . '/' : '';
		$subdomain2 = ( !empty( $web['subdomain'] ) ) ? '/' . $web['subdomain'] : '';
		
		if ( '0' == $web['version'] ) {
			$ftp_data = $this->get_ftp_data( $website_id );
			
			if ( $ftp_data ) {
				if ( mysql_errno() )
					return false;
				
				// Create website industry
				$this->db->insert( 'website_industries', array( 'website_id' => $website_id, 'industry_id' => $industry_id ), 'ii' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert website industry.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Send .htaccess and config file
				$web_domain = security::decrypt( base64_decode( $ftp_data['ftp_host'] ), ENCRYPTION_KEY );
				$username = security::decrypt( base64_decode( $ftp_data['ftp_username'] ), ENCRYPTION_KEY );
				$password = security::decrypt( base64_decode( $ftp_data['ftp_password'] ), ENCRYPTION_KEY );
				
				$ftp = new FTP( $website_id, "/public_html/" . $subdomain );
				$ftp->cwd = "/public_html/" . $subdomain;
				
				if ( !$ftp->add( OPERATING_PATH . '/media/data/htaccess.txt', '', '.htaccess' ) )
					return false;

				if ( !$ftp->add( OPERATING_PATH . '/media/data/config.php', '' ) )
					return false;
				
				
				// Make the root directory writable
				$ftp->chmod( 0777, '/public_html' . $subdomain2 );
				
				// Get data for SSH
				$svn['un_pw'] = '--username lacky --password KUWrq6RIO_r';
				$svn['repo_url'] = 'https://svn.codespaces.com/imagineretailer/system';
				
				// System version
				$system_version = trim( shell_exec( 'svn ls --no-auth-cache ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/tags | tail -n 1 | tr -d "/"' ) );
				
				// SSH Connection
				$ssh_connection = ssh2_connect( '199.204.138.145', 22 );
				ssh2_auth_password( $ssh_connection, 'root', 'GcK5oy29IiPi' );
				
				// Checkout
				ssh2_exec( $ssh_connection, 'svn checkout ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . "/trunk /home/$username/public_html" . $subdomain2 );
				
				// Install Cron
				ssh2_exec( $ssh_connection, 'echo -e "MAILTO=\"systemadmin@imagineretailer.com\"
0 03 * * * /usr/bin/php /home/' . $username . '/public_html/' . $subdomain . 'core/crons/daily.php > /dev/null
0 23 * * 0 /usr/bin/php /home/' . $username . '/public_html/' . $subdomain . 'core/crons/weekly.php > /dev/null" | crontab' );
				
				// Update config & .htaccess file
				$document_root = '\/home\/' . $username . '\/public_html' . $subdomain2;
				ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/{$subdomain}.htaccess" );
				
				ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/{$subdomain}config.php" );
				ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/$website_id/g' /home/$username/public_html/{$subdomain}config.php" );
				
				// Must use FTP to assign folders under the right user
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/' . $web['theme'] );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/cache' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/cache/css' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/cache/js' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/uploads/' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/uploads/files' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/uploads/images' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/uploads/pdf' );
				$ftp->mkdir( '/public_html/' . $subdomain . 'custom/uploads/video' );
				
				// Change the cache directories to writeable
				$ftp->chmod( 0777, '/public_html/' . $subdomain . 'custom/cache' );
				$ftp->chmod( 0777, '/public_html/' . $subdomain . 'custom/cache/css' );
				$ftp->chmod( 0777, '/public_html/' . $subdomain . 'custom/cache/js' );
				
				// Copy the shopping cart images
				ssh2_exec( $ssh_connection, "cp -R {$document_root}/media/images/shopping_cart/ {$document_root}/custom/uploads/images/shopping_cart/" );
				
				// Copy the newsletter image
				ssh2_exec( $ssh_connection, "cp -R {$document_root}/media/images/buttons/sign-up.png {$document_root}/custom/uploads/images/buttons/sign-up.png" );
				
				// Remove .svn/_notes folders
				ssh2_exec( $ssh_connection, "find {$document_root}/custom/uploads/images/shopping_cart/ -name '.svn' -o -name '_notes' -exec rm -rf {} \;" );
				
				// Change owner of shopping_cart folder
				ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/custom/uploads/images/shopping_cart" );
				
				// Set to normal permissions
				$ftp->chmod( 0755, '/public_html' . $subdomain2 );
				
				// Updated website version
				$this->update_website_version( $system_version, $website_id );
				
				// Insert pages
				$this->db->query( Pre_Data::pages_sql( $website_id ) );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert website pages.', __LINE__, __METHOD__ );
					return false;
				}
				
				$website_page_id = $this->db->get_var( "SELECT `website_page_id` FROM `website_pages` WHERE `website_id` = $website_id AND `slug` = 'sidebar'" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to get website page id.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Insert static sidebar elements
				$this->db->query( Pre_Data::attachments_sql( $website_page_id ) );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert website sidebar attachments.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create website settings
				$this->db->query( "INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES ( $website_id, 'page_room-planner-slug', 'plan-your-room' ), ( $website_id, 'page_room-planner-title', 'Plan Your Room' )" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert website settings.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create default email list
				$email_list_result = $this->db->insert( 'email_lists', array( 'website_id' => $website_id, 'name' => 'Default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert default email list.', __LINE__, __METHOD__ );
					return false;
				}
				
				$email_list_id = $this->db->insert_id;
				
				// Create default email autoresponder
				$this->db->insert( 'email_autoresponders', array( 'website_id' => $website_id, 'email_list_id' => $email_list_id, 'name' => 'Default', 'subject' => $web['title'] . ' - Current Offer', 'message' => '<p>Thank you for signing up for the latest tips, trends and special offers. Here is the current offer from our store.<p><br /><br />', 'current_offer' => 1, 'default' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssiis' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert email autoresponder.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create default email template
				$this->db->insert( 'email_templates', array( 'name' => 'Default', 'template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>[subject]</title><style type="text/css">body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }p { line-height: 21px; padding-bottom: 7px; }h2{ padding:0; margin:0; }td{ font-size: 13px; padding-right: 10px; }li { padding-top: 7px; }</style></head><body>[message]</body></html>', 'type' => 'default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'ssss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert email template result.', __LINE__, __METHOD__ );
					return false;
				}
				
				$email_template_id = $this->db->insert_id;
				
				// Create email template association
				$this->db->insert( 'email_template_associations', array( 'email_template_id' => $email_template_id, 'object_id' => $website_id, 'type' => 'website' ), 'iis' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert email template association.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create default settings
				$this->db->insert( 'email_settings', array( 'website_id' => $website_id, 'key' => 'timezone', 'value' => '-5.0' ), 'iss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->err( 'Failed to insert email setting.', __LINE__, __METHOD__ );
					return false;
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Removes a website
	 *
	 * @param int $website_id
	 * @return bool
	 */
	public function delete( $website_id ) {
		// Typecast
		$website_id = (int) $website_id;

        $this->db->query( "UPDATE `websites` SET `status` = 0 WHERE `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->err( 'Failed to delete website.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
	}
	
	/**
	 * Update website version
	 *
	 * @param string $system_version the current version of the system
	 * @param int $website_id
	 * @return bool
	 */
	public function update_website_version( $system_version, $website_id ) {
		$this->db->update( 'websites', array( 'version' => $system_version ), array( 'website_id' => $website_id ), 's', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website version.', __LINE__, __METHOD__ );
			return false;
		}
				
		return true;
	}
	
	/**
	 * Report
	 *
	 * @param array $criteria (optional|)
	 * @return array
	 */
	public function report( $criteria = '' ) {
		global $user;
		
		if ( empty( $criteria ) && empty( $_SESSION['reports']['where'] ) )
			return false;
		
		// Create the where
		$where = '';
		
		if ( empty( $criteria ) ) {
			$where = $_SESSION['reports']['where'];
		} else {
			if ( is_array( $criteria ) )
			foreach ( $criteria as $type => $type_array ) {
				if ( !is_array( $type_array ) )
					continue;
				
				switch ( $type ) {
					case 'brand':
						$where .= ' AND ( e.`brand_id` IN( ';
						
						$brand_where = '';
						
						foreach ( $type_array as $object_id => $value ) {
							if ( !empty( $brand_where ) )
								$brand_where .= ',';
							
							$brand_where .= (int) $object_id;
						}
						
						$where .= "$brand_where ) )";
					break;
					
					case 'checkboxes':
						$where .= ' AND ( 1';
						
						foreach ( $type_array as $service => $value ) {
							switch ( $service ) {
								case 'additional-email-addresses':
									$where .= ' AND a.`additional-email-addresses` = 1';
								break;
								
								case 'blog':
									$where .= ' AND a.`blog` = 1';
								break;
								
								case 'domain-registration':
									$where .= ' AND a.`domain_registration` = 1';
								break;
								
								case 'email-marketing':
									$where .= ' AND a.`email_marketing` = 1';
								break;
								
								case 'product-catalog':
									$where .= ' AND a.`product_catalog` = 1';
								break;
								
								case 'room-planner':
									$where .= ' AND a.`room_planner` = 1';
								break;
								
								case 'seo':
									$where .= ' AND a.`seo` = 1';
								break;
								
								case 'shopping-cart':
									$where .= ' AND a.`shopping_cart` = 1';
								break;
							}
						}
						
						$where .= " )";
					break;
					
					case 'company':
						$where .= ' AND ( c.`company_id` IN( ';
						
						$company_where = '';
						
						foreach ( $type_array as $object_id => $value ) {
							if ( !empty( $company_where ) )
								$company_where .= ',';
							
							$company_where .= (int) $object_id;
						}
						
						$where .= "$company_where ) )";
					break;
					
					case 'online_specialist':
						$where .= ' AND ( a.`os_user_id` IN( ';
						
						$online_specialist_where = '';
						
						foreach ( $type_array as $object_id => $value ) {
							if ( !empty( $online_specialist_where ) )
								$online_specialist_where .= ',';
							
							$online_specialist_where .= (int) $object_id;
						}
						
						$where .= "$online_specialist_where ) )";
					break;
				}
			}
			
			if ( $user['role'] < 8 ) $where .= " AND b.`company_id` = " . $user['company_id'] . " ";
			$_SESSION['reports']['where'] = $where;
		}
		
		// Title, Company, #of products, Date Signed Up
		$websites = $this->db->get_results( "SELECT a.`domain`, a.`title`, c.`name` AS company, CONCAT( SUM( COALESCE( d.`active`, 0 ) ), ' / ', a.`products` ) AS products, DATE_FORMAT( DATE( a.`date_created` ), '%m-%d-%Y' ) AS date_created FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) LEFT JOIN `website_products` AS d ON ( a.`website_id` = d.`website_id` ) LEFT JOIN `products` AS e ON ( d.`product_id` = e.`product_id` ) LEFT JOIN `brands` AS f ON ( e.`brand_id` = f.`brand_id` ) WHERE 1 $where GROUP BY a.`website_id` ORDER BY a.`title` ASC", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website report.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
	}
	
	/**
	 * Updates all installed websites
	 *
	 * @param bool $live
	 */
	/**
	 * Updates all installed websites
	 *
	 * @param bool $live
	 */
	/**
	 * Updates all installed websites
	 */
	public function upgrade_websites( $live = 0 ) {
		// Typecast
		$live = (int) $live;

		$omit_websites = array(
			96 // Testing
			, 182
		);

		$svn['un_pw'] = '--username lacky --password KUWrq6RIO_r --no-auth-cache';
		$svn['repo_url'] = 'https://svn.codespaces.com/imagineretailer/system';
		$svn['repo_trunk'] = $svn['repo_url'] . '/trunk';
		$svn['repo_tags'] =  $svn['repo_url'] . '/tags';

		$connection = ssh2_connect( '199.204.138.145', 22 );
        if ( !@ssh2_auth_password( $connection, 'root', 'GcK5oy29IiPi' ) )
            return;

		error_reporting( E_ALL );

		$system_version = trim( shell_exec( 'svn ls --no-auth-cache ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/tags' . ' | tail -n 1 | tr -d "/"' ) );

		$websites = $this->db->get_results( "SELECT `website_id`, `ftp_host`, `ftp_username`, `ftp_password`, `theme`, `version` FROM `websites` WHERE `version` <> '0' AND `version` <> '" . $system_version . "' AND `ftp_host` <> '' AND `ftp_username` <> '' AND `ftp_password` <> ''", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get websites for upgrading.', __LINE__, __METHOD__ );
			return false;
		}

		foreach ( $websites as $w ) {
			if ( in_array( $w['website_id'], $omit_websites ) && 1 == version_compare( $system_version, $w['version'] ) )
				continue;

			$username = security::decrypt( base64_decode( $w['ftp_username'] ), ENCRYPTION_KEY );
			echo $username . "<br />\n";

			$stream = ssh2_exec( $connection, 'svn update ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/trunk' . " /home/$username/public_html" );

			if ( !$stream ) {
				$connection = ssh2_connect( '199.204.138.145', 22 );

				if ( !@ssh2_auth_password( $connection, 'root', 'GcK5oy29IiPi' ) )
					die( "Couldn't connect to SSH Tunnel" );

				$stream = ssh2_exec( $connection, 'svn update ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/trunk' . " /home/$username/public_html" );

				if ( !$stream )
					die( "Failed to execute command twice" );
			}

			$stream = ssh2_exec( $connection, "chown -R $username:$username /home/$username/public_html/*" );


			if ( !$stream ) {
				$connection = ssh2_connect( '199.204.138.145', 22 );

				if ( !@ssh2_auth_password( $connection, 'root', 'GcK5oy29IiPi' ) )
					die( "Couldn't connect to SSH Tunnel" );

				$stream = ssh2_exec( $connection, "chown -R $username:$username /home/$username/public_html/*" );

				if ( !$stream )
					die( "Failed to execute command twice" );
			}


			$this->update_website_version( $system_version, $w['website_id'] );

			// Wait .2 seconds before going to the next item;
			//usleep( 200000 );
		}

		return true;
	}
	
	/**
	 * Deletes a website setting
	 *
	 * @param int $website_id
	 * @param string $key
	 * @return bool
	 */
	public function delete_settings( $website_id, $keys ) {
		foreach ( $keys as $key ) {
			$this->db->prepare( "DELETE FROM `website_settings` WHERE `website_id` = $website_id AND `key` = ?", 's', $key )->query('');
		}
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete website settings.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Updates a website setting (or creates it if it doesn't exist)
	 *
	 * @param int $website_id
	 * @param array $keys (an array of $key => $value to insert into the settings )
	 */
	public function update_settings( $website_id, $keys ) {
		// Type Juggling
		$website_id = (int) $website_id;
		
		$values = '';
		
		foreach ( $keys as $k => $v ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $website_id, '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "' )";
		}
		
		// Insert it or update it
		$this->db->query( "INSERT INTO `website_settings` ( `website_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to update website settings.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Gets a website setting
	 *
	 * @param int $website_id
	 * @param array $keys
	 * @return array $results
	 */
	public function get_settings( $website_id, $keys ) {
		if ( !is_array( $keys ) )
			return;
		
		$values = '';
		
		foreach ( $keys as $k ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "'" . $this->db->escape( $k ) . "'";
		}
		
		$settings = $this->db->get_results( "SELECT `key`, `value` FROM `website_settings` WHERE `website_id` = $website_id AND `key` IN ( $values )", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get website settings.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Assign the key
		$settings = ar::assign_key( $settings, 'key', true );
		
		foreach ( $keys as $key ) {
			if ( !isset( $settings[$key] ) )
				$settings[$key] = '';
		}
		
		return $settings;
	}
	
	/**
	 * Deletes the website image dimensions for a URL
	 *
	 * @param string $url
	 * @return bool
	 */
	public function delete_image_dimensions( $url ) {
		$this->db->prepare( 'DELETE FROM `website_image_dimensions` WHERE `image_url` = ?', 's', $url )->query('');
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to delete image dimensions.', __LINE__, __METHOD__ );
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
