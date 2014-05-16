<?php
class ContactUs extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $website_page_id, $key, $content, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_contact_us' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->get_tab_data( $fb_page_id );

		if ( 0 != $tab_data->website_page_id ) {
			// If there was a website page id, we need to get the content from elsewhere
			$account_page = $this->get_tab_page( $fb_page_id, $tab_data->website_page_id );
            $pagemeta = $this->get_tab_page_meta( $fb_page_id, $tab_data->website_page_id );

			// Form Tab
			$tab = '<h1>' . $account_page->title . '</h1>';

			$tab .= html_entity_decode( $account_page->content, ENT_QUOTES, 'UTF-8' );

            $website_location = new WebsiteLocation();
            $addresses = $website_location->get_by_website( $account_page->website_id );

			if ( is_array( $addresses ) ) {
				if ( 'true' == $pagemeta['multiple-location-map'] ) {
					$gmaps_url = 'http://maps.google.com/maps/api/staticmap?size=520x281&maptype=roadmap&sensor=false&markers=color:blue';
					$i = 0;
                    $locations = '';

					foreach ( $addresses as $ad ) {
						$gmaps_url .= '|' . urlencode( $ad->address . ',' . $ad->city . ' ' . $ad->state . ',' . $ad->zip );

						$locations .= '<div class="location-3">';
						$locations .= '<h2>' . $ad->location . '</h2>';
						$locations .= '<p>' . $ad->address . '<br />' . $ad->city . ', ' . $ad->state . ' ' . $ad->zip;

						if ( !empty( $ad->phone ) || !empty( $ad->fax ) || !empty( $ad->email ) || !empty( $ad->website ) ) {
							$locations .= '<p>';

							if ( !empty( $ad->phone ) )
								$locations .= $ad->phone . ' (Phone)<br />';

							if ( !empty( $ad->fax ) )
								$locations .= $ad->fax . ' (Fax)<br />';

							if ( !empty( $ad->email ) ) {
								$email_address = security::encrypt_email( $ad->email, 'Email ' . $ad->location, false );
								$display_email = ( strlen( $ad->email ) > 25 ) ? ( substr( $ad->email, 0, 22) ).'...' : $email_address;
								$locations .= '<a href="mailto:' . $email_address . '" title="Email ' . $ad->location . '">' . $display_email . '</a> (Email)<br/>';
							}

							if ( !empty( $ad->website ) ) {
								$link = ( !stristr( 'http://', $ad->website ) ) ? 'http://' . $ad->website : $ad->website;
								$locations .= '<a href="' . $link . '" title="' . $ad->location . '">' . $ad->website . '</a>';
							}

							$locations .= '</p>';
						}

						if ( !empty( $ad->store_hours ) )
							$locations .= '<p>' . $ad->store_hours . '</p>';

						$locations .= '</div>';

						$i++;

						// Needed for IE7 (won't wrap floated divs for some reason)
						if ( 0 == $i % 3 )
							$locations .= '<br clear="left" />';
					}

					$tab .= ( ( 'false' == $pagemeta['hide-all-maps'] || !isset( $pagemeta['hide-all-maps'] ) ) ? '<img src="' . $gmaps_url . '" alt="Locations" width="520" height="281" /><br /><br />' : '<br/>' ) . $locations;
				} else {
					foreach ( $addresses as $ad ) {
						$gmaps_address = urlencode( $ad->address . ',' . $ad->city . ' ' . $ad->state . ',' . $ad->zip );

						$tab .= '<div class="location" style="clear:both">';

						if ( 'false' == $pagemeta['hide-all-maps'] || !isset( $pagemeta['hide-all-maps'] ) ) {
							$tab .= '<div style="float: right">';
							$tab .= '<iframe marginheight="0" marginwidth="0" src="http://maps.google.com/maps?hl=en&amp;q=' . $gmaps_address . '&amp;ie=UTF8&amp;output=embed" scrolling="no" frameborder="0" width="280" height="200"></iframe>';
							$tab .= '<br />';
							$tab .= '<small><a href="http://maps.google.com/maps?hl=en&amp;q=' . $gmaps_address . '&amp;ie=UTF8" style="color: #0000FF;" target="_blank">View Larger Map</a></small>';
							$tab .= '</div>';
						}

						$tab .= '<h2><strong>' . $ad->location . '</strong></h2>';
						$tab .= '<p>' . $ad->address . '<br />' . $ad->city . ', ' . $ad->state . ' ' . $ad->zip . '</p>';

						if ( !empty( $ad->phone ) || !empty( $ad->fax ) || !empty( $ad->email ) || !empty( $ad->website ) ) {
							$tab .= '<p>';

							if ( !empty( $ad->phone ) )
								$tab .= $ad->phone . ' (Phone)<br />';

							if ( !empty( $ad->fax ) )
								$tab .= $ad->fax . ' (Fax)<br />';

							if ( !empty( $ad->email ) ) {
								$email_address = security::encrypt_email( $ad->email, 'Email ' . $ad->location, false );
								$display_email = ( strlen( $ad->email ) > 30 ) ? ( substr( $ad->email, 0, 27) ).'...' : $email_address;

								$tab .= '<a href="mailto:' . $email_address . '" title="Email ' . $ad->location . '">' . $display_email . '</a> (Email)<br/>';
							}

							if ( !empty( $ad->website ) ) {
								$link = ( !stristr( 'http://', $ad->website ) ) ? 'http://' . $ad->website : $ad->website;
								$tab .= "<a href='$link' title=\"" . $ad->location . "\">" . $ad->website . "</a>";
							}

							$tab .= '</p>';
						}

						if ( !empty( $ad->store_hours ) )
							$tab .= '<p>' . $ad->store_hours . '</p>';

						$tab .= '</div><br /><br /><br /><br /><br /><br />';
					}
				}
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
     * @return ContactUs
     */
    protected function get_tab_data( $fb_page_id ) {
        return $this->prepare(
            'SELECT IF( 0 = `website_page_id`, `content`, 0 ) AS content, `website_page_id` FROM `sm_contact_us` WHERE `fb_page_id` = :fb_page_id'
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'ContactUs' );
    }

    /**
     * Get Tab Page
     *
     * @param int $fb_page_id
     * @param int $website_page_id
     * @return AccountPage
     */
    protected function get_tab_page( $fb_page_id, $website_page_id ) {
        return $this->prepare(
            'SELECT wp.website_id, wp.`title`, wp.`content` FROM `website_pages` AS wp LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = wp.`website_id` ) LEFT JOIN `sm_contact_us` AS smcu ON ( smcu.`sm_facebook_page_id` = smfbp.`id` ) WHERE wp.`website_page_id` = :website_page_id AND smfbp.`status` = 1 AND smcu.`fb_page_id` = :fb_page_id'
            , 'is'
            , array( ':website_page_id' => $website_page_id, ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'AccountPage' );
    }

    /**
     * Get Tab Page Meta
     * 
     * @param int $fb_page_id
     * @param int $website_page_id
     * @return array
     */
    protected function get_tab_page_meta( $fb_page_id, $website_page_id ) {
        return ar::assign_key( $this->prepare(
            'SELECT wpm.`key`, wpm.`value` FROM `website_pagemeta` AS wpm LEFT JOIN `website_pages` AS wp ON ( wp.`website_page_id` = wpm.`website_page_id` ) LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = wp.`website_id` ) LEFT JOIN `sm_contact_us` AS smcu ON ( smcu.`sm_facebook_page_id` = smfbp.`id` ) WHERE wpm.`website_page_id` = :website_page_id AND smfbp.`status` = 1 AND smcu.`fb_page_id` = :fb_page_id'
            , 'is'
            , array( ':website_page_id' => $website_page_id, ':fb_page_id' => $fb_page_id )
        )->get_results( PDO::FETCH_ASSOC ), 'key', true );
    }

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, smcu.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_contact_us` AS smcu ON ( smcu.`sm_facebook_page_id` = smfbp.`id` ) WHERE smcu.`fb_page_id` = :fb_page_id'
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
}