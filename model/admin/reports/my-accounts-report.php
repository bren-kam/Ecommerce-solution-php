<?php
class MyAccountsReport extends CustomReport {
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

        if ( $this->user->has_permission( User::ROLE_SUPER_ADMIN ) || $this->user->has_permission( User::ROLE_ADMIN )){
            $where .= ' AND w.`os_user_id` = '. (int) $this->user->id;

            $report = $this->get_results( "SELECT  u.`email` AS email, u.`contact_name` AS name, c.`name` AS company, u2.`contact_name` as online_specialist FROM `websites` AS w LEFT JOIN `users` AS u ON ( u.`user_id` = w.`user_id` ) LEFT JOIN `companies` AS c ON ( c.`company_id` = u.`company_id` ) LEFT JOIN `users` AS u2 ON ( u2.`user_id` = w.`os_user_id` ) WHERE w.`status` = 1 $where ORDER BY company ASC", PDO::FETCH_ASSOC );
        }else{
            $report=array();
        }

        array_unshift( $report, array( 'Email Address', 'Name',  'Company', 'Online Specialist' ) );

        return $report;
    }
}
