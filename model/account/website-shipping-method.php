<?php
class WebsiteShippingMethod extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_shipping_method_id, $website_id, $type, $name, $method, $amount, $zip_codes, $extra, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_shipping_methods' );

        // We want to make sure they match
        if ( isset( $this->website_shipping_method_id ) )
            $this->id = $this->website_shipping_method_id;
    }

    /**
     * Get
     * @param int $website_shipping_method_id
     * @param int $account_id
     */
    public function get( $website_shipping_method_id, $account_id ) {
        $this->prepare(
            'SELECT `website_shipping_method_id`, `type`, `name`, `method`, `amount`, `zip_codes`, `extra` FROM `website_shipping_methods` WHERE `website_shipping_method_id` = :website_shipping_method_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':website_shipping_method_id' => $website_shipping_method_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_shipping_method_id;
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return WebsiteShippingMethod[]
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT `website_shipping_method_id`, `name`, `method`, `amount` FROM `website_shipping_methods` WHERE `website_id` = :account_id ORDER BY `date_created` ASC'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteShippingMethod' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'type' => strip_tags($this->type)
            , 'name' => strip_tags($this->name)
            , 'method' => strip_tags($this->method)
            , 'amount' => $this->amount
            , 'zip_codes' => strip_tags($this->zip_codes)
            , 'date_created' => $this->date_created
        ), 'isssiss' );

        $this->id = $this->website_shipping_method_id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
                'name' => strip_tags($this->name)
                , 'method' => strip_tags($this->method)
                , 'amount' => strip_tags($this->amount)
                , 'zip_codes' => strip_tags($this->zip_codes)
                , 'extra' => strip_tags($this->extra)
            ), array(
                'website_shipping_method_id' => $this->id
            ), 'sssss', 'i'
        );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'website_shipping_method_id' => $this->id
        ), 'i' );
    }

    /**
	 * List Shipping Methods
	 *
	 * @param $variables array( $where, $order_by, $limit )
	 * @return WebsiteShippingMethod[]
	 */
	public function list_all( $variables ) {
        // Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT `website_shipping_method_id`, `type`, `name`, `method`, `amount` FROM `website_shipping_methods` WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteShippingMethod' );
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
        return $this->prepare( "SELECT COUNT( `website_shipping_method_id` ) FROM `website_shipping_methods` WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
	}
}
