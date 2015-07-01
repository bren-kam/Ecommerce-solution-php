<?php
class ProductOption extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $product_id, $name, $type;

    /**
     * @var ProductOptionItem[]
     */
    protected $items;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'product_option' );
    }

    /**
     * Create
     */
    public function create() {
        $this->id = $this->insert([
            'website_id' => $this->website_id
            , 'product_id' => $this->product_id
            , 'name' => $this->name
            , 'type' => $this->type
        ], 'iiss');
    }

    /**
     * Get by product
     *
     * @param int $website_id
     * @param int $product_id
     * @return ProductOption[]
     */
    public function get_by_product( $website_id, $product_id ) {
        return $this->prepare(
            'SELECT `id`, `name`, `type` FROM `product_option` WHERE `website_id` = :website_id AND `product_id` = :product_id'
            , 'ii'
            , array( ':website_id' => $website_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'ProductOption' );
    }

    /**
     * Link to items
     *
     * @param bool $force_refresh [optional]
     * @return ProductOptionItem[]
     */
    public function items( $force_refresh = false ){
        if ( $force_refresh || empty( $this->items ) ) {
            $product_option_item = new ProductOptionItem();
            $this->items = $product_option_item->get_by_product_option( $this->id );
        }

        return $this->items;
    }
}
