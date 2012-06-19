<?php
/**
 * Handles all the stuff for Contact Us
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Contact_Us extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->db->prepare( 'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_contact_us` WHERE `fb_page_id` = ?', 's', $fb_page_id )->get_row( '', ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get tab.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( 0 != $tab_data['website_page_id'] ) {
			// If there was a website page id, we need to get the content from elsewhere
			$page = $this->db->prepare( "SELECT a.`title`, a.`content`, b.`domain` FROM `website_pages` AS a LEFT JOIN `websites` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_facebook_page` AS c ON ( b.`website_id` = c.`website_id` ) LEFT JOIN `sm_contact_us` AS d ON ( c.`id` = d.`sm_facebook_page_id` ) WHERE a.`website_page_id` = ? AND d.`fb_page_id` = ?", 'is', $tab_data['website_page_id'], $fb_page_id )->get_row( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get website page.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Get pagemeta
			$pagemeta = $this->db->prepare( 'SELECT a.`key`, a.`value` FROM `website_pagemeta` AS a LEFT JOIN `website_pages` AS b ON ( a.`website_page_id` = b.`website_page_id` ) LEFT JOIN `sm_facebook_page` AS c ON ( b.`website_id` = c.`website_id` ) LEFT JOIN `sm_contact_us` AS d ON ( c.`id` = d.`sm_facebook_page_id` ) WHERE a.`website_page_id` = ? AND d.`fb_page_id` = ?', 'is', $tab_data['website_page_id'], $fb_page_id )->get_results( '', ARRAY_A );
			
			// Handle any error
			if ( $this->db->errno() ) {
				$this->_err( 'Failed to get website pagemeta.', __LINE__, __METHOD__ );
				return false;
			}
			
			// Set it up correctly
			$pagemeta = ar::assign_key( $pagemeta, 'key', true );
			
			// Form Tab
			$tab = '<h1>' . $page['title'] . '</h1>';
			
			$tab .= html_entity_decode( $page['content'], ENT_QUOTES, 'UTF-8' );
			
			$addresses = unserialize( html_entity_decode( $pagemeta['addresses'], ENT_QUOTES, 'UTF-8') );
			
			if ( is_array( $addresses ) ) {
				if ( 'true' == $pagemeta['multiple-location-map'] ) {
					$gmaps_url = 'http://maps.google.com/maps/api/staticmap?size=520x281&maptype=roadmap&sensor=false&markers=color:blue';
					$i = 0;
					
					foreach ( $addresses as $ad ) {
						$gmaps_url .= '|' . urlencode( $ad['address'] . ',' . $ad['city'] . ' ' . $ad['state'] . ',' . $ad['zip'] );
						
						$locations .= '<div class="location-3">';
						$locations .= '<h2>' . $ad['location'] . '</h2>';
						$locations .= '<p>' . $ad['address'] . '<br />' . $ad['city'] . ', ' . $ad['state'] . ' ' . $ad['zip'];
						
						if ( !empty( $ad['phone'] ) || !empty( $ad['fax'] ) || !empty( $ad['email'] ) || !empty( $ad['website'] ) ) { 
							$locations .= '<p>';
							
							if ( !empty( $ad['phone'] ) ) 
								$locations .= $ad['phone'] . ' (Phone)<br />';
							
							if ( !empty( $ad['fax'] ) ) 
								$locations .= $ad['fax'] . ' (Fax)<br />';
							
							if ( !empty( $ad['email'] ) ) {
								$email_address = security::encrypt_email( $ad['email'], 'Email ' . $ad['location'], false );
								$display_email = ( strlen( $ad['email'] ) > 25 ) ? ( substr( $ad['email'], 0, 22) ).'...' : $email_address;
								$locations .= '<a href="mailto:' . $email_address . '" title="Email ' . $ad['location'] . '">' . $display_email . '</a> (Email)<br/>';
							}
							
							if ( !empty( $ad['website'] ) ) {
								$link = ( !stristr( 'http://', $ad['website'] ) ) ? 'http://' . $ad['website'] : $ad['website'];
								$locations .= '<a href="' . $link . '" title="' . $ad['location'] . '">' . $ad['website'] . '</a>'; 
							}
							
							$locations .= '</p>';
						}
						
						if ( !empty( $ad['store-hours'] ) )
							$locations .= '<p>' . $ad['store-hours'] . '</p>';
						
						$locations .= '</div>';
						
						$i++;
						
						// Needed for IE7 (won't wrap floated divs for some reason)
						if ( 0 == $i % 3 )
							$locations .= '<br clear="left" />';
					}
					
					$tab .= ( ( 'false' == $pagemeta['hide-all-maps'] || !isset( $pagemeta['hide-all-maps'] ) ) ? '<img src="' . $gmaps_url . '" alt="Locations" width="520" height="281" /><br /><br />' : '<br/>' ) . $locations;
				} else {
					foreach ( $addresses as $ad ) {
						$gmaps_address = urlencode( $ad['address'] . ',' . $ad['city'] . ' ' . $ad['state'] . ',' . $ad['zip'] );
						
						$tab .= '<div class="location" style="clear:both">';
						
						if ( 'false' == $pagemeta['hide-all-maps'] || !isset( $pagemeta['hide-all-maps'] ) ) {
							$tab .= '<div style="float: right">';
							$tab .= '<iframe marginheight="0" marginwidth="0" src="http://maps.google.com/maps?hl=en&amp;q=' . $gmaps_address . '&amp;ie=UTF8&amp;output=embed" scrolling="no" frameborder="0" width="280" height="200"></iframe>';
							$tab .= '<br />';
							$tab .= '<small><a href="http://maps.google.com/maps?hl=en&amp;q=' . $gmaps_address . '&amp;ie=UTF8" style="color: #0000FF;" target="_blank">View Larger Map</a></small>';
							$tab .= '</div>';
						}
							
						$tab .= '<h2><strong>' . $ad['location'] . '</strong></h2>';
						$tab .= '<p>' . $ad['address'] . '<br />' . $ad['city'] . ', ' . $ad['state'] . ' ' . $ad['zip'] . '</p>';
						
						if ( !empty( $ad['phone'] ) || !empty( $ad['fax'] ) || !empty( $ad['email'] ) || !empty( $ad['website'] ) ) {
							$tab .= '<p>';
							
							if ( !empty( $ad['phone'] ) )
								$tab .= $ad['phone'] . ' (Phone)<br />';
							
							if ( !empty( $ad['fax'] ) )
								$tab .= $ad['fax'] . ' (Fax)<br />';
							
							if ( !empty( $ad['email'] ) ) {
								$email_address = security::encrypt_email( $ad['email'], 'Email ' . $ad['location'], false );
								$display_email = ( strlen( $ad['email'] ) > 30 ) ? ( substr( $ad['email'], 0, 27) ).'...' : $email_address;
								
								$tab .= '<a href="mailto:' . $email_address . '" title="Email ' . $ad['location'] . '">' . $display_email . '</a> (Email)<br/>';
							}
							
							if ( !empty( $ad['website'] ) ) {
								$link = ( !stristr( 'http://', $ad['website'] ) ) ? 'http://' . $ad['website'] : $ad['website'];
								$tab .= "<a href='$link' title=\"" . $ad['location'] . "\">" . $ad['website'] . "</a>";
							}
							
							$tab .= '</p>';
						}
						
						if ( !empty( $ad['store-hours'] ) )
							$tab .= '<p>' . $ad['store-hours'] . '</p>';
						
						$tab .= '</div><br /><br /><br /><br /><br /><br />';
					}
				}
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
		$this->db->update( 'sm_contact_us', array( 'fb_page_id' => $fb_page_id ), array( 'key' => $key ), 's', 's' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to connected website.', __LINE__, __METHOD__ );
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
		$website = $this->db->get_row( "SELECT a.`title`, c.`key` FROM `websites` AS a `sm_facebook_page` AS b ON ( a.`website_id` = b.`website_id` ) LEFT JOIN `sm_contact_us` AS c ON ( b.`id` = c.`sm_facebook_page_id` ) WHERE c.`fb_page_id` = $fb_page_id", ARRAY_A );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get connected website.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $website;
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
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}