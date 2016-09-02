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
        if ( !$this->user->account->product_catalog )
            return new RedirectResponse('/');

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

        // Pricing points
        $pricing_points = array();

        $max_price = $account_product->get_max_price( $this->user->account->id );

        if ( $max_price > 100 ) {
            $quarter = round( ( $max_price / 4 ) );
            $digits = preg_match_all( "/[0-9]/", $quarter, $matches );
            $power = pow( 10, $digits - 1 );
            $quarter -= $quarter % ( 2.5 * $power ) ;
            //$quarter = floor( $quarter / $power ) * $power;

            if ( 0 == $quarter )
                $quarter = $power * 2.5;

            $pricing_points = array( $quarter, $quarter * 2, $quarter * 3 );
        }

        $this->resources->javascript( 'products/index' )
            ->css( 'products/index' )
            ->javascript_url( Config::resource( 'typeahead-js' ), Config::resource( 'jqueryui-js' ) );

        return $this->get_template_response( 'index')
            ->kb( 45 )
            ->menu_item( 'products/products/list' )
            ->set( compact( 'categories', 'product_count', 'coupons', 'pricing_points' ) );
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

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Your product(s) have been successfully added!') );
            $this->log( 'add-product-by-hand', $this->user->contact_name . ' has added products by hand to ' . $this->user->account->title, $_POST['products'] );

            return new RedirectResponse('/products/');
        }

        // Get variables
        $product_count = $account_product->count( $this->user->account->id );
        $categories = $category->sort_by_hierarchy();
        $brands = $brand->get_all();

        if ( $product_count > $this->user->account->products )
            $this->notify( _('Please contact your Online Specialist to add additional products. Product Usage has exceeded the number of items allowed.'), false );

        $this->resources
            ->javascript_url( Config::resource( 'typeahead-js' ) )
            ->javascript( 'products/add' )
            ->css( 'products/add' );

        $response = $this->get_template_response( 'add' )
            ->kb( 46 )
            ->menu_item( 'products/products/add' )
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
            ->kb( 47 )
            ->add_title( _('All Products') )
            ->menu_item( 'products/products/all' )
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
                        $this->log( 'catalog-dump-failed', $this->user->contact_name . ' has failed to dump a catalog into ' . $this->user->account->title, $_POST['hBrandID'] );
                    } else {
                        // Add bulk
                        $quantity = $account_product->add_bulk_by_brand( $this->user->account->id, $_POST['hBrandID'], $industries );

                        // Reorganize categories
                        $account_category = new AccountCategory();
                        $account_category->reorganize_categories( $this->user->account->id, new Category() );

                        // Clear CloudFlare Cache
                        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

                        if ( $cloudflare_zone_id ) {
                            library('cloudflare-api');
                            $cloudflare = new CloudFlareAPI( $this->user->account );
                            $cloudflare->purge( $cloudflare_zone_id );
                        }

                        // Notification
                        $this->notify( $quantity . ' ' . _('brand products added successfully!') );
                        $this->log( 'catalog-dump', $this->user->contact_name . ' has dumped a catalog into ' . $this->user->account->title, $_POST['hBrandID'] );
                    }
                }
            }
        }

        $brand = new Brand();
        $brands = $brand->get_all();

        $this->resources->javascript( 'products/catalog-dump' )
            ->css_url( Config::resource('jquery-ui') );

        $response = $this->get_template_response( 'catalog-dump' )
            ->kb( 48 )
            ->add_title( _('Catalog Dump') )
            ->menu_item( 'products/products/catalog-dump' )
            ->set( compact( 'js_validation', 'errs', 'brands' ) );

        return $response;
    }

    /**
     * Add Bulk
     *
     * @return TemplateResponse
     */
    protected function add_bulk() {
        $form = new BootstrapForm( 'fAddBulk' );
        $form->submit( _('Add Bulk'), '', 1 );
        $form->add_field( 'textarea', 'One SKU per Line', 'taSKUs' )
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
                $this->log( 'add-bulk-failed', $this->user->contact_name . ' did not have enough space to add bulk products to ' . $this->user->account->title, $skus );
            } else {
                // Add bulk
                list( $quantity, $already_existed, $not_added_skus ) = $account_product->add_bulk_all( $this->user->account->id, $this->user->account->get_industries(), $skus );

                // Reorganize categories
                $account_category = new AccountCategory();
                $account_category->reorganize_categories( $this->user->account->id, new Category() );

                // Clear CloudFlare Cache
                $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

                if ( $cloudflare_zone_id ) {
                    library('cloudflare-api');
                    $cloudflare = new CloudFlareAPI( $this->user->account );
                    $cloudflare->purge( $cloudflare_zone_id );
                }

                // Notification
                $this->notify( $quantity . ' ' . _('products added successfully!') );
                $this->log( 'add-bulk', $this->user->contact_name . ' added bulk products to ' . $this->user->account->title, $skus );
                $success = true;
            }

        }

        $form = $form->generate_form();

        return $this->get_template_response( 'add-bulk' )
            ->kb( 49 )
            ->add_title( _('Add Bulk') )
            ->menu_item( 'products/products/add-bulk' )
            ->set( compact( 'form', 'already_existed', 'not_added_skus', 'success' ) );
    }

    /**
     * Block Products
     *
     * @return TemplateResponse
     */
    protected function block_products() {
        $form = new BootstrapForm( 'fBlockProducts' );
        $form->submit( _('Hide Products'), "", 1 );
        $form->add_field( 'textarea', "Separate SKU's by putting one on each line", 'taSKUs' )
            ->add_validation( 'req', _('You must enter SKUs before you can add products') );

        $account_product = new AccountProduct();

        if ( $form->posted() ) {
            $skus = explode( "\n", str_replace( "\r", '', $_POST['taSKUs'] ) );

            $account_product->block_by_sku( $this->user->account->id, $this->user->account->get_industries(), $skus );

            // Reassign different product to categories linked for image
            foreach($skus as $sku){
                $account_category = new AccountCategory();
                $account_product = $account_product->get_by_sku($this->user->account->id, $sku);
                $account_category->reassign_image( $this->user->account->id, $account_product->product_id );
            }

            $this->notify( _('Hidden Products have been successfully updated!') );
            $this->log( 'block-products', $this->user->contact_name . ' blocked products on ' . $this->user->account->title, $skus );
        }

        $account_product = new AccountProduct();
        $blocked_products = $account_product->get_blocked( $this->user->account->id );

        $response = $this->get_template_response( 'block-products' )
            ->kb( 50 )
            ->add_title( _('Hidden Products') )
            ->menu_item( 'products/products/block-products' )
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

        $form = new BootstrapForm( 'fCategories' );
        $form->submit( _('Hide Categories'), '', 1 );
        $form->add_field( 'select', 'Select Categories to Hide', 'sCategoryIDs[]' )
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
            $this->log( 'hide-categories', $this->user->contact_name . ' hid categories on ' . $this->user->account->title, $_POST['sCategoryIDs'] );

            return new RedirectResponse( '/products/hide-categories/' );
        }

        $blocked_website_category_ids = $account_category->get_blocked_website_category_ids( $this->user->account->id );
        $blocked_categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $blocked_website_category_ids ) )
                continue;

            $blocked_categories[] = $category;
        }

        $this->resources->css('products/hide-categories');

        return $this->get_template_response( 'hide-categories' )
            ->kb( 51 )
            ->add_title( _('Hide Categories') )
            ->menu_item( 'products/products/hide-categories' )
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
            ->kb( 52 )
            ->add_title( _('Product Prices') )
            ->menu_item( 'products/products/auto-price' )
            ->set( compact( 'brands', 'categories' ) );
    }

    /**
     * Price Multiplier
     *
     * @return TemplateResponse
     */
    protected function price_multiplier() {
        $this->resources
            ->css( 'products/price-multiplier' )
            ->javascript( 'fileuploader', 'products/price-multiplier' );

        return $this->get_template_response( 'price-multiplier' )
            ->kb( 115 )
            ->add_title( _('Price Multiplier') )
            ->menu_item( 'products/products/auto-price' );
    }

    /**
     * Price Multiplier
     *
     * @return TemplateResponse
     */
    protected function auto_price() {
        // Find out what products will be affected
        $product = new AccountProduct();
        $auto_price_candidates = $product->get_auto_price_count( $this->user->account->id );
        $brand_ids = $product->get_auto_priceable_brands( $this->user->account->id );

        // Get auto prices
        $website_auto_price = new WebsiteAutoPrice();
        $auto_prices = $website_auto_price->get_all( $this->user->account->id );

        // Get Brands
        $brand = new Brand();
        $brands_array = $brand->get_by_ids( $brand_ids );
        $brands = array();

        foreach ( $brands_array as $brand ) {
            $brands[$brand->id] = $brand;
        }

        // Get categories
        $category = new Category();
        $account_category = new AccountCategory();

//        $categories = $category->filter_by_ids( $category->get_by_parent(0), $account_category->get_all_ids( $this->user->account->id ) );

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        foreach ( $categories_array as $category ) {
            if ( !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }


        if ( $this->verified() ) {
            $account_product = new AccountProduct();
            $category->get_all();

            foreach ( $_POST['auto-price'] as $brand_id => $auto_price_array ) {

                ksort($auto_price_array);

                foreach ( $auto_price_array as $category_id => $values ) {
                    $auto_price = new WebsiteAutoPrice();
                    $auto_price->get( $brand_id, $category_id, $this->user->account->id );

                    foreach( $values as $key => $value ) {
                        $auto_price->$key = $value;
                    }

                    // Save
                    $auto_price->save();

                    // & Run
                    $child_categories = $category->get_all_children( $auto_price->category_id );
                    $category_ids = array( $auto_price->category_id );

                    foreach ( $child_categories as $child_cat ) {
                        $category_ids[] = $child_cat->id;
                    }

                    $account_product->auto_price( $category_ids, $auto_price->brand_id, $auto_price->price, $auto_price->sale_price, $auto_price->alternate_price, $auto_price->ending, $this->user->account->id );
                }
            }

            // Adjust minimum prices if they haven't disabled it
            if ( '1' != $this->user->account->get_settings('disable-map-pricing') )
                $account_product->adjust_to_minimum_price( $this->user->account->id );

            // Reload auto prices
            $auto_prices = $website_auto_price->get_all( $this->user->account->id );

            // Clear Cloudflare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Your Auto Price settings have been successfully saved!') );
            $this->log( 'auto-price', $this->user->contact_name . ' auto-priced ' . $this->user->account->title, $_POST['auto-price'] );
        }

        // Get example product
        $account_product = new AccountProduct();
        $account_product->get_auto_price_example( $this->user->account->id );

        $product = new Product();
        $product->get( $account_product->product_id );
        $product->images = $product->get_images();

        $this->resources
            ->css( 'products/price-tools', 'products/auto-price')
            ->javascript( 'products/auto-price')
        ;

        return $this->get_template_response( 'auto-price' )
            ->kb( 134 )
            ->add_title( _('Auto Price') )
            ->set( array(
                'categories' => $categories
                , 'auto_price_candidates' => $auto_price_candidates
                , 'brands' => $brands
                , 'auto_prices' => $auto_prices
                , 'product' => $product
            ))
            ->menu_item( 'products/products/auto-price' );
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

            // Clear Cloudflare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Hidden Products have been successfully updated!') );
            $this->log( 'unblock-products', $this->user->contact_name . ' unblocked products on ' . $this->user->account->title, $_POST['unblock-products'] );
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

            // Clear Cloudflare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Hidden categories have been successfully updated!') );
            $this->log( 'unblock-products', $this->user->contact_name . ' unhid products on ' . $this->user->account->title, $_POST['unhide-categories'] );
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
        $form = new BootstrapForm( 'fSettings' );

        // Get settings
        $settings_array = array(
            'category-show-price-note'
            , 'hide-skus'
            , 'hide-request-quote'
            , 'hide-customer-ratings'
            , 'hide-product-brands'
            , 'hide-browse-by-brand'
            , 'replace-price-note'
            , 'disable-map-pricing'
            , 'myregistry-button-code'
            , 'hide-new-product-logo'
            , 'disable-out-of-stock-if-zero'
            , 'hide-out-of-stock-products'
        );

        $settings = $this->user->account->get_settings( $settings_array );

        $checkboxes = array(
        	'category-show-price-note' 	=> _('Show Price Note on Category Page')
        	, 'hide-skus' 				=> _('Hide Manufacturer SKUs')
        	, 'hide-request-quote' 		=> _('Hide "Request a Quote" Button')
        	, 'hide-customer-ratings' 	=> _('Hide Customer Ratings')
        	, 'hide-product-brands' 	=> _('Hide Product Brands')
        	, 'hide-browse-by-brand' 	=> _('Hide Browse By Brand')
            , 'replace-price-note'      => _('Replace Price Note with Product Option')
            , 'disable-map-pricing'     => _('Disable Map Pricing')
            , 'hide-new-product-logo'   => _('Hide New Product Logo')
            , 'disable-out-of-stock-if-zero'    => _("Don't Make Products 'Out of Stock' when Inventory reaches 0")
            , 'hide-out-of-stock-products'      => _("Automatically Hide 'Out of Stock' Products")
        );

        foreach( $checkboxes as $setting => $nice_name ) {
            $form->add_field( 'checkbox', $nice_name, $setting, $settings[$setting] );
        }

        if ( $this->user->account->is_new_template() ) {
            $form->add_field( 'blank', '' );
            $form->add_field( 'text', _('MyRegistry Site Key'), 'myregistry-button-code', ($settings['myregistry-button-code']) )
                            ->attribute( 'maxlength', '128' );
        }

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }

            if( $this->user->account->is_new_template() && isset( $_POST['myregistry-button-code'] ) ){
                $new_settings['myregistry-button-code'] = $_POST['myregistry-button-code'];
            }

            $this->user->account->set_settings( $new_settings );

            // Clear Cloudflare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Your settings have been successfully saved!') );
            $this->log( 'product-settings', $this->user->contact_name . ' has changed product settings on ' . $this->user->account->title, $_POST );

            // Refresh to get all the changes
            return new RedirectResponse('/products/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->kb( 61 )
            ->add_title( _('Settings') )
            ->menu_item( 'products/settings' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /**
     * Brands
     *
     * @return TemplateResponse
     */
    protected function brands() {
        $this->resources->javascript( 'products/brands' )
            ->javascript_url( Config::resource( 'typeahead-js' ), Config::resource( 'jqueryui-js' ) )
            ->css( 'products/brands' );

        $website_top_brand = new WebsiteTopBrand();

        return $this->get_template_response( 'brands' )
            ->kb( 58 )
            ->add_title( _('Brands') )
            ->menu_item( 'products/brands' )
            ->set( array(
                'top_brands' => $website_top_brand->get_by_account( $this->user->account->id )
                , 'link_brands' => $this->user->account->get_settings('link_brands')
            ) );
    }

    /**
     * Top Categories
     *
     * @return TemplateResponse
     */
    protected function top_categories() {
        // Initiate objects
        $category = new Category();
        $account_category = new AccountCategory();

        // Sort categories
        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $top_categories_array = json_decode( $this->user->account->get_settings('top-categories') );
        $top_categories_array = $top_categories_array ? $top_categories_array : array();
        $category_images = ar::assign_key( $account_category->get_website_category_images( $this->user->account->id, $website_category_ids ), 'category_id', true );
        $account_categories = ar::assign_key( $account_category->get_all( $this->user->account->id ), 'category_id' );

        foreach ( $account_categories as $cat_id => $ac ) {
            if ( !isset( $category_images[ $cat_id ] ) ) {
                $category_images[ $cat_id ] = $ac['image_url'];
            }
        }

        $categories = $top_categories = array();

        foreach ( $categories_array as $category ) {
            if ( in_array( $category->id, $website_category_ids ) )
                $categories[] = $category;
        }

        foreach ( $top_categories_array as $category_id ) {
            if ( !in_array( $category_id, $website_category_ids ) )
                continue;

            $top_categories[] = Category::$categories[$category_id];
        }

        // Get Account Parent Categories (Default Top Brands if first time)
        $default_top_categories = array();
        foreach( Category::$categories as $category ) {
            if ( $category->parent_category_id == 0 && in_array( $category->id, $website_category_ids ) )
                $default_top_categories[] = $category;
        }

        $this->resources->javascript( 'products/top-categories' )
            ->javascript_url( Config::Resource('jqueryui-js') )
            ->css( 'products/top-categories' );

        return $this->get_template_response( 'top-categories' )
            ->kb( 137 )
            ->add_title( _('Top Categories') )
            ->menu_item( 'products/top-categories' )
            ->set( compact( 'categories', 'top_categories', 'category_images', 'default_top_categories' ) );
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

        $product_option_item = new ProductOptionItem();
        $product_option_items_list = $product_option_item->export( $this->user->account->id );
        $product_option_items = [];

        $category = new Category;
        $category->get_all();

        foreach ( $product_option_items_list as $product_option_item ) {
            $product_option_items[$product_option_item->parent_product_id][] = $product_option_item;
        }

        $output[]  = array( 'ProductID', 'Type', 'SKU', 'Name', 'Description', 'Industry', 'Category', 'Brand', 'Image', 'Wholesale Price', 'MAP Price', 'Price', 'Sale Price', 'MSRP' );

        foreach ( $products as $product ) {
            $category->get( $product->category_id );

            $output[] = array( $product->product_id, 'product', $product->sku, $product->name
                , trim(strip_tags($product->description)), $product->industry, $category->taxonomy(), $product->brand
                , $product->image, $product->price_wholesale, $product->price_map, $product->price
                , $product->price_msrp, $product->price_sale );

            if ( $product_option_items[$product->product_id] )
            foreach ( $product_option_items[$product->product_id] as $product_option_item ) {
                $output[] = array( $product_option_item->product_id, 'option', $product_option_item->sku
                    , '[' . $product_option_item->product_option . ']' . $product_option_item->product_name
                    , trim(strip_tags($product_option_item->description)), 'N/A', 'N/A', 'N/A'
                    , $product_option_item->image, $product_option_item->price_wholesale, $product_option_item->price_map
                    , $product_option_item->price, $product_option_item->price_msrp, $product_option_item->price_sale
                );
            }
        }

        $this->log( 'export-products', $this->user->contact_name . ' generated a product export on ' . $this->user->account->title );

        return new CsvResponse( $output, format::slug( $this->user->account->title ) . '-products.csv' );
    }

    /**
     * Download non auto price products
     *
     * @return CsvResponse
     */
    protected function download_non_autoprice_products() {
        // Get the products
        $account_product = new AccountProduct();
        $products = $account_product->get_non_autoprice_products( $this->user->account->id );

        $output[]  = array( 'SKU', 'Price', 'Note', 'Name' );

        foreach ( $products as $product ) {
            $output[] = array( $product->sku, $product->price, $product->price_note, $product->name );
        }

        $this->log( 'download-non-autoprice-products', $this->user->contact_name . ' downloaded non-autopriceable products ' . $this->user->account->title );

        return new CsvResponse( $output, format::slug( $this->user->account->title ) . '-non-autoprice-products.csv' );
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

        $response->check( isset( $_GET['type'], $_GET['term'] ), _('Autocomplete failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ac_suggestions = array();

        // Get the right suggestions for the right type
        switch ( $_GET['type'] ) {
            case 'brand':
                $brand = new Brand;
                $ac_suggestions = $brand->autocomplete_all( $_GET['term'], $this->user->account->id );
            break;

            case 'product':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_GET['term'], 'name', $this->user->account->id );
            break;

            case 'sku':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_GET['term'], 'sku', $this->user->account->id );
            break;

            case 'sku-products':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_GET['term'], array( 'name', 'sku' ), $this->user->account->id );
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

        $response->check( isset( $_GET['type'], $_GET['term'] ), _('Autocomplete failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ac_suggestions = array();

        // Get the right suggestions for the right type
        switch ( $_GET['type'] ) {
            case 'brand':
                $brand = new Brand;
                $ac_suggestions = $brand->autocomplete_by_account( $_GET['term'], $this->user->account->id );
            break;

            case 'product':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_by_account( $_GET['term'], 'name', $this->user->account->id );
            break;

            case 'sku':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_by_account( $_GET['term'], 'sku', $this->user->account->id );
            break;

            default:
                if ( is_array($_GET['type']) ) {
                    $account_product = new AccountProduct();
                    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                    $ac_suggestions = $account_product->autocomplete_by_account( $_GET['term'], $_GET['type'], $this->user->account->id, $limit );
                }
            break;
        }

        // It needs to be empty if nothing else
        if ( !is_array( $ac_suggestions ) )
            $ac_suggestions = array();

        foreach ( $ac_suggestions as &$acs ) {
            // Escaping
            if ( isset($acs['name']) ) {
                $acs['name'] = html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' );
            }
        }

        // Sent by the autocompleter
        $response->add_response( 'suggestions', $ac_suggestions );

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

        // Clear Cloudflare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Let them know
        $response->notify( 'All discontinued products have been removed' );
        $this->log( 'remove-all-discontinued-products', $this->user->contact_name . ' removed all discontinued products on ' . $this->user->account->title );

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
        $order_by = '';

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

        // Exclude Blocked Categories
        $blocked_categories = $account_category->get_blocked_website_category_ids( $this->user->account->id );
        if ( $blocked_categories ) {
            foreach ( $blocked_categories as $bc_id ) {
                foreach ( $category->get_all_children( $bc_id ) as $bcc  ) {
                    $blocked_categories[] = $bcc->category_id;
                    foreach ( $category->get_all_children( $bcc->category_id ) as $bccc  ) {
                        $blocked_categories[] = $bccc->category_id;
                    }
                }
            }
            $where .= " AND c.`category_id` NOT IN ( " . implode( ',', $blocked_categories ) . " )";
        }

        // Pricing
        if ( !empty( $_POST['pr'] ) ) {
            if ( '0|0' == $_POST['pr'] ) {
                $where .= " AND ( wp.`sale_price` = 0 AND wp.`price` = 0 )";
            } else {
                list( $min, $max ) = explode( '|', $_POST['pr'] );
                $min = (int) $min;
                $max = (int) $max;
                $pricing_min_where = " ( wp.`sale_price` = 0 AND wp.`price` >= $min OR wp.`sale_price` >= $min )";

                $where .= ( empty( $max ) ) ? " AND $pricing_min_where" : " AND ( $pricing_min_where AND ( wp.`sale_price` = 0 AND wp.`price` < $max OR wp.`sale_price` > 0 AND wp.`sale_price` < $max ) )";
            }
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
                if ( _('Enter Brand...') != $_POST['v'] ) {
                    $where .= " AND b.`name` LIKE " . $account_product->quote( $_POST['v'] . '%' );
                    $order_by = 'b.`name` ASC';
                }
            break;
        }

        $products = $account_product->search( $this->user->account->id, $per_page, $where, $order_by, $page );
        $product_count = $account_product->search_count( $this->user->account->id, $where );

        foreach ( $products as $product ) {
            $product->name = utf8_encode($product->name);
            if ($this->user->account->is_new_template() ) {
                $product->link = 'http://' . $this->user->account->domain . '/product' . ( ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/' );
            } else {
                $product->link = 'http://' . $this->user->account->domain . ( ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/' );
            }

            $product->image_url = $product->get_image_url( $product->image, '', $product->industry, $product->product_id  );
        }

        $response = new AjaxResponse( true );
        $response->add_response( 'products', $products );
        $response->add_response( 'product_start', (($page -1) * $per_page + 1) < $product_count ? (($page -1) * $per_page + 1) : $product_count );
        $response->add_response( 'product_end', ($page * $per_page) < $product_count ? ($page * $per_page) : $product_count );
        $response->add_response( 'product_count', $product_count );
        return $response;
    }

    /**
     * Remove
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

        // Reassign different product to categories linked for image
        $account_category->reassign_image( $this->user->account->id, $account_product->product_id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notification
        $response->notify( 'Product removed.' );
        $this->log( 'remove-product', $this->user->contact_name . ' removed product on ' . $this->user->account->title, $_GET['pid'] );

        return $response;
    }

    /**
     * Block Products
     *
     * @return AjaxResponse
     */
    protected function block() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['pid'] ), _('Hiding product failed') );

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

        // Reassign different product to categories linked for image
        $account_category->reassign_image( $this->user->account->id, $product->product_id );

        // Clear Cloudflare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $response->notify( 'Product hidden' );
        $this->log( 'block-product', $this->user->contact_name . ' has hidden a product on ' . $this->user->account->title, $_GET['pid'] );

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
        $account_category->product_id = intval( urldecode( $_GET['i'] ) );
        $account_category->image_url = preg_replace( '/(.+\/products\/[0-9]+\/)(?:small\/)?([a-zA-Z0-9-.]+)/', "$1small/$2", urldecode( $_GET['i'] ) );
        $account_category->save();

        // Update AccountBrandCategory
        $account_brand_category = new AccountBrandCategory();
        $account_brand_category->get( $this->user->account->id, $_GET['bid'], $_GET['cid']);
        $account_brand_category->image_url = $account_category->image_url;

        if ( $account_brand_category->website_id ) {
            $account_brand_category->save();
        } else {
            $account_brand_category->website_id = $this->user->account->id;
            $account_brand_category->brand_id = $_GET['bid'];
            $account_brand_category->category_id = $_GET['cid'];
            $account_brand_category->create();
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notification
        $response->notify( 'Your category image has been set');
        $this->log( 'set-category-image', $this->user->contact_name . ' set a category image on ' . $this->user->account->title, $_GET );

        return $response;
    }

    /**
     * Edit (Get Product Dialog Info)
     *
     * @return CustomResponse
     */
    protected function edit() {
        // Instantiate objects
        $account_product = new AccountProduct();
        $product_option = new ProductOption();
        $website_coupon = new WebsiteCoupon();
        $product = new Product();  // For getting images
        $category = new Category();  // For getting public URL

        // Get variables
        $account_product->get( $_GET['pid'], $this->user->account->id );
        $account_product->coupons = $website_coupon->get_by_product( $this->user->account->id, $_GET['pid'] );
        $account_product->product_options();

        $product->get( $_GET['pid'] );
        $images = $product->get_images();
        $account_product->image = $product->get_image_url( $images[0], 'small', $product->industry, $product->id );
        $account_product->name = $product->name;

        // Get The Public URL Link
        if ($this->user->account->is_new_template() ) {
            $account_product->link = 'http://' . $this->user->account->domain . '/product';
        } else {
            $account_product->link = 'http://' . $this->user->account->domain;
        }
        $account_product->link .= ( ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/' );

        $coupons = $website_coupon->get_by_account( $this->user->account->id );

        // Get Product Options
        $child_products = $product->get_by_parent( $product->product_id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Add to response
        $response = new CustomResponse( $this->resources, 'products/edit' );
        $response->set( array(
            'product' => $account_product
            , 'child_products' => $child_products
            , 'coupons' => $coupons
        ) );

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

        if ( !empty( $_POST['tSalePrice'] ) )
            $response->check( !empty( $_POST['tPrice'] ), _('If you fill in the "Sale Price" you must also fill in the "Price"') );

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
        // if any price changed, we flag the product price as manually edited
        $account_product->manual_price = $account_product->manual_price || ( $account_product->price != $_POST['tPrice'] ) || ( $account_product->sale_price != $_POST['tSalePrice'] ) || ( $account_product->wholesale_price != $_POST['tWholesalePrice'] ) || ( $account_product->alternate_price != $_POST['tAlternatePrice'] );

        $account_product->alternate_price = $_POST['tAlternatePrice'];
        $account_product->price = $_POST['tPrice'];
        $account_product->sale_price = $_POST['tSalePrice'];
        $account_product->setup_fee = $_POST['tSetupFee'];
        $account_product->inventory = $_POST['tInventory'];
        $account_product->alternate_price_name = $_POST['tAlternatePriceName'];
        $account_product->price_note = $_POST['tPriceNote'];
        $account_product->product_note = $_POST['taProductNote'];
        $account_product->warranty_length = $_POST['tWarrantyLength'];
        $account_product->display_inventory = ( isset( $_POST['cbDisplayInventory'] ) ) ? 1 : 0;
        $account_product->inventory_tracking = ( isset( $_POST['cbInventoryTracking'] ) ) ? 1 : 0;        
        $account_product->on_sale = ( isset( $_POST['cbOnSale'] ) ) ? 1 : 0;
        $account_product->status = $_POST['sStatus'];
        $account_product->meta_title = $_POST['tMetaTitle'];
        $account_product->meta_description = $_POST['tMetaDescription'];
        $account_product->meta_keywords = $_POST['tMetaKeywords'];

        if ( $this->user->account->shopping_cart ) {
            $account_product->wholesale_price = $_POST['tWholesalePrice'];
            $account_product->additional_shipping_amount = $_POST['tAdditionalShipping'];
            $account_product->weight = $_POST['tWeight'];
            $account_product->additional_shipping_type = $_POST['rShippingMethod'];
            $account_product->ships_in = $_POST['tShipsIn'];
            $account_product->store_sku = $_POST['tStoreSKU'];

            $coupons = $_POST['hCoupons'];
        } else {
            $coupons = false;
        }

        // Update product
        $account_product->save();

        // If they haven't disabled it
        if ( '1' != $this->user->account->get_settings('disable-map-pricing') ) {
            // See if he had set prices too lower
            $adjusted_products = $account_product->adjust_to_minimum_price( $this->user->account->id );

            // Give a notification
            if ( $adjusted_products ) {
                $response->notify( 'Your price was too low and has been adjusted to the MAP price of $' . number_format( $account_product->price_min, 2 ), false );
                $account_product->get( $account_product->product_id, $account_product->website_id );
            }
        }

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
				if ( isset( $po['list_items'] ) )
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

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notification
        $response->notify( 'Product updated' );
        $this->log( 'update-product', $this->user->contact_name . ' updated a product on ' . $this->user->account->title, $_POST );

        return $response;
    }

    protected function update_product_status(){
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['product_id'], $_POST['status'] ), 'Error while trying to process your request, please try again' );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate objects
        $product = new Product();
        $account_product = new AccountProduct();

        // Get product
        $account_product->get( $_POST['product_id'], $this->user->account->id );

        // Make sure we have permission
        $response->check( $account_product->product_id, 'You do not have permission to modify this product');

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get product
        $product->get( $account_product->product_id );

        // Change visibility
        $product->publish_visibility = ( 'private' == $_POST['status'] ) ? Product::PUBLISH_VISIBILITY_PRIVATE : Product::PUBLISH_VISIBILITY_PUBLIC;
        $product->save();

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

        $response->check( isset( $_POST['sku'], $_POST['brand_id'] ) && !empty( $_POST['sku'] ) && !empty( $_POST['brand_id'] ), _('Please type in a SKU & Brand') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate objects
        $product = new Product();

        // Check to see if it already exists
        $product->get_by_sku_by_brand( $_POST['sku'], $_POST['brand_id'] );

        if ( $product->id ) {
            $account_product = new AccountProduct();
            $account_product->get( $product->id, $this->user->account->id );

            if ( $account_product->product_id && $account_product->active )
                $message = 'A product with same SKU already exists in record and it is already added in your website.';
            else
                $message = 'A product with the same SKU already exists in record.';

            $response->check( false, $message );
            return $response;
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
        $dt->add_where( " AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00' AND p.`parent_product_id` IS NULL " );

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
        	$dialog = '<a href="' . url::add_query_arg( 'pid', $product->id, '/products/get-product/' ) . '" title="View Product" data-modal>';
        	$actions = '<a href="javascript:;" class="add-product" data-id="'.$product->id.'" data-name="'.$product->name.'">Add Product</a>';

        	$data[] = array(
        		$dialog . format::limit_chars( utf8_encode( $product->name ) ,  37, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>'
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

        // We will need it to send him an email
        $catalog_manager_user = new User();
        $catalog_manager_user->get( User::CATALOG_MANAGER );

        // Add the request
        $ticket = new Ticket;

        $ticket_message = $subject = '';

        foreach ( $_POST['requests'] as $r ) {
        	if ( !empty( $ticket_message ) )
        		$ticket_message .= "<br><br>";

        	// Get the brand, sku and collection
        	$ticket_array = explode( '|', $r );

        	// Add it to the message
        	$ticket_message .= 'Brand: ' . $ticket_array[0] . "<br>";
        	$ticket_message .= 'SKU: ' . $ticket_array[1] . "<br><br>";
        	$ticket_message .= 'Collection: ' . $ticket_array[2];

        	$subject = ( $this->user->account->live ) ? 'Live' : 'Staging';
        }

        // Create Ticket
        $ticket->user_id = $this->user->id;
        $ticket->assigned_to_user_id = User::CATALOG_MANAGER;
        $ticket->website_id = $this->user->account->id;
        $ticket->summary = "$subject - Product Request";
        $ticket->message = $ticket_message;
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = Ticket::PRIORITY_NORMAL;
        $ticket->create();

        fn::mail(
            $catalog_manager_user->email
            , 'New Ticket - ' . $ticket->summary
            , "Name: " . $this->user->contact_name
            . "\nEmail: " . $this->user->email
            . "\nSummary: " . $ticket->summary
            . "\n\n" . str_replace( array("<br>", "<br />", "<br/>"), "\n", $ticket->message )
            . "\n\nhttp://admin." . $catalog_manager_user->domain . "/tickets/ticket/?tid=" . $ticket->id
        );

        $response->notify( 'You product request has been received, we will contact you soon.' );
        $this->log( 'add-product-request', $this->user->contact_name . ' created a product request ' . $this->user->account->title );
        $response->add_response( 'close_modal', 'close_modal' );

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
        $dt->add_where( ' AND wp.`website_id` = ' . (int) $this->user->account->id );

        if ( !empty( $_GET['b'] ) )
            $dt->add_where( ' AND p.`brand_id` = ' . (int) $_GET['b'] );

        $dt->search( array( 'p.`sku`' => false, 'p.`name`' => false, 'wp.`alternate_price`' => false, 'wp.`price`' => false, 'wp.`sale_price`' => false, 'wp.`price_note`' => false ) );

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

            // Set the order by
            $_GET['iSortingCols'] = 1;
            $_GET['iSortCol_0'] = 0;
            $_GET['sSortDir_0'] = 'ASC';
            $dt->order_by( 'wp.`sequence`' );
        } else {
            $dt->order_by( 'p.`sku`', 'p.`name`', 'wp.`alternate_price`', 'wp.`price`', 'wp.`sale_price`', 'wp.`price_note`' );
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
                , utf8_encode($product->name)
                , '<input type="text" class="form-control" value="' . $product->alternate_price . '" data-product-id="'. $product->product_id .'" data-name="alternate_price" />'
                , '<input type="text" class="form-control" value="' . $product->price . '" data-product-id="'. $product->product_id .'" data-name="price" />'
                , '<input type="text" class="form-control" value="' . $product->sale_price . '" data-product-id="'. $product->product_id .'" data-name="sale_price" />'
                , '<input type="text" class="form-control" value="' . $product->price_note . '" data-product-id="'. $product->product_id .'" data-name="price_note" />'
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

        // Make sure they haven't disabled it
        if ( '1' != $this->user->account->get_settings('disable-map-pricing') ) {
            // See if he had set prices too lower
            $adjusted_products = $account_product->adjust_to_minimum_price( $this->user->account->id );

            // Give a notification
            if ( $adjusted_products )
                $response->notify( 'Your price on ' . $adjusted_products . ' of your product(s) was too low and has been adjusted to the MAP price of that product.', false );
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notification
        $response->notify( 'Your products has been updated' );
        $this->log( 'set-product-prices', $this->user->contact_name . ' set product prices on ' . $this->user->account->title, $_POST['v'] );

        return $response;
    }

    /**
     * Update Product Sequence
     *
     * @return AjaxResponse
     */
    protected function update_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('Unable to update product sequence. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $sequence = explode( '|', $_POST['s'] );

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

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $this->log( 'update-product-sequence', $this->user->contact_name . ' updated product sequence ' . $this->user->account->title );

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

        $sequence = explode( '|', $_POST['s'] );

        $website_top_brand = new WebsiteTopBrand();
        $website_top_brand->update_sequence( $this->user->account->id, $sequence );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/products/' );
        }

        $this->log( 'update-brand-sequence', $this->user->contact_name . ' updated brand sequence ' . $this->user->account->title );

        return $response;
    }

    /**
     * Update Top Brand Sequence
     *
     * @return AjaxResponse
     */
    protected function update_top_category_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('Unable to update top category sequence. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $sequence = explode( '|', $_POST['s'] );

        $this->user->account->set_settings( array( 'top-categories' => json_encode( $sequence ) ) );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/products/' );
        }

        $this->log( 'update-top-category-sequence', $this->user->contact_name . ' updated top category sequence ' . $this->user->account->title );

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

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/products/' );
        }

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        $this->log( 'remove-top-brand', $this->user->contact_name . ' removed a top brand from ' . $this->user->account->title, $_GET['bid'] );

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
        $this->user->account->set_settings( array( 'link_brands' => $_POST['checked'] ) );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        $this->log( 'set-brands-to-link', $this->user->contact_name . ' brands to link on ' . $this->user->account->title );

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

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/products/' );
        }

        $response->add_response( 'brand', $brand );

        $this->log( 'add-top-brand', $this->user->contact_name . ' added a top brand to ' . $this->user->account->title, $_POST['bid'] );

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

        // Make sure they haven't disabled it
        if ( '1' != $this->user->account->get_settings('disable-map-pricing') ) {
            // See if he had set prices too lower
            $adjusted_products = $account_product->adjust_to_minimum_price( $this->user->account->id );

            // Give a notification
            if ( $adjusted_products ) {
                $this->notify( 'Your price on ' . $adjusted_products . ' of your product(s) was too low and has been adjusted to the MAP price of that product.', false );
            } else {
                $this->notify( _('Your prices have been successfully updated!') );
            }
        } else {
            $this->notify( _('Your prices have been successfully updated!') );
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $this->log( 'multiply-prices', $this->user->contact_name . ' multiplied prices on ' . $this->user->account->title );

        // Refresh
        $response->add_response( 'refresh', 1 );

        return $response;
    }

    /**
     * Remove Auto Price
     *
     * @return AjaxResponse
     */
    protected function remove_auto_price() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['bid'], $_GET['cid'] ), _('Unable to remove auto price. Please refresh the page and try again.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Get category
        $category = new Category();
        $category->get_all();
        $category->get( $_GET['cid'] );

        $categories = $category->get_all_children( $category->id );

        $category_ids = array( $category->id );

        foreach ( $categories as $cat ) {
            $category_ids[] = $cat->id;
        }

        // Get brand
        $brand = new Brand();
        $brand->get( $_GET['bid'] );

        // Now autoprice
        $account_product = new AccountProduct();
        $account_product->reset_prices( $category_ids, $this->user->account->id, $brand->id );

        // Reset pricing settings for this brand/category
        $website_auto_price = new WebsiteAutoPrice();
        $website_auto_price->get( $_GET['bid'], $_GET['cid'], $this->user->account->id );
        if ( $website_auto_price->website_id ) {
            $website_auto_price->alternate_price = 0;
            $website_auto_price->price = 0;
            $website_auto_price->sale_price = 0;
            $website_auto_price->ending = 0;
            $website_auto_price->save();
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $brand = ( $brand->id ) ? $brand->name . ' ' : '';
        $response->notify( _('Prices have been removed for ') . $brand . $category->name . ' category.' );
        $this->log( 'remove-auto-price', $this->user->contact_name . ' removed auto-pricing for a category on ' . $this->user->account->title, $_GET );

        return $response;
    }

    /**
     * Delete Auto Price
     *
     * @return AjaxResponse
     */
    protected function delete_auto_price() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['bid'], $_GET['cid'] ), _('Unable to delete auto price. Please refresh the page and try again.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Delete auto price setting
        $auto_price = new WebsiteAutoPrice();
        $auto_price->get( $_GET['bid'], $_GET['cid'], $this->user->account->id );
        $auto_price->remove();

        // Also remove all prices
        $category = new Category();
        $category->get_all();
        $category->get( $_GET['cid'] );

        $categories = $category->get_all_children( $category->id );

        $category_ids = array( $category->id );

        foreach ( $categories as $cat ) {
            $category_ids[] = $cat->id;
        }

        // Get brand
        $brand = new Brand();
        $brand->get( $_GET['bid'] );

        // Now autoprice
        $account_product = new AccountProduct();
        $account_product->reset_prices( $category_ids, $this->user->account->id, $brand->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notify
        $response->notify( _('Auto Price have been deleted.') );
        $this->log( 'delete-auto-price', $this->user->contact_name . ' deleted auto-pricing for a category on ' . $this->user->account->title, $_GET );

        return $response;
    }

    /**
     * Delete Auto Price
     *
     * @return AjaxResponse
     */
    protected function add_auto_price() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['bid'], $_POST['cid'], $_POST['price'], $_POST['sale_price'], $_POST['alternate_price'], $_POST['ending'] ), _('Unable to add auto price. Please refresh the page and try again.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        // Delete from database
        $auto_price = new WebsiteAutoPrice();
        $auto_price->website_id = $this->user->account->id;
        $auto_price->brand_id = $_POST['bid'];
        $auto_price->category_id = $_POST['cid'];
        $auto_price->price = $_POST['price'];
        $auto_price->sale_price = $_POST['sale_price'];
        $auto_price->alternate_price = $_POST['alternate_price'];
        $auto_price->ending = $_POST['ending'];

        try {
            $auto_price->create();
            $response->notify( _('Auto Price has been added successfully.') );
            $this->log( 'add-auto-price', $this->user->contact_name . ' added auto-pricing for a category on ' . $this->user->account->title, $_POST );
        } catch ( ModelException $e ) {
            switch ( $e->getCode() ) {
                case ActiveRecordBase::EXCEPTION_DUPLICATE_ENTRY:
                    // Let them know what happened
                    $this->notify( _('Ths brand/category already exists. Please modify the current row.' ), false );
                break;

                default:
                    // Don't know what happened
                    $this->notify( _('An error occurred while trying to add your auto price. If this problem continues, please contact your online specialist.'), false );

                    // Create a ticket
                    $ticket = new Ticket();
                    $ticket->website_id = $this->user->account->id;
                    $ticket->user_id = $this->user->id;
                    $ticket->assigned_to_user_id = User::TECHNICAL;
                    $ticket->summary = 'Unknown error on Products > Price Tools > Auto Price';
                    $ticket->message = 'Error code: ' . $e->getCode() . '<br>Error Message: ' . $e->getMessage();
                    $ticket->priority = Ticket::PRIORITY_HIGH;
                    $ticket->create();
                break;
            }
        }

        return $response;
    }

    /**
     * Manually Priced
     * Show all Manually Priced products
     *
     * @return TemplateResponse
     */
    protected function manually_priced() {
        $account_product = new AccountProduct();

        $products = $account_product->get_manually_priced_by_account( $this->user->account->id );

        $this->resources->javascript( 'products/manually-priced' );

        $response = $this->get_template_response( 'manually-priced' )
            ->kb( 142 )
            ->add_title( _('Manually Priced Products') )
            ->menu_item( 'products/products/manually-priced' )
            ->set( compact( 'products' ) );

        return $response;
    }

    /**
     * Manually Priced Remove
     *
     * @return AjaxResponse
     */
    protected function manually_priced_remove() {
        $response = new AjaxResponse( $this->verified() );

        $account_product = new AccountProduct();
        $account_product->get( $_GET['product-id'], $this->user->account->id );

        $response->check( $account_product->product_id, 'Product does not exists' );
        if ( $response->has_error() ) {
            return $response;
        }

        $account_product->manual_price = 0;
        $account_product->save();

        $response->notify( 'Your product has been removed from the Manual Price list.' );
        $this->log( 'remove-manually-priced', $this->user->contact_name . ' removed manually-priced mark from product on ' . $this->user->account->title, $_GET['product-id'] );

        return $response;
    }

    /**
     * Manually Priced Remove All
     * @return RedirectResponse
     */
    protected function manually_priced_remove_all() {
        if ( !$this->verified() )
            return new RedirectResponse( '/products/ ');

        $account_product = new AccountProduct();
        $account_product->null_manually_priced_by_account( $this->user->account->id );

        $this->log( 'remove-all-manually-priced', $this->user->contact_name . ' removed all manually-priced products on ' . $this->user->account->title );

        return new RedirectResponse( '/products/manually-priced/' );
    }

    /**
     * Manually Priced Lock All
     * @return RedirectResponse
     */
    protected function manually_priced_lock_all() {
        if ( !$this->verified() )
            return new RedirectResponse( '/products/ ');

        $account_product = new AccountProduct();
        $account_product->lock_prices_by_account( $this->user->account->id );

        $this->log( 'manually-price-lock-all', $this->user->contact_name . ' locked all products as manually-priced on ' . $this->user->account->title );

        return new RedirectResponse( '/products/manually-priced/' );
    }

    /**
     * Get
     *
     * @return AjaxResponse
     */
    protected function get() {
        $response = new AjaxResponse( $this->verified() );

        $account_product = new AccountProduct();
        $product = new Product();
        $category = new Category();

        // Get
        $account_product->get( $_GET['pid'], $this->user->account->id );

        // Get Product Images & Name
        $product->get( $_GET['pid'] );
        $images = $product->get_images();
        $account_product->image = $product->get_image_url( $images[0], 'small', $product->industry, $product->id );
        $account_product->name = $product->name;

        // Get Category
        $category->get( $product->category_id );

        // Get Product Link
        if ($this->user->account->is_new_template() ) {
            $account_product->link = 'http://' . $this->user->account->domain . '/product' . ( ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/' );
        } else {
            $account_product->link = 'http://' . $this->user->account->domain . ( 0 == $product->category_id ) ? '/' . $product->slug : $category->get_url( $product->category_id ) . $product->slug . '/';
        }

        $response->add_response( 'product', $account_product );
        return $response;
    }

    /**
     * Reset all prices
     *
     * Set all AccountProducts prices to 0
     *
     * @return RedirectResponse
     */
    protected function reset_all_prices() {

        $account_product = new AccountProduct();
        $account_product->reset_price_by_account( $this->user->account->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Notification
        $this->notify( _('All account prices has been reset.') );

        $this->log( 'reset-all-prices', $this->user->contact_name . ' reset all prices on ' . $this->user->account->title );

        return new RedirectResponse( "/products/settings/" );
    }

    /**
     * Import
     *
     * @return TemplateResponse
     */
    protected function import() {
        $this->resources
            ->css( 'products/import' )
            ->javascript( 'fileuploader', 'products/import' );

        return $this->get_template_response( 'import' )
            ->kb( 0 )
            ->select( 'products', 'products/import' )
            ->add_title( _('Import') );
    }

    /**
     * Import Products
     *
     * @return AjaxResponse
     */
    protected function prepare_import() {
        set_time_limit( 30 * 60 );
        ini_set( 'memory_limit', '512M' );

        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error, return
        if ( $response->has_error() )
            return $response;

        // All imported products are of the following brand
        $brand_id = (int) $_GET['brand_id'];

        // Get file uploader
        library('file-uploader');

        // Upload file
        $uploader = new qqFileUploader( array( 'csv', 'xls' ), 26214400 );
        $result = $uploader->handleUpload( 'gsrs_' );

        // Setup variables
        $file_extension = strtolower( f::extension( $_GET['qqfile'] ) );

        // get data regarding file extension
        switch ( $file_extension ) {
            case 'xls':
                // Load excel reader
                library('Excel_Reader/Excel_Reader');
                $er = new Excel_Reader();
                // Set the basics and then read in the rows
                $er->setOutputEncoding('ASCII');
                $er->read( $result['file_path'] );

                $rows = $er->sheets[0]['cells'];

                break;

            case 'csv':
                // Make sure it's opened properly
                $response->check( $handler = fopen( $result['file_path'], 'r' ), _('An error occurred while trying to read your file.') );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;

                // Loop through the rows
                while ( $row = fgetcsv( $handler ) ) {
                    $rows[] = $row;
                }

                fclose( $handler );

                break;

            default:
                // Display an error
                $response->check( false, _('Only CSV and Excel file types are accepted. File type: ') . $file_extension );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;
                break;
        }

        $response->check( is_array( $rows ), _('There were no emails to import') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        foreach ($rows as &$r)
            foreach ($r as &$c)
                $c = trim($c);

        $headers = array_shift( $rows );
        foreach ( $headers as &$column ) {
            $column = strtolower($column);
        }

        // Industries
        $industry = new Industry();
        $industries = $industry->get_all();

        // Categories
        $category = new Category();
        $categories = $category->get_all();
        $categories_by_name = [];

        foreach ( $categories as $category ) {
            if ( $category->has_children() )
                continue;

            $category_string = $category->name;
            $parents = $category->get_all_parents( $category->id );

            foreach ( $parents as $parent_category ) {
                $category_string = $parent_category->name . ' > ' . $category_string;
            }

            $categories_by_name[$category_string] = $category->id;
        }
        ksort( $categories_by_name );

        // Brands
        $brand = new Brand();
        $brands = $brand->get_all();
        $brands_by_name = [];

        foreach ( $brands as $brand ) {
            $brands_by_name[$brand->name] = $brand;
        }

        // Our products to import
        $products = array();

        // Products that won't be imported
        $skipped_products = array();

        // # of Products that will update
        $to_update = 0;

        // Empty array
        $last_product = [];

        foreach ( $rows as &$values ) {
            if ( count($headers) == count($values) ) {
                $r = array_combine( $headers, $values );
            } else {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Incomplete row.";
                $skipped_products[] = $r;
                continue;
            }

            // basic input validation
            $required_keys = array(
                'productid', 'type', 'sku', 'name', 'description', 'industry', 'category', 'brand', 'image'
                , 'wholesale price', 'map price', 'price', 'sale price', 'msrp' );

            $valid = true;
            foreach ($required_keys as $k) {
                if ( !isset( $r[$k] ) ) {
                    $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Required field '$k'. ";
                    $valid = false;

                    if ( !isset( $last_product[$k] ) )
                        $last_product[$k] = '';
                }
            }

            if ( 'product' == $r['type'] ) {
                $category_id = null;
                if ( isset( $categories_by_name[$r['category']] ) ) {
                    $category_id = $categories_by_name[$r['category']];
                }
            } else {
                $category_id = $last_product['category_id'];
            }

            if ( !$category_id ) {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Category not found '{$r['category']}'. ";
                $valid = false;
            }

            if ( 'product' == $r['type'] ) {
                $industry_id = null;
                foreach ($industries as $i)
                {
                    if (strcasecmp($i->name, $r['industry']) === 0)
                    {
                        $industry_id = $i->industry_id;
                        break;
                    }
                }
            } else {
                $industry_id = $last_product['industry_id'];
            }

            if ( !$industry_id ) {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Industry not found '{$r['industry']}'. ";
                $valid = false;
            }

            if ( 'product' == $r['type'] ) {
                $brand_id = $brands_by_name[$r['brand']]->id;

                if (!$brand_id)
                {
                    $brand->name = $r['brand'];
                    $brand_id = $brand->create();
                }
            } else {
                $brand_id = $last_product['brand_id'];
            }

            if ( !$valid ) {
                $skipped_products[] = $r;
                continue;
            }

            // see if the product exists
            $matching_product = new Product();

            if ( $r['productid'] )
                $matching_product->get_by_id_by_website( $r['productid'], $this->user->account->id );

            $image_list = explode(',', $r['image']);

            foreach( $image_list as $k => $image ) {
                $image = trim($image);

                if ( !regexp::match( $image, 'url' ) ) {
                    $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Bad image URL. ";
                    $valid = false;
                }

                // we ensure the url is decoded before encode
                $image = rawurldecode( $image );
                // encode url
                $url_parts = parse_url( $image );
                $path_parts = array_slice( explode( '/', $url_parts['path'] ), 1 );
                foreach ( $path_parts as &$part)
                    $part = rawurlencode( $part );
                $url_parts['path'] = implode( '/', $path_parts );
                $image = "{$url_parts['scheme']}://{$url_parts['host']}/{$url_parts['path']}?{$url_parts['query']}";

                // check if remote file exists
                $file_exists = curl::check_file( $image );
                if ( !$file_exists ) {
                    $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Image not found.";
                    $skipped_products[] = $r;
                    continue 2;
                }
            }

            if ( $matching_product->id )
                $to_update++;

            $product = array_slice($r, 0, 14);

            $product['map price'] = (float) preg_replace( '/[^0-9.]/', '', $r['map price']);
            $product['map price'] = $product['price_map'] ? $product['map price'] : 0;
            $product['wholesale price'] = (float) preg_replace( '/[^0-9.]/', '', $r['wholesale price']);
            $product['wholesale price'] = $product['wholesale price'] ? $product['wholesale price'] : 0;
            $product['price'] = (float) preg_replace( '/[^0-9.]/', '', $r['price']);
            $product['price'] = $product['price'] ? $product['price'] : 0;
            $product['sale price'] = (float) preg_replace( '/[^0-9.]/', '', $r['sale price']);
            $product['sale price'] = $product['sale price'] ? $product['sale price'] : 0;
            $product['msrp'] = (float) preg_replace( '/[^0-9.]/', '', $r['msrp']);
            $product['msrp'] = $product['msrp'] ? $product['msrp'] : 0;
            $product['category_id'] = $category_id;
            $product['industry_id'] = $industry_id;
            $product['brand_id'] = $brand_id;
            $product['image'] = $r['image'];
            $product['amazon_eligible'] = isset($r['amazon eligible']) ? $r['amazon eligible'] : null;
            $product['product_specifications'] = array();
            $product['product_id'] = $product['productid'];

            if ( 'option' == $product['type'] && $last_product['product_id'] ) {
                $product['parent_product_id'] = $last_product['product_id'];
                $product['category_id'] = $last_product['category_id'];
                $product['industry_id'] = $last_product['industry_id'];
                $product['brand_id'] = $last_product['brand_id'];
            }

            // Set product specifications
            $product_specifications = array_slice($r, 14);
            foreach ( $product_specifications as $spec_name => $spec_value ) {
                $product['product_specifications'][] = array( ucwords($spec_name), $spec_value );
            }

            // Append product
            $products[] = $product;

            if ( 'product' == $product['type'] )
                $last_product = $product;
        }

        $product = new Product();
        $product_import = new ProductImport();
        $product_import->delete_all( $this->user->account->id );

        foreach ( $products as $pi ) {
            $product_import = new ProductImport();
            $product_import->product_id = $pi['product_id'];
            $product_import->parent_product_id = $pi['parent_product_id'];
            $product_import->category_id = $pi['category_id'];
            $product_import->brand_id = $pi['brand_id'];
            $product_import->industry_id = $pi['industry_id'];
            $product_import->website_id = $this->user->account->id;
            $product_import->name = ucwords( $pi['name'] );
            $product_import->slug = format::slug( $pi['name'] );
            $product_import->description = $pi['description'];
            $product_import->status = 'in-stock';
            $product_import->sku = $pi['sku'];
            $product_import->price_min = $pi['map price'];
            $product_import->price_wholesale = $pi['wholesale price'];
            $product_import->price = $pi['price'];
            $product_import->sale_price = $pi['sale price'];
            $product_import->alternate_price = $pi['msrp'];
            $product_import->product_specifications = json_encode( $pi['product_specifications'] );
            $product_import->image = $pi['image'];
            $product_import->type = $pi['type'];
            $product_import->amazon_eligible = $pi['amazon_eligible'];
            $product_import->create();
        }


        // Add the response
        $response->add_response( 'count', count($rows) );
        $response->add_response( 'count_skipped', count($skipped_products) );
        $response->add_response( 'count_to_import', count($products) );
        $response->add_response( 'count_to_update', $to_update );
        $response->add_response( 'skipped_rows', $skipped_products );

        return $response;
    }

    /**
     * Confirm Import
     *
     * @return RedirectResponse
     */
    protected function confirm_import() {
        set_time_limit( 30 * 60 );
        ini_set( 'memory_limit', '512M' );

        if ( !$this->verified() )
            return new RedirectResponse( '/products/import/' );

        $product_import = new ProductImport();
        $products = $product_import->get_all( $this->user->account->id );

        // Add product to website
        $account_product = new AccountProduct();
        $account_products_array = $account_product->get_by_account($this->user->account->id);
        $account_products = [];

        foreach ( $account_products_array as $account_product ) {
            $account_products[$account_product->product_id] = $account_product;
        }

        $last_parent_product = null;

        foreach ( $products as $p ) {
            $product = new Product();
            $product->get_by_id_by_website( $p->product_id, $this->user->account->id );
            $product->category_id = $p->category_id;
            $product->brand_id = $p->brand_id;
            $product->industry_id = $p->industry_id;
            $product->website_id = $this->user->account->id;
            $product->parent_product_id = $p->parent_product_id;

            if ( 'option' == $p->type ) {
                preg_match( '/(\[[^\]]+])(.+)/', $p->name, $product_option_matches );
                $product->name =  $product_option_matches[2];
                $product->parent_product_id = $last_parent_product->product_id;
            } else {
                $product->name = $p->name;
            }

            $product->slug = $p->slug;
            $product->status = $p->status;
            $product->description = $p->description;
            $product->sku = $p->sku;
            $product->price = $p->price_wholesale;
            $product->price_min = $p->price_min;
            $product->user_id_modified = $this->user->id;
            $product->weight = 0;

            // a new product?
            if ( $product->id == null ) {
                $product->user_id_created = $this->user->id;

                $product->create();

                $product->publish_visibility = 'public';
                $product->publish_date = date( 'Y-m-d H:i:s' );
            } else {
                // Override Images
                //$product->delete_images();
            }

            // Make sure product options are added
            if ( 'option' == $p->type ) {
                $product_option = new ProductOption();
                $product_options = $product_option->sort_by_name( $this->user->account->id, $product->parent_product_id );
                $product_option_groups = explode(',', substr($product_option_matches[1], 1, - 1));

                foreach ($product_option_groups as $product_option_string) {
                    list ($product_option_name, $product_option_item_name) = explode('=', $product_option_string);
                    $po_name = strtolower($product_option_name);
                    $poi_name = strtolower($product_option_item_name);

                    if ($product_options[$po_name]) {
                        $product_option = $product_options[$po_name];
                    } else {
                        $product_option = new ProductOption();
                        $product_option->website_id = $this->user->account->id;
                        $product_option->product_id = $product->parent_product_id;
                        $product_option->name = $product_option_name;
                        $product_option->type = ProductOption::TYPE_DROP_DOWN;
                        $product_option->create();
                    }

                    $product_option_items = $product_option->items_by_name();

                    // Add the item
                    if ( $product_option_items[$poi_name] ) {
                        $product_option_item = $product_option_items[$poi_name];
                    } else {
                        $product_option_item = new ProductOptionItem();
                        $product_option_item->product_option_id = $product_option->id;
                        $product_option_item->name = $product_option_item_name;
                        $product_option_item->create();
                    }

                    $product_option_item->add_relations([$product->id]);
                }
            }

            $industry = format::slug( $p->industry_name );
            $product->industry = $industry;

            $image_list = explode(',', $p->image);
            $product_images = [];
            foreach( $image_list as $k => $image ) {
                $image = trim($image);
                $slug = f::strip_extension( f::name( $image ) );

                try {
                    $image_name = $product->upload_image( $image, $slug, $industry );
                } catch (Exception $e) {
                    $product->publish_visibility = Product::PUBLISH_VISIBILITY_PRIVATE;
                    $product->save();
                    continue 2;
                }
                $product_images[] = $image_name;
            }
            $product->add_images( $product_images );
            $product->save();

            if ( 'product' == $p->type )
                $last_parent_product = $product;

            $product_specifications = json_decode( $p->product_specifications, true );
            $product->delete_specifications(); // should I ?
            if ( $product_specifications )
                $product->add_specifications($product_specifications);

            // Create the product on their account
            if ( isset( $account_products[$product->id] ) ) {
                $account_product = $account_products[$product->id];
            } else {
                $account_product = new AccountProduct();
                $account_product->website_id = $this->user->account->id;
                $account_product->product_id = $product->id;
                $account_product->create();
            }

            $account_product->price = $p->price;
            $account_product->sale_price = $p->sale_price;
            $account_product->alternate_price = $p->alternate_price;
            $account_product->active = AccountProduct::ACTIVE;
            $account_product->save();

            // Check for Amazon Eligible Flag
            if ( $p->amazon_eligible == 1 ) {
                $productAmazon = new ProductAmazon();
                $productAmazon->product_id = $p->product_id;
                $productAmazon->create();
            } elseif ( strtolower($p->amazon_eligible) == 'x') {
                $productAmazon = new ProductAmazon();
                $productAmazon->remove_by_product($p->product_id);
            }
        }

        $product_import->delete_all( $this->user->account->id );

        $this->notify( _( 'Your products has been imported successfully!' ) );
        return new RedirectResponse( '/products/import/' );
    }



}