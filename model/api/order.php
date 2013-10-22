<?php
class Order extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $order_id, $user_id, $total_amount, $total_monthly, $type, $status, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'orders' );

        // We want to make sure they match
        if ( isset( $this->order_id ) )
            $this->id = $this->order_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'user_id' => $this->user_id
            , 'total_amount' => $this->total_amount
            , 'total_monthly' => $this->total_monthly
            , 'type' => strip_tags($this->type)
            , 'status' => $this->status
            , 'date_created' => $this->date_created
        ), 'iiisis' );

        $this->order_id = $this->id = $this->get_insert_id();
    }
}
