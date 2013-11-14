<?php
class EmailMessage extends ActiveRecordBase {
    const TEST_EMAIL_LIST = 'One-Time-Test';
    const STATUS_DRAFT = 0;
    const STATUS_SCHEDULED = 1;
    const STATUS_SENT = 2;

    public $id, $email_message_id, $website_id, $email_template_id, $mc_campaign_id, $subject, $message, $type, $status, $date_sent, $date_created;

    // Artifical columns
    public $email_lists, $meta;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_messages' );

        // We want to make sure they match
        if ( isset( $this->email_message_id ) )
            $this->id = $this->email_message_id;
    }

    /**
     * Get
     *
     * @param int $email_message_id
     * @param int $account_id
     */
    public function get( $email_message_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_messages` WHERE `email_message_id` = :email_message_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':email_message_id' => $email_message_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->email_message_id;
    }

    /**
     * Get Meta
     */
    public function get_smart_meta() {
        if ( !$this->id )
            return;

        // Get the meta data
        $meta_data = $this->get_meta();

        if ( 'product' == $this->type ) {
            // Start off the product ids
            $product_ids = array();

            if ( is_array( $meta_data ) )
            foreach ( $meta_data as $md ) {
                // Get variables
                $product_array = unserialize( html_entity_decode( $md['value'], ENT_QUOTES, 'UTF-8' ) );
                $this->meta[$product_array['product_id']]->price = $product_array['price'];
                $this->meta[$product_array['product_id']]->order = $product_array['order'];

                // Create list of product ids
                $product_ids[] = $product_array['product_id'];
            }

            // Causes an error otherwise
            if ( empty( $product_ids ) ) {
                $this->meta = array();
            } else {
                $product = new Product;

                // Get products
                $products = $product->get_by_ids( $product_ids );

                // Put the data in the meta
                foreach ( $products as $product ) {
                    $order = $this->meta[$product->id]->order;
                    $price = $this->meta[$product->id]->price;

                    $this->meta[$product->id] = $product;
                    $this->meta[$product->id]->order = $order;
                    $this->meta[$product->id]->price = $price;
                }
            }
        } else {
            $this->meta = ar::assign_key( $meta_data, 'type', true );
        }
    }

    /**
     * Get meta
     *
     * @return array
     */
    public function get_meta() {
        return $this->prepare(
            'SELECT `type`, `value` FROM `email_message_meta` WHERE `email_message_id` = :email_message_id'
            , 'i'
            , array( ':email_message_id' => $this->id )
        )->get_results( PDO::FETCH_ASSOC );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'email_template_id' => $this->email_template_id
            , 'subject' => strip_tags($this->subject)
            , 'message' => format::strip_only( $this->message, '<script>' )
            , 'type' => strip_tags($this->type)
            , 'date_sent' => strip_tags($this->date_sent)
            , 'date_created' => $this->date_created
        ), 'iisssss' );

        $this->id = $this->email_message_id = $this->get_insert_id();
    }

    /**
     * Add Associations
     *
     * @param array $email_list_ids
     */
    public function add_associations( array $email_list_ids ) {
        $email_message_id = (int) $this->id;

        $values = '';

        if ( is_array( $email_list_ids ) )
        foreach ( $email_list_ids as $el_id ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $email_message_id," . (int) $el_id . ' )';
        }

        $this->query( "INSERT INTO `email_message_associations` ( `email_message_id`, `email_list_id` ) VALUES $values" );
    }

    /**
     * Add Message Meta
     *
     * @param array $meta
     */
    public function add_meta( array $meta ) {
        $email_message_id = (int) $this->id;

        // Create values to insert
        $values = '';

        foreach ( $meta as $m ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $email_message_id, " . $this->quote( $m[0] ) . ", " . $this->quote( $m[1] ) . " )";
        }

        // Insert new meta
        if ( !empty( $values ) )
            $this->query( "INSERT INTO `email_message_meta` ( `email_message_id`, `type`, `value` ) VALUES $values" );
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'email_template_id' => $this->email_template_id
            , 'subject' => strip_tags($this->subject)
            , 'message' => format::strip_only( $this->message, '<script>' )
            , 'type' => strip_tags($this->type)
            , 'status' => $this->status
            , 'date_sent' => strip_tags($this->date_sent)
        ), array(
            'email_message_id' => $this->id
            , 'website_id' => $this->website_id
        ), 'isssis', 'ii' );
    }

    /**
     * Remove All
     *
     * @throws ModelException
     *
     * @param Account $account
     */
    public function remove_all( Account $account ) {
        if ( !$this->id )
            return;

        // Assuming the above is successful, delete everything about this email
        $this->remove_associations();
        $this->remove_meta();
        $this->remove();
    }

    /**
     * Remove/Delete
     */
    protected function remove() {
        $this->delete( array(
            'email_message_id' => $this->id
        ), 'i' );
    }


    /**
     * Remove Associations
     */
    public function remove_associations() {
        $this->prepare(
            'DELETE FROM `email_message_associations` WHERE `email_message_id` = :email_message_id'
            , 'i'
            , array( ':email_message_id' => $this->id )
        )->query();
    }

    /**
     * Remove Meta
     */
    public function remove_meta() {
        $this->prepare(
            'DELETE FROM `email_message_meta` WHERE `email_message_id` = :email_message_id'
            , 'i'
            , array( ':email_message_id' => $this->id )
        )->query();
    }

    /**
     * Get Dashboard Messages By Account
     *
     * @param int $account_id
     * @return EmailMessage[]
     */
    public function get_dashboard_messages_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_message_id`, `ac_campaign_id`, `subject` FROM `email_messages` WHERE `website_id` = :account_id AND `status` = ' . self::STATUS_SENT . ' ORDER BY `date_sent` DESC LIMIT 5'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * List Messages
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailMessage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `email_message_id`, `ac_campaign_id`, `subject`, `status`, `date_sent` FROM `email_messages` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * Count all the messages
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( `email_message_id` ) FROM `email_messages` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }

    /**
     * Test
     *
     * @throws ModelException
     *
     * @param string $email
     * @param Account $account
     */
    public function test( $email, Account $account ) {
        // Sendgrid
        $settings = $account->get_settings( 'sendgrid-username', 'sendgrid-password' );
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $account, $settings['sendgrid-username'], $settings['sendgrid-password'] );

        $sendgrid->setup_marketing_email();
        $sendgrid->setup_recipient();
        $sendgrid->setup_schedule();
        $sendgrid->setup_list();
        $sendgrid->setup_email();

        // Text email
        $text_mail = strip_tags( str_replace( '<br>', "\n", $this->message ) );

        $test_email_name = md5( $this->id );

        // Create message
        $sendgrid->marketing_email->add( $account->id, $test_email_name, $this->subject, $text_mail, $this->message );
        $sendgrid->list->delete( self::TEST_EMAIL_LIST );
        $sendgrid->list->add( self::TEST_EMAIL_LIST );
        $sendgrid->email->add( self::TEST_EMAIL_LIST, array( $email ) );
        $sendgrid->email->get( self::TEST_EMAIL_LIST );
        $sendgrid->list->get( self::TEST_EMAIL_LIST );
        $sendgrid->recipient->add( self::TEST_EMAIL_LIST, $test_email_name );
        $sendgrid->schedule->add( $test_email_name );

        if ( $sendgrid->error() )
            throw new ModelException( 'SendGrid failed to send test: ' . $sendgrid->message() );
    }

    /**
     * Schedule
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param array $email_lists
     */
    public function schedule( Account $account, array $email_lists ) {
        // Active Campaign
        $settings = $account->get_settings( 'sendgrid-username', 'sendgrid-password' );
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
        $sendgrid->setup_marketing_email();
        $sendgrid->setup_recipient();
        $sendgrid->setup_schedule();

        // Change any existing schedule
        $sendgrid->schedule->delete( $this->id );
        $sendgrid->marketing_email->delete( $this->id );

        $lists = $sendgrid->recipient->get( $this->id );

        foreach ( $lists as $list ) {
            $sendgrid->recipient->delete( $this->id, $list );
        }

        $now = new DateTime();
        $date_sent = new DateTime( $this->date_sent );

        // Get sendgrid date
        $sendgrid_date = dt::adjust_timezone( $this->date_sent, Config::setting('server-timezone'), Config::key('ac-timezone') );

        // Text email
        $text_mail = strip_tags( str_replace( '<br>', "\n", $this->message ) );

        // Create message
        $sendgrid->marketing_email->add( $account->id, $this->id, $this->subject, $text_mail, $this->message );

        foreach ( $email_lists as $email_list ) {
            $sendgrid->recipient->add( $email_list->name, $this->id );
        }

        if ( $date_sent > $now ) {
            // Schedule for the future
            $sendgrid->schedule->add( $this->id, $sendgrid_date );

            if ( $sendgrid->error() )
                throw new ModelException( "Failed to schedule Sendgrid marketing email:\n" . $sendgrid->message() );

            // Handle errors
            $this->status = self::STATUS_SCHEDULED;
            $this->save();
        } else {
            $sendgrid->schedule->add( $this->id );

            if ( $sendgrid->error() )
                throw new ModelException( "Failed to send Sendgrid marketing email:\n" . $sendgrid->message() );

            $this->status = self::STATUS_SENT;
            $this->save();
        }
    }
}