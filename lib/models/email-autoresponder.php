<?php
class EmailAutoresponder extends ActiveRecordBase {
    public $id, $email_autoresponder_id, $website_id, $email_list_id, $name, $subject, $message, $current_offer, $default, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_autoresponders' );

         // We want to make sure they match
        if ( isset( $this->email_autoresponder_id ) )
            $this->id = $this->email_autoresponder_id;
    }

    /**
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'email_list_id' => $this->email_list_id
            , 'name' => $this->name
            , 'subject' => $this->subject
            , 'message' => $this->message
            , 'current_offer' => $this->current_offer
            , 'default' => $this->default
            , 'date_created' => $this->date_created
        ), 'iisssiis' );

        $this->id = $this->email_autoresponder_id = $this->get_insert_id();
    }
}
