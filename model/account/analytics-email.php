<?php
class AnalyticsEmail extends ActiveRecordBase {
    // The columns we will have access to
    public $mc_campaign_id, $syntax_errors, $hard_bounces, $soft_bounces, $unsubscribes, $abuse_reports, $forwards
        , $forwards_opens, $opens, $unique_opens, $last_open, $clicks, $unique_clicks, $last_click, $users_who_clicked
        , $emails_sent, $last_updated;

    // Artificial field
    public $advice, $click_overlay;

    // Fields from other tables
    public $subject, $date_sent;

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
}