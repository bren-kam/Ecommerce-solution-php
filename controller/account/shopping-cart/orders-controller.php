<?php
class OrdersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'shopping-cart/orders/';
        $this->section = 'shopping-cart';
        $this->title = _('Orders | Shopping Cart');
    }

    /**
     * List Orders
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 120 )
            ->menu_item( 'shopping-cart/orders' );
    }

    /**
     * View an order
     *
     * @return TemplateResponse
     */
    protected function view() {
        if ( !isset( $_GET['woid'] ) )
            return new RedirectResponse('/shopping-cart/orders/');

        $order = new WebsiteOrder();
        $order->get_complete( $_GET['woid'], $this->user->account->id );

        $shipping = new WebsiteShippingMethod();
        $order->shipping_method = $shipping->get_description( $order->shipping_method );

        if ( !$order->id )
            return new RedirectResponse('/shopping-cart/orders/');

        $this->resources
            ->css('shopping-cart/orders/view')
            ->javascript('shopping-cart/orders/view');

        return $this->get_template_response( 'view' )
            ->kb( 121 )
            ->add_title( _('View Order') )
            ->menu_item( 'shopping-cart/orders' )
            ->set( compact( 'order' ) );
    }

    /***** AJAX *****/

    /**
     * List Shopping Cart Orders
     *
     * @return DataTableResponse
     */
    protected function list_orders() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $website_order = new WebsiteOrder();

        // Set Order by
        $dt->order_by( '`website_order_id`', '`total_cost`', '`status`', '`date_created`' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`website_order_id`' => false ) );

        // Get items
        $website_orders = $website_order->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_order->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var WebsiteOrder $order
         */
        if ( is_array( $website_orders ) )
        foreach ( $website_orders as $order ) {
            switch ( $order->status ) {
                case WebsiteOrder::STATUS_DECLINED:
                    $status = 'Declined';
                break;

                case WebsiteOrder::STATUS_PURCHASED:
                    $status = 'Purchased';
                break;

                case WebsiteOrder::STATUS_PENDING:
                    $status = 'Pending';
                break;

                case WebsiteOrder::STATUS_DELIVERED:
                    $status = 'Delivered';
                break;

                case WebsiteOrder::STATUS_RECEIVED:
                    $status = 'Received';
                    break;

                case WebsiteOrder::STATUS_SHIPPED:
                    $status = 'Shipped';
                    break;

                default:
                    $status = 'Error';
                break;
            }

            $date = new DateTime( $order->date_created );

            $link_text = '';
            if ( $order->is_ashley_express() ) {
                $link_text = " - Express Delivery";
            }

            $data[] = array(
                '<a href="' . url::add_query_arg( 'woid', $order->id, '/shopping-cart/orders/view/' ) . '" title="' . _('View') . '">' . $order->id . '</a>' . $link_text
                , $order->name
                , '$' . number_format( $order->total_cost, 2 )
                , $status
                , $date->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Update status
     */
    protected function update_status() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['woid'], $_POST['s'] ), _('You cannot update this order') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $website_order = new WebsiteOrder();
        $website_order->get( $_POST['woid'], $this->user->account->id );
        $website_order->status = $_POST['s'];
        $website_order->save();

        $this->log( 'update-order-status', $this->user->contact_name . ' updated the order status on ' . $this->user->account->title, $_POST );

        return $response;
    }

    /**
     * Download
     * @return CsvResponse
     */
    protected function download() {

        $website_order = new WebsiteOrder();
        $website_orders = $website_order->get_by_account( $this->user->account->id );

        $csv = [];
        $csv[] = [
            'website_order_id'
            , 'shipping_price'
            , 'tax_price'
            , 'coupon_discount'
            , 'total_cost'
            , 'email'
            , 'phone'
            , 'billing_first_name'
            , 'billing_last_name'
            , 'billing_address1'
            , 'billing_address2'
            , 'billing_city'
            , 'billing_state'
            , 'billing_zip'
            , 'billing_phone'
            , 'billing_alt_phone'
            , 'shipping_name'
            , 'shipping_first_name'
            , 'shipping_last_name'
            , 'shipping_address1'
            , 'shipping_address2'
            , 'shipping_city'
            , 'shipping_state'
            , 'shipping_zip'
            , 'status'
            , 'date_created'
            , 'shipping_track_number'
            , 'authorize_only'
        ];
        foreach( $website_orders as $order ) {
            $csv[] = [
                $order->website_order_id
                , $order->shipping_price
                , $order->tax_price
                , $order->coupon_discount
                , $order->total_cost
                , $order->email
                , $order->phone
                , $order->billing_first_name
                , $order->billing_last_name
                , $order->billing_address1
                , $order->billing_address2
                , $order->billing_city
                , $order->billing_state
                , $order->billing_zip
                , $order->billing_phone
                , $order->billing_alt_phone
                , $order->shipping_name
                , $order->shipping_first_name
                , $order->shipping_last_name
                , $order->shipping_address1
                , $order->shipping_address2
                , $order->shipping_city
                , $order->shipping_state
                , $order->shipping_zip
                , $order->status
                , $order->date_created
                , $order->shipping_track_number
                , $order->authorize_only
            ];
        }

        return new CsvResponse( $csv, 'orders-' . date('YmdHis') . '.csv' );
    }
}


