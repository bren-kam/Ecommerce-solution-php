<?php
class SocialMediaFacebookPage extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $name, $status, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_facebook_page' );
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return SocialMediaFacebookPage[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `id`, `name`, `date_created` FROM `sm_facebook_page` WHERE `status` = 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'SocialMediaFacebookPage' );
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
            "SELECT COUNT( `id` ) FROM `sm_facebook_page` WHERE `status` = 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
