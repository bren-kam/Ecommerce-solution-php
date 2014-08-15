<?php
class AccountProductOption extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id, $product_id, $product_option_id, $price, $required;

    // Columns from other tables
    public $product_option_list_item_id, $list_item_price, $alt_price, $alt_price2;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_product_options' );
    }

    /**
     * Get with list items
     *
     * @param int $account_id
     * @param int $product_id
     * @return AccountProductOption[]
     */
    public function get_with_list_items( $account_id, $product_id ) {
        $account_id = (int) $account_id;

        return $this->prepare(
            "SELECT po.`product_option_id`, poli.`product_option_list_item_id`, poli.`value`, wpo.`price`, wpo.`required`, wpoli.`price` AS list_item_price, wpoli.`alt_price`, wpoli.`alt_price2` FROM `product_options` AS po LEFT JOIN `product_option_list_items` AS poli ON ( poli.`product_option_id` = po.`product_option_id` ) INNER JOIN `website_product_options` AS wpo ON ( wpo.`product_option_id` = po.`product_option_id` ) INNER JOIN `website_product_option_list_items` AS wpoli ON ( wpoli.`product_option_id` = wpo.`product_option_id` AND wpoli.`product_option_list_item_id` = poli.`product_option_list_item_id` AND wpoli.`product_id` = wpo.`product_id` AND wpoli.`website_id` = $account_id ) WHERE wpo.`website_id` = $account_id AND wpo.`product_id` = :product_id AND ( po.`option_type` = 'checkbox' OR po.`option_type` = 'select' AND wpoli.`price` IS NOT NULL ) GROUP BY wpoli.`product_option_list_item_id` ORDER BY poli.`sequence` DESC"
            , 'i'
            , array( ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountProductOption' );
    }

    /**
     * Get without list items
     *
     * @param int $account_id
     * @param int $product_id
     * @return AccountProductOption[]
     */
    public function get_without_list_items( $account_id, $product_id ) {
        return $this->prepare(
            'SELECT po.`option_type`, po.`product_option_id`, wpo.`price`, wpo.`required` FROM `product_options` AS po LEFT JOIN `website_product_options` AS wpo ON ( wpo.`product_option_id` = po.`product_option_id` )  WHERE wpo.`website_id` = :account_id AND wpo.`product_id` = :product_id GROUP BY wpo.`product_option_id`'
            , 'ii'
            , array( ':account_id' => $account_id, ':product_id' => $product_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountProductOption' );
    }

    /**
     * Get All
     *
     * @param int $account_id
     * @param int $product_id
     * @return array
     */
    public function get_all( $account_id, $product_id ) {
        $product_options_array =  array_merge(
            $this->get_with_list_items( $account_id, $product_id )
            , $this->get_without_list_items( $account_id, $product_id )
        );

        $product_options = array();

        /**
         * @var AccountProductOption $product_option
         */
        foreach ( $product_options_array as $product_option ) {
            if ( empty( $product_option->product_option_list_item_id ) )
                continue;

            $product_options[$product_option->product_option_id]['price'] = $product_option->price;
            $product_options[$product_option->product_option_id]['required'] = $product_option->required;

            if ( ProductOption::MATTRESS_SIZES == $product_option->product_option_id ) {
                $product_options[$product_option->product_option_id]['list_items'][$product_option->product_option_list_item_id]['price'] = $product_option->list_item_price;
                $product_options[$product_option->product_option_id]['list_items'][$product_option->product_option_list_item_id]['alt_price'] = $product_option->alt_price;
                $product_options[$product_option->product_option_id]['list_items'][$product_option->product_option_list_item_id]['alt_price2'] = $product_option->alt_price2;
            } else {
                $product_options[$product_option->product_option_id]['list_items'][$product_option->product_option_list_item_id] = $product_option->list_item_price;
            }
        }

        return $product_options;
    }

    /**
     * Add Bulk
     *
     * @param int $account_id
     * @param int $product_id
     * @param array $product_options
     */
    public function add_bulk( $account_id, $product_id, array $product_options ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $product_id = (int) $product_id;

        // Setup variables
        $values = '';
        foreach ( $product_options as $product_option ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $account_id, $product_id, " . (int) $product_option['product_option_id'] . ', ' . (float) $product_option['price'] . ', ' . (int) $product_option['required'] . ' )';
        }

        $this->query( "INSERT INTO `website_product_options` ( `website_id`, `product_id`, `product_option_id`, `price`, `required` ) VALUES $values" );
    }

    /**
     * Add Bulk List Items
     *
     * @param int $account_id
     * @param int $product_id
     * @param array $product_option_list_items
     */
    public function add_bulk_list_items( $account_id, $product_id, array $product_option_list_items ) {
        // Type Juggling
        $account_id = (int) $account_id;
        $product_id = (int) $product_id;

        // Setup variables
        $values = '';

        foreach ( $product_option_list_items as $product_option_list_item ) {
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $account_id, $product_id, " . (int) $product_option_list_item['product_option_id'] . ', ' . (int) $product_option_list_item['product_option_list_item_id'] . ', ' . (float) $product_option_list_item['price'] . ', ' . (float) $product_option_list_item['alt_price'] . ', ' . $product_option_list_item['alt_price2'] . ' )';
        }

        $this->query( "INSERT INTO `website_product_option_list_items` ( `website_id`, `product_id`, `product_option_id`, `product_option_list_item_id`, `price`, `alt_price`, `alt_price2` ) VALUES $values" );
    }

    /**
     * Delete Website Product Options
     *
     * @param int $account_id
     * @param int $product_id
     */
    protected function delete_website_product_options( $account_id, $product_id ) {
        $this->prepare( 'DELETE FROM `website_product_options` WHERE `website_id` = :account_id AND `product_id` = :product_id'
              , 'ii'
              , array( ':account_id' => $account_id, ':product_id' => $product_id )
          )->query();
    }

    /**
     * Delete Website Product Option List Items
     *
     * @param int $account_id
     * @param int $product_id
     */
    protected function delete_website_product_option_list_items( $account_id, $product_id ) {
        $this->prepare( 'DELETE FROM `website_product_option_list_items` WHERE `website_id` = :account_id AND `product_id` = :product_id'
              , 'ii'
              , array( ':account_id' => $account_id, ':product_id' => $product_id )
          )->query();
    }

    /**
     * Delete By Product
     *
     * @param int $account_id
     * @param int $product_id
     */
    public function delete_by_product( $account_id, $product_id ) {
        $this->delete_website_product_option_list_items( $account_id, $product_id );
        $this->delete_website_product_options( $account_id, $product_id );
    }

    /**
     * Copy product options
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    public function copy_by_account( $template_account_id, $account_id ) {
        $this->copy_options( $template_account_id, $account_id );
        $this->copy_list_items( $template_account_id, $account_id );
    }

    /**
     * Copy options
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    protected function copy_options( $template_account_id, $account_id ) {
        $this->copy( $this->table, array(
                'website_id' => $account_id
                , 'product_id' => NULL
                , 'product_option_id' => NULL
                , 'price' => NULL
                , 'required' => NULL
            ), array( 'website_id' => $template_account_id )
        );
    }

    /**
     * Copy list items
     *
     * @param int $template_account_id
     * @param int $account_id
     */
    protected function copy_list_items( $template_account_id, $account_id ) {
        $this->copy( 'website_product_option_list_items', array(
                'website_id' => $account_id
                , 'product_id' => NULL
                , 'product_option_id' => NULL
                , 'product_option_list_item_id' => NULL
                , 'price' => NULL
                , 'alt_price' => NULL
                , 'alt_price2' => NULL
            ), array( 'website_id' => $template_account_id )
        );
    }
}