<?php


class ProductAmazon extends ActiveRecordBase {

    // Columns
    public $product_amazon_id, $product_id;


    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct('products_amazon');
    }

    /**
     * @return int $product_amazon_id
     */
    public function create() {
        $sql = 'INSERT INTO products_amazon (product_id) SELECT product_id FROM products WHERE parent_product_id = :product_id ON DUPLICATE KEY UPDATE product_id = product_id';
        $this->prepare($sql, 'i', array( ':product_id' => $this->product_id ));
        return $this->insert(
            ['product_id' => $this->product_id]
            , 'i'
            , true
        );
    }

    /**
     * @param int $product_id
     */
    public function remove_by_product($product_id) {
        $this->delete(
            ['product_id' => $product_id], 'i'
        );
    }
}