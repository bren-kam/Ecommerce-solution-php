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
     * Get By Type
     *
     * @param string $type
     * @param int $account_id
     * @return WebsiteShippingMethod
     */
    public function get_by_type($type, $account_id) {
        return $this->prepare(
            'SELECT `website_shipping_method_id`, `name`, `method`, `amount` FROM `website_shipping_methods` WHERE `website_id` = :account_id AND `type` = :shipping_type ORDER BY `date_created` ASC'
            , 'is'
            , array( ':account_id' => $account_id, ':shipping_type' => $type )
        )->get_results( PDO::FETCH_CLASS, 'WebsiteShippingMethod' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->id = $this->website_shipping_method_id = $this->insert( array(
            'website_id' => $this->website_id
            , 'type' => strip_tags($this->type)
            , 'name' => strip_tags($this->name)
            , 'method' => strip_tags($this->method)
            , 'amount' => $this->amount
            , 'zip_codes' => strip_tags($this->zip_codes)
            , 'date_created' => $this->date_created
            , 'extra' => strip_tags($this->extra)
        ), 'isssisss' );
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
     * Remove a specific record based on type.
     * @param string $type
     * @param int $website_id
     */
    public function remove_by_type($type, $website_id) {
        $this->delete(['type' => $type, 'website_id' => $website_id], 'si');
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
            "SELECT `website_shipping_method_id`, `type`, `name`, `method`, `amount`, `extra` FROM `website_shipping_methods` WHERE 1 $where $order_by LIMIT $limit"
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

    /**
     * Get Description
     * @param  string $name
     * @return string
     */
    public function get_description( $name = null) {
        $services = array(
            '02' => _('UPS Second Day Air')
            , '03' => _('UPS Ground')
            , '07' => _('UPS Worldwide Express')
            , '08' => _('UPS Worldwide Expedited')
            , '11' => _('UPS Standard')
            , '12' => _('UPS Three-Day Select')
            , '13' => _('Next Day Air Saver')
            , '14' => _('UPS Next Day Air Early AM')
            , '54' => _('UPS Worldwide Express Plus')
            , '59' => _('UPS Second Day Air AM')
            , '65' => _('UPS Saver')
            , 'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => _('Europe First International Priority')
            , 'FEDEX_1_DAY_FREIGHT' => _('FedEx 1 Day Freight')
            , 'FEDEX_2_DAY' => _('FedEx 2 Day')
            , 'FEDEX_2_DAY_FREIGHT' => _('FedEx 2 Day Freight')
            , 'FEDEX_3_DAY_FREIGHT' => _('FedEx 3 Day Freight')
            , 'FEDEX_EXPRESS_SAVER' => _('FedEx Express Saver')
            , 'FEDEX_GROUND' => _('FedEx Ground')
            , 'FIRST_OVERNIGHT' => _('First Overnight')
            , 'GROUND_HOME_DELIVERY' => _('Ground Home Delivery')
            , 'INTERNATIONAL_ECONOMY' => _('International Economy')
            , 'INTERNATIONAL_ECONOMY_FREIGHT' => _('International Economy Freight')
            , 'INTERNATIONAL_FIRST' => _('International First')
            , 'INTERNATIONAL_PRIORITY' => _('International Priority')
            , 'INTERNATIONAL_PRIORITY_FREIGHT' => _('International Priority Freight')
            , 'PRIORITY_OVERNIGHT' => _('Priority Overnight')
            , 'SMART_POST' => _('Smart Post')
            , 'STANDARD_OVERNIGHT' => _('Standard Overnight')
            , 'FEDEX_FREIGHT' => _('FedEx Freight')
            , 'FEDEX_NATIONAL_FREIGHT' => _('FedEx National Freight')
        );

        if ( !$name ) {
            $name = $this->name;
        }

        return isset( $services[ $name ] ) ? $services[ $name ] : $name;
    }
}
