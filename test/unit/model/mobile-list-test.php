<?php

require_once 'test/base-database-test.php';

class MobileListTest extends BaseDatabaseTest {
    /**
     * @var MobileList
     */
    private $mobile_list;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->mobile_list = new MobileList();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->mobile_list->website_id = -3;
        $this->mobile_list->name = 'Wee Hart';
        $this->mobile_list->frequency = 10;
        $this->mobile_list->description = 'For the Brave';
        $this->mobile_list->create();

        // Make sure it's in the database
        $name = $this->phactory->get_var( 'SELECT `name` FROM `mobile_lists` WHERE `mobile_list_id` = ' . (int) $this->mobile_list->id );

        $this->assertEquals( 'Wee Hart', $name );

        // Delete
        $this->phactory->delete( 'mobile_lists', array( 'mobile_list_id' => $this->mobile_list->id ), 'i' );
    }

    /**
     * Test getting accounts indexed by their name
     *
     * @depends testCreate
     */
    public function testGetNameIndexByAccount() {
        // Declare variables
        $account_id = -5;

        // Create
        $this->mobile_list->website_id = $account_id;
        $this->mobile_list->name = 'Wee Hart';
        $this->mobile_list->frequency = 10;
        $this->mobile_list->description = 'For the Brave';
        $this->mobile_list->create();

        $mobile_list_id = $this->mobile_list->id;

        $this->mobile_list->name = 'Large Heart';
        $this->mobile_list->description = ' For the Weak';
        $this->mobile_list->create();

        $mobile_list_id2 = $this->mobile_list->id;

        // Get the lists
        $lists = $this->mobile_list->get_name_index_by_account( $account_id );

        $this->assertEquals( array(
            'Wee Hart' => $mobile_list_id
            , 'Large Heart' => $mobile_list_id2
        ), $lists );

        // Delete lists
        $this->phactory->delete( 'mobile_lists', array( 'website_id' => $account_id ), 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->mobile_list = null;
    }
}
