<?php
class CouponsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'shopping-cart/coupons/';
        $this->section = 'Coupons';
    }

    /**
     * List Coupons
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 129 )
            ->select( 'shopping-cart', 'shopping-cart/coupons' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse
     */
    protected function add_edit() {
        // Get Coupon
        $coupon = new WebsiteCoupon();
        $shipping_method = new WebsiteShippingMethod();
        $v = new Validator( 'fAddEditCoupon' );

        // Setup validation
        $v->add_validation( 'tName', 'req', _('The "Name" field is required') );
        $v->add_validation( 'tCode', 'req', _('The "Code" field is required') );
        $v->add_validation( 'tItemLimit', 'int', _('The "Item Limit" field may only contain a number') );

        $js_validation = $v->js_validation();

        if ( isset( $_GET['wcid'] ) )
            $coupon->get( $_GET['wcid'], $this->user->account->id );

        $errs = '';

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $coupon->website_id = $this->user->account->id;
                $coupon->name = $_POST['tName'];
                $coupon->code = $_POST['tCode'];
                $coupon->type = $_POST['rType'];
                $coupon->amount = $_POST['tAmount'];
                $coupon->minimum_purchase_amount = $_POST['tMinimumPurchaseAmount'];
                $coupon->store_wide = $_POST['cbStoreWide'];
                $coupon->buy_one_get_one_free = $_POST['cbBuyOneGetOneFree'];
                $coupon->item_limit = $_POST['tItemLimit'];
                $coupon->date_start = $_POST['tStartDate'];
                $coupon->date_end = $_POST['tEndDate'];

                if ( $coupon->id ) {
                    $coupon->save();
                } else {
                    $coupon->create();
                }

                $coupon->delete_free_shipping_methods();

                if ( isset( $_POST['cbFreeShippingMethods'] ) )
                    $coupon->add_free_shipping_methods( $_POST['cbFreeShippingMethods'] );

                $this->notify( _('Your coupon has been created/updated successfully!') );
                return new RedirectResponse('/shopping-cart/coupons/');
            }
        }

        $shipping_methods = $shipping_method->get_by_account( $this->user->account->id );
        $free_shipping_methods = $coupon->get_free_shipping_methods();

        $this->resources
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) )
            ->javascript_url( Config::resource( 'bootstrap-datepicker-js' ) )
            ->javascript( 'shopping-cart/coupons/add-edit' );

        return $this->get_template_response( 'add-edit' )
            ->kb( 130 )
            ->select( 'shopping-cart', 'shopping-cart/coupons' )
            ->set( compact( 'coupon', 'shipping_methods','free_shipping_methods', 'js_validation', 'errs' ) );
    }

    /***** AJAX *****/

    /**
     * List Coupons
     *
     * @return DataTableResponse
     */
    protected function list_coupons() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set variables
        $dt->order_by( '`name`', '`amount`', '`type`', '`item_limit`', '`date_created`' );
        $dt->add_where( " AND `website_id` = " . $this->user->account->id );
        $dt->search( array( '`name`' => true ) );

        // Get Coupons
        $website_coupon = new WebsiteCoupon();
        $coupons = $website_coupon->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_coupon->count_all( $dt->get_count_variables() ) );

        // Setup variables
        $confirm = _('Are you sure you want to delete this coupon? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $data = array();

        // Create output
        if ( is_array( $coupons ) )
        foreach ( $coupons as $coupon ) {
            $date = new DateTime( $coupon->date_created );

            $actions = '<a href="' . url::add_query_arg( 'wcid', $coupon->id, '/shopping-cart/coupons/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>';
           	$actions .= ' | <a href="' . url::add_query_arg( array( '_nonce' => $delete_nonce, 'wcid' => $coupon->id ), '/shopping-cart/coupons/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';

            $data[] = array(
                $coupon->name . '<div class="actions">' . $actions . '</div>'
                , number_format( $coupon->amount, 2 )
                , $coupon->type
                , $coupon->item_limit
                , $date->format( 'F jS, Y' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['wcid'] ), _('Failed to delete coupon') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get ticket comment
        $website_coupon = new WebsiteCoupon();
        $website_coupon->get( $_GET['wcid'], $this->user->account->id );

        // Then delete
        $website_coupon->remove();

        // Redraw the table
        $response->add_response( 'reload_datatable', 'reload_datatable' );

        return $response;
    }

    /**
     * Apply To Brand
     *
     * @return TemplateResponse
     */
    protected function apply_to_brand() {
        // Instantiate classes
        $form = new BootstrapForm( 'fApplyToBrand' );

        $brand = new Brand();
        $brands = $brand->get_by_account( $this->user->account->id );
        $brands_options = array();
        foreach ( $brands as $b ) {
            $brands_options[$b->id] = $b->name;
        }

        $form->add_field( 'select', _('Brand'), 'brand' )
            ->options( $brands_options );

        $coupon = new WebsiteCoupon();
        $coupons = $coupon->get_by_account( $this->user->account->id );
        $coupons_options = array();
        foreach ( $coupons as $c ) {
            $coupons_options[$c->id] = "{$c->code} - {$c->name}";
        }

        $form->add_field( 'select', _('Coupon'), 'coupon' )
            ->options( $coupons_options );

        $form->submit( 'Add to Brand Products' );

        if ( $form->posted() ) {
            $coupon = new WebsiteCoupon();
            $coupon->add_relations_by_brand( $_POST['coupon'], $this->user->account->id, $_POST['brand'] );
            $this->notify( 'Your coupon has been added to the brand successfully' );
        }

        return $this->get_template_response( 'apply-to-brand' )
            ->kb( 143 )
            ->add_title( _('Apply to Brand | Coupons') )
            ->select( 'shopping-cart', 'shopping-cart/coupons' )
            ->set( array(
                'form' => $form->generate_form()
            ) );
    }

    /**
     * List Products in Coupon
     *
     * @return TemplateResponse
     */
    protected function products() {
        $website_coupon = new WebsiteCoupon();
        $coupons = $website_coupon->get_by_account( $this->user->account->id );

        $this->resources
            ->javascript( 'shopping-cart/coupons/products' )
            ->css( 'shopping-cart/coupons/products' );
        return $this->get_template_response( 'products' )
            ->kb( 0 )
            ->select( 'shopping-cart', 'shopping-cart/coupons' )
            ->set( compact( 'coupons' ) );
    }

    /**
     * List Products in Coupon
     *
     * @return DataTableResponse
     */
    protected function list_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Get Coupon
        $website_coupon = new WebsiteCoupon();
        $website_coupon->get( $_SESSION['coupons']['wcid'], $this->user->account->id );

        if ( !$website_coupon->id ) {
            $dt->set_data( array() );
            return $dt;
        }

        // Set variables
        $dt->order_by( 'wc.`name`' );
        $dt->add_where( " AND wc.`website_id` = " . $this->user->account->id );
        $dt->add_where( " AND wc.`website_coupon_id` = " . $website_coupon->id );
        $dt->search( array( 'p.`name`' => true, 'p.`sku`' => true ) );

        // Get Products in Coupon
        $products = $website_coupon->list_products_in_coupon( $dt->get_variables() );
        $dt->set_row_count( $website_coupon->count_products_in_coupon( $dt->get_count_variables() ) );

        // Setup variables
        $confirm = _('Are you sure you want to delete this product from the coupon?');
        $delete_nonce = nonce::create( 'delete_product' );
        $data = array();

        // Create output
        if ( is_array( $products ) )
            foreach ( $products as $product ) {
                $actions = ' <a href="' . url::add_query_arg( array( '_nonce' => $delete_nonce, 'pid' => $product->id, 'wcid' => $website_coupon->id ), '/shopping-cart/coupons/delete-product/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Remove from coupon') . '</a>';

                $data[] = array(
                    $product->name . '<div class="actions">' . $actions . '</div>'
                    , $product->sku
                    , $product->brand
                    , $product->category
                );
            }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete Product
     *
     * Deletes a single product association (coupon,product)
     *
     * @return AjaxResponse
     */
    protected function delete_product() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $website_coupon = new WebsiteCoupon();
        $website_coupon->get( $_GET['wcid'], $this->user->account->id );

        if ( $website_coupon->id ) {
            $website_coupon->delete_relation( $_GET['wcid'], $_GET['pid'] );

            jQuery('.dt:first')->dataTable()->fnDraw();
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

}

