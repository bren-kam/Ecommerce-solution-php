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
     * List Products
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Initiate objects
        $category = new Category();
        $account_category = new AccountCategory();
        $account_product = new AccountProduct();
        $coupon = new WebsiteCoupon();

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $coupons = $coupon->get_by_account( $this->user->account->id );
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

        return $this->get_template_response( 'index')
            ->select( 'sub-products', 'view' )
            ->set( compact( 'categories', 'product_count', 'coupons' ) );
    }

    /**
     * Add Products by hand
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add() {
        // Make sure they can be here
        if ( $this->user->role <= 5  && '1' == $this->user->account->get_settings( 'limited-products' ) )
            return new RedirectResponse('/products/');

        // Instantiate Variables
        $account_product = new AccountProduct();
        $category = new Category();
        $brand = new Brand();

        if ( $this->verified() ) {
            $account_category = new AccountCategory();

            $account_product->add_bulk_by_ids( $this->user->account->id, $_POST['products'] );
            $account_category->reorganize_categories( $this->user->account->id, $category );

            $this->notify( _('Your product(s) have been successfully added!') );

            return new RedirectResponse('/products/');
        }

        // Get variables
        $product_count = $account_product->count( $this->user->account->id );
        $categories = $category->sort_by_hierarchy();
        $brands = $brand->get_all();

        if ( $product_count > $this->user->account->products )
            $this->notify( _('Please contact your Online Specialist to add additional products. Product Usage has exceeded the number of items allowed.'), false );

        $this->resources->javascript( 'products/add' )
            ->css( 'products/add' )
            ->css_url( Config::resource('jquery-ui') );

        $response = $this->get_template_response( 'add' )
            ->select( 'sub-products', 'add' )
            ->set( compact( 'product_count', 'categories', 'brands' ) );

        return $response;
    }

    /**
     * All
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function all() {
        $account_product = new AccountProduct();

        $products = $account_product->get_by_account( $this->user->account->id );

        $response = $this->get_template_response( 'all' )
            ->add_title( _('All Products') )
            ->select( 'sub-products', 'all' )
            ->set( compact( 'products' ) );

        return $response;
    }

    /**
     * Catalog Dump
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function catalog_dump() {
        // Setup validation
        $v = new Validator( 'fCatalogDump' );
        $v->add_validation( 'hBrandID', 'req', _('You must select a brand before dumping') );

        // Setup variables
        $js_validation = $v->js_validation();
        $errs = '';

        // If they posted
        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                // Get industries
                $industries = $this->user->account->get_industries();

                if ( empty( $industries ) ) {
                    $this->notify( _("This website has no industries.  Please contact your online specialist for assistance with this issue."), false );
                } else {
                    // Instantiate objects
                    $account_product = new AccountProduct();

                    // How many free slots do we have
                    $free_slots = $this->user->account->products - $account_product->count( $this->user->account->id );
                    $quantity = $free_slots - $account_product->add_bulk_by_brand_count( $this->user->account->id, $_POST['hBrandID'], $industries );

                    if ( $quantity < 0 ) {
                        // Make it show up right
                        $quantity *= -1;

                        $this->notify( _("There is not enough free space to add this brand. Delete at least $quantity products, or expand the size of the product catalog."), false );
                    } else {
                        // Add bulk
                        $quantity = $account_product->add_bulk_by_brand( $this->user->account->id, $_POST['hBrandID'], $industries );

                        // Reorganize categories
                        $account_category = new AccountCategory();
                        $account_category->reorganize_categories( $this->user->account->id, new Category() );

                        $this->notify( $quantity . ' ' . _('brand products added successfully!') );
                    }
                }
            }
        }

        $this->resources->javascript( 'products/catalog-dump' )
            ->css_url( Config::resource('jquery-ui') );

        $response = $this->get_template_response( 'catalog-dump' )
            ->add_title( _('Catalog Dump') )
            ->select( 'sub-products', 'catalog-dump' )
            ->set( compact( 'js_validation', 'errs' ) );

        return $response;
    }

    /**
     * Add Bulk
     *
     * @return TemplateResponse
     */
    protected function add_bulk() {
        $form = new FormTable( 'fAddBulk' );
        $form->submit( _('Add Bulk'), '', 1 );
        $form->add_field( 'textarea', '', 'taSKUs' )
            ->add_validation( 'req', _('You must enter SKUs before you can add products') );

        $success = false;

        if ( $form->posted() ) {
            $account_product = new AccountProduct();
            $skus = explode( "\n", str_replace( "\r", '', $_POST['taSKUs'] ) );

            // How many free slots do we have
            $free_slots = $this->user->account->products - $account_product->count( $this->user->account->id );
            $quantity = $free_slots - $account_product->add_bulk_count( $this->user->account->id, $this->user->account->get_industries(), $skus );

            if ( $quantity < 0 ) {
                // Make it show up right
                $quantity *= -1;

                $this->notify( _("There is not enough free space to add these products. Delete at least $quantity products, or expand the size of the product catalog."), false );
            } else {
                // Add bulk
                list( $quantity, $already_existed, $not_added_skus ) = $account_product->add_bulk_all( $this->user->account->id, $this->user->account->get_industries(), $skus );

                // Reorganize categories
                $account_category = new AccountCategory();
                $account_category->reorganize_categories( $this->user->account->id, new Category() );

                $this->notify( $quantity . ' ' . _('products added successfully!') );
                $success = true;
            }

        }

        $form = $form->generate_form();

        return $this->get_template_response( 'add-bulk' )
            ->add_title( _('Add Bulk') )
            ->select( 'sub-products', 'add-bulk' )
            ->set( compact( 'form', 'already_existed', 'not_added_skus', 'success' ) );
    }

    /**
     * Block Products
     *
     * @return TemplateResponse
     */
    protected function block_products() {
        $form = new FormTable( 'fBlockProducts' );
        $form->submit( _('Block Products'), '', 1 );
        $form->add_field( 'textarea', '', 'taSKUs' )
            ->add_validation( 'req', _('You must enter SKUs before you can add products') );

        $account_product = new AccountProduct();

        if ( $form->posted() ) {
            $skus = explode( "\n", str_replace( "\r", '', $_POST['taSKUs'] ) );

            $account_product->block_by_sku( $this->user->account->id, $this->user->account->get_industries(), $skus );

            $this->notify( _('Blocked Products have been successfully updated!') );
        }

        $blocked_products = $account_product->get_blocked( $this->user->account->id );

        $response = $this->get_template_response( 'block-products' )
            ->add_title( _('Block Products') )
            ->select( 'sub-products', 'block-products' )
            ->set( array( 'form' => $form->generate_form(), 'blocked_products' => $blocked_products ) );

        return $response;
    }

    /**
     * Hide Categories
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function hide_categories() {
        // Setup objects
        $category = new Category();
        $account_category = new AccountCategory();

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[$category->id] = str_repeat( '&nbsp;', $category->depth * 5 ) . $category->name;
        }

        $form = new FormTable( 'fCategories' );
        $form->submit( _('Hide Categories'), '', 1 );
        $form->add_field( 'select', '', 'sCategoryIDs[]' )
            ->attribute( 'multiple', 'multiple' )
            ->attribute( 'class', 'height-200' )
            ->options( $categories );

        if ( $form->posted() ) {
            // Hide them
            $account_category->hide( $this->user->account->id, $_POST['sCategoryIDs'] );

            // Remove any of them
            $account_category->remove_categories( $this->user->account->id, $_POST['sCategoryIDs'] );
            $account_category->reorganize_categories( $this->user->account->id, $category );

            $this->notify( _('Hidden categories have been successfully updated!') );

            return new RedirectResponse( '/products/hide-categories/' );
        }

        $blocked_website_category_ids = $account_category->get_blocked_website_category_ids( $this->user->account->id );
        $blocked_categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $blocked_website_category_ids ) )
                continue;

            $blocked_categories[] = $category;
        }

        return $this->get_template_response( 'hide-categories' )
            ->add_title( _('Hide Categories') )
            ->select( 'sub-products', 'hide-categories' )
            ->set( array( 'form' => $form->generate_form(), 'hidden_categories' => $blocked_categories ) );
    }

    /**
     * Product Prices
     *
     * @return TemplateResponse
     */
    protected function product_prices() {
        $brand = new Brand();
        $category = new Category();
        $account_category = new AccountCategory();

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }

        $brands = $brand->get_by_account( $this->user->account->id );

        $this->resources
            ->css( 'products/product-prices' )
            ->javascript( 'products/product-prices' );

        return $this->get_template_response( 'product-prices' )
            ->add_title( _('Product Prices') )
            ->select( 'sub-products', 'product-prices' )
            ->set( compact( 'brands', 'categories' ) );
    }

    /**
     * Price Multiplier
     *
     * @return TemplateResponse
     */
    protected function price_multiplier() {
        $this->resources->javascript( 'fileuploader', 'products/multiply-prices' );

        return $this->get_template_response( 'price-multiplier' )
            ->add_title( _('Price Multiplier') )
            ->select( 'sub-products', 'price-multiplier' );
    }

    /**
     * Unblock products
     *
     * @return RedirectResponse
     */
    protected function unblock_products() {
        if ( $this->verified() ) {
            $account_product = new AccountProduct();
            $account_product->unblock( $this->user->account->id, $_POST['unblock-products'] );
            $this->notify( _('Blocked Products have been successfully updated!') );
        }

        return new RedirectResponse('/products/block-products/');
    }

    /**
     * Unhide categories
     *
     * @return RedirectResponse
     */
    protected function unhide_categories() {
        if ( $this->verified() ) {
            $account_category = new AccountCategory();
            $account_category->unhide( $this->user->account->id, $_POST['unhide-categories'] );
            $account_category->reorganize_categories( $this->user->account->id, new Category() );

            $this->notify( _('Hidden categories have been successfully updated!') );
        }

        return new RedirectResponse('/products/hide-categories/');
    }

    /**
     * Settings
     *
     * @return TemplateResponse
     */
    protected function settings() {
        // Instantiate classes
        $form = new FormTable( 'fSettings' );

        // Get settings
        $settings_array = array( 'request-a-quote-email', 'category-show-price-note', 'hide-skus', 'hide-request-quote', 'hide-customer-ratings', 'hide-product-brands', 'hide-browse-by-brand', 'replace-price-note' );
        $settings = $this->user->account->get_settings( $settings_array );
        $checkboxes = array(
        	'category-show-price-note' 	=> _('Show Price Note on Category Page')
        	, 'hide-skus' 				=> _('Hide Manufacturer SKUs')
        	, 'hide-request-quote' 		=> _('Hide "Request a Quote" Button')
        	, 'hide-customer-ratings' 	=> _('Hide Customer Ratings')
        	, 'hide-product-brands' 	=> _('Hide Product Brands')
        	, 'hide-browse-by-brand' 	=> _('Hide Browse By Brand')
            , 'replace-price-note'      => _('Replace Price Note with Product Option')
        );

        // Create form
        $form->add_field( 'text', _('Request-a-Quote Email'), 'request-a-quote-email', $settings['request-a-quote-email'] )
            ->attribute( 'maxlength', '150' )
            ->add_validation( 'req', 'email', _('The "Request-a-Quote Email" field must contain a valid email') );

        foreach( $checkboxes as $setting => $nice_name ) {
            $form->add_field( 'checkbox', $nice_name, $setting, $settings[$setting] );
        }

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }

            $this->user->account->set_settings( $new_settings );

            $this->notify( _('Your settings have been successfully saved!') );

            // Refresh to get all the changes
            return new RedirectResponse('/products/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->add_title( _('Settings') )
            ->select( 'products', 'settings' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /**
     * Brands
     *
     * @return TemplateResponse
     */
    protected function brands() {
        $this->resources->javascript( 'products/brands' )
                    ->css( 'products/brands' )
                    ->css_url( Config::resource('jquery-ui') );

        $website_top_brand = new WebsiteTopBrand();

        return $this->get_template_response( 'brands' )
            ->add_title( _('Brands') )
            ->select( 'products', 'brands' )
            ->set( array( 'top_brands' => $website_top_brand->get_by_account( $this->user->account->id ) ) );
    }

    /**
     * Export
     *
     * @return CsvResponse
     */
    protected function export() {
        // Get the products
        $account_product = new AccountProduct();
        $products = $account_product->get_by_account( $this->user->account->id );

        $output[]  = array( 'Product Name', 'SKU', 'Category', 'Brand', 'Created By' );

        foreach ( $products as $product ) {
            $output[] = array( $product->name, $product->sku, $product->category, $product->brand, $product->created_by );
        }

        return new CsvResponse( $output, format::slug( $this->user->account->title ) . '-products.csv' );
    }

    /***** AJAX *****/

    /**
     * Autocomplete
     *
     * @return AjaxResponse
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
                $ac_suggestions = $brand->autocomplete_all( $_POST['term'], $this->user->account->id );
            break;

            case 'product':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_POST['term'], 'name', $this->user->account->id );
            break;

            case 'sku':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_POST['term'], 'sku', $this->user->account->id );
            break;

            case 'sku-products':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_POST['term'], array( 'name', 'sku' ), $this->user->account->id );
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
     * Autocomplete Owned
     *
     * @return AjaxResponse
     */
    protected function autocomplete_owned() {
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
     *
     * @return AjaxResponse
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
     *
     * @return AjaxResponse
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
        $account_product->active = 0;
        $account_product->save();

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
     *
     * @return AjaxResponse
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
     *
     * @return AjaxResponse
     */
    protected function set_category_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['i'], $_GET['cid'] ), _('Please choose an image to set') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_category = new AccountCategory();

        // Get variables
        $account_category->get( $this->user->account->id, $_GET['cid'] );
        $account_category->image_url = preg_replace( '/(.+\/products\/[0-9]+\/)(?:small\/)?([a-zA-Z0-9-.]+)/', "$1small/$2", urldecode( $_GET['i'] ) );
        $account_category->save();

        $response->check( false, _('Your category image has been set!') );

        return $response;
    }

    /**
     * Get Product Dialog Info
     *
     * @return AjaxResponse
     */
    protected function get_product_dialog_info() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['pid'] ), _('Please select a product to edit') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate objects
        $account_product = new AccountProduct();
        $account_product_option = new AccountProductOption();
        $product_option = new ProductOption();
        $website_coupon = new WebsiteCoupon();

        // Get variables
        $account_product->get( $_POST['pid'], $this->user->account->id );
        $account_product->coupons = $website_coupon->get_by_product( $this->user->account->id, $_POST['pid'] );
        $account_product->product_options = $account_product_option->get_all( $this->user->account->id, $_POST['pid'] );
        $product_options_array = $product_option->get_by_product( $_POST['pid'] );

        $product_options = array();

        if ( $product_options_array )
		foreach ( $product_options_array as $po ) {
			$product_options[$po->id]['option_type'] = $po->type;
			$product_options[$po->id]['option_name'] = $po->name;
			$product_options[$po->id]['list_items'][$po->product_option_list_item_id] = $po->value;
		}

        // Add to response
        $response
            ->add_response( 'product', (array) $account_product )
            ->add_response( 'product_options', $product_options );

        return $response;
    }

    /**
     * Update Product
     *
     * @return AjaxResponse
     */
    protected function update_product() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['hProductID'] ), _('Please select a product to edit') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $account_product = new AccountProduct();
        $website_coupon = new WebsiteCoupon();
        $account_product_option = new AccountProductOption();

        // Get variables
        $account_product->get( $_POST['hProductID'], $this->user->account->id );

        /***** UPDATE PRODUCT *****/
        $account_product->alternate_price = $_POST['tAlternatePrice'];
        $account_product->price = $_POST['tPrice'];
        $account_product->sale_price = $_POST['tSalePrice'];
        $account_product->inventory = $_POST['tInventory'];
        $account_product->alternate_price_name = $_POST['tAlternatePriceName'];
        $account_product->price_note = $_POST['tPriceNote'];
        $account_product->product_note = $_POST['taProductNote'];
        $account_product->warranty_length = $_POST['tWarrantyLength'];
        $account_product->display_inventory = ( isset( $_POST['cbDisplayInventory'] ) ) ? 1 : 0;
        $account_product->on_sale = ( isset( $_POST['cbOnSale'] ) ) ? 1 : 0;
        $account_product->status = $_POST['sStatus'];
        $account_product->meta_title = $_POST['tMetaTitle'];
        $account_product->meta_description = $_POST['tMetaDescription'];
        $account_product->meta_keywords = $_POST['tMetaKeywords'];

        if ( $this->user->account->shopping_cart ) {
            $account_product->wholesale_price = $_POST['tWholesalePrice'];
            $account_product->additional_shipping_amount = ( 'Flat Rate' == $_POST['rShippingMethod'] ) ? $_POST['tShippingFlatRate'] : $_POST['tShippingPercentage'];
            $account_product->weight = $_POST['tWeight'];
            $account_product->protection_amount = ( 'Flat Rate' == $_POST['rProtectionMethod'] ) ? $_POST['tProtectionFlatRate'] : $_POST['tProtectionPercentage'];
            $account_product->additional_shipping_type = $_POST['rShippingMethod'];
            $account_product->protection_type = $_POST['rProtectionMethod'];
            $account_product->ships_in = $_POST['tShipsIn'];
            $account_product->store_sku = $_POST['tStoreSKU'];

            $coupons = ( empty( $_POST['hCoupons'] ) ) ? false : explode( '|', $_POST['hCoupons'] );
        } else {

            $coupons = false;
        }

        // Update product
        $account_product->save();

        /***** UPDATE COUPONS *****/
        $website_coupon->delete_relations_by_product( $this->user->account->id, $account_product->product_id );

        if ( $coupons ) {
            // Get website coupon IDs
            $website_coupons = $website_coupon->get_by_account( $this->user->account->id );
            $new_coupons = array();

            // Only add coupons that belong to this account
            foreach ( $website_coupons as $wc ) {
                if ( in_array( $wc->id, $coupons ) )
                    $new_coupons[] = $wc->id;

            }

            // Add the relations
            $website_coupon->add_relations( $account_product->product_id, $new_coupons );
        }

        /***** UPDATE PRODUCT OPTIONS *****/
        $account_product_option->delete_by_product( $this->user->account->id, $account_product->product_id );

        // Set the product options
        $product_options = array();

        if ( isset( $_POST['product_options'] ) )
        foreach ( $_POST['product_options'] as $po_id => $value ) {
            if ( isset( $_POST['tPrice' . $po_id] ) ) {
                $product_options[$po_id] = $_POST['tPrice' . $po_id];
            } else {
                $product_options[$po_id]['required'] = ( isset( $_POST['cbRequired' . $po_id] ) ) ? 1 : 0;
            }

            if ( isset( $_POST['product_list_items'][$po_id] ) )
            foreach ( $_POST['product_list_items'][$po_id] as $li_id => $val ) {
                $product_options[$po_id]['list_items'][(int) $li_id] = $_POST['tPrices'][$po_id][$li_id];
            }
        }

        if ( !empty( $product_options ) ) {
        	$product_option_values = $product_option_list_item_values = $product_option_ids = array();

			foreach ( $product_options as $po_id => $po ) {
				$dropdown = is_array( $po );

				if ( $dropdown ) {
					$price = 0;
					$required = $po['required'];
				} else {
					$price = $po;
					$required = 0;
				}

				// Add the values
				$product_option_values[] = array(
                    'product_option_id' => $po_id
                    , 'price' => $price
                    , 'required' => $required
                );

				// For error handling
				$product_option_ids .= $po_id;

				// If it's a drop down, set the values
				if ( $dropdown )
				foreach ( $po['list_items'] as $li_id => $price ) {
                    if ( is_array( $price ) ) {
                        $alt_price = $price['reg'];
                        $alt_price2 = $price['our-price'];
                        $price = $price['sale'];
                    } else {
                        $alt_price = $alt_price2 = 0;
                    }

					$product_option_list_item_values[] = array(
                        'product_option_id' => $po_id
                        , 'product_option_list_item_id' => $li_id
                        , 'price' => $price
                        , 'alt_price' => $alt_price
                        , 'alt_price2' => $alt_price2
                    );
				}
			}

			// Insert new product options
            $account_product_option->add_bulk( $this->user->account->id, $account_product->product_id, $product_option_values );

            // Insert new product option list items
            if ( !empty( $product_option_list_item_values ) )
                $account_product_option->add_bulk_list_items( $this->user->account->id, $account_product->product_id, $product_option_list_item_values );
		}

        jQuery('.close:visible:first')->click();
        jQuery( '#sPrice' . $account_product->product_id )->text( $account_product->price );
        jQuery( '#sAlternatePrice' . $account_product->product_id )->text( $account_product->alternate_price );
        jQuery( '#sAlternatePriceName' . $account_product->product_id )->text( $account_product->alternate_price_name );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Check to see if a SKU already exists
     *
     * @return AjaxResponse
     */
    protected function sku_exists() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['sku'] ), _('Please type in a SKU') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate objects
        $product = new Product();

        // Check to see if it already exists
        $product->get_by_sku( $_POST['sku'] );

        if ( $product->id ) {
            $account_product = new AccountProduct();
            $account_product->get( $product->id, $this->user->account->id );

            $response->check( $account_product->product_id && 1 == $account_product->active, _('A product with same SKU already exists in record and it is already added in your website.') );

            if ( $response->has_error() )
                return $response;

            // Now we know what to do
            $response
                ->add_response( 'product', array( 'product_id' => $product->id, 'name' => $product->name ) )
                ->add_response( 'confirm', _('A product with same SKU already exists in record. Do you want to add into your product list?') );
        } else {
            $response->add_response( 'product', false );
        }

        return $response;
    }

    /**
     * List Add Products
     *
     * @return DataTableResponse
     */
    protected function list_add_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $product = new Product();

        // Set Order by
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`' );
        $dt->add_where( ' AND ( p.`website_id` = 0 || p.`website_id` = ' . $this->user->account->id . ')' );
        $dt->add_where( " AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00'" );

        switch ( $_GET['sType'] ) {
        	case 'sku':
        		if ( _('Enter SKU...') != $_GET['s'] )
        			$dt->add_where( " AND p.`sku` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'product':
        		if ( _('Enter Product Name...') != $_GET['s'] )
        			$dt->add_where( " AND p.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'brand':
        		if ( _('Enter Brand...') != $_GET['s'] )
        			$dt->add_where( " AND b.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;
        }

        // Do a category search
        if ( !empty( $_GET['c'] ) ) {
        	$category = new Category;
        	$categories = $category->get_all_children( $_GET['c'] );
            $category_ids[] = (int) $_GET['c'];

            foreach( $categories as $category ) {
                $category_ids[] = (int) $category->id;
            }

        	$dt->add_where( ' AND c.`category_id` IN(' . implode( ',', $category_ids ) . ')' );
        }

        // Get account pages
        $products = $product->list_all( $dt->get_variables() );
        $dt->set_row_count( $product->count_all( $dt->get_count_variables() ) );

        // Nonce
        $data = array();

        // Create output
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
        	$dialog = '<a href="' . url::add_query_arg( 'pid', $product->id, '/products/get-product/' ) . '#dProductDialog' . $product->id . '" title="' . _('View') . '" rel="dialog">';
        	$actions = '<a href="#" class="add-product" id="aAddProduct' . $product->id . '" name="' . $product->name . '" title="' . _('Add') . '">' . _('Add Product') . '</a>';

        	$data[] = array(
        		$dialog . format::limit_chars( $product->name,  37, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>'
        		, $product->brand
        		, $product->sku
        	);
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Get Product
     *
     * @return CustomResponse
     */
    protected function get_product() {
        // Instantiate Object
        $product = new Product();
        $category = new Category();

        // Get Product
        $product->get( $_GET['pid'] );
        $product->images = $product->get_images();

        $category->get( $product->category_id );

        $response = new CustomResponse( $this->resources, 'products/get-product' );
        $response->set( compact( 'product', 'category' ) );

        return $response;
    }

    /**
     * Handle a request
     *
     * @return AjaxResponse
     */
    protected function request() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['requests'] ), _('Please click "Add Request" before sending the request') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Add the request
        $ticket = new Ticket;

        $ticket_message = $subject = '';

        foreach ( $_POST['requests'] as $r ) {
        	if ( !empty( $ticket_message ) )
        		$ticket_message .= "\n\n";

        	// Get the brand, sku and collection
        	$ticket_array = explode( '|', $r );

        	// Add it to the message
        	$ticket_message .= 'Brand: ' . $ticket_array[0] . "\n";
        	$ticket_message .= 'SKU: ' . $ticket_array[1] . "\n";
        	$ticket_message .= 'Collection: ' . $ticket_array[2];

        	$subject = ( $this->user->account->live ) ? 'Live' : 'Staging';
        }

        // Create Ticket
        $ticket->summary = "$subject - Product Request";
        $ticket->message = $ticket_message;
        $ticket->status = 1;
        $ticket->create();

        // Empty the list
        jQuery('#dRequestList')->empty();

        // Close Dialog
        jQuery('#aClose')->click();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * List Product Prices
     *
     * @return DataTableResponse
     */
    protected function list_product_prices() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_product = new AccountProduct();

        // Set Order by
        $dt->order_by( 'p.`sku`', 'wp.`price`', 'wp.`price_note`', 'wp.`alternate_price_name`', 'wp.`sale_price`' );
        $dt->add_where( ' AND wp.`website_id` = ' . (int) $this->user->account->id );

        if ( !empty( $_GET['b'] ) )
            $dt->add_where( ' AND p.`brand_id` = ' . (int) $_GET['b'] );

        if ( !empty( $_GET['cid'] ) ) {
            $category = new Category();
            $account_category = new AccountCategory();
            $categories = $category->get_all_children( $_GET['cid'] );
            $account_categories = $account_category->get_all_ids( $this->user->account->id );
            $category_ids[] = (int) $_GET['cid'];

            foreach ( $categories as $c ) {
                if ( !in_array( $c->id, $account_categories ) )
                    continue;

                $category_ids[] = (int) $c->id;
            }


            $dt->add_where( ' AND p.`category_id` IN ( ' . implode( ',', $category_ids ) . ' )' );
        }

        // Get account pages
        $products = $account_product->list_product_prices( $dt->get_variables() );
        $dt->set_row_count( $account_product->count_product_prices( $dt->get_count_variables() ) );

        // Nonce
        $data = array();

        // Create output
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
            $data[] = array(
                $product->sku
                , '<input type="text" class="price" id="tPrice' . $product->id . '" value="' . $product->price . '" />'
                , '<input type="text" class="price_note" id="tPriceNote' . $product->id . '" value="' . $product->price_note . '" />'
                , '<input type="text" class="alternate_price_name" id="tAlternatePriceName' . $product->id . '" value="' . $product->alternate_price_name . '" />'
                , '<input type="text" class="alternate_price" id="tAlternatePrice' . $product->id . '" value="' . $product->alternate_price . '" />'
                , '<input type="text" class="sale_price" id="tSalePrice' . $product->id . '" value="' . $product->sale_price . '" />'
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Set Product Prices
     *
     * @return AjaxResponse
     */
    protected function set_product_prices() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['v'] ), _('Unable to set Product Prices. Please contact your online specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $account_product = new AccountProduct();
        $account_product->set_product_prices( $this->user->account->id, $_POST['v'] );

        jQuery('span.success')->show()->delay(5000)->hide();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Update Brand Sequence
     *
     * @return AjaxResponse
     */
    protected function update_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('Unable to update brand sequence. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $sequence = explode( '&dProduct[]=', $_POST['s'] );
        $sequence[0] = substr( $sequence[0], 11 );

        // Adjust them if it's not the first page
        if ( '1' != $_POST['p'] ) {
        	$increment = ( (int) $_POST['p'] - 1 ) * $_POST['pp'];
            $new_sequence = array();

        	foreach ( $sequence as $index => $product_id ) {
        		$new_sequence[$index + $increment] = $product_id;
        	}

        	$sequence = $new_sequence;
        }

        $account_product = new AccountProduct();
        $account_product->update_sequence( $this->user->account->id, $sequence );

        return $response;
    }

    /**
     * Update Brand Sequence
     *
     * @return AjaxResponse
     */
    protected function update_brand_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('Unable to update brand sequence. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $sequence = explode( '&dBrand[]=', $_POST['s'] );
        $sequence[0] = substr( $sequence[0], 9 );

        $website_top_brand = new WebsiteTopBrand();
        $website_top_brand->update_sequence( $this->user->account->id, $sequence );

        return $response;
    }

    /**
     * Remove
     *
     * @return AjaxResponse
     */
    protected function remove_brand() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['bid'] ), _('Unable to remove brand. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Remove brand
        $website_top_brand = new WebsiteTopBrand();
        $website_top_brand->remove( $this->user->account->id, $_GET['bid'] );

        jQuery( '#dBrand_' . $_GET['bid'] )
        	->remove()
        	->updateBrandsSequence();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Set Brand Link
     *
     * @return AjaxResponse
     */
    protected function set_brand_link() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Set link brands
        $this->user->account->link_brands = $_POST['checked'];
        $this->user->account->save();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Add Brand
     *
     * @return AjaxResponse
     */
    protected function add_brand() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['bid'], $_POST['s'] ), _('Unable to add brand. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Get the brand, then add it
        $website_top_brand = new WebsiteTopBrand();
        $brand = new Brand();
        $brand->get( $_POST['bid'] );

        $website_top_brand->website_id = $this->user->account->id;
        $website_top_brand->brand_id = $_POST['bid'];
        $website_top_brand->sequence = $_POST['s'];
        $website_top_brand->create();

        // Now add it to the page
        $dBrand = '<div id="dBrand_' . $brand->id . '" class="brand">';
       	$dBrand .= '<img src="' . $brand->image . '" title="' . $brand->name . '" />';
       	$dBrand .= '<h4>' . $brand->name . '</h4>';
       	$dBrand .= '<p class="brand-url"><a href="' . $brand->link . '" title="' . $brand->name . '" target="_blank">' . $brand->link . '</a></p>';
       	$dBrand .= '<a href="' . url::add_query_arg( array( '_nonce' => nonce::create('remove_brand'), 'bid' => $brand->id ), '/products/remove-brand/' ) . '" title="' . _('Remove') . '" ajax="1" confirm="' . _('Are you sure you want to remove this brand?') . '">' . _('Remove') . '</a>';
       	$dBrand .= '</div>';

       	jQuery('#brands')
       		->append( $dBrand )
       		->sparrow();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Multiply Prices
     *
     * @return AjaxResponse
     */
    protected function multiply_prices() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Upload file
        $uploader = new qqFileUploader( array( 'csv', 'xls' ), 26214400 );
        $result = $uploader->handleUpload( 'gsrs_' );

        // Setup variables
        $file_extension = strtolower( f::extension( $_GET['qqfile'] ) );
        $rows = $prices = false;

        switch ( $file_extension ) {
            case 'xls':
                // Load excel reader
                library('Excel_Reader/Excel_Reader');
                $er = new Excel_Reader();
                // Set the basics and then read in the rows
                $er->setOutputEncoding('ASCII');
                $er->read( $result['file_path'] );

                $rows = $er->sheets[0]['cells'];
                $index = 1;
            break;

            case 'csv':
                // Make sure it's opened properly
                $response->check( $handle = fopen( $result['file_path'], "r"), _('An error occurred while trying to read your file.') );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;

                // Loop through the rows
                while( $row = fgetcsv( $handle ) ) {
                    $rows[] = $row;
                }

                // Close the file
                fclose( $handle );
                $index = 0;
            break;

            default:
                // Display an error
                $response->check( false, _('Only CSV and Excel file types are accepted. File type: ') . $file_extension );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;
            break;
        }

        $response->check( is_array( $rows ), _('There were no prices to multiply') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $response->check( 0 != $_GET['price'] || 0 != $_GET['sale_price'] || 0 != $_GET['alternate_price'], _('There were no prices to multiply') );

        // If there is an error, return
        if ( $response->has_error() )
            return $response;

        /**
         * Loop through prices
         *
         * @var int $index
         * @var string $price_column
         * @var array $rows
         * @var array $emails
         */
        foreach ( $rows as $r ) {
            // Determine the column being used for name or email
            if ( !isset( $sku_column ) || !isset( $price_column ) )
            if ( stristr( $r[0 + $index], 'price' ) && stristr( $r[1 + $index], 'sku' ) ) {
                $sku_column = 1 + $index;
                $price_column =  0 + $index;
                continue;
            } else {
                $sku_column = 0 + $index;
                $price_column = 1 + $index;

                if ( stristr( $r[0 + $index], 'sku' ) && stristr( $r[1 + $index], 'price' ) )
                    continue;
            }

            // Reset array
            if( !isset( $r[$price_column] ) )
                continue;

            $prices[] = array( 'sku' => $r[$sku_column], 'price' => $r[$price_column], 'price_note' => $r[$index + 2] );
        }

        // Make sure we have something to update
        $response->check( is_array( $prices ), _('No prices to adjust') );

        // If there is an error, return
        if ( $response->has_error() )
            return $response;

        $account_product = new AccountProduct();
        $account_product->multiply_product_prices_by_sku( $this->user->account->id, $prices, $_GET['price'], $_GET['sale_price'], $_GET['alternate_price'] );

        // Notify
        $this->notify( _('Your prices have been successfully updated!') );

        // Refresh
        $response->add_response( 'refresh', 1 );

        return $response;
    }
}