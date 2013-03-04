<?php
class AnalyticsEmail extends ActiveRecordBase {
    // The columns we will have access to
    public $mc_campaign_id, $syntax_errors, $hard_bounces, $soft_bounces, $unsubscribes, $abuse_reports, $forwards
        , $forwards_opens, $opens, $unique_opens, $last_open, $clicks, $unique_clicks, $last_click, $users_who_clicked
        , $emails_sent, $last_updated;

    // Artificial field
    public $advice, $click_overlay;

    // Fields from other tables
    public $email_message_id, $subject, $date_sent;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'analytics_emails' );
    }
    
    /**
     * Gets an individual email
     *
     * @throws ModelException
     * @param string $mc_campaign_id
     * @param int $account_id
     */
    public function get_complete( $mc_campaign_id, $account_id ) {
        library( 'MCAPI' );
        $mc = new MCAPI( Config::key('mc-api') );

        // Get the statistics
        $s = $mc->campaignStats( $mc_campaign_id );

        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Update the analytics
        $this->update_analytics( $mc_campaign_id, $s );

        // Get analytics data
        $this->get( $mc_campaign_id, $account_id );

        // Get advice
        $this->advice = $mc->campaignAdvice( $mc_campaign_id );

        // Handle errors
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );

        // Get click overlay
        $this->click_overlay = $mc->campaignClickStats( $mc_campaign_id );

        // Handle errors
        if ( $mc->errorCode )
            throw new ModelException( $mc->errorMessage, $mc->errorCode );
    }

    /**
     * Get
     *
     * @param string $mc_campaign_id
     * @param int $account_id
     */
    public function get( $mc_campaign_id, $account_id ) {
        $this->prepare(
            'SELECT ae.*, em.`mc_campaign_id`, em.`subject`, em.`date_sent` FROM `analytics_emails` AS ae INNER JOIN `email_messages` AS em ON ( em.`mc_campaign_id` = ae.`mc_campaign_id` ) WHERE ae.`mc_campaign_id` = :mc_campaign_id AND em.`website_id` = :account_id'
            , 'si'
            , array( ':mc_campaign_id' => $mc_campaign_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array( 
            'mc_campaign_id' => $this->mc_campaign_id
            , 'syntax_errors' => $this->syntax_errors
            , 'hard_bounces' => $this->hard_bounces
            , 'soft_bounces' => $this->soft_bounces
            , 'unsubscribes' => $this->unsubscribes
            , 'abuse_reports' => $this->abuse_reports
            , 'forwards' => $this->forwards
            , 'forwards_opens' => $this->forwards_opens
            , 'opens' => $this->opens
            , 'unique_opens' => $this->unique_opens
            , 'last_open' => $this->last_open
            , 'clicks' => $this->clicks
            , 'unique_clicks' => $this->unique_clicks
            , 'last_click' => $this->last_click
            , 'users_who_clicked' => $this->users_who_clicked
            , 'emails_sent' => $this->emails_sent
        ), 'siiiiiiiiisiisii', true );
    }

    /**
     * Update Analytics
     *
     * @param string $mc_campaign_id
     * @param array $s
     */
    protected function update_analytics( $mc_campaign_id, array $s ) {
        $this->mc_campaign_id = $mc_campaign_id;
        $this->syntax_errors = $s['syntax_errors'];
        $this->hard_bounces = $s['hard_bounces'];
        $this->soft_bounces = $s['soft_bounces'];
        $this->unsubscribes = $s['unsubscribes'];
        $this->abuse_reports = $s['abuse_reports'];
        $this->forwards = $s['forwards'];
        $this->forwards_opens = $s['forwards_opens'];
        $this->opens = $s['opens'];
        $this->unique_opens = $s['unique_opens'];
        $this->last_open = $s['last_open'];
        $this->clicks = $s['clicks'];
        $this->unique_clicks = $s['unique_clicks'];
        $this->last_click = $s['last_click'];
        $this->users_who_clicked = $s['users_who_clicked'];
        $this->emails_sent = $s['emails_sent'];
        $this->create();
    }

    /**
     * Update Email Analytics By Account
     *
     * @throws ModelException
     *
     * @param int $account_id
     */
    public function update_by_account( $account_id ) {
        $mc_campaign_ids = $this->get_emails_without_statistics( $account_id );

        // If there are any statistics to get
        if ( count( $mc_campaign_ids ) > 0 ) {
            library( 'MCAPI' );
            $mc = new MCAPI( Config::key('mc-api') );

            // Loop through each one
            foreach ( $mc_campaign_ids as $mc_campaign_id ) {
                // Get the statistics
                $s = $mc->campaignStats( $mc_campaign_id );

                if ( $mc->errorCode ) {
                    continue;
                    throw new ModelException( $mc->errorMessage, $mc->errorCode );
                }

                $this->update_analytics( $mc_campaign_id, $s );
            }
        }
    }

    /**
     * Get emails without statistics
     *
     * @param int $account_id
     * @return array
     */
    public function get_emails_without_statistics( $account_id ) {
        return $this->prepare(
            'SELECT `mc_campaign_id` FROM `email_messages` WHERE `website_id` = :account_id AND `status` = 2 AND `mc_campaign_id` NOT IN ( SELECT ae.`mc_campaign_id` FROM `analytics_emails` AS ae LEFT JOIN `email_messages` AS em ON ( em.`mc_campaign_id` = ae.`mc_campaign_id` ) WHERE em.`website_id` = :account_id2 AND `status` = 2 )'
            , 'ii'
            , array( ':account_id' => $account_id, ':account_id2' => $account_id )
        )->get_col();
    }

    /**
     * List All
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return EmailMessage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT em.`email_message_id`, ae.`mc_campaign_id`, em.`subject`, ae.`opens`, ae.`clicks`, ae.`emails_sent`, em.`date_sent`, ae.`last_updated` FROM `analytics_emails` AS ae INNER JOIN `email_messages` AS em ON ( em.`mc_campaign_id` = ae.`mc_campaign_id` ) WHERE em.`status` = 2 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AnalyticsEmail' );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( em.`email_message_id` ) FROM `analytics_emails` AS ae INNER JOIN `email_messages` AS em ON ( em.`mc_campaign_id` = ae.`mc_campaign_id` ) WHERE em.`status` = 2 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}