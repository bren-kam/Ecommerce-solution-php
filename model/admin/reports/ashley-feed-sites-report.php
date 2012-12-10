<?php
class AshleyFeedSitesReport extends CustomReport {
    /**
     * @var User
     */
    protected $user;

    /**
     * Setup
     *
     * @param User $user
     */
    public function __construct( User $user ) {
        parent::__construct();

        $this->user = $user;
    }

    /**
     * Get the All Accounts Report
     *
     * @return array
     */
    public function report() {
        $where = '';

        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            $where .= ' AND u.`company_id` = ' . (int) $this->user->company_id;

        $report = $this->get_results( "SELECT w.`title`, c.`name`, ws.`value` AS ashley_ftp_username, u2.`contact_name`, CONCAT( 'http://', w.`domain`, '/' ), COUNT( wp.`product_id` ) FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`os_user_id` ) LEFT JOIN `website_products` AS wp ON ( wp.`website_id` = w.`website_id` ) LEFT JOIN `products` AS p ON ( p.`product_id` = wp.`product_id` ) LEFT JOIN `website_settings` AS ws ON ( ws.`website_id` = w.`website_id` AND ws.`key` = 'ashley-ftp-username' ) LEFT JOIN `website_settings` AS ws2 ON ( ws2.`website_id` = w.`website_id` AND ws2.`key` = 'ashley-ftp-password' ) WHERE w.`status` = 1 AND wp.`active` = 1 AND p.`publish_visibility` = 'public' AND ws.`value` <> '' AND ws2.`value` <> '' $where GROUP BY w.`website_id`", PDO::FETCH_ASSOC );

        foreach ( $report as &$r ) {
            $r['ashley_ftp_username'] = security::decrypt( base64_decode( $r['ashley_ftp_username'] ), ENCRYPTION_KEY );
        }

        array_unshift( $report, array( 'Website Title', 'Company', 'Ashley FTP Username', 'Online Specialist', 'Link', 'Product Count' ) );
		
        return $report;
    }
}
