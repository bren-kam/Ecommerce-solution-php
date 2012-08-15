<?php
class CompanyPackage extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $company_package_id, $company_id, $website_id, $name;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'company_packages' );

        // We want to make sure they match
        if ( isset( $this->company_package_id ) )
            $this->id = $this->company_package_id;
    }

    /**
     * Get all the accounts
     *
     * @param int $account_id
     * @return array
     */
    public function get_all( $account_id ) {
        return $this->prepare( 'SELECT a.`company_package_id`, a.`name` FROM `company_packages` AS a LEFT JOIN `users` AS b ON ( a.`company_id` = b.`company_id` ) LEFT JOIN `websites` AS c ON ( b.`user_id` = c.`user_id` ) WHERE c.`website_id` = :account_id ORDER BY a.`name` ASC'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'CompanyPackage' );
    }
}
