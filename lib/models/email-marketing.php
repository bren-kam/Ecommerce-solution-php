<?php
class EmailMarketing extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
	 * Synchronize email lists
	 */
	public function synchronize_email_lists() {
        library('MCAPI');
        $mailchimp = new MCAPI( Config::key('mc-api') );

		$this->remove_bad_emails( $mailchimp );
		$this->update_email_lists( $mailchimp );
	}

    /***** PROTECTED FUNCTIONS *****/

    /**
	 * Removes unsubscribed addresses
     *
     * @param MCAPI $mailchimp
	 */
	protected function remove_bad_emails( $mailchimp ) {
		// Get the website lists and mc_list_ids
		$mc_list_ids = $this->get_mailchimp_website_index();

        // Set date
        $yesterday = new DateTime();
        $yesterday->sub( new DateInterval('P1D') );

		// Go through all the websites
		foreach ( $mc_list_ids as $account_id => $mc_list_id ) {
			// Get the unsubscribers since the last day
			$unsubscribers = $mailchimp->listMembers( $mc_list_id, 'unsubscribed', $yesterday->format('Y-m-d H:i:s') );

			// Error Handling
			if ( $mailchimp->errorCode ) {
                // Do stuff
				// $this->_err( "Unable to get Unsubscribed Members\n\nList_id: $mc_list_id\nCode: " . $mailchimp->errorCode . "\nError Message: " . $mailchimp->errorMessage . "\nMembers returned: " . count( $unsubscribers ), __LINE__, __METHOD__ );
            }

			$emails = array();

			if ( is_array( $unsubscribers ) )
			foreach ( $unsubscribers as $unsubscriber ) {
				$emails[] = $unsubscriber['email'];
			}

			// Mark the users as unsubscribed
            if ( !empty( $emails ) )
                $this->bulk_unsubscribe( $account_id, $emails );

			// Get the cleaned for the last day
			$cleaned = $mailchimp->listMembers( $mc_list_id, 'cleaned', $yesterday->format('Y-m-d H:i:s') );

			// Error Handling
			if ( $mailchimp->errorCode ) {
                // Do stuff
				// $this->_err( "Unable to get Cleaned Members\n\nList_id: $mc_list_id\nCode: " . $mailchimp->errorCode . "\nError Message: " . $mailchimp->errorMessage . "\nMembers returned: " . count( $cleaned ), __LINE__, __METHOD__ );
            }

			$emails = array();

			if ( is_array( $cleaned ) )
			foreach ( $cleaned as $clean ) {
				$emails[] = $clean['email'];
			}

			// Mark the users as cleaned
			if ( !empty( $emails ) )
                $this->bulk_mark_cleaned( $account_id, $emails );
		}
	}

    /**
	 * Update email lists
	 *
     * @param MCAPI $mailchimp
	 */
	protected function update_email_lists( $mailchimp ) {
        // Get all emails that need to be updated
        $email = new Email;
        $emails = $email->get_unsynced();
        
        // Make sure we have a reason to go on
        if ( empty( $emails ) )
            return;

		// Create array
		$email_lists = $email_interests = array();

		/**
         * We know an array exists or we would have aborted above
         * @var Email $email
         */
		foreach ( $emails as $email ) {
			if ( !$email->mc_list_id ) {
				// Do error stuff
				//$this->_err( 'There was no MailChimp List ID.', __LINE__, __METHOD__ );
				continue;
			}

			$email_lists[$email->mc_list_id][$email->id] = array(
				'EMAIL' => $email->email,
				'EMAIL_TYPE' => 'html',
				'FNAME' => $email->name,
				'INTERESTS' => $email->interests
			);

			$email_interests[$email->mc_list_id] = ( isset( $email_interests[$email->mc_list_id] ) ) ? array_merge( $email_interests[$email->mc_list_id], explode( ',', $email->interests ) ) : explode( ',', $email->interests );
			$email_interests[$email->mc_list_id] = array_unique( $email_interests[$email->mc_list_id] );
		}

        // Make sure we have a reason to go on.
		if ( empty( $email_lists ) )
            return;

		// Create array to hold email ids
		$synced_email_ids = array();

		foreach ( $email_lists as $mc_list_id => $emails ) {
            // Get unique interested
			$interests = array_unique( $email_interests[$mc_list_id] );

            // Get the groups from Mailchimp
			$groups_result = $mailchimp->listInterestGroups( $mc_list_id );

			// Error Handling
			if ( $mailchimp->errorCode ) {
                // Do stuff
				// $this->_err( "Unable to get Interest Groups\n\nList_id: $mc_list_id\nCode: " . $mailchimp->errorCode . "\nError Message: " . $mailchimp->errorMessage, __LINE__, __METHOD__ );
            }

			foreach ( $interests as $i ) {
				if ( !in_array( $i, $groups_result['groups'] ) ) {
					$mailchimp->listInterestGroupAdd( $mc_list_id, $i );

					// Error Handling
					if ( $mailchimp->errorCode ) {
                        // Do stuff
						// $this->_err( "Unable to add Interest Group\n\nList_id: $mc_list_id\nInterest Group: $i\nCode: " . $mailchimp->errorCode . "\nError Message: " . $mailchimp->errorMessage, __LINE__, __METHOD__ );
                    }
				}
			}

			// list_id, batch of emails, require double optin, update existing users, replace interests
			$vals = $mailchimp->listBatchSubscribe( $mc_list_id, $emails, false, true, true );

			if ( $mailchimp->errorCode ) {
                // Do stuff
				//$this->_err( "Unable to get Batch Subscribe\n\nList_id: $mc_list_id\nCode: " . $mailchimp->errorCode . "\nError Message: " . $mailchimp->errorMessage, __LINE__, __METHOD__ );
			} else {
				// Handle errors if there were any
				if ( $vals['error_count'] > 0 ) {
					$errors = '';

					foreach ( $vals['errors'] as $val ) {
						$errors .= "Email: " . $val['email'] . "\nCode: " . $val['code'] . "\nError Message: " . $val['message'] . "\n\n";
					}

                    // Show error
					// $this->_err( "List_id: $mc_list_id\n" . $vals['error_count'] . ' out of ' . $vals['error_count'] + $vals['success_count'] . " emails were unabled to be subscribed\n\n$errors", __LINE__, __METHOD__ );
				}

				$synced_email_ids = array_merge( $synced_email_ids, array_keys( $emails ) );
			}
		}

        // Make sure we have a reason to go on
        if ( empty( $synced_email_ids ) )
            return;

        $this->synchronize_emails( $synced_email_ids );
	}

    /**
     * Get Mailchimp > Website Index
     *
     * @return array
     */
    protected function get_mailchimp_website_index() {
        return ar::assign_key( $this->get_results( "SELECT `website_id`, `mc_list_id` FROM `websites` WHERE `mc_list_id` <> '0' AND `email_marketing` <> 0", PDO::FETCH_ASSOC ), 'website_id', true );
    }

    /**
     * Bulk unsubscribe by account
     *
     * @param int $account_id
     * @param array $emails
     */
    protected function bulk_unsubscribe( $account_id, array $emails ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );
        $email_format = substr( str_repeat( ',?', $email_count ), 1 );

        $this->prepare(
            "UPDATE `emails` SET `status` = 0 WHERE `website_id` = $account_id AND `email` IN ( $email_format )"
            , str_repeat( 's', $email_count )
            , $emails
        );
    }

    /**
     * Bulk mark cleaned
     *
     * @param int $account_id
     * @param array $emails
     */
    protected function bulk_mark_cleaned( $account_id, array $emails ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $email_count = count( $emails );
        $email_format = substr( str_repeat( ',?', $email_count ), 1 );

        $this->prepare(
            "UPDATE `emails` SET `status` = 2 WHERE `website_id` = $account_id AND `email` IN ( $email_format )"
            , str_repeat( 's', $email_count )
            , $emails
        );
    }

    /**
     * Get Unsynced Emails
     *
     * @return array
     */
    protected function get_unsynced_emails() {
        return $this->get_results( "SELECT e.`email`, e.`email_id`, e.`name`, GROUP_CONCAT( el.`name` ) AS interests, w.`mc_list_id`, w.`website_id` FROM `emails` AS e INNER JOIN `email_associations` AS ea ON ( ea.`email_id` = e.`email_id` ) INNER JOIN `email_lists` AS el ON ( el.`email_list_id` = ea.`email_list_id` ) INNER JOIN `websites` AS w ON ( w.`website_id` = el.`website_id` ) WHERE e.`status` = 1 AND ( e.`date_synced` = '0000-00-00 00:00:00' OR e.`timestamp` > e.`date_synced` ) AND w.`email_marketing` = 1 GROUP BY el.`website_id`, e.`email`", PDO::FETCH_OBJ );
    }

    /**
     * Synchronize emails
     *
     * @param array $email_ids
     */
    protected function synchronize_emails( array $email_ids ) {
        // Type Juggling
        foreach ( $email_ids as &$email_id ) {
            $email_id = (int) $email_id;
        }

        $email_ids = implode( ',', $email_ids );

        // Mark these emails as synced
        $this->query( "UPDATE `emails` SET `date_synced` = NOW() WHERE `email_id` IN( $email_ids )" );
    }
}
