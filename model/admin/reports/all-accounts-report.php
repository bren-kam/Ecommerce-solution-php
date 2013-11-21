<?php
class AllAccountsReport extends CustomReport {
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

        $report = $this->get_results( "SELECT w.`title`, u.`contact_name`, u.`work_phone`, u.`email`, u.`billing_state`, CONCAT( 'http://', w.`domain`, '/' ) FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) WHERE w.`status` = 1 $where ORDER BY u.`billing_state` ASC, w.`title` ASC", PDO::FETCH_ASSOC );

        array_unshift( $report, array( 'Website Title', 'Store Owner', 'Phone Number', 'Email Address', 'State', 'Link' ) );

        return $report;
    }
}
