<?php

require_once 'base-database-test.php';

class AccountPageAttachmentTest extends BaseDatabaseTest {
    /**
     * @var AccountPageAttachment
     */
    private $account_page_attachment;

    /**
     * Will be executed before every test
     */
    public function setUp() {
        $this->account_page_attachment = new AccountPageAttachment();
    }

    public function testReplace() {

    }

    /**
     * Will be executed after every test
     */
    public function tearDown() {
        $this->account_page_attachment = null;
    }
}
