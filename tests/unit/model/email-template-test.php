<?php

require_once 'base-database-test.php';

class EmailTemplateTest extends BaseDatabaseTest {
    /**
     * @var EmailTemplate
     */
    private $email_template;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->email_template = new EmailTemplate();
    }

    /**
     * Test create
     */
    public function testCreate() {
        $this->email_template->template = 'purple people eaters';
        $this->email_template->type = 'default';
        $this->email_template->create();

        $this->assertTrue( !is_null( $this->email_template->id ) );

        // Make sure it's in the database
        $template = $this->db->get_var( 'SELECT `template` FROM `email_templates` WHERE `email_template_id` = ' . (int) $this->email_template->id );

        $this->assertEquals( 'purple people eaters', $template );

        // Delete
        $this->db->delete( 'email_templates', array( 'email_template_id' => $this->email_template->id ), 'i' );
    }

    /**
     * Test Add Association
     */
    public function testAddAssociation() {
        // Declare variables
        $this->email_template->id = -3;
        $object_id = '-5';
        $type = 'website';

        // Delete any associations before hand
        $this->db->delete( 'email_template_associations', array( 'email_template_id' => $this->email_template->id ) , 'i' );

        // Add association
        $this->email_template->add_association( $object_id, $type );

        // Make sure it's in the database
        $fetched_object_id = $this->db->get_var( 'SELECT `object_id` FROM `email_template_associations` WHERE `email_template_id` = ' . (int) $this->email_template->id );

        $this->assertEquals( $fetched_object_id, $object_id );

        // Delete any associations after
        $this->db->delete( 'email_template_associations', array( 'email_template_id' => $this->email_template->id ) , 'i' );
    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->email_template = null;
    }
}
