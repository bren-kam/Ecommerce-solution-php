<?php
/**
 * Handles all the Mobile Marketing
 *
 * Interact with Avid Mobile API wrapper
 * @package Grey Suit Retail
 * @since 1.0
 */
class Mobile_Marketing extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Create a Trumpia Account
	 *
     * @param int $website_id
     * @param string $mobile_plan_id
     * @return Response
	 */
	public function create_trumpia_account( $website_id, $mobile_plan_id ) {
		global $u;

        $w = new Websites;
        $i = new Industries();
        $c = new Curl();

		$website = $w->get_website( $website_id );
        $user = $u->get_user( $website['user_id'] );

        $this->_login( $c );

        // Now that we're logged in, lets create the account
        $industries = $w->get_industries( $website_id );
        $industry = $i->get( $industries[0] );
        $timezone_object = new DateTimeZone( $w->get_setting( $website_id, 'timezone' ) );
        $timezone = $timezone_object->getOffset( new DateTime( 'now', $timezone_object ) ) / 3600;
        $password = security::generate_password();

        if ( empty( $timezone ) || 0 === $timezone || -12 === $timezone )
            $timezone = -5;

        // Move to right format
        $timezone *= 1000;

        if ( $timezone > -10000 && $timezone < 0 ) {
            $timezone = '-0' . ( $timezone * -1 );
        } elseif ( 0 === $timezone ) {
            $timezone = '+00000';
        } elseif ( $timezone > 0 && $timezone < 10000 ) {
            $timezone = '+0' . $timezone;
        } else {
            $timezone = '+' . $timezone;
        }

        list( $first_name, $last_name ) = explode( ' ', $user['contact_name'] );
        $mobile = ( empty( $user['cell_phone'] ) ) ? $user['cell_phone'] : $user['work_phone'];

        if ( empty( $mobile ) )
            $mobile = '8185551234';

		$post_fields = array(
            'wlw_uid' => 7529
            , 'mode' => 'signup'
			, 'promo_code' => ''
            , 'username' => format::slug( $website['title'] )
            , 'password1' => $password
            , 'password2' => $password
			, 'organization_name' => $website['title']
			, 'firstname' => $first_name
			, 'lastname' => $last_name
			, 'email' => 'mobile@' . url::domain( $website['domain'], false )
			, 'mobile' => $mobile
            , 'industry' => $industry
            , 'timezone' => $timezone
            , 'send_confirmation' => 0
            , 'send_welcome' => 0
        );

        $page = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_createCustomer.php', $post_fields );

        $success = preg_match( '/action="[^"]+"/', $page );

        if ( !$success )
            return new Response( false, _('Failed to create Trumpia customer') );

        // Get Member's User ID
        $list_page = $c->get( 'http://greysuitmobile.com/admin/MemberManagement/memberSearch.php?mode=&plan=&status=&radio_memberSearch=2&search=' . urlencode( $user['email'] ) . '&x=28&y=15' );

        // Isolate the user ID
        preg_match( "/uid=([0-9]+)/", $list_page, $matches );

        // Get USER ID
        $user_id = $matches[1];

        // Get mobile plan
        $plan = $this->get_plan( $mobile_plan_id );

        // Determine how many more credits they need
        $plus_credits = $plan['credits'] - 10;

        // Update Plan
        $update_plan_fields = array(
            'mode' => 'updatePlan'
            , 'member_uid' => $user_id
            , 'arg1' => $plan['trumpia_plan_id']
        );

        $update_plan = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $update_plan_fields );

        $success = '<script type="text/javascript">history.go(-1);</script>' == $update_plan;

        if ( !$success )
            return new Response( false, _("Failed to update customer's plan") );

        // Update Credits
        $update_credits_fields = array(
            'mode' => 'addCredit'
            , 'member_uid' => $user_id
            , 'arg1' => $plus_credits
        );

        $update_credits = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $update_credits_fields );

        $success = '<script type="text/javascript">history.go(-1);</script>' == $update_credits;

        if ( !$success )
            return new Response( false, _("Failed to update customer's credits") );

        // Create API Key
        $api_fields = array(
            'mode' => 'createAPIKey'
            , 'member_uid' => $user_id
            , 'arg1' => $user_id
        );

        $api_creation = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $api_fields );

        $success = preg_match( '/action="[^"]+"/', $api_creation );

        if ( !$success )
            return new Response( false, _('Failed to create API key') );

        // Assign API to All IP Addresses
        $assign_ip_fields = array(
            'mode' => 'updateAPIKey'
            , 'member_uid' => $user_id
            , 'ipType' => 'ip'
            , 'ip1' => '199'
            , 'ip2' => '79'
            , 'ip3' => '48'
            , 'ip4' => '137'
        );

        $update_api = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_apiCustomers.php', $assign_ip_fields );

        $success = preg_match( '/action="[^"]+"/', $update_api );

        if ( !$success )
            return new Response( false, _('Failed to update API Key to the right IP address') );

        // Get API Key
        $api_page = $c->get( 'http://greysuitmobile.com/admin/MemberManagement/apiCustomers.php' );

        preg_match( '/' . $user_id . '\'\)"><\/td>\s*<td>([^<]+)</', $api_page, $matches );
        $api_key = $matches[1];

        // Update the setting with the API Key. YAY!
        $w->update_settings( $website_id, array( 'trumpia-api-key' => $api_key, 'trumpia-user-id' => $user_id, 'mobile-plan-id' => $mobile_plan_id ) );

        // Now we want to create the home page for mobile
        $this->db->insert( 'mobile_pages', array( 'website_id' => $website_id, 'slug' => 'home', 'title' => 'Home', 'date_created' => dt::date('Y-m-d H:i:s') ), 'isss' );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to insert mobile page.', __LINE__, __METHOD__ );
			return new Response( false, _('Failed to create Mobile Home page') );
		}

        // We need to get their DNS zone if they are live
        if ( '1' == $website['live'] && '1' == $website['pages'] ) {
            library('r53');
            $r53 = new Route53( config::key('aws_iam-access-key'), config::key('aws_iam-secret-key') );

            $zone_id = $w->get_setting( $website_id, 'r53-zone-id' );
            $r53->changeResourceRecordSets( $zone_id, array( $r53->prepareChange( 'CREATE', 'm.' . url::domain( $website['domain'], false ) .'.', 'A', '14400', '199.79.48.138' ) ) );

            // We need to create their subdomain
            $ftp = $w->get_ftp_data( $_GET['wid'] );
            $username = security::decrypt( base64_decode( $ftp['ftp_username'] ), ENCRYPTION_KEY );

            // Load cPanel API
            library('cpanel-api');
            $cpanel = new cPanel_API( $username );

            // Add the subdomain
            $cpanel->add_subdomain( url::domain( $website['domain'], false ), 'm', 'public_html' );
        }

        return new Response( true );
	}

    /**
     * Get Plans
     *
     * @return array
     */
    public function get_plans() {
        $plans = $this->db->get_results( 'SELECT * FROM `mobile_plans`', ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get plans.', __LINE__, __METHOD__ );
			return false;
		}

        return $plans;
    }

    /**
     * Get Plan
     *
     * @param int $mobile_plan_id
     * @return array
     */
    public function get_plan( $mobile_plan_id ) {
        // Type Juggling
        $mobile_plan_id = (int) $mobile_plan_id;

        $plan = $this->db->get_row( "SELECT `trumpia_plan_id`, `name`, `credits`, `keywords` FROM `mobile_plans` WHERE `mobile_plan_id` = $mobile_plan_id", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get plan.', __LINE__, __METHOD__ );
			return false;
		}

        return $plan;
    }

    /**
     * Synchronize Account Contacts
     *
     * @return bool
     */
    public function synchronize_contacts() {
        // Instantiate Classes
        $c = new Curl();
        $w = new Websites;

		// Include the library
        library('trumpia');
		
        $user_ids = $this->db->get_results( "SELECT `website_id`, `value` FROM `website_settings` WHERE `key` = 'trumpia-user-id'", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get user_ids.', __LINE__, __METHOD__ );
			return false;
		}

        $user_ids = ar::assign_key( $user_ids, 'website_id', true );

        if ( !is_array( $user_ids ) || 0 == count( $user_ids ) )
            return false;

        // Login
        // Login to Grey Suit Apps
        $login_fields = array(
            'username' => config::key('trumpia-admin-username')
            , 'password' => config::key('trumpia-admin-password')
        );
		
        $c->post( 'http://greysuitmobile.com/admin/action/action_login.php', $login_fields );

        foreach ( $user_ids as $website_id => $uid ) {
			// Trying to set timeout limit
			set_time_limit(300);
			
            // Set settings
            $c->get( "http://greysuitmobile.com/main/action/action_main.php?mode=x__admin_signin&uid=$uid" );

            $page = $c->get( 'http://greysuitmobile.com/manageContacts/export_contacts_step1.php' );
            preg_match_all( '/option value="([^"]+)"/', $page, $matches );

            $list_ids = $matches[1];

            if ( !is_array( $list_ids ) || 0 == count( $list_ids ) )
                continue;

            /**
             * Intermediate Step -- not necessary
             *
             $post_fields = array(
                'mylist' => ''
                , 'addlist' => ''
                , 'normal_data' => array( '5' ) // Lists
                , 'contact_data' => array( '2' ) // Phone Number
                , 'mode' => 'create'
                , 'selected_list' => implode( ',', $list_ids )
            );

            $page = $c->post( 'http://greysuitmobile.com/manageContacts/export_contacts_result.php', $post_fields );
            */

            $post_fields = array(
                'exportCount' => '1'
                , 'export_list' => '["' . implode( '","', $list_ids ) . '"]'
                , 'normal_data' => '["5"]' // Lists
                , 'contact_data' => '["2"]' // Phone Number
                , 'custom_data' => 'null'
                , 'total_subscription_selected' => '1'
                , 'subscription_opted_private' => '1'
            );

            $page = $c->post( 'greysuitmobile.com/manageContacts/action/action_contacts_export.php', $post_fields );

            $success = preg_match( '/action="[^"]+"/', $page );

            if ( !$success )
                //return false;
			
			// This is done to create a lag between export and download -- see function for more information
			if ( isset( $values ) && isset( $last_website_id ) && $last_website_id )
				$this->_update_mobile_subscribers( $last_website_id, $values, $w );
			
            $page = $c->get( 'http://greysuitmobile.com/manageContacts/manage_contact_summary.php' );

            preg_match( '/export_contacts_result\.php\?uid=([0-9]+)/', $page, $matches );

            $export_id = $matches[1];
			
			// Give it some more time
			if ( isset( $excel_file_handle ) ) {
				fclose( $excel_file_handle );
				unlink( $excel_file );
			}
			
            $excel_file = tempnam( sys_get_temp_dir(), 'gsr_' );
            $excel_file_handle = fopen( $excel_file, 'w+' );
			
			$page = $c->save_file( "http://greysuitmobile.com/manageContacts/action/action_export_download.php?uid=$export_id", $excel_file_handle );
            
            // Load excel reader
            library('Excel_Reader/Excel_Reader');
            $er = new Excel_Reader();
            // Set the basics and then read in the rows
            $er->setOutputEncoding('ASCII');
            $er->read( $excel_file );
			
			$values = array();
			$subscribers = $er->sheets[0]['cells'];
			
			if ( is_array( $subscribers ) && count( $subscribers ) > 1 ) {
				array_shift( $subscribers );
			
				foreach ( $subscribers as $s ) {
					$mobile_number = $s[2];
					
					$lists = explode( ',', $s[1] );
					
					foreach( $lists as $key => &$l ) {
						if ( empty( $l ) ) {
							unset( $lists[$key] );
						}
						
						$l = preg_replace( '/^[0-9]+\s/', '', $l );
					}
					
					$values[$this->db->escape( $mobile_number )] = array_values( $lists );
				}
			}
			
			// Set the last website_id
			$last_website_id = $website_id;
		}
		
		if ( isset( $excel_file_handle ) ) {
			fclose( $excel_file_handle );
			unlink( $excel_file );
		}
		
		// Do the last one
		if ( isset( $values ) && isset( $last_website_id ) && $last_website_id )
			$this->_update_mobile_subscribers( $last_website_id, $values, $w );
    }

    /**
     * Login to the backend
     *
     * @param object $c Curl object
     * @return string
     */
    private function _login( $c ) {
         // Login to Grey Suit Apps
        $login_fields = array(
            'username' => config::key('trumpia-admin-username')
            , 'password' => config::key('trumpia-admin-password')
        );

        return $c->post( 'http://greysuitmobile.com/admin/action/action_login.php', $login_fields );
    }
	
	/**
	 * Update Mobile Subscribers
	 * 
	 * This will only run the second time around and is done so that there is a slight lag between when the export 
	 * of the contact lists is initiated (command above) and when they download it (command below), which will 
	 * hopefully minimize any grabbing of old lists. If the list is alrge it may take a couple of seconds, hopefully
	 * this will slow it down just a little while
	 *
	 * If this doesn't work, we can go through this loop twice, initiating the export, then second time we go through
	 * the loop of all the sites we download it
	 *
	 * @param int $website_id
	 * @param array $values
	 * @param object $w (Websites)
	 * @return bool
	 */
	private function _update_mobile_subscribers( $website_id, $values, $w ) {
		// Type Juggling
		$website_id = (int) $website_id;
		
		// Now clear out the database and then readd subscribers
		$this->db->update( 'mobile_subscribers', array( 'status' => 0 ), array( 'website_id' => $website_id ), 'i', 'i' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to unsubscribe subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		$mobile_numbers = array_keys( $values );
		
		$subscriber_values = "( $website_id, '" . implode( "', NOW(), NOW() ),( $website_id, '", $mobile_numbers ) . "', NOW(), NOW() )";
		
		$this->db->query( "INSERT INTO `mobile_subscribers` (`website_id`, `phone`, `date_created`, `date_synced`) VALUES $subscriber_values ON DUPLICATE KEY UPDATE `status` = 1, `date_synced` = NOW()" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to mass subscribe subscribers.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Now remove all lists from subscribers who have no status
		$this->db->query( "DELETE a.* FROM `mobile_associations` AS a LEFT JOIN `mobile_subscribers` AS b ON ( a.`mobile_subscriber_id` = b.`mobile_subscriber_id` ) WHERE ( b.`mobile_subscriber_id` IS NULL OR b.`status` = 0 ) AND b.`website_id` = $website_id" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete mobile subscriber associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get all of the lists with this account
		$mobile_lists = $this->db->get_results( "SELECT `mobile_list_id`, `name` FROM `mobile_lists` WHERE `website_id` = $website_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get mobile lists.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get Subscribers
		$mobile_subscribers = $this->db->get_results( "SELECT `mobile_subscriber_id`, `phone` FROM `mobile_subscribers` WHERE `website_id` = $website_id AND `phone` IN('" . implode( "','", $mobile_numbers ) . "')", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get mobile subscrbers.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Get Mobile Associations
		$full_mobile_associations = $this->db->get_results( "SELECT a.* FROM `mobile_associations` AS a LEFT JOIN `mobile_subscribers` AS b ON ( a.`mobile_subscriber_id` = b.`mobile_subscriber_id` ) WHERE b.`website_id` = $website_id AND b.`status` = 1", ARRAY_A );

		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get mobile assocations.', __LINE__, __METHOD__ );
			return false;
		}
		
		$mobile_associations = array();
		// Format the mobile associations so we can find them
		if ( is_array( $full_mobile_associations ) )
		foreach ( $full_mobile_associations as $fma ) {
			$mobile_associations[$fma['mobile_subscriber_id'] . '-' . $fma['mobile_list_id']] = $fma['trumpia_contact_id'];
		}

		$mobile_lists = ar::assign_key( $mobile_lists, 'name', true );
		$mobile_subscribers = ar::assign_key( $mobile_subscribers, 'phone', true );
		$association_values = array();
		
		// Get the API Key
        $api_key = $w->get_setting( $website_id, 'trumpia-api-key' );
		
		// Setup Trumpia
        $trumpia = new Trumpia( $api_key );

		
		// Now we have to readd them
		foreach ( $values as $mobile_number => $lists ) {
			// Want to make sure we can get the subscriber
			if ( isset( $mobile_subscribers[$mobile_number] ) ) {
				$mobile_subscriber_id = (int) $mobile_subscribers[$mobile_number];
			} else {
				continue;
			}
			
			foreach( $lists as $list ) {
				if ( !isset( $mobile_lists[$list] ) ) {
					$this->db->insert( 'mobile_lists', array( 'website_id' => $website_id, 'name' => $list, 'frequency' => 10, 'description' => 'Unknown', 'date_created' => dt::date('Y-m-d H:i:s') ), 'isiss' );
					
					// Handle any error
					if ( $this->db->errno() ) {
						$this->_err( 'Failed to create mobile list.', __LINE__, __METHOD__ );
						return false;
					}
					
					$mobile_lists[$list] = $this->db->insert_id;
				}
				
				$mobile_list_id = (int) $mobile_lists[$list];
				
				// Get the trumpia contact id
				$trumpia_contact_id = (int) $mobile_associations["$mobile_subscriber_id-$mobile_list_id"];
				
				// Get the trumpia contact id via API if necessary
				if ( !$trumpia_contact_id )
					$trumpia_contact_id = (int) $trumpia->get_contact_id( $this->_format_mobile_list_name( $list ), 2, $mobile_number );
				
				$association_values[] = "( $mobile_subscriber_id, $mobile_list_id, $trumpia_contact_id )";
			}
		}
		
		$association_values_string = implode( ',', $association_values );
		
		// Insert all the mobile lists
		$this->db->query( "INSERT INTO `mobile_associations` ( `mobile_subscriber_id`, `mobile_list_id`, `trumpia_contact_id` ) VALUES $association_values_string ON DUPLICATE KEY UPDATE `trumpia_contact_id` = VALUES( `trumpia_contact_id` )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to insert mobile associations.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
     * Format Mobile List Name
     *
     * @param string $name
     * @return string
     */
    private function _format_mobile_list_name( $name ) {
        return substr( preg_replace( '/[^a-zA-Z0-9]/', '', $name ), 0, 32 );
    }
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @param bool $debug
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}