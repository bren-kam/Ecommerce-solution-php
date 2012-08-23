<?php
class AttributesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/attributes/';
        $this->section = 'products';
    }

    /**
     * List Attributes
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' )
            ->add_title( _('Attributes') )
            ->select( 'attributes', 'view' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * List Attributes
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`title`' );
        $dt->search( array( '`title`' => true ) );

        // Get attribute
        $attribute = new Attribute();

        // Get attributes
        $attributes = $attribute->list_all( $dt->get_variables() );
        $dt->set_row_count( $attribute->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this attribute? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        if ( is_array( $attributes ) )
        foreach ( $attributes as $a ) {
            $data[] = array(
                $a->title . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'aid', $a->id, '/products/attributes/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'aid' => $a->id, '_nonce' => $delete_nonce ), '/products/attributes/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a></div>'
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
        if ( $response->has_error() || !isset( $_GET['aid'] ) )
            return $response;

        // Get the attribute
        $attribute = new Attribute();
        $attribute->get( $_GET['aid'] );

        // Delete attribute
        if ( $attribute->id ) {
            $attribute->delete();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}