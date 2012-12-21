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
}


