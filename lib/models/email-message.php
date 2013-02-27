<?php
class EmailMessage extends ActiveRecordBase {
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
            $product_ids = array();;

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
            , 'mc_campaign_id' => $this->mc_campaign_id
            , 'subject' => $this->subject
            , 'message' => $this->message
            , 'type' => $this->type
            , 'date_sent' => $this->date_sent
        ), array(
            'email_template_id' => $this->email_template_id
            , 'website_id' => $this->website_id
        ), 'iissss', 'ii' );
    }

    /**
     * Update Mailchimp
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param array $email_lists
     */
    public function update_mailchimp( $account, $email_lists ) {
        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        $segmentation_options = array(
            'match' => 'any',
            'conditions' => array(
                array(
                      'field' => 'interests',
                      'op' => 'one',
                      'value' => implode( ',', $email_lists )
                )
            )
        );

        // Do segment test to make sure it would work
        if ( !$mc->campaignSegmentTest( $account->mc_list_id, $segmentation_options ) )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update campaign
        $mc->campaignUpdate( $this->mc_campaign_id, 'segment_opts', $segmentation_options );

        // Handle any error
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update Subject
        $mc->campaignUpdate( $this->mc_campaign_id, 'subject', $this->subject );

        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update Message
        $this->get_smart_meta();
        $email_template = new EmailTemplate();
        $html_message = $email_template->get_complete( $account, $this );

        $mc->campaignUpdate( $this->mc_campaign_id, 'content', array( 'html' => $html_message ) );

        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update From Email
        $settings = $account->get_email_settings( 'from_email', 'from_name' );
        $from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $account->domain : $settings['from_email'];

        $mc->campaignUpdate( $this->mc_campaign_id, 'from_email', $from_email );

        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update From Name
        $from_name = ( empty( $settings['from_name'] ) ) ? $account->title : $settings['from_name'];

        $mc->campaignUpdate( $this->mc_campaign_id, 'from_name', $from_name );

        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );
    }

    /**
     * Remove All
     */
    public function remove_all() {
        if ( !$this->id )
            return;

        // Delete from Mailchimp
        if ( $this->mc_campaign_id ) {
            library( 'MCAPI' );
            $mc = new MCAPI( Config::key('mc-api') );

            // Delete the campaign
            $mc->campaignDelete( $this->mc_campaign_id );

            // Simply note the error, don't stop
            if ( $mc->errorCode )
                throw new ModelException( $mc->errorMessage, $mc->errorCode );
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
	 * Update scheduled emails
	 *
	 * This function assumes MailChimp will send the email at the right time.
	 * We simply mark it as sent when it has past the date it is SUPPOSED to send
	 *
	 * @return bool
	 */
	public function update_scheduled_emails() {
		$this->query( "UPDATE `email_messages` SET `status` = 2 WHERE `status` = 1 AND `date_sent` < NOW()" );
    }

    /**
     * Get Dashboard Messages By Account
     *
     * @param int $account_id
     * @return EmailMessage[]
     */
    public function get_dashboard_messages_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `email_message_id`, `mc_campaign_id`, `subject` FROM `email_messages` WHERE `website_id` = :account_id AND `status` = 2 ORDER BY `date_sent` DESC LIMIT 5'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * List Pages
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailMessage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `email_message_id`, `mc_campaign_id`, `subject`, `status`, `date_sent` FROM `email_messages` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'EmailMessage' );
    }

    /**
     * Count all the pages
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
     * @param string $email
     * @param Account $account
     * @param array $email_lists
     */
    public function test( $email, Account $account, array $email_lists ) {
        if ( !$this->mc_campaign_id )
            $this->create_mailchimp_campaign( $account, $email_lists );

        // Send a test
        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );
        $mc->campaignSendTest( $this->mc_campaign_id, array( $email ) );
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
        if ( $this->mc_campaign_id ) {
            $email_list = new EmailList();
            $email_list->synchronize( $account );
        } else {
            $this->create_mailchimp_campaign( $account, $email_lists );
        }

        $now = new DateTime();
        $date_sent = new DateTime( $this->date_sent );

        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        if ( $date_sent > $now ) {
            $date_sent->add( new DateInterval('P5H') );

            $mc->campaignSchedule( $this->mc_campaign_id, $date_sent->format('Y-m-d H:i:s') );

            // Handle errors
            if ( $mc->errorCode )
                throw new ModelException( $mc->errorMessage, $mc->errorCode );

            $this->status = 1;
            $this->save();
        } else {
            // Send campaign now
            $mc->campaignSendNow( $this->mc_campaign_id );

            // Handle errors
            if ( $mc->errorCode )
                throw new ModelException( $mc->errorMessage, $mc->errorCode );

            $this->status = 2;
            $this->save();
        }
    }

    /**
     * Create Mailchimp Campaign
     *
     * @throws ModelException
     *
     * @param Account $account
     * @param array $email_lists
     */
    public function create_mailchimp_campaign( Account $account, $email_lists ) {
        $email_list = new EmailList();
        $email_list->synchronize( $account );

        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        // Check to make sure all the interest groups exist
        $interest_groups = $mc->listInterestGroups( $account->mc_list_id );

        // Simply note the error, don't stop
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        foreach ( $email_lists as $el ) {
            if ( in_array( $el, $interest_groups['groups'] ) )
                continue;

            $mc->listInterestGroupAdd( $account->mc_list_id, $el );

            // Handle Errors
            if ( $mc->errorCode )
                throw new ModelException( $mc->errorMessage, $mc->errorCode );
        }

        $segmentation_options = array(
            'match' => 'any',
            'conditions' => array(
                array(
                      'field' => 'interests',
                      'op' => 'one',
                      'value' => implode( ',', $email_lists )
                )
            )
        );

        // Handle Errors
        if ( !$mc->campaignSegmentTest( $account->mc_list_id, $segmentation_options ) ) {
            $message = ( empty( $mc->errorMessage ) ) ? "MailChimp was unable to segment campaign" : $mc->errorMessage;
            throw new ModelException( $message, $mc->errorCode );
        }

        $settings = $account->get_email_settings( 'from_email', 'from_name' );

        // Determine from email
        $from_email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . $account->domain : $settings['from_email'];
        $from_name = ( empty( $settings['from_name'] ) ) ? $account->title : $settings['from_name'];

        $options = array(
            'list_id' => $account->mc_list_id,
            'subject' => $this->subject,
            'from_email' => $from_email,
            'from_name' => $from_name,
            'to_email' => $account->title . ' Subscribers',
            'tracking' => array(
                    'opens' => true,
                    'html_clicks' => true,
                    'text_clicks' => true
                ),
            'analytics' => array( 'google' => $account->ga_tracking_key ),
            'generate_text' => true
        );

        // Put the message in the template
        $template = new EmailTemplate();
        $message = $template->get_complete( $account, $this );

        $content = array(
            'html' => $message
        );

        $this->mc_campaign_id = $mc->campaignCreate( 'regular', $options, $content, $segmentation_options );
        $this->save();

        // Handle Errors
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );
    }
}
