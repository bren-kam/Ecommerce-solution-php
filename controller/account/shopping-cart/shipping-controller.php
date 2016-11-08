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
        return $this->get_template_response( 'index' )
            ->kb( 124 )
            ->menu_item( 'shopping-cart/shipping/list' );
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

        $form = new BootstrapForm( 'fAddEditCustom' );

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

                $this->log( 'update-custom-shipping-method', $this->user->contact_name . ' updated a custom shipping method on ' . $this->user->account->title, $_POST );
                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'custom';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->log( 'create-custom-shipping-method', $this->user->contact_name . ' created a custom shipping method on ' . $this->user->account->title, $_POST );
                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        return $this->get_template_response( 'add-edit' )
            ->kb( 125 )
            ->menu_item( 'shopping-cart/shipping/add-custom' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'Custom'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit Custom') : _('Add Custom') ) );
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

        $form = new BootstrapForm( 'fAddEditUPS' );

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

        $form->add_field( 'checkbox', _('Calculate the total weight of all packages for shipping charges:'), 'cbTotalWeight',  $shipping_method->extra['total-weight']);

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'pickup_type' => $_POST['sPickupType']
                , 'packaging_type' => $_POST['sPackagingType']
                , 'total-weight' => $_POST['cbTotalWeight']
            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->log( 'update-ups-shipping-method', $this->user->contact_name . ' updated a UPS shipping method on ' . $this->user->account->title, $shipping_method->id );
                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'ups';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->log( 'create-ups-shipping-method', $this->user->contact_name . ' created a UPS shipping method on ' . $this->user->account->title, $shipping_method->id );
                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        return $this->get_template_response( 'add-edit' )
            ->kb( 126 )
            ->menu_item( 'shopping-cart/shipping/add-ups' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'UPS'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit UPS') : _('Add UPS') ) );
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

        $shipping_fedex = $this->user->account->get_settings( 'shipping-fedex' );

        if ( !empty( $shipping_fedex ) )
            $shipping_fedex = unserialize( $shipping_fedex );

        if ( empty( $shipping_fedex ) || in_array( '', $shipping_fedex ) ) {
            $this->notify( _('You must set up your Fedex Account before adding Fedex shipping methods.'), false );
            url::redirect('/shopping-cart/shipping/settings/');
        }

        /***** CREATE FORM *****/

        $form = new BootstrapForm( 'fAddEditFedex' );

        $services = array(
            'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => _('Europe First International Priority')
            , 'FEDEX_1_DAY_FREIGHT' => _('FedEx 1 Day Freight')
            , 'FEDEX_2_DAY' => _('FedEx 2 Day')
            , 'FEDEX_2_DAY_FREIGHT' => _('FedEx 2 Day Freight')
            , 'FEDEX_3_DAY_FREIGHT' => _('FedEx 3 Day Freight')
            , 'FEDEX_EXPRESS_SAVER' => _('FedEx Express Saver')
            , 'FEDEX_GROUND' => _('FedEx Ground')
            , 'FIRST_OVERNIGHT' => _('First Overnight')
            , 'GROUND_HOME_DELIVERY' => _('Ground Home Delivery')
            , 'INTERNATIONAL_ECONOMY' => _('International Economy')
            , 'INTERNATIONAL_ECONOMY_FREIGHT' => _('International Economy Freight')
            , 'INTERNATIONAL_FIRST' => _('International First')
            , 'INTERNATIONAL_PRIORITY' => _('International Priority')
            , 'INTERNATIONAL_PRIORITY_FREIGHT' => _('International Priority Freight')
            , 'PRIORITY_OVERNIGHT' => _('Priority Overnight')
            , 'SMART_POST' => _('Smart Post')
            , 'STANDARD_OVERNIGHT' => _('Standard Overnight')
            , 'FEDEX_FREIGHT' => _('FedEx Freight')
            , 'FEDEX_NATIONAL_FREIGHT' => _('FedEx National Freight')
        );

        $form->add_field( 'select', _('Service'), 'sService', $shipping_method->name )
            ->options( $services );

        $packaging_types = array(
            'YOUR_PACKAGING' => _('Your Packaging')
            , 'FEDEX_BOX' => _('FedEx Box')
            , 'FEDEX_ENVELOPE' => _('FedEx Evelope')
            , 'FEDEX_PAK' => _('FedEx Pak')
            , 'FEDEX_TUBE' => _('FedEx Tube')
            , 'FEDEX_10KG_BOX' => _('FedEx 10Kg Box')
            , 'FEDEX_25KG_BOX' => _('FedEx 25Kg Box')
        );

        $form->add_field( 'select', _('Packaging Type'), 'sPackagingType', $shipping_method->extra['packaging_type'] )
            ->options( $packaging_types );

        $form->add_field( 'checkbox', _('Calculate the total weight of all packages for shipping charges:'), 'cbTotalWeight',  $shipping_method->extra['total-weight']);

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'pickup_type' => $_POST['sPickupType']
                , 'packaging_type' => $_POST['sPackagingType']
                , 'total-weight' => $_POST['cbTotalWeight']
            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->notify( _('Your shipping method has been updated successfully!') );
                $this->log( 'update-fedex-shipping-method', $this->user->contact_name . ' updated a FedEx shipping method on ' . $this->user->account->title, $shipping_method->id );
            } else {
                $shipping_method->type = 'fedex';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->notify( _('Your shipping method has been added successfully!') );
                $this->log( 'create-fedex-shipping-method', $this->user->contact_name . ' created a FedEx shipping method on ' . $this->user->account->title, $shipping_method->id );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        return $this->get_template_response( 'add-edit' )
            ->kb( 127 )
            ->menu_item( 'shopping-cart/shipping/add-fedex' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'FedEx'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit FedEx') : _('Add FedEx') ) );
    }

    /**
     * Add / Edit an Ashley Express Fedex Shipping Method
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit_ashley_express_fedex() {
        // Determine if we're adding or editing the user
        $website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? (int) $_GET['wsmid'] : false;

        $shipping_method = new WebsiteShippingMethod();

        if ( $website_shipping_method_id ) {
            $shipping_method->get( $website_shipping_method_id, $this->user->account->id );

            if ( !empty( $shipping_method->extra ) )
                $shipping_method->extra = unserialize( $shipping_method->extra );
        }

        $shipping_fedex = $this->user->account->get_settings( 'shipping-fedex' );

        if ( !empty( $shipping_fedex ) )
            $shipping_fedex = unserialize( $shipping_fedex );

        if ( empty( $shipping_fedex ) || in_array( '', $shipping_fedex ) ) {
            $this->notify( _('You must set up your Fedex Account before adding Fedex shipping methods.'), false );
            url::redirect('/shopping-cart/shipping/settings/');
        }

        /***** CREATE FORM *****/

        $form = new BootstrapForm( 'fAddEditFedex' );

        $services = array(
            'EUROPE_FIRST_INTERNATIONAL_PRIORITY' => _('Europe First International Priority')
        , 'FEDEX_1_DAY_FREIGHT' => _('FedEx 1 Day Freight')
        , 'FEDEX_2_DAY' => _('FedEx 2 Day')
        , 'FEDEX_2_DAY_FREIGHT' => _('FedEx 2 Day Freight')
        , 'FEDEX_3_DAY_FREIGHT' => _('FedEx 3 Day Freight')
        , 'FEDEX_EXPRESS_SAVER' => _('FedEx Express Saver')
        , 'FEDEX_GROUND' => _('FedEx Ground')
        , 'FIRST_OVERNIGHT' => _('First Overnight')
        , 'GROUND_HOME_DELIVERY' => _('Ground Home Delivery')
        , 'INTERNATIONAL_ECONOMY' => _('International Economy')
        , 'INTERNATIONAL_ECONOMY_FREIGHT' => _('International Economy Freight')
        , 'INTERNATIONAL_FIRST' => _('International First')
        , 'INTERNATIONAL_PRIORITY' => _('International Priority')
        , 'INTERNATIONAL_PRIORITY_FREIGHT' => _('International Priority Freight')
        , 'PRIORITY_OVERNIGHT' => _('Priority Overnight')
        , 'SMART_POST' => _('Smart Post')
        , 'STANDARD_OVERNIGHT' => _('Standard Overnight')
        , 'FEDEX_FREIGHT' => _('FedEx Freight')
        , 'FEDEX_NATIONAL_FREIGHT' => _('FedEx National Freight')
        );

        $form->add_field( 'select', _('Service'), 'sService', $shipping_method->name )
            ->options( $services );

        $packaging_types = array(
            'YOUR_PACKAGING' => _('Your Packaging')
            , 'FEDEX_BOX' => _('FedEx Box')
            , 'FEDEX_ENVELOPE' => _('FedEx Evelope')
            , 'FEDEX_PAK' => _('FedEx Pak')
            , 'FEDEX_TUBE' => _('FedEx Tube')
            , 'FEDEX_10KG_BOX' => _('FedEx 10Kg Box')
            , 'FEDEX_25KG_BOX' => _('FedEx 25Kg Box')
        );

        $form->add_field( 'select', _('Packaging Type'), 'sPackagingType', $shipping_method->extra ? $shipping_method->extra['packaging_type'] : '')
            ->options( $packaging_types );

        $form->add_field( 'select', 'Ashley Distribution Center', 'sAshleyDistributionCenter', $shipping_method->extra ? $shipping_method->extra['ashley_distribution_center'] : '' )
            ->options(array(
                'Leesport_PA' => 'Leesport, PA'
                , 'Advance_NC' => 'Advance, NC'
                , 'Arcadia_WI' => 'Arcadia, WI'
                , 'Ecru_MS' => 'Ecru, MS'
                , 'Colton_CA' => 'Colton, CA'
            ));

        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'packaging_type' => $_POST['sPackagingType']
                , 'ashley_distribution_center' => $_POST['sAshleyDistributionCenter']

            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->log( 'update-ashley-express-fedex-shipping-method', $this->user->contact_name . ' updated an Ashley Express FedEx shipping method on ' . $this->user->account->title, $shipping_method->id );
                $this->notify( _('Your shipping method has been updated successfully!') );
            } else {
                $shipping_method->type = 'ashley-express-fedex';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->log( 'create-ashley-express-fedex-shipping-method', $this->user->contact_name . ' created an Ashley Express FedEx shipping method on ' . $this->user->account->title, $shipping_method->id );
                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        return $this->get_template_response( 'add-edit' )
            ->kb( 127 )
            ->menu_item( 'shopping-cart/shipping/add-ashley-express-fedex' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'FedEx'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit FedEx') : _('Add FedEx') ) );
    }


    /**
     * Add / Edit a Ashley Express (UPS) Shipping Method
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit_ashley_express_ups() {
        // Determine if we're adding or editing the user
        $website_shipping_method_id = ( isset( $_GET['wsmid'] ) ) ? (int) $_GET['wsmid'] : false;

        $shipping_method = new WebsiteShippingMethod();

        if ( $website_shipping_method_id ) {
            $shipping_method->get( $website_shipping_method_id, $this->user->account->id );

            if ( !empty( $shipping_method->extra ) )
                $shipping_method->extra = unserialize( $shipping_method->extra );
        }

        /***** CREATE FORM *****/

        $form = new BootstrapForm( 'fAddEditUPS' );

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

        $form->add_field( 'select', _('Pickup Type'), 'sPickupType', $shipping_method->extra ? $shipping_method->extra['pickup_type'] : '' )
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

        $form->add_field( 'select', _('Packaging Type'), 'sPackagingType', $shipping_method->extra ? $shipping_method->extra['packaging_type'] : '' )
            ->options( $packaging_types );

        $form->add_field( 'select', 'Ashley Distribution Center', 'sAshleyDistributionCenter', $shipping_method->extra ? $shipping_method->extra['ashley_distribution_center'] : '')
            ->options(array(
                'Leesport_PA' => 'Leesport, PA'
                , 'Advance_NC' => 'Advance, NC'
                , 'Arcadia_WI' => 'Arcadia, WI'
                , 'Ecru_MS' => 'Ecru, MS'
                , 'Colton_CA' => 'Colton, CA'
            ));


        if ( $form->posted() ) {
            $shipping_method->name = $_POST['sService'];
            $shipping_method->method = 'N/A';
            $shipping_method->extra = serialize( array(
                'pickup_type' => $_POST['sPickupType']
                , 'packaging_type' => $_POST['sPackagingType']
                , 'ashley_distribution_center' => $_POST['sAshleyDistributionCenter']
            ));

            if ( $website_shipping_method_id ) {
                $shipping_method->save();

                $this->notify( _('Your shipping method has been updated successfully!') );
                $this->log( 'update-ashley-express-ups-shipping-method', $this->user->contact_name . ' updated an Ashley Express UPS shipping method on ' . $this->user->account->title, $shipping_method->id );
            } else {
                $shipping_method->type = 'ashley-express-ups';
                $shipping_method->website_id = $this->user->account->id;
                $shipping_method->create();

                $this->log( 'create-ashley-express-ups-shipping-method', $this->user->contact_name . ' create an Ashley Express UPS shipping method on ' . $this->user->account->title, $shipping_method->id );
                $this->notify( _('Your shipping method has been added successfully!') );
            }

            return new RedirectResponse('/shopping-cart/shipping/');
        }

        return $this->get_template_response( 'add-edit' )
            ->kb( 126 )
            ->menu_item( 'shopping-cart/shipping/add-ashley-express-ups' )
            ->set( array(
                'form' => $form->generate_form()
                , 'shipping_method' => $shipping_method
                , 'type' => 'UPS'
            ) )
            ->add_title( ( ( $website_shipping_method_id ) ? _('Edit UPS for Ashley Express') : _('Add UPS for Ashley Express') ) );
    }

    /**
     * Settings
     *
     * @return TemplateResponse
     */
    protected function settings() {
        $shipping_settings = array(
            'shipper_company' => ''
            , 'shipper_contact' => ''
            , 'shipper_address' => ''
            , 'shipper_city' => ''
            , 'shipper_state' => ''
            , 'shipper_zip' => ''
            , 'shipper_country' => ''
        );

        $shipping_ups = array(
            'access_key' => ''
            , 'username' => ''
            , 'password' => ''
            , 'account_number' => ''
        );

        $shipping_fedex = array(
            'development_key' => ''
            , 'password' => ''
            , 'account_number' => ''
            , 'meter_number' => ''
        );

        $shipping_amazon = [
            'amazon_aws_access_key_id' => ''
            , 'amazon_aws_secret_access_key' => ''
            , 'amazon_aws_merchant_id' => ''
        ];

        $settings = $this->user->account->get_settings( 'shipping-settings', 'shipping-ups', 'shipping-fedex', 'taxable-shipping', 'shipping-amazon' );

        if ( !empty( $settings['shipping-settings'] ) )
            $shipping_settings = unserialize( $settings['shipping-settings'] );

        if ( !empty( $settings['shipping-ups'] ) )
            $shipping_ups = unserialize( $settings['shipping-ups'] );

        if ( !empty( $settings['shipping-fedex'] ) )
            $shipping_fedex = unserialize( $settings['shipping-fedex'] );

        if ( !empty( $settings['shipping-amazon'] ) )
            $shipping_amazon = unserialize( $settings['shipping-amazon'] );

        // Create form

        $form = new BootstrapForm( 'fShippingSettings' );

        // Generic Settings
        $form->add_field( 'title', _('Generic Settings') );
        $form->add_field( 'text', _('Shipper Company'), 'tShipperCompany', $shipping_settings['shipper_company'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Shipper Contact'), 'tShipperContact', $shipping_settings['shipper_contact'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Shipper Address'), 'tShipperAddress', $shipping_settings['shipper_address'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Shipper City'), 'tShipperCity', $shipping_settings['shipper_city'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Shipper State'), 'tShipperState', $shipping_settings['shipper_state'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Shipper Zip'), 'tShipperZip', $shipping_settings['shipper_zip'] )
            ->attribute( 'maxlength', 10 )
            ->add_validation( 'zip', _('The "Shipping Zip" field must contain a valid zip code') );

        $form->add_field( 'select', _('Shipper Country'), 'sShipperCountry', $shipping_settings['shipper_country'] )
            ->options( array_merge( array('' => ''), data::countries( false ) ) );

        $form->add_field( 'blank', '' );

        // UPS Settings
        $form->add_field( 'title', _('UPS Settings') );

        $form->add_field( 'text', _('Access Key'), 'tUPSAccessKey', $shipping_ups['access_key'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Username'), 'tUPSUsername', $shipping_ups['username'] )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'password', _('Password'), 'pUPSPassword', $shipping_ups['password'] )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'text', _('Account Number'), 'tUPSAccountNumber', $shipping_ups['account_number'] )
            ->attribute( 'maxlength', 32 );

        $form->add_field( 'row', _('Weight Unit:'), 'Pounds' );
        $form->add_field( 'row', _('Length Unit:'), 'Inches' );
        $form->add_field( 'blank', '' );

        // FedEx Settings
        $form->add_field( 'title', _('FedEx Settings') );

        $form->add_field( 'text', _('Development Key'), 'tFedExDevelopmentKey', $shipping_fedex['development_key'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'password', _('Password'), 'pFedExPassword', $shipping_fedex['password'] )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'text', _('Account Number'), 'tFedExAccountNumber', $shipping_fedex['account_number'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Meter Number'), 'tFedExMeterNumber', $shipping_fedex['meter_number'] )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'row', _('Weight Unit:'), 'Pounds' );
        $form->add_field( 'row', _('Length Unit:'), 'Inches' );
        $form->add_field( 'blank', '' );

        // Amazon Settings
        // The following field adds a button with a onclick function that is located under view/account/js/shopping-cart/settings/settings.js
        // Unfortunately, I'm not sure what would be the best practice to achieve this result using the $form object.
        // The goal is to provide the user with a button that allows the complete removal of Amazon Settings from the database.
        $form->add_field( 'title', _('Fulfillment By Amazon Settings - <a class="btn btn-warning" onclick="Settings.removeAmazon()">Remove</a>') );

        $form->add_field( 'text', _('AWS Access Key Id'), 'tAmazonAwsAccessKeyId', $shipping_amazon['aws_access_key_id'] )
            ->attribute( 'maxlength', 32 );

        $form->add_field( 'text', _('Aws Secret Access Key'), 'tAmazonAwsSecretAccessKey', $shipping_amazon['aws_secret_access_key'] )
            ->attribute( 'maxlength', 64 );

        $form->add_field( 'text', _('Merchant Id'), 'tAmazonMerchantId', $shipping_amazon['merchant_id'] )
            ->attribute( 'maxlength', 32 );

        $form->add_field( 'text', _('Marketplace Id'), 'tAmazonMarketplaceId', $shipping_amazon['marketplace_id'] )
            ->attribute( 'maxlength', 32 );
        $form->add_field( 'blank', '' );

        // Tax Settings
        $form->add_field( 'title', _('Tax Settings') );

        $form->add_field( 'checkbox', _('Taxable Shipping'), 'cbTaxableShipping', $settings['taxable-shipping'] );

        // Handle posting

        if ( $form->posted() ) {

            $website_settings = array(
                'shipping-settings' => serialize( array(
                    'shipper_company' => $_POST['tShipperCompany']
                    , 'shipper_contact' => $_POST['tShipperContact']
                    , 'shipper_address' => $_POST['tShipperAddress']
                    , 'shipper_city' => $_POST['tShipperCity']
                    , 'shipper_state' => $_POST['tShipperState']
                    , 'shipper_zip' => $_POST['tShipperZip']
                    , 'shipper_country' => $_POST['sShipperCountry']
                ) )
                , 'shipping-ups' => serialize( array(
                    'access_key' => $_POST['tUPSAccessKey']
                    , 'username' => $_POST['tUPSUsername']
                    , 'password' => $_POST['pUPSPassword']
                    , 'account_number' => $_POST['tUPSAccountNumber']
            ) )
                , 'shipping-fedex' => serialize( array(
                    'development_key' => $_POST['tFedExDevelopmentKey']
                    , 'password' => $_POST['pFedExPassword']
                    , 'account_number' => $_POST['tFedExAccountNumber']
                    , 'meter_number' => $_POST['tFedExMeterNumber']
            ) )
                , 'taxable-shipping' => ( isset( $_POST['cbTaxableShipping'] ) ) ? '1' : '0'
            );

            if ( !empty($_POST['tAmazonAwsAccessKeyId']) ) {
                $website_settings = array_merge($website_settings,
                    [
                        'shipping-amazon' => serialize(array(
                            'aws_access_key_id' => $_POST['tAmazonAwsAccessKeyId']
                            , 'aws_secret_access_key' => $_POST['tAmazonAwsSecretAccessKey']
                            , 'merchant_id' => $_POST['tAmazonMerchantId']
                            , 'marketplace_id' => $_POST['tAmazonMarketplaceId']
                        )   )
                    ]);


                $website_shipping_methods = new WebsiteShippingMethod();
                $website_shipping_amazon = $website_shipping_methods->get_by_type('amazon-outbound', $this->user->account->website_id);
                if(!isset($website_shipping_amazon->website_shipping_method_id) || empty($website_shipping_amazon->website_shipping_method_id)) {
                    $website_shipping_methods->type = 'amazon-outbound';
                    $website_shipping_methods->website_id = $this->user->account->website_id;
                    $website_shipping_methods->name = 'Fulfillment By Amazon';
                    $website_shipping_methods->method = 'N/A';
                    $website_shipping_methods->amount = 0;
                    $website_shipping_methods->create();
                }
            }

            $this->user->account->set_settings( $website_settings );

            $this->log( 'update-shipping-settings', $this->user->contact_name . ' updated shipping settings on ' . $this->user->account->title );
            $this->notify( _('Your settings have been successfully saved!') );

            // Simply refresh the page
            return new RedirectResponse( '/shopping-cart/shipping/settings/' );
        }

        $this->resources->javascript( 'shopping-cart/settings/settings');

        return $this->get_template_response( 'settings' )
            ->kb( 128 )
            ->menu_item( 'shopping-cart/shipping/settings' )
            ->set( array(
                'form' => $form->generate_form()
            ) )
            ->add_title( _('Settings') );
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

                case 'ashley-express-fedex':
                    $type = 'FedEx (Ashley Express)';
                    $name = ucwords( strtolower( str_replace( '_', ' ', $method->name ) ) );

                    if ( !empty( $method->extra ) ) {
                        $method->extra = unserialize($method->extra);
                        $name .= " - " . $method->extra['ashley_distribution_center'];
                    }

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
                case 'ashley-express-ups':
                    $type = 'UPS (Ashley Express)';

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

                    if ( !empty( $method->extra ) ) {
                        $method->extra = unserialize($method->extra);
                        $name .= " - " . $method->extra['ashley_distribution_center'];
                    }

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
    protected function delete() {
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
        $response->add_response( 'reload_datatable', 'reload_datatable' );

        $this->log( 'delete-shipping-method', $this->user->contact_name . ' deleted a shipping method on ' . $this->user->account->title, $_GET['wsmid'] );

        return $response;
    }


    public function remove_amazon() {
        $this->user->account->remove_setting( 'shipping-amazon' );
        $website_shipping_method = new WebsiteShippingMethod();
        $website_shipping_method->remove_by_type('amazon-outbound', $this->user->account->website_id);
        return new AjaxResponse(true);
    }
}


