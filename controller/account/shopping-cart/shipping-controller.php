<?php
class ShippingController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'shopping-cart/shipping/';
        $this->section = 'shopping-cart';
        $this->title = _('Shipping | Shopping Cart');
    }

    /**
     * List Orders
     *
     * @return TemplateResponse
     */
    protected function index() {
        $response = $this->get_template_response( 'index' )
            ->select( 'shipping' );

        return $response;
    }

    /**
     * Add / Edit a Custom Shipping Method
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit_custom() {
        // Determine if we're adding or editing the user
        $website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? (int) $_GET['wsmid'] : false;

        $shipping_method = new WebsiteShippingMethod();

        if ( $website_shipping_method_id )
            $shipping_method->get( $website_shipping_method_id, $this->user->account->id );

        /***** CREATE FORM *****/

        $form = new FormTable( 'fAddEditCustom' );

        $form->add_field( 'text', _('Name'), 'tName', $shipping_method->name )
            ->attribute( 'maxlength', 50 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $methods = array(
            '' => '-- ' . _('Select Method') . ' --'
            , 'Flat Rate' => _('Flat Rate')
            , 'Percentage' => _('Percentage')
        );

        $form->add_field( 'select', _('Method'), 'sMethod', $shipping_method->method )
            ->add_validation( 'req', _('The "Method" field is required') )
            ->options( $methods );

        $form->add_field( 'text', _('Amount'), 'tAmount', $shipping_method->amount )
            ->add_validation( 'req', _('The "Amount" field is required') );

        $zip_codes = ( empty( $shipping_method->zip_codes ) ) ? '' : implode( "\n", unserialize( $shipping_method->zip_codes ) );

        $form->add_field( 'textarea', _('Zip Codes'), 'taZipCodes', $zip_codes );

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['tName'];
            $shipping_method->method = $_POST['sMethod'];
            $shipping_method->amount = $_POST['tAmount'];
            $shipping_method->zip_codes = serialize( format::trim_deep( explode( "\n", $_POST['taZipCodes'] ) ) );

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'custom';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        $response = $this->get_template_response( 'add-edit' )
            ->select( 'shipping', 'add-edit-custom' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'Custom'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit Custom') : _('Add Custom') ) );

        return $response;
    }

    /**
     * Add / Edit a UPS Shipping Method
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit_ups() {
        // Determine if we're adding or editing the user
        $website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? (int) $_GET['wsmid'] : false;

        $shipping_method = new WebsiteShippingMethod();

        if ( $website_shipping_method_id ) {
            $shipping_method->get( $website_shipping_method_id, $this->user->account->id );

            if ( !empty( $shipping_method->extra ) )
                $shipping_method->extra = unserialize( $shipping_method->extra );
        }

        /***** CREATE FORM *****/

        $form = new FormTable( 'fAddEditUPS' );

        $services = array(
            '02' => _('UPS Second Day Air')
            , '03' => _('UPS Ground')
            , '07' => _('UPS Worldwide Express')
            , '08' => _('UPS Worldwide Expedited')
            , '11' => _('UPS Standard')
            , '12' => _('UPS Three-Day Select')
            , '13' => _('Next Day Air Saver')
            , '14' => _('UPS Next Day Air Early AM')
            , '54' => _('UPS Worldwide Express Plus')
            , '59' => _('UPS Second Day Air AM')
            , '65' => _('UPS Saver')
        );

        $form->add_field( 'select', _('Service'), 'sService', $shipping_method->name )
            ->options( $services );

        $pickup_types = array(
            '01' => _('Daily Pickup')
            , '03' => _('Customer Counter')
            , '06' => _('One Time Pickup')
            , '07' => _('On Call Air')
        );

        $form->add_field( 'select', _('Pickup Type'), 'sPickupType', $shipping_method->extra['pickup_type'] )
            ->options( $pickup_types );

        $packaging_types = array(
            '01' => 'UPS Letter'
            , '02' => 'Your Packaging'
            , '03' => 'Tube'
            , '04' => 'PAK'
            , '21' => 'Express Box'
            , '24' => '25KG Box'
            , '25' => '10KG Box'
            , '30' => 'Pallet'
            , '2a' => 'Small Express Box'
            , '2b' => 'Medium Express Box'
            , '2c' => 'Large Express Box'
        );

        $form->add_field( 'select', _('Packaging Type'), 'sPackagingType', $shipping_method->extra['packaging_type'] )
            ->options( $packaging_types );

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'pickup_type' => $_POST['sPickupType']
                , 'packaging_type' => $_POST['sPackagingType']
            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'ups';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        $response = $this->get_template_response( 'add-edit' )
            ->select( 'shipping', 'add-edit-ups' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'UPS'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit UPS') : _('Add UPS') ) );

        return $response;
    }

    /**
     * Add / Edit a Fedex Shipping Method
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit_fedex() {
        // Determine if we're adding or editing the user
        $website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? (int) $_GET['wsmid'] : false;

        $shipping_method = new WebsiteShippingMethod();

        if ( $website_shipping_method_id ) {
            $shipping_method->get( $website_shipping_method_id, $this->user->account->id );

            if ( !empty( $shipping_method->extra ) )
                $shipping_method->extra = unserialize( $shipping_method->extra );
        }

        /***** CREATE FORM *****/

        $form = new FormTable( 'fAddEditFedex' );

        $services = array(
            '02' => _('UPS Second Day Air')
            , '03' => _('UPS Ground')
            , '07' => _('UPS Worldwide Express')
            , '08' => _('UPS Worldwide Expedited')
            , '11' => _('UPS Standard')
            , '12' => _('UPS Three-Day Select')
            , '13' => _('Next Day Air Saver')
            , '14' => _('UPS Next Day Air Early AM')
            , '54' => _('UPS Worldwide Express Plus')
            , '59' => _('UPS Second Day Air AM')
            , '65' => _('UPS Saver')
        );

        $form->add_field( 'select', _('Service'), 'sService', $shipping_method->name )
            ->options( $services );

        $pickup_types = array(
            '01' => _('Daily Pickup')
            , '03' => _('Customer Counter')
            , '06' => _('One Time Pickup')
            , '07' => _('On Call Air')
        );

        $form->add_field( 'select', _('Pickup Type'), 'sPickupType', $shipping_method->extra['pickup_type'] )
            ->options( $pickup_types );

        $packaging_types = array(
            '01' => 'UPS Letter'
            , '02' => 'Your Packaging'
            , '03' => 'Tube'
            , '04' => 'PAK'
            , '21' => 'Express Box'
            , '24' => '25KG Box'
            , '25' => '10KG Box'
            , '30' => 'Pallet'
            , '2a' => 'Small Express Box'
            , '2b' => 'Medium Express Box'
            , '2c' => 'Large Express Box'
        );

        $form->add_field( 'select', _('Packaging Type'), 'sPackagingType', $shipping_method->extra['packaging_type'] )
            ->options( $packaging_types );

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'pickup_type' => $_POST['sPickupType']
                , 'packaging_type' => $_POST['sPackagingType']
            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'fedex';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        $response = $this->get_template_response( 'add-edit' )
            ->select( 'shipping', 'add-edit-ups' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'FedEx'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit FedEx') : _('Add FedEx') ) );

        return $response;
    }

    /***** AJAX *****/

    /**
     * List Shipping Methods
     *
     * @return DataTableResponse
     */
    protected function list_shipping_methods() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $shipping_method = new WebsiteShippingMethod();

        // Set Order by
        $dt->order_by( '`name`', '`method`', '`amount`' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`name`' => false ) );

        // Get items
        $shipping_methods = $shipping_method->list_all( $dt->get_variables() );
        $dt->set_row_count( $shipping_method->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this shipping method? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );

        /**
         * @var WebsiteShippingMethod $method
         */
        if ( is_array( $shipping_methods ) )
        foreach ( $shipping_methods as $method ) {
            $percentage = 'Percentage' == $method->method;

            switch ( $method->type ) {
                default:
                case 'custom':
                    $type = 'Custom';
                    $name = $method->name;
                break;

                case 'fedex':
                    $type = 'FedEx';
                    $name = ucwords( strtolower( str_replace( '_', ' ', $method->name ) ) );
                break;

                case 'ups':
                    $type = 'UPS';

                    $services = array(
                        '02' => _('UPS Second Day Air')
                        , '03' => _('UPS Ground')
                        , '07' => _('UPS Worldwide Express')
                        , '08' => _('UPS Worldwide Expedited')
                        , '11' => _('UPS Standard')
                        , '12' => _('UPS Three-Day Select')
                        , '13' => _('Next Day Air Saver')
                        , '14' => _('UPS Next Day Air Early AM')
                        , '54' => _('UPS Worldwide Express Plus')
                        , '59' => _('UPS Second Day Air AM')
                        , '65' => _('UPS Saver')
                    );

                    $name = $services[$method->name];
                break;

                case 'USPS':
                    $type = 'USPS';
                    $name = ucwords( strtolower( $method->name ) );
                break;
            }

            $data[] = array(
                $name . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'wsmid', $method->id, '/shopping-cart/shipping/add-edit-' . $method->type . '/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'wsmid' => $method->id, '_nonce' => $delete_nonce ), '/shopping-cart/shipping/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' .
                    '</div>'
                , $type
                , $method->method
                , ( ( !$percentage ) ? '$' : '' ) . number_format( $method->amount, 2 ) . ( ( $percentage ) ? '%' : '' )
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
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['wsmid'] ), _('You cannot delete this shipping method') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $shipping_method = new WebsiteShippingMethod();
        $shipping_method->get( $_GET['wsmid'], $this->user->account->id );
        $shipping_method->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


