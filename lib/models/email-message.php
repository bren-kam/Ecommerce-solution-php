<?php
class EmailMessage extends ActiveRecordBase {
    const STATUS_DRAFT = 0;
    const STATUS_SCHEDULED = 1;
    const STATUS_SENT = 2;

    public $id, $email_message_id, $website_id, $email_template_id, $mc_campaign_id, $ac_campaign_id, $ac_message_id, $subject, $message, $type, $status, $date_sent, $date_created;

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
     * Get
     *
     * @param int $ac_campaign_id
     * @param int $account_id
     */
    public function get_by_ac_campaign_id( $ac_campaign_id, $account_id ) {
        $this->prepare(
            'SELECT * FROM `email_messages` WHERE `ac_campaign_id` = :ac_campaign_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':ac_campaign_id' => $ac_campaign_id, ':account_id' => $account_id )
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
     * Get Sent emails for ac_campaign_list
     *
     * @param int $account_id
     * @return array
     */
    public function get_sent_emails( $account_id ) {
        return $this->prepare(
            'SELECT `ac_campaign_id` FROM `email_messages` WHERE `website_id` = :account_id AND `status` = ' . self::STATUS_SENT . ' ORDER BY `date_sent` DESC LIMIT 10'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_col();
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'email_template_id' => $this->email_template_id
            , 'subject' => $this->subject
            , 'message' => $this->message
            , 'type' => $this->type
            , 'date_sent' => $this->date_sent
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
            , 'ac_message_id' => $this->ac_message_id
            , 'ac_campaign_id' => $this->ac_campaign_id
            , 'subject' => $this->subject
            , 'message' => $this->message
            , 'type' => $this->type
            , 'status' => $this->status
            , 'date_sent' => $this->date_sent
        ), array(
            'email_message_id' => $this->id
            , 'website_id' => $this->website_id
        ), 'iiisssis', 'ii' );
    }

    /**
    * Update Active Campaign
    *
    * @throws ModelException
    *
    * @param Account $account
    * @param array $ac_list_ids
    */
    public function update_ac_message( $account, $ac_list_ids ) {
        $ac = EmailMarketing::setup_ac( $account );
        $ac->setup_message();

        $settings = $account->get_settings( 'from_email', 'from_name' );

        // Determine from email
        $from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $account->domain : $settings['from_email'];
        $from_name = ( empty( $settings['from_name'] ) ) ? $account->title : $settings['from_name'];

        // Put the message in the template
        $template = new EmailTemplate();
        $message = $template->get_complete( $account, $this );

        // Create message
        $ac->message->edit( $this->ac_message_id, $this->subject, $from_email, $from_name, $from_email, $message, $this->message, $ac_list_ids );

        if ( $ac->error() )
            throw new ModelException( "Failed to update Active Campaign message:\n" . $ac->message() );
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

        // Delete from Active Campaign
        if ( $this->ac_campaign_id ) {
            $ac = EmailMarketing::setup_ac( $account );
            $ac->setup_campaign();

            // Delete the campaign
            if ( $this->ac_message_id ) {
                $ac->setup_message();

                if ( !$ac->message->delete( $this->ac_message_id ) )
                    throw new ModelException( $ac->message() );
            }

            if ( !$ac->campaign->delete( $this->ac_campaign_id ) )
                throw new ModelException( $ac->message() );
        }

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
     * @param array $ac_list_ids
     */
    public function test( $email, Account $account, array $ac_list_ids ) {
        $ac = EmailMarketing::setup_ac( $account );
        $ac->setup_campaign();

        // Make sure it's created
        if ( !$this->ac_campaign_id )
            $this->create_ac_campaign( $ac, $account, $ac_list_ids );

        // Send test
        $ac->campaign->send( $email, $this->ac_campaign_id, $this->ac_message_id, 'test' );

        if ( $ac->error() )
            throw new ModelException( "Failed to send test ActiveCampaign message:\n" . $ac->message() );
    }

    /**
     * Schedule
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param array $ac_list_ids
     */
    public function schedule( Account $account, array $ac_list_ids ) {
        // Active Campaign
        $ac = EmailMarketing::setup_ac( $account );
        $ac->setup_campaign();

        // Make sure it's scheduled
        if ( !$this->ac_campaign_id )
            $this->create_ac_campaign( $ac, $account, $ac_list_ids );

        $now = new DateTime();
        $date_sent = new DateTime( $this->date_sent );

        // Get active campaign date
        $ac_date = dt::adjust_timezone( $date_sent, Config::setting('server-timezone'), Config::key('ac-timezone') );

        if ( $date_sent > $now ) {
            $ac->campaign->update( $this->ac_campaign_id, ActiveCampaignCampaignAPI::STATUS_SCHEDULED, $ac_date );

            if ( $ac->error() )
                throw new ModelException( "Failed to schedule ActiveCampaign Campaign:\n" . $ac->message() );

            // Handle errors
            $this->status = self::STATUS_SCHEDULED;
            $this->save();
        } else {
            $ac->campaign->update( $this->ac_campaign_id, ActiveCampaignCampaignAPI::STATUS_SCHEDULED, $ac_date );

            if ( $ac->error() )
                throw new ModelException( "Failed to send ActiveCampaign Campaign:\n" . $ac->message() );

            $this->status = self::STATUS_SENT;
            $this->save();
        }
    }

    /**
     * Create Active Campaign Campaign
     *
     * @throws ModelException
     *
     * @param ActiveCampaignAPI $ac
     * @param Account $account
     * @param array $ac_list_ids
     */
    public function create_ac_campaign( ActiveCampaignAPI $ac, Account $account, array $ac_list_ids ) {
        // Creating a campaign/message
        $ac->setup_campaign();
        $ac->setup_message();

        $settings = $account->get_settings( 'from_email', 'from_name' );

        // Determine from email
        $from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $account->domain : $settings['from_email'];
        $from_name = ( empty( $settings['from_name'] ) ) ? $account->title : $settings['from_name'];

        // Put the message in the template
        $template = new EmailTemplate();
        $message = $template->get_complete( $account, $this );

        // Create message
        $this->ac_message_id = $ac->message->add( $this->subject, $from_email, $from_name, $from_email, $message, strip_tags( $this->message ), $ac_list_ids );

        if ( !is_int( $this->ac_message_id ) || $this->ac_message_id <= 0 )
            throw new ModelException( "Active Campaign failed to create message:\n" . $ac->message() );

        // Turn it into a date
        $ac_date = dt::adjust_timezone( $this->date_sent, Config::setting('server-timezone'), Config::setting('ac-timezone') );

        $this->ac_campaign_id = $ac->campaign->create( $this->ac_message_id, $this->subject, $ac_date, $ac_list_ids );

        if ( !is_int( $this->ac_campaign_id ) || $this->ac_campaign_id <= 0 )
            throw new ModelException( "Active Campaign failed to create campaign:\n" . $ac->message() );

        // Save
        $this->save();
    }
}