<?php
/**
 * Handles All product feed gateways
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
abstract class ProductFeedGateway extends ActiveRecordBase {
    /**
     * Setup the array for existing products
     *
     * @var array
     */
    protected $existing_products;

    /**
     * Set the UserId Responsible
     */
    protected $user_id;

    /**
     * See how many products we go through
     */
    protected $product_count = 0;

    /**
     * See how many products we skip
     */
    protected $skip_count = 0;

    /**
     * Determine what was not identical
     */
    protected $not_identical = array();

    /**
     * Construct
     */
    public function __construct( $user_id ) {
        parent::__construct('');

        $this->user_id = $user_id;
    }

    /**
     * Run the gateway
     */
    public function run() {
        $this->setup();
        $this->get_existing_products();
        $this->get_data();
        $this->process();
        $this->send_report();
    }

    /**
     * Set the existing products
     */
    protected function get_existing_products() {
        $products = $this->prepare(
            "SELECT a.`product_id`, a.`brand_id`, a.`industry_id`, a.`name`, a.`slug`, a.`description`, a.`status`, a.`sku`, a.`price`, a.`weight`, a.`volume`, a.`product_specifications`, a.`publish_visibility`, a.`publish_date`, b.`name` AS industry, GROUP_CONCAT( `image` ORDER BY `sequence` ASC SEPARATOR '|' ) AS images, COALESCE( pc.`category_id`, 0 ) AS category_id FROM `products` AS a INNER JOIN `industries` AS b ON (a.`industry_id` = b.`industry_id`) LEFT JOIN `product_images` AS c ON ( a.`product_id` = c.`product_id` ) LEFT JOIN `product_categories` AS pc ON ( a.`product_id` = pc.`product_id` ) WHERE a.`user_id_created` = :user_id_created GROUP BY a.`product_id`"
            , 'i'
            , array( ':user_id_created' => $this->user_id )
        )->get_results( PDO::FETCH_ASSOC );

        $this->existing_products = ar::assign_key( $products, 'sku' );
    }

    /**
     * See if something exists and return product id if it does
     *
     * @param mixed $key
     * @return mixed
     */
    protected function get_existing_product( $key ) {
        $key = (string) $key;

        return ( array_key_exists( $key, $this->existing_products ) ) ? $this->existing_products[$key] : false;
    }

    /**
     * Check to see if a Slug is already being used
     *
     * @param string $slug
     * @return string
     */
    protected function unique_slug( $slug ) {
        $existing_slug = $this->prepare( "SELECT `slug` FROM `products` WHERE `user_id_created` = :user_id_created AND `publish_visibility` <> 'deleted' AND `slug` = :slug"
            , 'is'
            , array( ':user_id_created' => $this->user_id, ':slug' => $slug )
        )->get_var();

        // See if the slug already exists
        if ( $slug == $existing_slug ) {
            // Check to see if it has been incremented before
            if ( preg_match( '/-([0-9]+)$/', $slug, $matches ) > 0 ) {
                // The number to increment it by
                $increment = $matches[1] * 1 + 1;

                // Give it the new increment
                $slug = preg_replace( '/-[0-9]+$/', "-$increment", $slug );

                // Make sure it's unique
                $slug = $this->unique_slug( $slug );
            } else {
                // It has not been incremented before, start with 2
                $slug .= '-2';
            }
        }

        // Return the unique slug
        return $slug;
    }

    /**
     * Checks if something is identical, and returns it new one if it's empty
     *
     * @param string $variable
     * @param string $original
     * @param string $type
     * @return mixed
     */
    public function identical( $variable, $original, $type ) {
        // Nothing there, need original
        if ( empty( $variable ) )
            return $original;

        // They're not equal, so we need to mark it down
        if ( $variable != $original )
            $this->not_identical[] = $type;

        // Return the variable
        return $variable;
    }

    /**
     * Is identical -- checks if there any not identical parts
     *
     * @return bool
     */
    public function is_identical() {
        return 0 == count( $this->not_identical );
    }

    /**
     * Reset identical
     */
    public function reset_identical() {
        $this->not_identical = array();
    }

    /**
     * Ticks off a counter for how many products there are
     */
    protected function tick() {
        $this->product_count++;
    }

    /**
     * Ticks off a counter for how many products were skipped
     */
    protected function skip() {
        $this->skip_count++;
    }

    // The functions that must be created
    abstract protected function setup();
    abstract protected function get_data();
    abstract protected function process();
    abstract protected function send_report();
}
