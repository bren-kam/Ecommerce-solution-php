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
    public $category_id, $brand_id, $industry_id, $website_id, $name, $slug,
            $description, $status, $sku, $price, $price_min, 
            $product_specifications, $image;
            
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
        
        return $this->prepare(
            'INSERT INTO `product_import`(`category_id`, `brand_id`, `industry_id`, `website_id`, `name`, `slug`, `description`, `status`, `sku`, `price`, `price_min`, `product_specifications`, `image`)
             VALUES (:category_id, :brand_id, :industry_id, :website_id, :name, :slug, :description, :status, :sku, :price, :price_min, :product_specifications, :image)'
            , 'iiiisssssdds'
            , array(
                ':category_id' => $this->category_id
                ,':brand_id' => $this->brand_id
                ,':industry_id' => $this->industry_id
                ,':website_id' => $this->website_id
                ,':name' => $this->name
                ,':slug' => $this->slug
                ,':description' => $this->description
                ,':status' => $this->status
                ,':sku' => $this->sku
                ,':price' => $this->price
                ,':price_min' => $this->price_min
                ,':product_specifications' => $this->product_specifications
                ,':image' => $this->image
            )
        )->query();
    }
    
    
    
}
