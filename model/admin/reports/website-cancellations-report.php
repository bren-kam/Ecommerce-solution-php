<?php
class WebsiteCancellationsReport extends CustomReport {
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

        $report = $this->get_results( "SELECT w.`title`, CONCAT( 'http://', w.`domain`, '/' ), w.`type`, w.`date_updated`, c.`name`, u2.`contact_name` FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`os_user_id` ) WHERE w.`status` = 0 $where ORDER BY w.`date_updated` DESC", PDO::FETCH_ASSOC );

        array_unshift( $report, array( 'Website Title', 'Link', 'Type', 'Cancellation Date', 'Company', 'Online Specialist' ) );

        return $report;
    }
}
