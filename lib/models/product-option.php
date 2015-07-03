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
     * Save
     */
    public function save() {
        $this->update([
            'name' => $this->name
        ], [
            'id' => $this->id
        ], 's', 'i' );
    }

    /**
     * Get by id
     *
     * @param int $product_option_id
     * @param int $website_id
     */
    public function get( $product_option_id, $website_id ) {
        $this->prepare(
            'SELECT `id`, `name`, `type` FROM `product_option` WHERE `id` = :product_option_id AND `website_id` = :website_id'
            , 'ii'
            , array( ':product_option_id' => $product_option_id, ':website_id' => $website_id )
        )->get_row( PDO::FETCH_INTO, $this );
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
     * @return ProductOptionItem[]|array
     */
    public function items( $force_refresh = false ){
        if ( $force_refresh || empty( $this->items ) ) {
            $product_option_item = new ProductOptionItem();
            $items = $product_option_item->get_by_product_option( $this->id );

            foreach ( $items as $item ) {
                $this->items[$item->id] = $item;
            }
        }

        return ( $this->items ) ? $this->items : array();
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete_associated_products();
        $this->remove_self();
    }

    /**
     * Remove self
     */
    protected function remove_self() {
        $this->delete([
            'id' => $this->id
        ], 'i' );
    }

    /**
     * Deleted all products associated with product option
     */
    protected function delete_associated_products() {
        $this->prepare(
            'DELETE p.* FROM `products` p LEFT JOIN `product_option_item_product` poip ON ( poip.`product_id` = p.`product_id` ) LEFT JOIN `product_option_item` poi ON ( poi.`id` = poip.`product_option_item_id` ) WHERE poi.`product_option_id` = :product_option_id'
            , 'i'
            , [':product_option_id' => $this->id]
        )->query();
    }
}
