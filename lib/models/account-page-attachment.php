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
     * Get by account page ids
     *
     * @param array $account_page_ids
     * @return array
     */
    public function get_by_account_page_ids( array $account_page_ids ) {
        foreach ( $account_page_ids as &$apid ) {
            $apid = (int) $apid;
        }

        return $this->get_results( 'SELECT `website_attachment_id`, `website_page_id`, `key`, `value`, `extra`, `meta`, `sequence` FROM `website_attachments` WHERE `status` = 1 AND `website_page_id` IN (' . implode( ', ', $account_page_ids ) . ')', PDO::FETCH_CLASS, 'AccountPageAttachment' );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'website_page_id' => $this->website_page_id
            , 'key' => $this->key
            , 'value' => $this->value
            , 'extra' => $this->extra
            , 'meta' => $this->meta
            , 'sequence' => $this->sequence
        ), 'isssss' );

        $this->id = $this->website_attachment_id = $this->get_insert_id();
    }

    /**
     * Delete by attachments
     *
     * @param array $account_page_ids
     */
    public function delete_unique_attachments( array $account_page_ids ) {
        // Make sure they're all integers
        foreach ( $account_page_ids as &$apid ) {
            $apid = (int) $apid;
        }

        $this->query( "DELETE FROM `website_attachments` WHERE `key` IN( 'video', 'search', 'email' ) AND `website_page_id` IN( " . implode( ',', $account_page_ids ) . ' )' );
    }
}
