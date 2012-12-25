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

        $response = $this->get_template_response( 'index', _('Products') )
            ->select( 'sub-products', 'view' )
            ->set( compact( 'categories', 'product_count', 'coupons' ) );

        return $response;
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
        $website_coupons = new WebsiteCoupon();

        // Get variables
        $account_product->get( $_POST['pid'], $this->user->account->id );
        $account_product->coupons = $website_coupons->get_by_product( $this->user->account->id, $_POST['pid'] );
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
        $account_product->get( $account_product['hProductID'], $this->user->account->id );

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
        $website_coupon->delete_by_product( $this->user->account->id, $account_product->product_id );

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
        	$product_option_values = $product_option_list_item_values = $product_option_ids = $product_option_list_item_ids = '';

			foreach ( $product_options as $po_id => $po ) {
				$dropdown = is_array( $po );

				if ( $dropdown ) {
					$price = 0;
					$required = $po['required'];
				} else {
					$price = $po;
					$required = 0;
				}

				if ( !empty( $product_option_values ) )
					$product_option_values .= ', ';

				if ( !empty( $product_option_ids ) )
					$product_option_ids .= ', ';

				// Add the values
				$product_option_values .= sprintf( "( $website_id, $product_id, %d, %f, %d )", $po_id, $price, $required );

				// For error handling
				$product_option_ids .= $po_id;

				// If it's a drop down, set the values
				if ( $dropdown )
				foreach ( $po['list_items'] as $li_id => $price ) {
					if ( !empty( $product_option_list_item_values ) )
						$product_option_list_item_values .= ',';

					if ( !empty( $product_option_list_item_ids ) )
						$product_option_list_item_ids .= ',';

					$product_option_list_item_values .= sprintf( "( $website_id, $product_id, %d, %d, %f )", $po_id, $li_id, $price );
				}
			}

			// Insert new product options
			$this->db->query( "INSERT INTO `website_product_options` ( `website_id`, `product_id`, `product_option_id`, `price`, `required` ) VALUES $product_option_values" );

			if ( $product_option_list_item_values != '' ) {
				// Insert new product option list items
				$this->db->query( "INSERT INTO `website_product_option_list_items` ( `website_id`, `product_id`, `product_option_id`, `product_option_list_item_id`, `price` ) VALUES $product_option_list_item_values" );
			}
		}

        jQuery('.close:visible:first')->click();
        jQuery( '#sPrice' . $account_product->product_id )->text( $account_product->price );
        jQuery( '#sAlternatePrice' . $account_product->product_id )->text( $account_product->alternate_price );
        jQuery( '#sAlternatePriceName' . $account_product->product_id )->text( $account_product->alternate_price_name );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


