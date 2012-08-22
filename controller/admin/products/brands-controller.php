<?php
class BrandsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/brands/';
        $this->section = 'products';
    }

    /**
     * List Users
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' )
            ->add_title( _('Brands') )
            ->select( 'brands', 'view' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`name`', '`link`' );
        $dt->search( array( '`name`' => true, '`link`' => true ) );

        // Instantiate brand
        $brand = new Brand();

        // Get brands
        $brands = $brand->list_all( $dt->get_variables() );
        $dt->set_row_count( $brand->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this brand? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        if ( is_array( $brands ) )
        foreach ( $brands as $b ) {
            $data[] = array(
                $b->name . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'bid', $b->id, '/products/brands/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'bid' => $b->id, '_nonce' => $delete_nonce ), '/products/brands/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a></div>'
                , $b->link
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
        if ( $response->has_error() || !isset( $_GET['bid'] ) )
            return $response;

        // Get the brand
        $brand = new Brand();
        $brand->get( $_GET['bid'] );

        // Delete brand
        if ( $brand->id ) {
            // First delete product options
            $product_option = new ProductOption();
            $product_option->delete_by_brand( $brand->id );

            // Then delete image from Amazon
            if ( !empty( $brand->image ) ) {
                $f = new File();

                $url_info = parse_url( $brand->image );
                $image_path = substr( $url_info['path'], 1 );

                if ( !empty( $image_path ) )
                    $f->delete_image( $image_path, 'brands' );
            }

            // Now delete the brand
            $brand->delete();

            // Delete product options by brand
            if ( !$po->delete_relations_by_brand( $brand_id ) )
                return false;

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}