<?php
class CurrentAd extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $website_page_id, $key, $content, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_current_ad' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
     * @param bool $success
	 * @return string
	 */
	public function get_tab( $fb_page_id, $success ) {
		// Get the tab
		$tab_data = $this->get_tab_data( $fb_page_id );

		if ( 0 != $tab_data->website_page_id ) {
            // Get attachments
            $account_page_attachment = new AccountPageAttachment();
            $attachments = $account_page_attachment->get_by_account_page_ids( array( $tab_data->website_page_id ) );

			// Get website
			$account = $this->get_account( $fb_page_id );

			// Form Tab
			$tab = '<h1>Current Ad</h1>';

            /**
             * @var AccountPageAttachment $attachment
             */
            if ( is_array( $attachments ) )
            foreach ( $attachments as $attachment ) {
				$k = $attachment->key;
				$v = $attachment->value;
				$e = $attachment->extra;

				// No empty values
				if ( empty( $v ) && !in_array( $k, array( 'search', 'email' ) ) )
					continue;

				switch ( $k ) {
					case 'email':
						// Add validation
						if ( !$success ) {
							$tab .= '<br clear="left" />';
							$tab .= '<form name="fSignUp" method="post" action="/facebook/current-ad/tab/">';
							$tab .= '<p>Sign up for our online-only special offers and discounts.</p>';
							$tab .= '<table cellpadding="0" cellspacing="0">';
							$tab .= '<tr>';
							$tab .= '<td><label for="tName">Name:</label></td>';
							$tab .= '<td><input type="text" class="tb" name="tName" id="tName" value="' . $_POST['tName'] . '" /></td>';
							$tab .= '</tr>';
							$tab .= '<tr>';
							$tab .= '<td><label for="tEmail">Email:</label></td>';
							$tab .= '<td><input type="text" class="tb" name="tEmail" id="tEmail" value="' . $_POST['tEmail'] . '" /></td>';
							$tab .= '</tr>';
							$tab .= '<tr>';
							$tab .= '<td>&nbsp;</td>';
							$tab .= '<td><input type="submit" class="button" value="Sign Up" /></td>';
							$tab .= '</tr>';
							$tab .= '</table>';
							$tab .= '<input type="hidden" name="signed_request" value="' . $_REQUEST['signed_request'] . '" />';
							$tab .= nonce::field( 'sign-up', '_nonce', false );
						}
					break;

					case 'room-planner':
                        $room_planner_slug = $account->get_settings('page_room-planner-slug');

						$tab .= '<div id="dRoomPlanner" class="box"><a href="http://' . $account->domain . '/' . $room_planner_slug . '/" title="Plan Your Room" target="_blank"><img src="http://' . $account->domain . '/' . $v . '" alt="Room Planner" /></a></div>';
					break;

					case 'sidebar-image':
						$tab .= '<div class="box">';

						if ( !empty( $e ) )
							$tab .= '<a href="' . $e . '" target="_blank">';

						$url = ( stristr( $v, 'websites.retailcatalog.us' ) ) ? $v : 'http://' . $account->domain .$v;

						$tab .= '<img src="' . $url . '" alt="" />';

						if ( !empty( $e ) )
							$tab .= '</a>';

						$tab .= '</div>';
					break;

					case 'video':
						$key = substr( substr( md5( 'imagineretailer.com' . '17e972798ee5066d58c' ), 11, 30 ), 0, -2 );

						$tab .= '<div id="video" class="box">';
							$tab .= '<div id="player" style="width:239px; height:213px;"></div>';
							$tab .= '<script type="text/javascript" language="javascript" src="http://' . $account->domain . '/core/js/flashdetect.js"></script>';
							$tab .= '<script type="text/javascript" language="javascript" src="http://' . $account->domain . '/core/js/flowplayer.js"></script>';
							$tab .= '<script type="text/javascript" language="javascript">';
								$tab .= '$f("player", "http://' . $account->domain . '/media/' . 'flash/flowplayer.unlimited-3.1.5.swf", {';
									$tab .= "key: '$key',";
									$tab .= 'playlist: [';
										$tab .= '{';
											$tab .= "url: '$v',";
											$tab .= 'autoPlay: false,';
											$tab .= 'autoBuffering: true';
										$tab .= '}';
									$tab .= '],';
									$tab .= 'plugins: {';
										$tab .= 'controls: {';
											$tab .= "autoHide: 'never',";
											$tab .= "backgroundColor: '#111009',";
											$tab .= 'backgroundGradient: [0.2,0.1,0],';
											$tab .= "borderRadius: '0px',";
											$tab .= "bufferColor: '#151515',";
											$tab .= 'bufferGradient: [0.2,0.1,0],';
											$tab .= "buttonColor: '#888888',";
											$tab .= "buttonOverColor: '#adadad',";
											$tab .= "durationColor: '#FFFFFF',";
											$tab .= 'fullscreen: false,';
											$tab .= 'height: 25,';
											$tab .= 'opacity: 1,';
											$tab .= "progressColor: '#6A6969',";
											$tab .= 'progressGradient: [0.8,0.3,0],';
											$tab .= "sliderBorder: '1px solid rgba(15, 15, 15, 1)',";
											$tab .= "sliderColor: '#151515',";
											$tab .= 'sliderGradient: [0.2,0.1,0],';
											$tab .= "timeBgColor: '#0E0E0E',";
											$tab .= "timeBorder: '0px solid rgba(0, 0, 0, 0.3)',";
											$tab .= "timeColor: '#656565',";
											$tab .= "timeSeparator: ' / ',";
											$tab .= "volumeBorder: '1px solid rgba(128, 128, 128, 0.7)',";
											$tab .= "volumeColor: '#ffffff',";
											$tab .= "volumeSliderColor: '#000000',";
											$tab .= 'volumeSliderGradient: [0.1,0],';
											$tab .= "tooltipColor: '#000000',";
											$tab .= "tooltipTextColor: '#ffffff'";
										$tab .= '}';
									$tab .= '}';
								$tab .= '});';
							$tab .= '</script>';
						$tab .= '</div>';
					break;

					default:
						continue 2;
					break;
				}

				$tab .= '<br />';
			}
		} else {
			$tab = $tab_data->content;
		}

		return $tab;
	}

    /**
     * Get Tab Data
     *
     * @param string $fb_page_id
     * @return CurrentAd
     */
    protected function get_tab_data( $fb_page_id ) {
        return $this->prepare(
            'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_current_ad` WHERE `fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'CurrentAd' );
    }

    /**
     * Get Domain
     *
     * @param int $fb_page_id
     * @return Account
     */
    protected function get_account( $fb_page_id ) {
        return $this->prepare(
            'SELECT w.`website_id`, w.`domain` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_current_ad` AS smca ON ( smca.`sm_facebook_page_id` = smfbp.`id` ) WHERE smfbp.`status` = 1 AND smca.`fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'Account' );
    }

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, smca.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_current_ad` AS smca ON ( smca.`sm_facebook_page_id` = smfbp.`id` ) WHERE smca.`fb_page_id` = :fb_page_id'
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_OBJ );
	}

    /**
     * Connect
     *
     * @param int $fb_page_id
     * @param string $key
     */
    public function connect( $fb_page_id, $key ) {
        parent::update( array(
            'fb_page_id' => $fb_page_id
        ), array(
            'key' => $key
        ), 'i', 's' );
    }

    /**
	 * Adds an email to the appropriate categories
	 *
	 * @param int $fb_page_id
	 * @param string $name
	 * @param string $email_address
	 * @return bool
	 */
	public function add_email( $fb_page_id, $name, $email_address ) {
        // We only want lowercase email addresses
		$email_address = strtolower( $email_address );

        // Get account
        $account = $this->get_account( $fb_page_id );

		// We need to get the email_id
        $email = new Email();
        $email->get_email_by_email( $account->id, $email_address );

        // The status needs to be 1 in either case of existence or lack thereof
        $email->status = 1;

        // Add or update email address
		if ( $email->id ) {
            $email->save();
		} else {
            $email->website_id = $account->id;
            $email->name = $name;
            $email->email = $email_address;
            $email->create();
		}

		// Get default email list id
        $email_list = new EmailList();
        $email_list->get_default_email_list( $account->id );

        // Add association
        $email->add_associations( array( $email_list->id ) );
	}
}