<?php
class SocialMediaSweepstakes extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $email_list_id, $key, $before, $after, $start_date, $end_date
        , $contest_rules_url, $share_title, $share_image_url, $share_text, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_sweepstakes' );
    }

    /**
     * Get
     *
     * @param int $sm_facebook_page_id
     */
    public function get( $sm_facebook_page_id ) {
        $this->prepare(
            'SELECT * FROM `sm_sweepstakes` WHERE `sm_facebook_page_id` = :sm_facebook_page_id'
            , 'i'
            , array( ':sm_facebook_page_id' => $sm_facebook_page_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
            , 'key' => strip_tags($this->key)
            , 'date_created' => $this->date_created
        ), 'iiss' );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'email_list_id' => $this->email_list_id
            , 'before' => strip_tags($this->before)
            , 'after' => strip_tags($this->after)
            , 'start_date' => strip_tags($this->start_date)
            , 'end_date' => strip_tags($this->end_date)
            , 'contest_rules_url' => strip_tags($this->contest_rules_url)
            , 'share_title' => strip_tags($this->share_title)
            , 'share_image_url' => strip_tags($this->share_image_url)
            , 'share_text' => strip_tags($this->share_text)
        ), array(
            'sm_facebook_page_id' => $this->sm_facebook_page_id
        ), 'issssssss', 'i' );
    }
}