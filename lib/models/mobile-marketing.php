<?php
class MobileMarketing extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
     * Synchronize Account Contacts
     */
    public function synchronize_contacts() {
		// Include the library
        library('trumpia');
        library('Excel_Reader/Excel_Reader'); // Being optimistic and assuming it's necessary

        // Instantiate Classes
        $curl = new Curl();
        $mobile_subscriber = new MobileSubscriber();
        $mobile_list = new MobileList();

        // Get data
        $user_ids = $this->get_customers();

        /** To be explored
         *$w = new Websites;

        $websites = $this->db->get_results( "SELECT ws.`website_id`, ws.`value` AS user_id, ws2.`value` AS api_key FROM `website_settings` AS ws LEFT JOIN `website_settings` AS ws2 ON ( ws.`website_id` = ws2.`website_id` ) WHERE ws.`key` = 'trumpia-user-id' AND ws2.`key` = 'trumpia-api-key' AND ws.`website_id` = 1122", ARRAY_A );

        // Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get user_ids.', __LINE__, __METHOD__ );
			return false;
		}

		// Include the library
        library('trumpia-v1');

		foreach ( $websites as $website ) {
			$trumpia = new TrumpiaV1( $website['api_key'], $website['user_id'] );
			$subscriptions = $trumpia->get_subscriptions();
			print_r( $subscriptions );
		} */

        // Make sure we have a reason to continue
        if ( empty( $user_ids ) )
            return;

        // Login
        $login_fields = array(
            'username' => Config::key('trumpia-admin-username')
            , 'password' => Config::key('trumpia-admin-password')
        );

        $curl->post( 'http://greysuitmobile.com/admin/action/action_login.php', $login_fields );

        // Now cycle through each user
        foreach ( $user_ids as $website_id => $user_id ) {
			// Trying to set timeout limit
			set_time_limit(300);

            // Sign in under that user (set cookies)
            $curl->get( "http://greysuitmobile.com/main/action/action_main.php?mode=x__admin_signin&uid={$user_id}" );

            // Get the page
            $page = $curl->get( 'http://greysuitmobile.com/manageContacts/export_contacts_step1.php' );

            // Get all the list IDs
            preg_match_all( '/option value="([^"]+)"/', $page, $matches );

            $list_ids = $matches[1];

            // Make sure we have a reason to continue
            if ( empty( $list_ids ) )
                continue;

            // Send request to create an export of the lists
            $post_fields = array(
                'exportCount' => '1'
                , 'export_list' => '["' . implode( '","', $list_ids ) . '"]'
                , 'normal_data' => '["5"]' // Lists
                , 'contact_data' => '["2"]' // Phone Number
                , 'custom_data' => 'null'
                , 'total_subscription_selected' => '1'
                , 'subscription_opted_private' => '1'
            );

            $page = $curl->post( 'greysuitmobile.com/manageContacts/action/action_contacts_export.php', $post_fields );

            // Test success
            if ( !preg_match( '/action="[^"]+"/', $page ) )
                return;

			// This is done to create a lag between export and download -- see function for more information -- essentially checks if the previous one is ready, then it goes on
			if ( isset( $values ) && isset( $last_website_id ) && $last_website_id ) {
                $account = new Account();
                $account->get( $last_website_id );
				$this->update_mobile_subscribers( $account, $mobile_subscriber, $mobile_list, $values );
            }

            // Totally forget what this does... ?
            $page = $curl->get( 'http://greysuitmobile.com/manageContacts/manage_contact_summary.php' );

            preg_match( '/export_contacts_result\.php\?uid=([0-9]+)/', $page, $matches );

            $export_id = $matches[1];

			// Give it some more time and remove the excel from previous run through
			if ( isset( $excel_file_handle ) ) {
				fclose( $excel_file_handle );

                /**
                 * @var string $excel_file
                 */
                unlink( $excel_file );
			}

            // Create temporary file to store contact lists in
            $excel_file = tempnam( sys_get_temp_dir(), 'gsr_' );
			chmod( $excel_file, 0777 );
            $excel_file_handle = fopen( $excel_file, 'w+' );

            // Download and save the excel spreadsheet to our temporary file
			$curl->save_file( "http://greysuitmobile.com/manageContacts/action/action_export_download.php?uid=$export_id", $excel_file_handle );

            // Setup excel reader to read the users
            $er = new Excel_Reader();

            // Set the basics and then read in the rows
            $er->setOutputEncoding('ASCII');
            $er->read( $excel_file );

			$values = array();
			$subscribers = $er->sheets[0]['cells'];

            // Loop through subscribers
			if ( is_array( $subscribers ) && count( $subscribers ) > 1 ) {
                // We don't need the first row
				array_shift( $subscribers );

                // Get the mobile numbers
				foreach ( $subscribers as $s ) {
					$mobile_number = $s[2];

                    // Split up the fields
					$lists = explode( ',', $s[1] );

                    // Figure out what lists they have and format it to match ours
                    // (i.e., '12 hot-prospects' to 'hot-prospects')
					foreach( $lists as $key => &$l ) {
						if ( empty( $l ) )
							unset( $lists[$key] );

						$l = trim( preg_replace( '/^[0-9]+\s/', '', $l ) );
					}

                    // Setup the value and lists
					$values[$mobile_number] = array_values( $lists );
				}
			}

			// Set the last website_id
			$last_website_id = $website_id;

			// Do the last one
			if ( !empty( $values ) && $last_website_id ) {
                $account = new Account();
                $account->get( $last_website_id );
				$this->update_mobile_subscribers( $account, $mobile_subscriber, $mobile_list, $values );
            }
		}

		if ( isset( $excel_file_handle ) ) {
			fclose( $excel_file_handle );

            /**
             * @var string $excel_file
             */
			unlink( $excel_file );
		}
    }

    /**
	 * Update Mobile Subscribers
	 *
	 * This will only run the second time around and is done so that there is a slight lag between when the export
	 * of the contact lists is initiated (command above) and when they download it (command below), which will
	 * hopefully minimize any grabbing of old lists. If the list is large it may take a couple of seconds, hopefully
	 * this will slow it down just a little while
	 *
	 * If this doesn't work, we can go through this loop twice, initiating the export, then second time we go through
	 * the loop of all the sites we download it
	 *
	 * @param Account $account
     * @param MobileSubscriber $mobile_subscriber
     * @param MobileList $mobile_list
	 * @param array $values
	 * @return bool
	 */
	protected function update_mobile_subscribers( Account $account, MobileSubscriber $mobile_subscriber, MobileList $mobile_list, $values ) {
        // Declare variables
        $mobile_numbers = array_keys( $values );

		// Now clear out the database and then readd subscribers
		$mobile_subscriber->empty_by_account( $account->id );
        $mobile_subscriber->add_bulk( $account->id, $mobile_numbers );

        // Delete subscriber/list associations
        $mobile_subscriber->delete_associations_by_account( $account->id );

		// Get everything we need
        $mobile_lists = $mobile_list->get_name_index_by_account( $account->id );
		$mobile_subscribers = $mobile_subscriber->get_phone_index_by_account( $account->id, $mobile_numbers );
		$full_mobile_associations = $mobile_subscriber->get_associations_by_account( $account->id );

		$mobile_associations = array();

		// Format the mobile associations so we can find them
		if ( is_array( $full_mobile_associations ) )
		foreach ( $full_mobile_associations as $fma ) {
			$mobile_associations[$fma->mobile_subscriber_id . '-' . $fma->mobile_list_id] = $fma->trumpia_contact_id;
		}

		// Setup Trumpia
        $trumpia = new Trumpia( $account->get_settings( 'trumpia-api-key' ) );

        // Declare variables
		$association_values = array();
		$i = 0;

		// Now we have to readd them
		foreach ( $values as $mobile_number => $lists ) {
			// Want to make sure we can get the subscriber
			if ( isset( $mobile_subscribers[$mobile_number] ) ) {
				$mobile_subscriber_id = $mobile_subscribers[$mobile_number];
			} else {
				continue;
			}

			foreach ( $lists as $list ) {
				$i++;

				if ( !isset( $mobile_lists[$list] ) ) {
                    // Create the list
                    $mobile_list = new MobileList();
                    $mobile_list->website_id = $account->id;
                    $mobile_list->name = $list;
                    $mobile_list->frequency = 10;
                    $mobile_list->description = 'Unknown';
                    $mobile_list->create();

					$mobile_lists[$list] = $mobile_list->id;
				}

				$mobile_list_id = $mobile_lists[$list];

				// Get the trumpia contact id
				$trumpia_contact_id = $mobile_associations["{$mobile_subscriber_id}-{$mobile_list_id}"];

				// Get the trumpia contact id via API if necessary
				if ( !$trumpia_contact_id )
					$trumpia_contact_id = $trumpia->get_contact_id( $this->format_mobile_list_name( $list ), 2, $mobile_number );

				$association_values[] = array( $mobile_subscriber_id, $mobile_list_id, $trumpia_contact_id );

				if ( 0 == $i % 10 ) {
                    $mobile_subscriber->add_bulk_associations( $association_values );
					$association_values = array();
				}
			}
		}

        // Do the last one
		if ( 0 != count( $association_values ) )
			$mobile_subscriber->add_bulk_associations( $association_values );
	}

    /**
     * Get Trumpia Customers
     *
     * @return array
     */
    public function get_customers() {
        return ar::assign_key( $this->get_results( "SELECT `website_id`, `value` FROM `website_settings` WHERE `key` = 'trumpia-user-id'", PDO::FETCH_ASSOC ), 'value', true );
    }

    /**
     * Format Mobile List Name
     *
     * @param string $name
     * @return string
     */
    protected function format_mobile_list_name( $name ) {
        return substr( preg_replace( '/[^a-zA-Z0-9]/', '', $name ), 0, 32 );
    }
}
