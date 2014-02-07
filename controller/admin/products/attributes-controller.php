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
        $this->title = _('Attributes');
    }

    /**
     * List Attributes
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->kb( 14 )
            ->add_title( _('Attributes') )
            ->select( 'attributes', 'view' );
    }

    /**
     * Add/Edit an Attribute
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get the attribute_id if there is one
        $attribute_id = ( isset( $_GET['aid'] ) ) ? (int) $_GET['aid'] : false;

        // Get Attribute
        $attribute = new Attribute();

        // Get the attribute
        if ( $attribute_id ) {
            $attribute->get( $attribute_id );

            $attribute_item = new AttributeItem();
            $attribute_items = $attribute_item->get_by_attribute( $attribute_id );
        } else {
            $attribute_items = array();
        }

        $v = new Validator( _('fAddEditAttribute') );

        $v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
        $v->add_validation( 'tName', 'req', _('The "Name" field is required') );
        $validation = $v->js_validation();

        $errs = false;

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $attribute->title = $_POST['tTitle'];
                $attribute->name = $_POST['tName'];

                if ( $attribute_id ) {
                    $attribute->save();
                    $message = _('Your attribute has been successfully updated!' );
                } else {
                    $attribute->create();
                    $message = _('Your attribute has been successfully created!' );
                }

                $sequence = 0;
                $attribute_item_ids = array();

                if ( isset( $_POST['list-items'] ) )
                foreach ( $_POST['list-items'] as $attribute_item_id => $ai ) {
                    if ( stristr( $attribute_item_id, 'ai' ) ) {
                        // Updating attribute
                        $attribute_item_ids[] = $attribute_item_id = str_replace( 'ai', '', $attribute_item_id );

                        // Get Attribute
                        $attribute_item = new AttributeItem();
                        $attribute_item->get( $attribute_item_id );
                        $attribute_item->name = $ai;
                        $attribute_item->sequence = $sequence;

                        // Update it
                        $attribute_item->save();
                    } else {
                        // New attribute item

                        // Set properties
                        $attribute_item = new AttributeItem();
                        $attribute_item->name = $ai;
                        $attribute_item->sequence = $sequence;
                        $attribute_item->attribute_id = $attribute_id;

                        // Create the attribute
                        $attribute_item->create();

                        $attribute_item_ids[] = $attribute_item->id;
                    }

                    $sequence++;
                }

                // Now we need to remove any attribute items that were not mentioned and add relations
                if ( $attribute_id && 0 != count( $attribute_item_ids ) )
                foreach ( $attribute_items as $ai ) {
                    if ( !in_array( $ai->id, $attribute_item_ids ) ) {
                        $attribute_item = new AttributeItem();
                        $attribute_item->get( $ai->id );
                        $attribute_item->remove();
                    }
                }

                // Let them know what happened
                $this->notify( $message );

                // Redirect
                return new RedirectResponse( '/products/attributes/' );
            }
        }

        $this->resources
            ->javascript( 'products/attributes/add-edit' )
            ->css('products/attributes/add-edit');

        return $this->get_template_response( 'add-edit' )
            ->kb( 15 )
            ->select( 'attributes', 'add' )
            ->add_title( ( $attribute_id ) ? _('Edit') : _('Add') )
            ->set( compact( 'attribute', 'attribute_items', 'validation', 'errs' ) );
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
            $attribute->remove();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}