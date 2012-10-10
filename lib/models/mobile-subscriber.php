<?php
class MobileSubscriber extends ActiveRecordBase {
    public $id, $mobile_subscriber_id, $website_id, $phone, $status, $date_created, $date_unsubscribed, $date_synced, $timestamp;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_subscribers' );

        // We want to make sure they match
        if ( isset( $this->mobile_subscriber_id ) )
            $this->id = $this->mobile_subscriber_id;
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @param array $mobile_numbers
     * @return array
     */
    public function get_phone_index_by_account( $account_id, array $mobile_numbers ) {
        // Setup values
        $account_id = (int) $account_id;
        $mobile_number_count = count( $mobile_numbers );
        $values = substr( str_repeat( ',?', $mobile_number_count ), 1 );

        return ar::assign_key( $this->prepare(
            "SELECT `mobile_subscriber_id`, `phone` FROM `mobile_subscribers` WHERE `account_id` = $account_id AND `phone` IN( $values )"
            , str_repeat( 's', $mobile_number_count )
            , $mobile_numbers
        )->get_results( PDO::FETCH_ASSOC ), 'phone', true );
    }

    /**
     * Empty By Account
     *
     * @param int $account_id
     */
    public function empty_by_account( $account_id ) {
        parent::update( array( 'status' => 0 ), array( 'website_id' => $account_id ), 'i', 'i' );
    }
    
    /**
     * Add Bulk
     * 
     * @param int $account_id
     * @param array $mobile_numbers
     */
    public function add_bulk( $account_id, array $mobile_numbers ) {
        if ( empty( $mobile_numbers ) )
            return;

        // Type Juggling
        $account_id = (int) $account_id;

        // Setup values
        $mobile_number_count = count( $mobile_numbers );
        $subscriber_values = substr( str_repeat( ",( $account_id, ?, NOW(), NOW() )", $mobile_number_count ), 1 );

		$this->prepare(
            "INSERT INTO `mobile_subscribers` (`website_id`, `phone`, `date_created`, `date_synced`) VALUES $subscriber_values ON DUPLICATE KEY UPDATE `status` = 1, `date_synced` = NOW()"
            , str_repeat( 's', $mobile_number_count )
            , $mobile_numbers
        )->query();
    }

    /**
     * Add Bulk Associations
     *
     * @param array $values
     */
    public function add_bulk_associations( array $values ) {
        foreach ( $values as &$value_array ) {
            foreach ( $value_array as &$id ) {
                $id = (int) $id;
            }
        }

        $values = implode( ',', $values );

        // Insert all the mobile lists
        $this->query( "INSERT INTO `mobile_associations` ( `mobile_subscriber_id`, `mobile_list_id`, `trumpia_contact_id` ) VALUES $values ON DUPLICATE KEY UPDATE `trumpia_contact_id` = VALUES( `trumpia_contact_id` )" );
    }

    /**
     * Get Associations By Account
     *
     * @param int $account_id
     * @return array
     */
    public function get_associations_by_account( $account_id ) {
        return $this->prepare(
            "SELECT ma.* FROM `mobile_associations` AS ma LEFT JOIN `mobile_subscribers` AS ms ON ( ms.`mobile_subscriber_id` = ma.`mobile_subscriber_id` ) WHERE ms.`website_id` = :account_id AND ms.`status` = 1"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_OBJ );
    }

    /**
     * Delete Associations
     *
     * @param int $account_id
     */
    public function delete_associations_by_account( $account_id ) {
        // Now remove all lists from subscribers who have no status
		$this->prepare(
            "DELETE ma.* FROM `mobile_associations` AS ma LEFT JOIN `mobile_subscribers` AS ms ON ( ms.`mobile_subscriber_id` = ma.`mobile_subscriber_id` ) WHERE ( ms.`mobile_subscriber_id` IS NULL OR ms.`status` = 0 ) AND ms.`website_id` = :account_id"
            , array( ':account_id' => $account_id )
            , 'i'
        )->query();
    }
}
