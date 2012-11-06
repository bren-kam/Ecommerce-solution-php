<?php
class OrderItem extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $order_item_id, $order_id, $item, $quantity, $amount, $monthly;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'order_items' );

        // We want to make sure they match
        if ( isset( $this->order_item_id ) )
            $this->id = $this->order_item_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'order_id' => $this->order_id
            , 'item' => $this->item
            , 'quantity' => $this->quantity
            , 'amount' => $this->amount
            , 'monthly' => $this->monthly
        ), 'isiii' );

        $this->order_item_id = $this->id = $this->get_insert_id();
    }
}
