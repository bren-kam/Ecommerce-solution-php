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
        $response = $this->get_template_response( 'index' )
            ->select( 'orders' );

        return $response;
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

        if ( !$order->id )
            return new RedirectResponse('/shopping-cart/orders/');

        $this->resources
            ->css('shopping-cart/orders/view')
            ->javascript('shopping-cart/orders/view');

        $response = $this->get_template_response( 'view' )
            ->add_title( _('View Order') )
            ->select( 'orders' )
            ->set( compact( 'order' ) );

        return $response;
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
                case -1:
                    $status = _('Declined');
                break;

                case 0:
                    $status = _('Purchased');
                break;

                case 1:
                    $status = _('Pending');
                break;

                case 2:
                    $status = _('Delivered');
                break;

                default:
                    $status = _('Error');
                break;
            }

            $date = new DateTime( $order->date_created );

            $data[] = array(
                '<a href="' . url::add_query_arg( 'woid', $order->id, '/shopping-cart/orders/view/' ) . '" title="' . _('View') . '">' . $order->id . '</a>'
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

        return $response;
    }
}


