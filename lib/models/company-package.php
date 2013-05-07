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
     * Get
     *
     * @param int $company_package_id
     */
    public function get( $company_package_id ) {
        $this->prepare( 'SELECT `company_package_id`, `name`, `website_id` FROM `company_packages` WHERE `company_package_id` = :company_package_id'
            , 'i'
            , array( ':company_package_id' => $company_package_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->company_package_id;
    }

    /**
     * Get all the accounts
     *
     * @param int $account_id
     * @return CompanyPackage[]
     */
    public function get_all( $account_id ) {
        return $this->prepare( 'SELECT cp.`company_package_id`, cp.`name` FROM `company_packages` AS cp LEFT JOIN `users` AS u ON ( u.`company_id` = cp.`company_id` ) LEFT JOIN `websites` AS w ON ( w.`user_id` = u.`user_id` ) WHERE w.`website_id` = :account_id ORDER BY cp.`name` ASC'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'CompanyPackage' );
    }
}
