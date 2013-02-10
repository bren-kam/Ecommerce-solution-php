<?php
class CustomProductsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'products/custom-products/';
        $this->section = _('Custom Products');
    }

    /**
     * List Reaches page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        // Make sure they can be here
        if ( '2' == $this->user->account->get_settings('limited-products') ) // Change to 1
            return new RedirectResponse('/products/');

        $this->resources
            ->css( 'products/custom-products/index' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'jquery.datatables', 'products/custom-products/index' );

        return $this->get_template_response( 'index' )
            ->select( 'custom-products', 'view' );
    }

    /***** AJAX *****/

    /**
     * List Products
     *
     * @return DataTableResponse
     */
    protected function list_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $product = new Product();

        // Set Order by
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );
        $dt->add_where( " AND p.`website_id` = " . (int) $this->user->account->id );
        $dt->search( array( 'p.`name`' => true, 'b.`name`' => false, 'p.`sku`' => false ) );

        switch ( $_GET['sType'] ) {
        	case 'sku':
        		if ( _('Enter SKU...') != $_GET['s'] )
        			$dt->add_where( " AND p.`sku` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'product':
        		if ( _('Enter Product Name...') != $_GET['s'] )
        			$dt->add_where( " AND a.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'brand':
        		if ( _('Enter Brand...') != $_GET['s'] )
        			$dt->add_where( " AND d.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;
        }

        // Get
        $products = $product->list_custom_products( $dt->get_variables() );
        $dt->set_row_count( $product->count_custom_products( $dt->get_count_variables() ) );

        // Setup Base dete
        $delete_nonce = nonce::create( 'delete' );
        $confirm = _('Are you sure you want to delete this product? This cannot be undone.');
        $data = array();


        // Create output
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
            $date = new DateTime( $product->publish_date );
            $actions = '<a href="' . url::add_query_arg( 'pid', $product->id, '/products/custom-products/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>';
            $actions .= ' | <a href="' . url::add_query_arg( 'pid', $product->id, '/products/custom-products/clone/' ) . '" title="' ._('Clone') . '">' . _('Clone') . '</a>';
            $actions .= ' | <a href="' . url::add_query_arg( array( 'pid' => $product->id, '_nonce' => $delete_nonce ), '/products/custom-products/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';

            $data[] = array(
                $product->name . '<div class="actions">' . $actions . '</div>'
                , $product->brand
                , $product->sku
                , $product->category
                , ucwords( $product->status )
                , $date->format( 'F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}

