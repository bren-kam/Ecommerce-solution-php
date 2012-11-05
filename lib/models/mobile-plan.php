<?php
class MobilePlan extends ActiveRecordBase {
    public $id, $mobile_plan_id, $trumpia_plan_id, $name, $credits, $keywords;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'mobile_plans' );

        // We want to make sure they match
        if ( isset( $this->mobile_plan_id ) )
            $this->id = $this->mobile_plan_id;
    }

    /**
     * Get
     *
     * @param int $mobile_plan_id
     */
    public function get( $mobile_plan_id ) {
        $this->prepare(
            'SELECT * FROM `mobile_plans` WHERE `mobile_plan_id` = :mobile_plan_id'
            , 'i'
            , array( ':mobile_plan_id' => $mobile_plan_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->mobile_plan_id;
    }

    /**
     * Get all
     *
     * @return array
     */
    public function get_all() {
        return $this->get_results( 'SELECT * FROM `mobile_plans`', PDO::FETCH_CLASS, 'MobilePlan' );
    }
}
