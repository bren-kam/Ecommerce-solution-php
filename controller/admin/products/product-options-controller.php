<?php
class ProductOptionsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/product-options/';
        $this->section = 'products';
    }

    /**
     * List Users
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' )
            ->add_title( _('Product Options') )
            ->select( 'product_options', 'view' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * List Product Options
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`title`', '`name`', '`type`' );
        $dt->search( array( '`title`' => true, '`name`' => true, '`type`' => true ) );

        // Get product option
        $product_option = new ProductOption();

        // Get attributes
        $product_options = $product_option->list_all( $dt->get_variables() );
        $dt->set_row_count( $product_option->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this product option? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        if ( is_array( $product_options ) )
        foreach ( $product_options as $po ) {
            $data[] = array(
                $po->title . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'poid', $po->id, '/products/product-options/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'poid' => $po->id, '_nonce' => $delete_nonce ), '/products/product-options/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a></div>'
                , $po->name
                , $po->type
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

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['poid'] ) )
            return $response;

        // Get the product option
        $product_option = new ProductOption();
        $product_option->get( $_GET['poid'] );

        // Delete attribute
        if ( $product_option->id ) {
            $product_option->delete();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}