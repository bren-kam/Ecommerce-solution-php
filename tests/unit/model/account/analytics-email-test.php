<?php

require_once 'base-database-test.php';

class AnalyticsEmailTest extends BaseDatabaseTest {
    /**
     * @var AnalyticsEmail
     */
    private $analytics_email;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $_SERVER['MODEL_PATH'] = basename( __DIR__ );
        $this->analytics_email = new AnalyticsEmail();
    }

    /**
     * Test create
     */
    public function testCreate() {
        // Create
        $this->analytics_email->mc_campaign_id = -5;
        $this->analytics_email->create();

        // Make sure it's in the database
        $email = $this->phactory->get_row( 'SELECT * FROM `analytics_emails` WHERE `mc_campaign_id` = ' . (int) $this->analytics_email->mc_campaign_id );

        $this->assertEquals( $this->analytics_email->mc_campaign_id, $email->mc_campaign_id );

        // Delete
        $this->phactory->delete( 'analytics_emails', array( 'mc_campaign_id' => $this->analytics_email->mc_campaign_id ), 'i' );
    }

    /**
     * Test Get
     *
     * @depends testCreate
     */
    public function testGet() {
        // Set variables
        $mc_campaign_id = -5;
        $account_id = -7;
        $subject = 'wowow';

        // Create
        $this->analytics_email->mc_campaign_id = $mc_campaign_id;
        $this->analytics_email->create();

        $email_message_id = $this->phactory->insert( 'email_messages', array(
            'website_id' => $account_id
            , 'mc_campaign_id' => $mc_campaign_id
            , 'subject' => $subject
        ), 'ii' );

        // Get
        $this->analytics_email->get( $mc_campaign_id, $account_id );

        // Make sure we grabbed the right one
        $this->assertEquals( $subject, $this->analytics_email->subject );

        // Clean up
        $this->phactory->delete( 'email_messages', compact( 'email_message_id' ), 'i' );
        $this->phactory->delete( 'analytics_emails', compact( 'mc_campaign_id' ), 'i' );
    }

    /**
     * Test Update Analytics
     *
     * @depends testGet
     */
    public function testUpdateAnalytics() {
        // Test protected method
        $class = new ReflectionClass('AnalyticsEmail');
        $method = $class->getMethod( 'update_analytics' );
        $method->setAccessible(true);

        // Set variables
        $mc_campaign_id = -15;
        $campaign_array = array(
            'syntax_errors' => 0
            , 'hard_bounces' => 1
            , 'soft_bounces' => 2
            , 'unsubscribes' => 3
            , 'abuse_reports' => 4
            , 'forwards' => 5
            , 'forwards_opens' => 6
            , 'opens' => 7
            , 'unique_opens' => 8
            , 'last_open' => 9
            , 'clicks' => 10
            , 'unique_clicks' => 11
            , 'last_click' => 12
            , 'users_who_clicked' => 13
            , 'emails_sent' => 14
        );

        // Update Analytics
        $method->invokeArgs( $this->analytics_email, array(
            $mc_campaign_id
            , $campaign_array
        ) );

        // Make sure it's in the database
        $analytics_email = $this->phactory->get_row( 'SELECT * FROM `analytics_emails` WHERE `mc_campaign_id` = ' . (int) $this->analytics_email->mc_campaign_id );

        $this->assertEquals( $campaign_array['users_who_clicked'], $analytics_email->users_who_clicked );

        // Delete
        $this->phactory->delete( 'analytics_emails', array( 'mc_campaign_id' => $mc_campaign_id ), 'i' );
    }

    /**
     * Get Emails Without Statistics
     */
    public function testGetEmailsWithoutStatistics() {
        // Variables
        $account_id = -5;
        $status = 2;
        $mc_campaign_id = -9;

        // Create
        $email_message_id = $this->phactory->insert( 'email_messages', array(
            'website_id' => $account_id
            , 'mc_campaign_id' => $mc_campaign_id
            , 'status' => $status
        ), 'iii' );

        $mc_campaign_ids = $this->analytics_email->get_emails_without_statistics( $account_id );

        $this->assertTrue( is_array( $mc_campaign_ids ) );

        // Delete
        $this->phactory->delete( 'email_messages', array( 'email_message_id' => $email_message_id ), 'i' );
    }

    /**
     * Update By Account
     *
     * @depends testGetEmailsWithoutStatistics
     * @depends testGet
     */
    public function testUpdateByAccount() {
        // Declare variables
        $mc_campaign_id = -15;
        $website_id = -7;
        $status = 2;
        $campaign_array = array(
            'syntax_errors' => 0
            , 'hard_bounces' => 1
            , 'soft_bounces' => 2
            , 'unsubscribes' => 3
            , 'abuse_reports' => 4
            , 'forwards' => 5
            , 'forwards_opens' => 6
            , 'opens' => 7
            , 'unique_opens' => 8
            , 'last_open' => '1234-56-78 01:23:45'
            , 'clicks' => 10
            , 'unique_clicks' => 11
            , 'last_click' => 12
            , 'users_who_clicked' => 13
            , 'emails_sent' => 14
        );

        // Get stub
        library( 'MCAPI' );
        $stub_mc = $this->getMock( 'MCAPI', array(), array(), '', false );
        $stub_mc->expects($this->any())->method('campaignStats')->with( $mc_campaign_id )->will($this->returnValue( $campaign_array ) );

        // Create
        $this->phactory->insert( 'email_messages', array(
            'website_id' => $website_id
            , 'mc_campaign_id' => $mc_campaign_id
            , 'status' => $status
        ), 'iii' );

        // Do the update
        $this->analytics_email->update_by_account( $website_id, $stub_mc );

        // Get the email
        $this->analytics_email->get( $mc_campaign_id, $website_id );

        $this->assertEquals( $campaign_array['unique_opens'], $this->analytics_email->unique_opens );

        // Clean Up
        $this->phactory->delete( 'email_messages', compact( 'mc_campaign_id' ), 'i' );
        $this->phactory->delete( 'analytics_emails', compact( 'mc_campaign_id' ), 'i' );
    }

    /**
     * List All
     */
    public function testListAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'em.`subject`', 'ae.`emails_sent`', 'ae.`open`', 'ae.`clicks`', 'em.`date_sent`' );

        $emails = $this->analytics_email->list_all( $dt->get_variables() );

        // Make sure we have an array
        $this->assertTrue( current( $emails ) instanceof AnalyticsEmail );

        // Get rid of everything
        unset( $user, $_GET, $dt, $emails );
    }

    /**
     * Count All
     */
    public function testCountAll() {
        $user = new User();
        $user->get_by_email('test@greysuitretail.com');

        // Determine length
        $_GET['iDisplayLength'] = 30;
        $_GET['iSortingCols'] = 1;
        $_GET['iSortCol_0'] = 1;
        $_GET['sSortDir_0'] = 'asc';

        $dt = new DataTableResponse( $user );
        $dt->order_by( 'em.`subject`', 'ae.`emails_sent`', 'ae.`open`', 'ae.`clicks`', 'em.`date_sent`' );

        $count = $this->analytics_email->count_all( $dt->get_count_variables() );

        // Make sure they exist
        $this->assertGreaterThan( 0, $count );

        // Get rid of everything
        unset( $user, $_GET, $dt, $count );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        unset( $_SERVER['MODEL_PATH'] );
        $this->analytics_email = null;
    }
}
