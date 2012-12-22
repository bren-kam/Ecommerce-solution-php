<?php
class ProductsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'products/';
        $this->section = 'products';
        $this->title = _('Products');
    }

    /**
     * List Shopping Cart Users
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Initiate objects
        $category = new Category();
        $account_category = new AccountCategory();
        $account_product = new AccountProduct();

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }

        $product_count = $account_product->count( $this->user->account->id );

        $this->resources->javascript( 'products/index' )
            ->css( 'products/index' )
            ->css_url( Config::resource('jquery-ui') );

        $response = $this->get_template_response( 'index', _('Products') )
            ->select( 'sub-products', 'view' )
            ->set( compact( 'categories', 'product_count' ) );

        return $response;
    }

    /***** AJAX *****/

    /**
     * Autocomplete
     */
    protected function autocomplete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['type'], $_POST['term'] ), _('Autocomplete failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ac_suggestions = array();

        // Get the right suggestions for the right type
        switch ( $_POST['type'] ) {
            case 'brand':
                $brand = new Brand;
                $ac_suggestions = $brand->autocomplete_by_account( $_POST['term'], $this->user->account->id );
            break;

            case 'product':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_by_account( $_POST['term'], 'name', $this->user->account->id );
            break;

            case 'sku':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_by_account( $_POST['term'], 'sku', $this->user->account->id );
            break;

            case 'sku-products':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_by_account( $_POST['term'], array( 'name', 'sku' ), $this->user->account->id );
            break;

            default: break;
        }

        // It needs to be empty if nothing else
        $suggestions = array();

        if ( is_array( $ac_suggestions ) )
        foreach ( $ac_suggestions as $acs ) {
            $suggestions[] = array( 'name' => html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' ), 'value' => $acs['value'] );
        }

        // Sent by the autocompleter
        $response->add_response( 'suggestions', $suggestions );

        return $response;
    }

    /**
     * Remove All Discontinued Products
     */
    protected function remove_all_discontinued_products() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_product = new AccountProduct();
        $account_category = new AccountCategory();

        // Remove discontinued and reorganize categories
        $account_product->remove_discontinued( $this->user->account->id );
        $account_category->reorganize_categories( $this->user->account->id, new Category() );

        // Let them know
        $response->check( false, _('All discontinued products have been removed') );

        // Reset products to blank
        jQuery('#dProductList')->empty();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Get Products
     *
     * @return CustomResponse
     */
    protected function search() {
        // Setup objects
        $account_product = new AccountProduct();
        $category = new Category();
        $account_category = new AccountCategory();

        // Set variables
        $where = '';
        $category_id = (int) $_POST['cid'];
        $per_page = ( $_POST['n'] > 100 ) ? 20 : (int) $_POST['n'];
        $page = ( empty( $_POST['p'] ) ) ? 1 : (int) $_POST['p'];

        // Category ID
        if ( $category_id ) {
            // Get all child categories
            $child_categories_array = $category->get_all_children( $category_id );
            $account_categories = $account_category->get_all_ids( $this->user->account->id );
            $child_category_ids = array();

            foreach( $child_categories_array as $child_category ) {
                if ( !in_array( $child_category->id, $account_categories ) )
                    continue;

                $child_category_ids[] = $child_category->id;
            }

            $where .= ' AND c.`category_id` IN (' . preg_replace( '/[^0-9,]/', '', implode( ',', array_merge( array( $category_id ), $child_category_ids ) ) ) . ')';
        }

        // If they only want discontinued products, then only grab them
        if ( '1' == $_POST['od'] )
            $where .= " AND p.`status` = 'discontinued'";

        // Search type
        if ( !empty( $_POST['v'] ) && _('Enter Name...') != $_POST['v'] )
        switch ( $_POST['s'] ) {
            case 'sku':
                if ( _('Enter SKU...') != $_POST['v'] )
                    $where .= " AND p.`sku` LIKE " . $account_product->quote( $_POST['v'] . '%' );
            break;

            case 'product':
                if ( _('Enter Product Name...') != $_POST['v'] )
                    $where .= " AND p.`name` LIKE " . $account_product->quote( $_POST['v'] . '%' );
            break;

            case 'brand':
                if ( _('Enter Brand...') != $_POST['v'] )
                    $where .= " AND b.`name` LIKE " . $account_product->quote( $_POST['v'] . '%' );
            break;
        }

        $products = $account_product->search( $this->user->account->id, $per_page, $where, $page );
        $product_count = $account_product->search_count( $this->user->account->id, $where );

        foreach ( $products as $product ) {
            $product->link = ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/';
		}

        $user = $this->user;

        // Make sure it's a valid ajax call
        $response = new CustomResponse( $this->resources, 'products/search' );
        $response->set( compact( 'product_count', 'products', 'page', 'per_page', 'user' ) );

        return $response;
    }

    /**
     * Remove All Discontinued Products
     */
    protected function remove() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['pid'] ), _('Removing product failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_product = new AccountProduct();
        $account_category = new AccountCategory();

        // Remove discontinued and reorganize categories
        $account_product->get( $_GET['pid'], $this->user->account->id );
        $account_product->remove();

        // Reorganize categories
        $account_category->reorganize_categories( $this->user->account->id, new Category() );

        // Remove the product then lower the count
        jQuery('#dProduct_' . $_GET['pid'])
            ->remove()
            ->lowerProductCount();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Remove All Discontinued Products
     */
    protected function block() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['pid'] ), _('Blocking product failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_product = new AccountProduct();
        $product = new Product();
        $account_category = new AccountCategory();

        // Get variables
        $product->get( $_GET['pid'] );
        $industries = $this->user->account->get_industries();

        $account_product->block_by_sku( $this->user->account->id, $industries, array( $product->sku ) );

        // Reorganize categories
        $account_category->reorganize_categories( $this->user->account->id, new Category() );

        // Remove the product then lower the count
        jQuery('#dProduct_' . $_GET['pid'])
            ->remove()
            ->lowerProductCount();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Set Category Image
     */
    protected function set_category_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( !empty( $_GET['i'] ), _('Please choose an image to set') );
        $response->check( !empty( $_GET['cid'] ), _('Please select a category first') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_category = new AccountCategory();

        // Get variables
        $account_category->get( $this->user->account->id, $_GET['cid'] );
        $account_category->set_image( $_GET['i'] );

        $response->check( false, _('Your category image has been set!') );

        return $response;
    }
}


