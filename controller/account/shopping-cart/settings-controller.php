<?php
class SettingsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'shopping-cart/settings/';
        $this->section = 'shopping-cart';
        $this->title = _('Settings | Shopping Cart');
    }

    /**
     * Show Settings
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        $settings = $this->user->account->get_settings( 'email-receipt', 'receipt-message', 'add-product-popup' );

        $form = new FormTable( 'fSettings' );

        $form->add_field( 'text', _('Email Receipt'), 'tReceipt', $settings['email-receipt'] )
            ->attribute( 'maxlength', 150 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $form->add_field( 'textarea', _('Receipt Message'), 'taReceiptMessage', $settings['receipt-message'] )
            ->attribute( 'rte', '1' );

        $form->add_field( 'checkbox', _('Show Related Products on Checkout Page'), 'add-product-popup', $settings['add-product-popup'] );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                'email-receipt' => $_POST['tReceipt']
                , 'receipt-message' => $_POST['taReceiptMessage']
                , 'add-product-popup' => $_POST['add-product-popup']
            ) );

            $this->notify( _('Your settings have been successfully saved.') );
            return new RedirectResponse( '/shopping-cart/settings/' );
        }

        $form = $form->generate_form();

        $response = $this->get_template_response( 'index' )
            ->set( compact( 'form' ) )
            ->select( 'settings', 'general' );

        return $response;
    }

    /**
     * Payment Gateway
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function payment_gateway() {
        $settings = $this->user->account->get_settings( 'payment-gateway-status', 'aim-login', 'aim-transaction-key' );

        // Create Form
        $form = new FormTable( 'fPaymentGateway' );

        $form->add_field( 'row', _('Payment Gateway:'), _('Authorize.net AIM') );

        $form->add_field( 'select', _('Status'), 'sStatus', $settings['payment-gateway-status'] )
            ->options( array(
                0 => _('Testing')
                , 1 => _("Live")
            )
        );

        $form->add_field( 'text', _('AIM Login'), 'tAIMLogin', security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 30 )
            ->add_validation( 'req', _('The "AIM Login" field is required') );

        $form->add_field( 'text', _('AIM Transaction Key'), 'tAIMTransactionKey', security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 30 )
            ->add_validation( 'req', _('The "AIM Transaction Key" field is required') );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                'payment-gateway-status' => $_POST['sStatus']
                , 'aim-login' => base64_encode( security::encrypt( $_POST['tAIMLogin'], PAYMENT_DECRYPTION_KEY ) )
                , 'aim-transaction-key' => base64_encode( security::encrypt( $_POST['tAIMTransactionKey'], PAYMENT_DECRYPTION_KEY ) )
            ) );

            $this->notify( _('Your settings have been successfully saved.') );
            return new RedirectResponse( '/shopping-cart/settings/payment-gateway/' );
        }

        $form = $form->generate_form();

        $response = $this->get_template_response( 'payment-gateway' )
            ->set( compact( 'form' ) )
            ->select( 'settings', 'payment-gateway' );

        return $response;
    }

    /**
     * Taxes
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function taxes() {
        // Define variables
        $taxes = $this->user->account->get_settings( 'taxes' );

        if ( !empty( $taxes ) )
            $taxes = unserialize( html_entity_decode( $taxes ) );

        $states = data::states( false );

        if ( $this->verified() ) {
            $zip_codes = array();

            if ( isset( $_POST['zip_codes'] ) )
            foreach ( $_POST['zip_codes'] as $state => $taxes ) {
                $rows = explode( "\n", $taxes );
                foreach ( $rows as $r ) {
                    list( $zip, $cost ) = explode( ' ', str_replace("\t", ' ', $r ) );
                    $zip_codes[$state][$zip] = $cost;
                }
            }

            $this->user->account->set_settings( array(
                'taxes' => serialize( array(
                    'states' => $_POST['states']
                    , 'zip_codes' => $zip_codes
                ) )
            ) );

            $this->notify( _('Taxes successfully saved!') );
            return new RedirectResponse('/shopping-cart/settings/taxes/');
        }

        $this->resources
            ->css( 'shopping-cart/settings/taxes' )
            ->javascript( 'shopping-cart/settings/taxes' );

        $response = $this->get_template_response( 'taxes' )
            ->select( 'settings', 'tax-settings' )
            ->set( compact( 'taxes', 'states' ) );

        return $response;
    }
}


