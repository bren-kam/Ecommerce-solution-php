<?php

require_once 'base-database-test.php';

class MobileSubscriberTest extends BaseDatabaseTest {
    /**
     * @var MobileSubscriber
     */
    private $mobile_subscriber;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_subscriber = new MobileSubscriber();
    }

    /**
     * Test Adding bulk users
     */
    public function testAddBulk() {
        // Declare variables
        $account_id = -5;
        $mobile_numbers = array( '5555555555', '8185551234', '8185554321' );

        // Now delete them
        $this->db->delete( 'mobile_subscribers', array( 'website_id' => $account_id ), 'i' );

        // Add the subscribers
        $this->mobile_subscriber->add_bulk( $account_id, $mobile_numbers );

        // Get the numbers
        $numbers = $this->db->get_col( "SELECT `phone` FROM `mobile_subscribers` WHERE `website_id` = $account_id AND `status` = 1 ORDER BY `phone` ASC" );

        $this->assertEquals( $mobile_numbers, $numbers );

        // Now delete them
        $this->db->delete( 'mobile_subscribers', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test get the phone index of subscribers by an account
     *
     * @depends testAddBulk
     */
    public function testGetPhoneIndexByAccount() {
        // Declare variables
        $account_id = -5;
        $mobile_numbers = array( '8185551234', '8185554321', '5555555555' );

        // Now delete them
        $this->db->delete( 'mobile_subscribers', array( 'website_id' => $account_id ), 'i' );

        // Add the subscribers
        $this->mobile_subscriber->add_bulk( $account_id, $mobile_numbers );

        // Now get the index
        $fetched_index = $this->mobile_subscriber->get_phone_index_by_account( $account_id, $mobile_numbers );

        $this->assertEquals( count( $fetched_index ), count( $mobile_numbers ) );

        // Now delete them
        $this->db->delete( 'mobile_subscribers', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Empty account
     *
     * @depends testAddBulk
     */
    public function testEmptyAccount() {
        // Declare variables
        $account_id = -5;
        $mobile_numbers = array( '8185551234', '8185554321', '5555555555' );

        // Add the subscribers
        $this->mobile_subscriber->add_bulk( $account_id, $mobile_numbers );

        // Now empty the account
        $this->mobile_subscriber->empty_by_account( $account_id );

        // Make sure there aren't any
        $numbers = $this->db->get_col( "SELECT `phone` FROM `mobile_subscribers` WHERE `website_id` = $account_id AND `status` = 1" );

        $this->assertTrue( empty( $numbers ) );

        // Delete
        $this->db->delete( 'mobile_subscribers', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Test Adding bulk associations (between subscribers and lists)
     */
    public function testAddBulkAssociations() {
        // Declare variables
        $associations = array(
            array(
                'mobile_subscriber_id' => '-1'
                , 'mobile_list_id' => '-2'
                , 'trumpia_contact_id' => '-3'
            ), array(
                'mobile_subscriber_id' => '-1'
                , 'mobile_list_id' => '-3'
                , 'trumpia_contact_id' => '-4'
            ), array(
                'mobile_subscriber_id' => '-2'
                , 'mobile_list_id' => '-3'
                , 'trumpia_contact_id' => '-5'
            )
        );

        // Now delete them
        $this->db->query( "DELETE FROM `mobile_associations` WHERE `mobile_subscriber_id` IN ( -1, -2 )" );

        // Add the associations
        $this->mobile_subscriber->add_bulk_associations( $associations );

        // Get Associations
        $fetched_associations = $this->db->get_results( "SELECT * FROM `mobile_associations` WHERE `mobile_subscriber_id` IN ( -1, -2 ) ORDER BY `mobile_subscriber_id` DESC, `mobile_list_id` DESC", PDO::FETCH_ASSOC );

        $this->assertEquals( $associations, $fetched_associations );

        // Now delete them
        $this->db->query( "DELETE FROM `mobile_associations` WHERE `mobile_subscriber_id` IN ( -1, -2 )" );
    }

    /**
     * Test Getting associations by account
     *
     * @depends testAddBulkAssociations
     */
    public function testGetAssociationsByAccount() {
        // Declare variables
        $account_id = -5;
        $mobile_subscriber_id = -55;

        // Create subscriber
        $this->db->insert( 'mobile_subscribers', array( 'mobile_subscriber_id' => $mobile_subscriber_id, 'website_id' => $account_id, 'phone' => '8185551234', 'status' => 1 ), 'iisi' );

        $mobile_subscriber_id = $this->db->get_insert_id();

        $associations = array(
            array( $mobile_subscriber_id, -2, -3 ) // Mobile_subscriber_id, mobile list id, trumpia_contact_id
            , array( $mobile_subscriber_id, -3, -4 )
        );

        // Add the associations
        $this->mobile_subscriber->add_bulk_associations( $associations );

        // Get the associations
        $fetched_associations = $this->mobile_subscriber->get_associations_by_account( $account_id );

        $this->assertEquals( count( $associations ), count( $fetched_associations ) );
        $this->assertTrue( current( $fetched_associations ) instanceof stdClass );

        // Delete everything
        $this->db->delete( 'mobile_subscribers', array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 'i' );
        $this->db->delete( 'mobile_associations', array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 'i' );
    }

    /**
     * Test Deleting associations by account (for deleted subscribers)
     *
     * @depends testAddBulkAssociations
     * @depends testGetAssociationsByAccount
     */
    public function testDeleteAssociationsByAccount() {
        // Declare variables
        $account_id = -5;

        // Create subscriber
        $this->db->insert( 'mobile_subscribers', array( 'website_id' => $account_id, 'phone' => '8185551234', 'status' => 0 ), 'is' );

        $mobile_subscriber_id = $this->db->get_insert_id();

        $associations = array(
            array( $mobile_subscriber_id, -2, -3 ) // Mobile_subscriber_id, mobile list id, trumpia_contact_id
            , array( $mobile_subscriber_id, -3, -4 )
        );

        // Add the associations
        $this->mobile_subscriber->add_bulk_associations( $associations );

        // Delete them
        $this->mobile_subscriber->delete_associations_by_account( $account_id );

        // Get the associations
        $fetched_associations = $this->mobile_subscriber->get_associations_by_account( $account_id );

        $this->assertTrue( empty( $fetched_associations ) );

        // Delete subscriber
        $this->db->delete( 'mobile_subscribers', array( 'mobile_subscriber_id' => $mobile_subscriber_id ), 'i' );
    }


    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_subscriber = null;
    }
}
