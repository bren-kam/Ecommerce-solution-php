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
            $where .= ' AND b.`company_id` = ' . (int) $this->user->company_id;

        $report = $this->get_results( "SELECT a.`title`, b.`contact_name`, b.`billing_state`, CONCAT( 'http://', a.`domain`, '/' ) FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) WHERE a.`status` = 1 $where ORDER BY b.`billing_state` ASC, a.`title` ASC", PDO::FETCH_ASSOC );

        array_unshift( $report, array( 'Website Title', 'Store Owner', 'State', 'Link' ) );

        return $report;
    }
}
