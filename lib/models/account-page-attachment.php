<?php
class AccountPageAttachment extends ActiveRecordBase {
    public $id, $website_attachment_id, $website_page_id, $key, $value, $extra, $meta, $sequence, $status;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_attachments' );

        // We want to make sure they match
        if ( isset( $this->website_attachment_id ) )
            $this->id = $this->website_attachment_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_page_id' => $this->website_page_id
            , 'key' => $this->key
            , 'value' => $this->value
            , 'sequence' => $this->sequence
        ), 'isss' );

        $this->id = $this->website_attachment_id = $this->get_insert_id();
    }
}
