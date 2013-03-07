<?php
class CouponsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'products/coupons/';
        $this->section = 'Coupons';
    }

    /**
     * List Coupons
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'coupons', 'view' );
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
            }
        }

        $shipping_methods = $shipping_method->get_by_account( $this->user->account->id );
        $free_shipping_methods = $coupon->get_free_shipping_methods();

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'products/coupons/add-edit' );

        return $this->get_template_response( 'add-edit' )
            ->select( 'coupons', 'add' )
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

            $actions = '<a href="' . url::add_query_arg( 'wcid', $coupon->id, '/products/coupons/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>';
           	$actions .= ' | <a href="' . url::add_query_arg( array( '_nonce' => $delete_nonce, 'wcid' => $coupon->id ), '/products/coupons/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';

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
    public function delete() {
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
        jQuery('.dt:first')->dataTable()->fnDraw();


        // Add jquery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}

