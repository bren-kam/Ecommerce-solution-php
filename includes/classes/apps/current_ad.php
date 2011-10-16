<?php
/**
 * Handles all the stuff for Current Ad
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Current_Ad extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
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
		$tab_data = $this->db->prepare( 'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_current_ad` WHERE `fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		if( 0 != $tab_data['website_page_id'] ) {
			// Need to get the attachments
			$attachments = $this->db->prepare( 'SELECT `key`, `value`, `extra` FROM `website_attachments` WHERE `website_page_id` = ? AND `status` = 1 ORDER BY `sequence` ASC', 'i', $tab_data['website_page_id'] )->get_results( '', ARRAY_A );
			
			// Handle any error
			if( $this->db->errno() ) {
				$this->err( 'Failed to get website page attachments.', __LINE__, __METHOD__ );
				return false;
			}
			

			// Get website
			$domain = $this->db->prepare( "SELECT IF( '' = `subdomain`, `domain`, CONCAT( `subdomain`, '.', `domain` ) ) AS domain FROM `websites` AS a LEFT JOIN `sm_current_ad` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`fb_page_id` = ?", 's', $fb_page_id )->get_var('');
			
			// Handle any error
			if( $this->db->errno() ) {
				$this->err( 'Failed to get domain.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Form Tab
			$tab = '<h1>Current Ad</h1>';
			
			
			foreach( $attachments as $att ) {
				$k = $att['key'];
				$v = $att['value'];
				$e = $att['extra'];
				
				// No empty values
				if( empty( $v ) && !in_array( $k, array( 'search', 'email' ) ) )
					continue;
				
				switch( $k ) {
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
							$tab .= nonce::field('sign-up', '_nonce', false);
						}
					break;
					
					case 'room-planner':
						// Get the slug
						$room_planner_slug = $this->db->prepare( "SELECT a.`value` FROM `website_settings` AS a LEFT JOIN `sm_current_ad` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`key` = 'page_room-planner-slug' AND b.`fb_page_id` = ?", 's', $fb_page_id )->get_var('');
						
						// Handle any error
						if( $this->db->errno() ) {
							$this->err( 'Failed to get room planner slug.', __LINE__, __METHOD__ );
							return false;
						}
						
						$tab .= '<div id="dRoomPlanner" class="box"><a href="http://' . $domain . '/' . $room_planner_slug . '/" title="Plan Your Room" target="_blank"><img src="http://' . $domain . '/' . $v . '" alt="Room Planner" /></a></div>';
					break;
					
					case 'sidebar-image':
						$tab .= '<div class="box">';
						
						if ( !empty( $e ) )
							$tab .= '<a href="' . $e . '" target="_blank">';
						
						$tab .= '<img src="http://' . $domain . $v . '" alt="" />';
						
						if( !empty( $e ) )
							$tab .= '</a>';
						
						$tab .= '</div>';
					break;
					
					case 'video':
						$key = substr( substr( md5( 'imagineretailer.com' . '17e972798ee5066d58c' ), 11, 30 ), 0, -2 );
						
						$tab .= '<div id="video" class="box">';
							$tab .= '<div id="player" style="width:239px; height:213px;"></div>';
							$tab .= '<script type="text/javascript" language="javascript" src="http://' . $domain . '/core/js/flashdetect.js"></script>';
							$tab .= '<script type="text/javascript" language="javascript" src="http://' . $domain . '/core/js/flowplayer.js"></script>';
							$tab .= '<script type="text/javascript" language="javascript">';
								$tab .= '$f("player", "http://' . $domain . '/media/' . 'flash/flowplayer.unlimited-3.1.5.swf", {';
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
			$tab = $tab_data['content'];
		}

		return $tab;
	}
	
	/**
	 * Connect a website
	 *
	 * @param string $fb_page_id
	 * @param string $key
	 * @return array
	 */
	public function connect( $fb_page_id, $key ) {
		// Connect the websites
		$this->db->update( 'sm_current_ad', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get Connected Website
	 *
	 * @param int $fb_user_id
	 * @return array
	 */
	public function get_connected_website( $fb_page_id ) {
		// Type Juggling
		$fb_page_id = (int) $fb_page_id;
		
		// Get the connected website
		$website = $this->db->get_row( "SELECT a.`title`, b.`key` FROM `websites` AS a LEFT JOIN `sm_current_ad` AS b ON ( a.`website_id` = b.`website_id` ) WHERE b.`fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
	}
	
	/**
	 * Adds an email to the appropriate categories
	 *
	 * @param int $fb_page_id
	 * @param string $name
	 * @param string $email
	 * @return bool
	 */
	public function add_email( $fb_page_id, $name, $email ) {
		$email = strtolower( $email );
		
		// We need to get the email_id
		$email_data = $this->db->prepare( 'SELECT a.`email_id`, b.`website_id` FROM `emails` AS a LEFT JOIN `sm_current_ad` AS b ON ( a.`website_id` = b.`website_id` ) WHERE a.`email` = ? AND b.`fb_page_id` = ?', 'ss', $email, $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get email data', __LINE__, __METHOD__ );
			return false;
		}
		
		// If there was no email, then grab the other fields
		if( !$email_data ) {
			// @Fix the above query should be able to grab the fields even if email_id is null
			
		 	// We need to get the email_id
			$email_data = $this->db->prepare( 'SELECT `website_id` FROM `sm_current_ad` WHERE `fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to get email data', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		if( $email_data['email_id'] ) {
			// Type juggling for insertion later
			$email_id = (int) $email_data['email_id'];
			
			$this->db->update( 'emails', array( 'status' => 1 ), array( 'email_id' => $email_id ), 'i', 'i' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to update email', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			$this->db->insert( 'emails', array( 'website_id' => $email_data['website_id'], 'name' => $name, 'email' => $email, 'date_created' => date_time::date( 'Y-m-d H:i:s' ) ), 'isss' );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->err( 'Failed to insert email', __LINE__, __METHOD__ );
				return false;
			}
			
			$email_id = (int) $this->db->insert_id;
		}
		
		// Get default email list id
		$default_email_list_id = (int) $this->db->prepare( 'SELECT `email_list_id` FROM `email_lists` WHERE `website_id` = ? AND `category_id` = 0', 'i', $email_data['website_id'] )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to get default email list id', __LINE__, __METHOD__ );
			return false;
		}
		
		$this->db->query( "INSERT INTO `email_associations` ( `email_id`, `email_list_id` ) VALUES ( $email_id, $default_email_list_id ) ON DUPLICATE KEY UPDATE `email_id` = VALUES( `email_id` )" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add email to lists', __LINE__, __METHOD__ );
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