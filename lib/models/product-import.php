<?php
/**
 * Product Import
 *
 * @author gbrunacci
 */
class ProductImport extends ActiveRecordBase {

    /**
     * Table columns
     * @var mixed 
     */
    public $product_id, $category_id, $brand_id, $industry_id, $website_id, $parent_product_id, $name, $description
        , $sku, $slug, $status, $price_wholesale, $price_min, $price, $sale_price, $alternate_price,
        $product_specifications, $image, $inventory, $type, $amazon_eligible;
            
    /**
     * Columns from other tables
     * @var mixed
     */
    public $industry_name;
          
    
    /**
     * Class contruct
     */
    public function __construct() {
        parent::__construct( 'product_import' );
    }
    
    /**
     * Delete all
     * @param int $website_id
     */
    public function delete_all( $website_id = null ) {
        $where = '';
        if ( $website_id ) {
            $where = " WHERE website_id = {$website_id} ";
        }
        $this->prepare("DELETE FROM `product_import` {$where}", '', array())
            ->query();
    }

    /**
     * Get all
     * @param int $website_id
     * @return ProductImport[]
     */
    public function get_all( $website_id = null ) {
        $where = '';
        if ( $website_id ) {
            $where = " WHERE pi.website_id = {$website_id} ";
        }
        return $this->prepare(
            "SELECT `pi`.*, `i`.`name` as `industry_name` FROM `product_import` `pi` INNER JOIN `industries` `i` ON `pi`.`industry_id` = `i`.`industry_id` {$where}"
            , ''
            , array()
        )->get_results( PDO::FETCH_CLASS, 'ProductImport' );
    }
    
    /**
     * Create
     */
    public function create() {
        $this->insert([
            'product_id' => $this->product_id
            , 'category_id' => $this->category_id
            , 'brand_id' => $this->brand_id
            , 'industry_id' => $this->industry_id
            , 'website_id' => $this->website_id
            , 'parent_product_id' => $this->parent_product_id
            , 'name' => $this->name
            , 'slug' => $this->slug
            , 'description' => $this->description
            , 'status' => $this->status
            , 'sku' => $this->sku
            , 'price_wholesale' => $this->price_wholesale
            , 'price_min' => $this->price_min
            , 'price' => $this->price
            , 'sale_price' => $this->sale_price
            , 'alternate_price' => $this->alternate_price
            , 'product_specifications' => $this->product_specifications
            , 'image' => $this->image
            , 'inventory' => $this->inventory
            , 'type' => $this->type
            , 'amazon_eligible' => $this->amazon_eligible
        ], 'iiiiiisssisdddddssis' );
    }
    
    
    
}
