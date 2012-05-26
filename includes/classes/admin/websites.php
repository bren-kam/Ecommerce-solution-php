<?php

/**
 * Handles all the website information
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Websites extends Base_Class {
    /**
     * Holds Files
     * @var Files
     */
    private $f;

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
			$this->_err(  'Failed to create website.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Assign the website_id
		$website_id = $this->db->insert_id;
		
		// Create checklist
		$this->db->insert( 'checklists', array( 'website_id' => $website_id, 'type' => 'Website Setup', 'date_created' => dt::now() ), 'iss' );
		
		// Handle errors
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to create checklist.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to create checklist items', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to update website', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to update website header', __LINE__, __METHOD__ );
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

		$website = $this->db->get_row( "SELECT a.`website_id`, a.`company_package_id`, a.`user_id`, a.`os_user_id`, a.`domain`, a.`subdomain`, a.`title`, a.`plan_name`, a.`plan_description`, a.`theme`, a.`logo`, a.`phone`, a.`pages`, a.`products`, a.`product_catalog`, a.`link_brands`, a.`blog`, a.`email_marketing`, a.`mobile_marketing`, a.`shopping_cart`, a.`seo`, a.`room_planner`, a.`craigslist`, a.`social_media`, a.`domain_registration`, a.`additional_email_addresses`, a.`ga_profile_id`, a.`ga_tracking_key`, a.`wordpress_username`, a.`wordpress_password`, a.`mc_list_id`, a.`type`, a.`version`, a.`live`, a.`date_created`, a.`date_updated`, b.`status` AS user_status, c.`name` AS company  FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) WHERE a.`website_id` = $website_id", ARRAY_A );
	
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
	}

    /**
	 * Get website industries
	 *
     * @param int $website_id
	 * @return array
	 */
	public function get_website_industries( $website_id ) {
		// Type Juggling
        $website_id = (int) $website_id;

		$industry_ids = $this->db->get_col( "SELECT `industry_id` FROM `website_industries` WHERE `website_id` = $website_id" );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get industry ids.', __LINE__, __METHOD__ );
			return false;
		}

		return $industry_ids;
	}

    /**
	 * Gets a metadata for a page
	 *
	 * @param int $website_id
	 * @param string $key_1, $key_2, $key_3, etc.
	 * @return array
	 */
	public function get_pagemeta_by_key( $website_id ) {
		// Get the arguments
		$arguments = func_get_args();

		// Needs to have at least two arguments
		if ( count( $arguments ) <= 1 )
			return false;

		// Typecast
		$website_id = (int) array_shift( $arguments );

		// Get keys, escape them and turn them into comma separated values
		array_walk( $arguments, array( $this->db, 'escape' ) );
		$keys = "'" . implode( "', '", $arguments ) . "'";

		// Get the meta data
		$metadata = $this->db->get_results( "SELECT `key`, `value` FROM `website_pagemeta` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) WHERE a.`key` IN ($keys) AND b.`website_id` = $website_id", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get metadata.', __LINE__, __METHOD__ );
			return false;
		}

		// Set the array
		$new_metadata = array_fill_keys( $arguments, '' );

		// Decrypt any meta data
		if ( is_array( $metadata ) )
		foreach ( $metadata as $md ) {
			$new_metadata[$md['key']] = html_entity_decode( $md['value'], ENT_QUOTES, 'UTF-8' );
		}

		return ( 1 == count( $new_metadata ) ) ? array_shift( $new_metadata ) : $new_metadata;
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
			$this->_err( 'Failed to get user websites.', __LINE__, __METHOD__ );
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
		
        if ( 251 == $user['user_id'] ) {
            $where = ( empty( $where ) ) ? ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user['company_id'] . ' )' : $where . ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user['company_id'] . ' )';
        } else {
            // If they are below 8, that means they are a partner
            if ( $user['role'] < 8 )
                $where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user['company_id'] : $where . ' AND b.`company_id` = ' . $user['company_id'];
        }

		// What other sites we might need to omit
		$omit_sites = ( $user['role'] < 8 ) ? ', 96, 114, 115, 116' : '';

		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";
				
		// Get the websites
		$websites = $this->db->get_results( "SELECT a.`website_id`, IF( '' = a.`subdomain`, a.`domain`, CONCAT( a.`subdomain`, '.', a.`domain` ) ) AS domain, a.`title`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, IF ( '' = b.`cell_phone`, b.`work_phone`, b.`cell_phone` ) AS phone, c.`contact_name` AS online_specialist FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) $where GROUP BY a.`website_id` ORDER BY $order_by LIMIT $limit", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$user_info = '[ ' . implode( ",", $user ) . ' ]';
			$this->_err( ( 'Failed to get all websites for user: ' . $user_info ), __LINE__, __METHOD__ );
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
		
        if ( 251 == $user['user_id'] ) {
            $where = ( empty( $where ) ) ? ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user['company_id'] . ' )' : $where . ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user['company_id'] . ' )';
        } else {
            // If they are below 8, that means they are a partner
            if ( $user['role'] < 8 )
                $where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user['company_id'] : $where . ' AND b.`company_id` = ' . $user['company_id'];
        }
		
		// What other sites we might need to omit
		$omit_sites = ( $user['role'] < 8 ) ? ', 96, 114, 115, 116' : '';
		
		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";
		
		// @Fix -- shouldn't have to count the results
		// Get the website count
		$website_count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`website_id` ) FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) $where" );

		// Handle any error
		if ( $this->db->errno() ) {
			$user_info = '[ ' . implode( ",", $user ) . ' ]';
			$this->_err( 'Failed to count websites.  User info: ' . $user_info, __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get autocomplete entries.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get autocomplete online specialists entries.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $results;
	}

    /**
	 * Gets the data for an autocomplete marketing specialists request
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_marketing_specialists( $query ) {
		// Get results
		$results = $this->db->prepare( "SELECT `user_id` AS object_id, `contact_name` AS marketing_specialist FROM `users` WHERE `role` = 6 AND `contact_name` LIKE ? AND `status` = 1 ORDER BY `contact_name`", 's', $query . '%' )->get_results( '', ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get autocomplete marketing specialists entries.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get FTP data.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get industries.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get industries.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to delete website industries.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to delete create website note.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to update website note.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to delete website note.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get website notes.', __LINE__, __METHOD__ );
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
					$this->_err( 'Failed to insert website industry.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Send .htaccess and config file
				$username = security::decrypt( base64_decode( $ftp_data['ftp_username'] ), ENCRYPTION_KEY );

				// SSH Connection
				$ssh_connection = ssh2_connect( '199.79.48.137', 22 );
				ssh2_auth_password( $ssh_connection, 'root', 'WIxp2sDfRgLMDTL5' );
				
                // Copy files
                ssh2_exec( $ssh_connection, "cp -R /gsr/platform/copy/. /home/$username/public_html" . $subdomain2 );

				// Update config & .htaccess file
				$document_root = '\/home\/' . $username . '\/public_html' . $subdomain2;

				ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/{$subdomain}config.php" );
				ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/$website_id/g' /home/$username/public_html/{$subdomain}config.php" );
				
				// Must use FTP to assign folders under the right user
                ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/{$subdomain}custom" );
                ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/{$subdomain}custom/" . $web['theme'] );
                ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/{$subdomain}custom/cache" );
                ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/{$subdomain}custom/cache/css" );
                ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/{$subdomain}custom/cache/js" );

                ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/{$subdomain}custom/cache" );
                ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/{$subdomain}" );

				// Updated website version
				$this->update_website_version( '1', $website_id );
				
				// Insert pages
				$this->db->query( Pre_Data::pages_sql( $website_id ) );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert website pages.', __LINE__, __METHOD__ );
					return false;
				}
				
				$website_page_id = $this->db->get_var( "SELECT `website_page_id` FROM `website_pages` WHERE `website_id` = $website_id AND `slug` = 'sidebar'" );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to get website page id.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Insert static sidebar elements
				$this->db->query( Pre_Data::attachments_sql( $website_page_id ) );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert website sidebar attachments.', __LINE__, __METHOD__ );
					return false;
				}

				// Create default email list
				$this->db->insert( 'email_lists', array( 'website_id' => $website_id, 'name' => 'Default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert default email list.', __LINE__, __METHOD__ );
					return false;
				}
				
				$email_list_id = $this->db->insert_id;
				
				// Create default email autoresponder
				$this->db->insert( 'email_autoresponders', array( 'website_id' => $website_id, 'email_list_id' => $email_list_id, 'name' => 'Default', 'subject' => $web['title'] . ' - Current Offer', 'message' => '<p>Thank you for signing up for the latest tips, trends and special offers. Here is the current offer from our store.<p><br /><br />', 'current_offer' => 1, 'default' => 1, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssiis' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert email autoresponder.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create default email template
				$this->db->insert( 'email_templates', array( 'name' => 'Default', 'template' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>[subject]</title><style type="text/css">body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }p { line-height: 21px; padding-bottom: 7px; }h2{ padding:0; margin:0; }td{ font-size: 13px; padding-right: 10px; }li { padding-top: 7px; }</style></head><body>[message]</body></html>', 'type' => 'default', 'date_created' => dt::date('Y-m-d H:i:s') ), 'ssss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert email template result.', __LINE__, __METHOD__ );
					return false;
				}
				
				$email_template_id = $this->db->insert_id;
				
				// Create email template association
				$this->db->insert( 'email_template_associations', array( 'email_template_id' => $email_template_id, 'object_id' => $website_id, 'type' => 'website' ), 'iis' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert email template association.', __LINE__, __METHOD__ );
					return false;
				}
				
				// Create default settings
				$this->db->insert( 'email_settings', array( 'website_id' => $website_id, 'key' => 'timezone', 'value' => 'America/New_York' ), 'iss' );
				
				// Handle any error
				if ( $this->db->errno() ) {
					$this->_err( 'Failed to insert email setting.', __LINE__, __METHOD__ );
					return false;
				}
			}
			
			return true;
		} else {
			return false;
		}
	}

    /**
     * Install a Package
     *
     * @param int $website_id
     * @return bool
     */
    public function install_package( $website_id ) {
        $website = $this->get_website( $website_id );
		$company_package_id = (int) $website['company_package_id'];
		
        // Get the package
        $package = $this->db->get_row( "SELECT `name`, `website_id` FROM `company_packages` WHERE `company_package_id` = $company_package_id", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get package.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->copy_website( $package['website_id'], $website_id );
	}
	
	/**
	 * Copy a Website
	 *
	 * The original website will becalled the "Template website
	 *
	 * @param int $template_website_id
	 * @param int $website_id
     * @return bool
	 */
	public function copy_website( $template_website_id, $website_id ) {
		$ftp = $this->get_ftp_data( $website_id );
        $username = security::decrypt( base64_decode( $ftp['ftp_username'] ), ENCRYPTION_KEY );
        
        // Get the template website
        $template_website = $this->get_website( $template_website_id );
        $template_theme = $template_website['theme'];
		
		
        // Set the theme of the current website
        $this->update( $website_id, array( 'theme' => $template_theme, 'logo' => $template_website['logo'] ), 'ss' );

        // Get the username
        $template_ftp = $this->get_ftp_data( $template_website_id );
        $template_username = security::decrypt( base64_decode( $template_ftp['ftp_username'] ), ENCRYPTION_KEY );
		
        /* Copy over the folders */

        // SSH Connection
        $ssh_connection = ssh2_connect( '199.79.48.137', 22 );
        ssh2_auth_password( $ssh_connection, 'root', 'WIxp2sDfRgLMDTL5' );
		
        // Make The new theme directory
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/$template_theme" );

        // Copy over all the theme files
        ssh2_exec( $ssh_connection, "cp -Rf /home/$template_username/public_html/custom/. /home/$username/public_html/custom" );

		// Copy over config file
        ssh2_exec( $ssh_connection, "yes | cp -rf /home/$template_username/public_html/config.php /home/$username/public_html/config.php" );
		
		ssh2_exec( $ssh_connection, "sed -i 's/$template_username/$username/g' /home/$username/public_html/config.php" );
		ssh2_exec( $ssh_connection, "sed -i 's/$template_website_id/$website_id/g' /home/$username/public_html/config.php" );
		
        /***** Copy Website Pages *****/
		
        $this->db->copy( 'website_pages', array(
            'website_id' => $website_id
            , 'slug' => NULL
            , 'title' => NULL
            , 'content' => NULL
            , 'meta_title' => NULL
            , 'meta_description' => NULL
            , 'meta_keywords' => NULL
            , 'mobile' => NULL
            , 'date_created' => "'" . dt::date('Y-m-d H:i:s') . "'"
        ), array( 'website_id' => $template_website_id ) );
		
		
        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy website pages.', __LINE__, __METHOD__ );
			return false;
		}

		/***** Copy Website Attachments *****/
		
        $website_pages = $this->db->get_results( "SELECT `website_page_id`, `slug` FROM `website_pages` WHERE `website_id` = $website_id" );
		
        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website pages.', __LINE__, __METHOD__ );
			return false;
		}
		
        $website_pages = ar::assign_key( $website_pages, 'slug', true );

        // First get website pages
        $template_website_pages = $this->db->get_results( "SELECT `website_page_id`, `slug` FROM `website_pages` WHERE `website_id` = $template_website_id" );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get template website pages.', __LINE__, __METHOD__ );
			return false;
		}
		
        // Assigned the keys
        $template_website_pages = ar::assign_key( $template_website_pages, 'website_page_id', true );

        // Now, get the attachments
        $template_website_attachments = $this->db->get_results( 'SELECT `website_page_id`, `key`, `value`, `extra`, `meta`, `sequence` FROM `website_attachments` WHERE `status` = 1 AND `website_page_id` IN (' . implode( ', ', array_keys( $template_website_pages ) ) . ')' );
		
        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get template website attachments.', __LINE__, __METHOD__ );
			return false;
		}
		
        $new_website_attachments = array();

        if ( is_array( $template_website_attachments )  )
        foreach ( $template_website_attachments as $twa ) {
            $website_page_id = (int) $website_pages[$template_website_pages[$twa['website_page_id']]];

            // Checks if its S# and uploads it
            $value = $this->_check_s3( $website_id, $twa['value'], 'websites' );

            $new_website_attachments[] = "( $website_page_id, '" . $twa['key'] . "', '" . $this->db->escape( $value ) . "', '" . $this->db->escape( $twa['extra'] ) . "', '" . $this->db->escape( $twa['meta'] ) . "', " . $twa['sequence'] . ' )';
        }
		
        if ( 0 != count( $new_website_attachments ) ) {
            // Delete certain sidebar elements that you can only have one of
            $this->db->query( "DELETE FROM `website_attachments` WHERE `key` IN( 'video', 'search', 'email' ) AND `website_page_id` IN( " . implode( ", ", array_values( $website_pages ) ) . ")" );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to delete old sidebar elements.', __LINE__, __METHOD__ );
                return false;
            }

            // Insert them into the database
            $this->db->query( "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value`, `extra`, `meta`, `sequence` ) VALUES " . implode( ', ', $new_website_attachments ) );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to insert website attachments.', __LINE__, __METHOD__ );
                return false;
            }
			
        }

        /***** Copy Website Industries *****/
		
        $this->db->copy( 'website_industries', array( 'website_id' => $website_id, 'industry_id' => NULL ), array( 'website_id' => $template_website_id ) );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy website industries.', __LINE__, __METHOD__ );
			return false;
		}

        /***** Copy Website PageMeta *****/
		
        $pagemeta_keys = array( 'display-coupon', 'email-coupon', 'hide-all-maps' );
		$template_website_page_ids = implode( ', ', array_keys( $template_website_pages ) );
		
        $template_pagemeta = $this->db->get_results( "SELECT `website_page_id`, `key`, `value` FROM `website_pagemeta` WHERE `website_page_id` IN ( $template_website_page_ids ) AND `key` IN( '" . implode ( "', '", $pagemeta_keys ) . "' )" );
		
        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get template pagemeta.', __LINE__, __METHOD__ );
			return false;
		}
		
        $new_pagemeta = array();

        if ( is_array( $template_pagemeta )  )
        foreach ( $template_pagemeta as $tpm ) {
             $website_page_id = (int) $website_pages[$template_website_pages[$template_pagemeta['website_page_id']]];

            $new_pagemeta[] = "( $website_page_id, '" . $tpm['key'] . "', '" . $this->db->escape( $tpm['value'] ) . "' )";
        }
		
        if ( 0 != count( $new_pagemeta ) ) {
            // Insert them into the database
            $this->db->query( "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES " . implode( ', ', $new_pagemeta ) . " ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )" );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to insert website pagemeta.', __LINE__, __METHOD__ );
                return false;
            }
        }

        /***** Copy Website Brands *****/
		
        $this->db->copy( 'website_top_brands', array( 'website_id' => $website_id, 'brand_id' => NULL, 'sequence' => NULL ), array( 'website_id' => $template_website_id ) );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy website top brands.', __LINE__, __METHOD__ );
			return false;
		}

        /***** Copy Website Products *****/
		
		$this->db->copy( 'website_products', array( 
			'website_id' => $website_id
			, 'product_id' => NULL
			, 'status' => NULL
			, 'on_sale' => NULL
			, 'sequence' => NULL
			, 'active' => 1
		), array( 'website_id' => $template_website_id, 'active' => 1 ) );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy website products.', __LINE__, __METHOD__ );
			return false;
		}
		
        $p = new Products();
		
        $p->reorganize_categories( $website_id );

		/***** Copy Website Settings *****/
		$website_settings_array = array( 'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-effect', 'banner-hide-scroller', 'sidebar-image-width' );
		
		$this->db->copy( 'website_settings', array( 'website_id' => $website_id, 'key' => NULL, 'value' => NULL ), array( 'website_id' => $template_website_id, 'key' => $website_settings_array ) );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to copy website settings.', __LINE__, __METHOD__ );
			return false;
		}
		
        return true;
    }

    /**
     * Checks S3
     *
     * If a value is in Amazon S3, if it is, then it will copy it to the given website
     *
     * @param int $website_id
     * @param string $value
     * @param string $bucket
     * @return mixed
     */
    private function _check_s3( $website_id, $value, $bucket ) {
        if ( !stristr( $value, 'retailcatalog.us' ) )
            return $value;

        if ( !isset( $this->f ) )
            $this->f = new Files;

        $new_url = $this->f->copy_file( $website_id, $value, $bucket );

        if ( $new_url ) {
            $this->db->insert( 'website_files', array( 'website_id' => $website_id, 'file_path' => $new_url, 'date_created' => dt::date('Y-m-d H:i:s') ), 'iss' );

            // Handle any error
            if ( $this->db->errno() ) {
                $this->_err( 'Failed to add website file.', __LINE__, __METHOD__ );
                return false;
            }
        }

        return $new_url;
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
            $this->_err( 'Failed to delete website.', __LINE__, __METHOD__ );
            return false;
        }

        return true;
	}

    /**
	 * Deletes all the categories and products from a website
	 *
	 * @param int $website_id
	 * @return bool
	 */
	public function delete_categories_and_products( $website_id ) {
		// Typecast
		$website_id = (int) $website_id;

        $this->db->query( "DELETE FROM `website_categories` WHERE `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to delete website categories.', __LINE__, __METHOD__ );
            return false;
        }

        $this->db->query( "DELETE FROM `website_products` WHERE `website_id` = $website_id" );

        // Handle any error
        if ( $this->db->errno() ) {
            $this->_err( 'Failed to delete website products.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to update website version.', __LINE__, __METHOD__ );
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

                                case 'mobile-marketing':
                                    $where .= ' AND a.`mobile_marketing` = 1';
                                break;

								case 'product-catalog':
									$where .= ' AND a.`product_catalog` = 1';
								break;
								
								case 'room-planner':
									$where .= ' AND a.`room_planner` = 1';
								break;

                                case 'craigslist':
                                    $where .= ' AND a.`craigslist` = 1';
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

                    case 'marketing_specialist':
                        $where .= ' AND ( b.`role` = 6 AND b.`user_id` IN( ';

                        $marketing_specialist_where = '';

                        foreach ( $type_array as $object_id => $value ) {
                            if ( !empty( $marketing_specialist_where ) )
                                $marketing_specialist_where .= ',';

                            $marketing_specialist_where .= (int) $object_id;
                        }

                        $where .= "$marketing_specialist_where ) )";
                    break;


                    case 'billing_state':
                        $where .= ' AND b.`billing_state` IN( ';

                        $state_where = '';
                        
                        foreach ( $type_array as $object_id => $value ) {
                            if ( !empty( $state_where ) )
                                $state_where .= ',';

                            $state_where .= "'" . $this->db->escape( $object_id ) . "'";
                        }

                        $where .= "$state_where )";
					break;

                    case 'package':
                        $where .= ' AND ( a.`company_package_id` IN( ';

						$company_package_where = '';

						foreach ( $type_array as $object_id => $value ) {
							if ( !empty( $company_package_where ) )
								$company_package_where .= ',';

							$company_package_where .= (int) $object_id;
						}

						$where .= "$company_package_where ) )";
                    break;
				}
            }
			
			if ( $user['role'] < 8 )
                $where .= " AND b.`company_id` = " . $user['company_id'] . " ";

            $_SESSION['reports']['where'] = $where;
		}
		
		// Title, Company, #of products, Date Signed Up
		$websites = $this->db->get_results( "SELECT a.`website_id`, a.`domain`, a.`title`, c.`name` AS company, CONCAT( SUM( COALESCE( d.`active`, 0 ) ), ' / ', a.`products` ) AS products, DATE_FORMAT( DATE( a.`date_created` ), '%m-%d-%Y' ) AS date_created FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) LEFT JOIN `website_products` AS d ON ( a.`website_id` = d.`website_id` ) LEFT JOIN `products` AS e ON ( d.`product_id` = e.`product_id` ) LEFT JOIN `brands` AS f ON ( e.`brand_id` = f.`brand_id` ) WHERE a.`status` = 1 $where GROUP BY a.`website_id` ORDER BY a.`title` ASC", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website report.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $websites;
	}

	/**
	 * Updates all installed websites
	 *
	 * @param bool $live
     * @return bool
	 */
	public function upgrade_websites( $live = false ) {
		// Typecast
		$live = (int) $live;

		$omit_websites = array(
			96 // Testing
			, 182
		);

		$svn['un_pw'] = '--username lacky --password KUWrq6RIO_r --no-auth-cache';
		$svn['repo_url'] = 'http://svn.codespaces.com/imagineretailer/system';
		$svn['repo_trunk'] = $svn['repo_url'] . '/trunk';
		$svn['repo_tags'] =  $svn['repo_url'] . '/tags';

		// SSH Connection
        $connection = ssh2_connect( '199.79.48.137', 22 );
        if ( !@ssh2_auth_password( $connection, 'root', 'WIxp2sDfRgLMDTL5' ) )
            return;

		error_reporting( E_ALL );

		$system_version = trim( shell_exec( 'svn ls --no-auth-cache ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/tags' . ' | tail -n 1 | tr -d "/"' ) );

		$websites = $this->db->get_results( "SELECT `website_id`, `ftp_host`, `ftp_username`, `ftp_password`, `theme`, `version` FROM `websites` WHERE `version` <> '0' AND `version` <> '" . $system_version . "' AND `ftp_host` <> '' AND `ftp_username` <> '' AND `ftp_password` <> ''", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get websites for upgrading.', __LINE__, __METHOD__ );
			return false;
		}

		foreach ( $websites as $w ) {
			if ( in_array( $w['website_id'], $omit_websites ) && 1 == version_compare( $system_version, $w['version'] ) )
				continue;

			$username = security::decrypt( base64_decode( $w['ftp_username'] ), ENCRYPTION_KEY );
			echo $username . "<br />\n";

			$stream = ssh2_exec( $connection, 'svn update ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/trunk' . " /home/$username/public_html" );

			if ( !$stream ) {
				$connection = ssh2_connect( '199.79.48.137', 22 );

				if ( !@ssh2_auth_password( $connection, 'root', 'WIxp2sDfRgLMDTL5' ) )
					die( "Couldn't connect to SSH Tunnel" );

				$stream = ssh2_exec( $connection, 'svn update ' . $svn['un_pw'] . ' ' . $svn['repo_url'] . '/trunk' . " /home/$username/public_html" );

				if ( !$stream )
					die( "Failed to execute command twice" );
			}

			$stream = ssh2_exec( $connection, "chown -R $username:$username /home/$username/public_html/*" );


			if ( !$stream ) {
				$connection = ssh2_connect( '199.79.48.137', 22 );

				if ( !@ssh2_auth_password( $connection, 'root', 'WIxp2sDfRgLMDTL5' ) )
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
	 * @param string $keys
	 * @return bool
	 */
	public function delete_settings( $website_id, $keys ) {
		foreach ( $keys as $key ) {
			$this->db->prepare( "DELETE FROM `website_settings` WHERE `website_id` = $website_id AND `key` = ?", 's', $key )->query('');
		}
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete website settings.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Updates a website setting (or creates it if it doesn't exist)
	 *
	 * @param int $website_id
	 * @param array $keys (an array of $key => $value to insert into the settings )
     * @return bool
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
			$this->_err( 'Failed to update website settings.', __LINE__, __METHOD__ );
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
			$this->_err( 'Failed to get website settings.', __LINE__, __METHOD__ );
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
	 * Get Website Setting
	 *
     * @param int $website_id
	 * @param string $key
	 * @return string
	 */
	public function get_setting( $website_id, $key ) {
		$value = $this->db->prepare( 'SELECT `value` FROM `website_settings` WHERE `website_id` = ? AND `key` = ?', 'is', $website_id, $key )->get_var('');

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get website setting.', __LINE__, __METHOD__ );
			return false;
		}

		return $value;
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
			$this->_err( 'Failed to delete image dimensions.', __LINE__, __METHOD__ );
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
