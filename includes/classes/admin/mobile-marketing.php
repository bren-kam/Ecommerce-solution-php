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
     * @param string $level
     * @return bool
	 */
	public function create_trumpia_account( $website_id, $level ) {
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
			, 'email' => $user['email']
			, 'mobile' => $mobile
            , 'industry' => $industry
            , 'timezone' => $timezone
            , 'send_confirmation' => 0
            , 'send_welcome' => 0
        );

        $page = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_createCustomer.php', $post_fields );

        $success = preg_match( '/action="[^"]+"/', $page );

        if ( !$success )
            return false;

        // Get Member's User ID
        $list_page = $c->get( 'http://greysuitmobile.com/admin/MemberManagement/memberSearch.php?mode=&plan=&status=&radio_memberSearch=2&search=' . urlencode( $user['email'] ) . '&x=28&y=15' );

        // Isolate the user ID
        preg_match( "/uid=([0-9]+)/", $list_page, $matches );

        // Get USER ID
        $user_id = $matches[1];

        // Assign Plan Level
        switch ( $level ) {
            case 'level-1':
                $trumpia_plan = 36456;
                $plus_credits = 490;
            break;

            default:
            case 'level-2':
                $trumpia_plan = 36455;
                $plus_credits = 990;
            break;

            case 'level-3':
                $trumpia_plan = 36457;
                $plus_credits = 2490;
            break;

            case 'level-4':
                $trumpia_plan = 36458;
                $plus_credits = 4990;
            break;

            case 'level-5':
                $trumpia_plan = 36459;
                $plus_credits = 7490;
            break;
        }

        // Update Plan
        $update_plan_fields = array(
            'mode' => 'updatePlan'
            , 'member_uid' => $user_id
            , 'arg1' => $trumpia_plan
        );

        $update_plan = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $update_plan_fields );

        $success = '<script type="text/javascript">history.go(-1);</script>' == $update_plan;

        if ( !$success )
            return false;

        // Update Credits
        $update_credits_fields = array(
            'mode' => 'addCredit'
            , 'member_uid' => $user_id
            , 'arg1' => $plus_credits
        );

        $update_credits = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $update_credits_fields );

        $success = '<script type="text/javascript">history.go(-1);</script>' == $update_credits;

        if ( !$success )
            return false;

        // Create API Key
        $api_fields = array(
            'mode' => 'createAPIKey'
            , 'member_uid' => $user_id
            , 'arg1' => $user_id
        );

        $api_creation = $c->post( 'http://greysuitmobile.com/admin/MemberManagement/action/action_memberDetail.php', $api_fields );

        $success = preg_match( '/action="[^"]+"/', $api_creation );

        if ( !$success )
            return false;

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
            return false;

        // Get API Key
        $api_page = $c->get( 'http://greysuitmobile.com/admin/MemberManagement/apiCustomers.php' );

        preg_match( '/' . $user_id . '\'\)"><\/td>\s*<td>([^<]+)</', $api_page, $matches );
        $api_key = $matches[1];

        // Update the setting with the API Key. YAY!
        $w->update_settings( $website_id, array( 'trumpia-api-key' => $api_key, 'trumpia-user-id' => $user_id ) );

        return true;
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

        foreach ( $user_ids as $uid ) {
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

            //$page = $c->post( 'greysuitmobile.com/manageContacts/action/action_contacts_export.php', $post_fields );

            //$success = preg_match( '/action="[^"]+"/', $page );

            //if ( !$success )
                //return false;

            $page = $c->get( 'http://greysuitmobile.com/manageContacts/manage_contact_summary.php' );

            preg_match( '/export_contacts_result\.php\?uid=([0-9]+)/', $page, $matches );

            $export_id = $matches[1];

            $excel_file = tempnam( sys_get_temp_dir(), 'gsr_' );
            $excel_file_handle = fopen( $excel_file, 'w+' );
			
			$page = $c->save_file( "http://greysuitmobile.com/manageContacts/action/action_export_download.php?uid=$export_id", $excel_file_handle );

            fclose( $excel_file_handle );
            
            // Load excel reader
            library('Excel_Reader/Excel_Reader');
            $er = new Excel_Reader();
            // Set the basics and then read in the rows
            $er->setOutputEncoding('ASCII');
            $er->read( $excel_file );

            fn::info( $er->sheets[0]['cells'] );
            exit;
        }
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