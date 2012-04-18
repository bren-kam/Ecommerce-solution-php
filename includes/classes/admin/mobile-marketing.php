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

        // Login to Grey Suit Apps
        $login_fields = array(
            'username' => config::key('trumpia-admin-username')
            , 'password' => config::key('trumpia-admin-password')
        );

        $c->post( 'http://greysuitmobile.com/admin/action/action_login.php', $login_fields );

        // Now that we're logged in, lets create the account
        $industries = $w->get_industries( $website_id );
        $industry = $i->get( $industries[0] );
        $timezone = (int) $w->get_setting( $website_id, 'timezone' );
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
        $w->update_settings( $website_id, array( 'trumpia-api-key' => $api_key ) );

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
     * @param bool $debug
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '', $debug = true ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method, $debug );
	}
}