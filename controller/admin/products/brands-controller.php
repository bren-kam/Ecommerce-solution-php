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
        $this->title .= _('Brands');
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

    /**
     * Add/Edit a Brand
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get the brand_id if there is one
        $brand_id = ( isset( $_GET['bid'] ) ) ? (int) $_GET['bid'] : false;

        $brand = new Brand();
        $product_option = new ProductOption();

        $product_options_array = $product_option->get_all();

        // Get the attribute
        if ( $brand_id ) {
            $brand->get( $brand_id );
            $product_option_ids = $brand->get_product_option_relations();
        } else {
            $product_option_ids = array();
        }

        $v = new Validator( _('fAddEditBrand') );

        $v->add_validation( 'tName', 'req', _('The "Name" field is required') );
        $v->add_validation( 'tSlug', 'req', _('The "Slug" field is required') );

        $validation = $v->js_validation();

        $errs = false;

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $brand->name = $_POST['tName'];
                $brand->slug = $_POST['tSlug'];
                $brand->link = $_POST['tWebsite'];

                // Handle file upload
                if ( !empty( $_FILES['fImage']['name'] ) ) {
                    $f = new File();

                    // Delete old image
                    if ( $brand_id && !empty( $brand->image ) ) {
                        $old_url_info = parse_url( $brand->image );
                        $old_image_path = substr( $old_url_info['path'], 1 );

                        if ( !empty( $old_image_path ) )
                            $f->delete_image( $old_image_path, 'brands' );
                    }

                    // Upload new image
                    $image = $f->upload_image( $_FILES['fImage'], format::slug( $_POST['tName'] ), 120, 120, 'brands', '', true, true );

                    // If it was successful, assign it the correct name
                    if ( $image )
                        $brand->image = 'http://brands.retailcatalog.us/' . $image;
                } elseif ( !$brand_id ) {
                    $brand->image = '';
                }

                if ( $brand_id ) {
                    $brand->update();
                    $message = _('Your brand has been successfully updated!' );

                    // Delete any relations there may have been
                    $brand->delete_product_option_relations();
                } else {
                    $brand->create();
                    $message = _('Your brand has been successfully created!' );
                }

                // Add product option relations
                if ( isset( $_POST['product-options'] ) && is_array( $_POST['product-options'] ) )
                    $brand->add_product_option_relations( $_POST['product-options'] );

                // Let them know what happened
                $this->notify( $message );

                // Redirect
                return new RedirectResponse( '/products/brands/' );
            }
        }

        $template_response = $this->get_template_response( 'add-edit' )
            ->select( 'brands', 'add' )
            ->add_title( ( $brand_id ) ? _('Edit') : _('Add') )
            ->set( compact( 'brand', 'product_options_array', 'product_option_ids', 'validation', 'errs' ) );

        $this->resources
            ->javascript( 'products/brands/add-edit' )
            ->css( 'products/brands/add-edit' );

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
            // First delete product option relations
            $brand->delete_product_option_relations();

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

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}