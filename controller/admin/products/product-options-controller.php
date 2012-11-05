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
        $this->title .= _('Product Options');
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

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get the id if there is one
        $product_option_id = ( isset( $_GET['poid'] ) ) ? (int) $_GET['poid'] : false;

        $product_option = new ProductOption();

        if ( $product_option_id ) {
            $product_option->get( $product_option_id );
            $button = _('Save');

            $product_option_list_item = new ProductOptionListItem();
            $product_option_list_items = $product_option_list_item->get_all( $product_option_id );
        } else {
            $button = _('Add');
            $product_option_list_items = array();
        }

        // Setup for Drop Down List Form
        $v = new Validator( _('fAddEditDropDownList') );

        $v->add_validation( 'tDropDownListTitle', 'req', _('The "Title" field is required') );
        $v->add_validation( 'tDropDownListName', 'req', _('The "Name" field is required') );
        $validation = $v->js_validation();

        // Create Checkbox Form
        $form_checkbox = new FormTable( 'fAddEditCheckbox' );
        $form_checkbox->submit( $button );
        $form_checkbox->add_field( 'text', _('Title'), 'tCheckboxTitle', $product_option->title )
            ->add_validation( 'req', _('The "Title" field is required') )
            ->attribute( 'maxlength', 50 );

        $form_checkbox->add_field( 'text', _('Name'), 'tCheckboxName', $product_option->name )
            ->add_validation( 'req', _('The "Name" field is required') )
            ->attribute( 'maxlength', 200 );

        $form_checkbox->add_field( 'hidden', 'hType', 'checkbox' );

        // Create Text Form
        $form_text = new FormTable( 'fAddEditText' );
        $form_text->submit( $button );
        $form_text->add_field( 'select', _('Size'), _('tSize'), $product_option->type )
            ->options( array(
                'text' => _('One Line')
                , 'textarea' => _('Multiple Lines')
            ));

        $form_text->add_field( 'text', _('Title'), 'tTextTitle', $product_option->title )
            ->add_validation( 'req', _('The "Title" field is required') )
            ->attribute( 'maxlength', 50 );

        $form_text->add_field( 'text', _('Name'), 'tTextName', $product_option->name )
            ->add_validation( 'req', _('The "Title" field is required') )
            ->attribute( 'maxlength', 200 );

        $form_text->add_field( 'hidden', 'hType', 'text' );

        $errs = false;

        if ( isset( $_POST['hType'] ) ) {
            $validated = false;

            switch ( $_POST['hType'] ) {
                case 'drop-down-list':
                    if ( empty( $errs ) ) {
                        $validated = true;
                        $product_option->title = $_POST['tDropDownListTitle'];
                        $product_option->name = $_POST['tDropDownListName'];
                        $product_option->type = 'select';
                    }
                break;

                case 'checkbox':
                    if ( $form_checkbox->posted() ) {
                        $validated = true;
                        $product_option->title = $_POST['tCheckboxTitle'];
                        $product_option->name = $_POST['tCheckboxName'];
                        $product_option->type = 'checkbox';
                    }
                break;

                case 'text':
                    if ( $form_text->posted() ) {
                        $validated = true;
                        $product_option->title = $_POST['tTextTitle'];
                        $product_option->name = $_POST['tTextName'];
                        $product_option->type = ( 'text' == $_POST['tSize'] ) ? 'text' : 'textarea';
                    }
                break;
            }

            if ( $validated ) {
                // Update or create as necessary
                if ( $product_option->id ) {
                    $product_option->update();
                    $message = _('Your product option was successfully updated!');
                } else {
                    $product_option->create();
                    $message = _('Your product option was successfully created!');
                }

                $sequence = 0;
                $product_option_list_item_ids = array();

                // Now add list items, as necessary
                if ( 'select' == $product_option->type && isset( $_POST['list-items'] ) )
                foreach ( $_POST['list-items'] as $product_option_list_item_id => $poli ) {
                    if ( stristr( $product_option_list_item_id, 'poli' ) ) {
                        // Updating product option list item ids
                        $product_option_list_item_ids[] = $product_option_list_item_id = str_replace( 'poli', '', $product_option_list_item_id );

                        // Get Product Option List Item
                        $product_option_list_item = new ProductOptionListItem();
                        $product_option_list_item->get( $product_option_list_item_id );
                        $product_option_list_item->value = $poli;
                        $product_option_list_item->sequence = $sequence;

                        // Update it
                        $product_option_list_item->update();
                    } else {
                        // Set properties
                        $product_option_list_item = new ProductOptionListItem();
                        $product_option_list_item->value = $poli;
                        $product_option_list_item->sequence = $sequence;
                        $product_option_list_item->product_option_id = $product_option->id;

                        // Create the product option list item
                        $product_option_list_item->create();

                        $product_option_list_item_ids[] = $product_option_list_item->id;
                    }

                    $sequence++;
                }

                // Now we need to remove any attribute items that were not mentioned and add relations
                if ( $product_option_id && 0 != count( $product_option_list_item_ids ) )
                foreach ( $product_option_list_items as $poli ) {
                    if ( !in_array( $poli->id, $product_option_list_item_ids ) ) {
                        $product_option_list_item = new ProductOptionListItem();
                        $product_option_list_item->get( $poli->id );
                        $product_option_list_item->delete();
                    }
                }

                $this->notify( $message );

                return new RedirectResponse('/products/product-options/');
            }
        }

        $forms = array(
            'checkbox' => $form_checkbox->generate_form()
            , 'text' => $form_text->generate_form()
        );

        $template_response = $this->get_template_response( 'add-edit' )
            ->select( 'product_options', 'add' )
            ->add_title( ( $product_option_id ) ? _('Edit') : _('Add') )
            ->set( compact( 'product_option', 'forms', 'product_option_list_items', 'validation', 'errs', 'button' ) );

        $this->resources
            ->javascript( 'products/product-options/add-edit' )
            ->css( 'products/product-options/add-edit' );

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