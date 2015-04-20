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
        $settings = $this->user->account->get_settings( 'email-receipt', 'receipt-message', 'add-product-popup', 'google-feed', 'authorize-net-id', 'authorize-net-authorize-only' );

        $form = new BootstrapForm( 'fSettings' );

        $form->add_field( 'text', _('Email Receipt'), 'tReceipt', $settings['email-receipt'] )
            ->attribute( 'maxlength', 150 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $form->add_field( 'textarea', _('Receipt Message'), 'taReceiptMessage', $settings['receipt-message'] )
            ->attribute( 'rte', '1' );

        $form->add_field( 'checkbox', _('Show Related Products on Checkout Page'), 'add-product-popup', $settings['add-product-popup'] );

        $url = 'http://' . $this->user->account->domain . '/google-feed/';
        $form->add_field( 'checkbox', _('Enable Google Feed') . ' (<a href="' . $url . '" target="_blank" title="Google Feed">' . $url . '</a>)', 'google-feed', $settings['google-feed'] );

        $form->add_field( 'text', _('Authorize.net  - Logo ID'), 'authorize-net-id', $settings['authorize-net-id'] )
            ->attribute( 'maxlength', 50 );

        $form->add_field( 'checkbox', _('Authorize.net - Authorize  Only'), 'authorize-net-authorize-only', $settings['authorize-net-authorize-only'] );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                'email-receipt' => $_POST['tReceipt']
                , 'receipt-message' => $_POST['taReceiptMessage']
                , 'add-product-popup' => $_POST['add-product-popup']
                , 'google-feed' => $_POST['google-feed']
                , 'authorize-net-id' => $_POST['authorize-net-id']
                , 'authorize-net-authorize-only' => $_POST['authorize-net-authorize-only']
            ) );

            $this->notify( _('Your settings have been successfully saved.') );
            $this->log( 'update-shopping-cart-settings', $this->user->contact_name . ' updated shopping cart settings on ' . $this->user->account->title );

            return new RedirectResponse( '/shopping-cart/settings/' );
        }

        $form = $form->generate_form();

        return $this->get_template_response( 'index' )
            ->kb( 131 )
            ->set( compact( 'form' ) )
            ->menu_item( 'shopping-cart/settings/settings' );
    }

    /**
     * Payment Settings
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function payment_settings() {
        $settings = $this->user->account->get_settings(
            'payment-gateway-status'
            , 'aim-login'
            , 'aim-transaction-key'
            , 'stripe-account'
            , 'selected-gateway'
            , 'paypal-express-username'
            , 'paypal-express-password'
            , 'paypal-express-signature'
            , 'bill-me-later'
            , 'crest-financial-dealer-id'
        );

        // Create Form
        $form = new BootstrapForm( 'fPaymentSettings' );


        $form->add_field( 'row', '', _('All Payment Methods') );

        $form->add_field( 'select', _('Status'), 'sStatus', $settings['payment-gateway-status'] )
            ->options( array(
                0 => _('Testing')
                , 1 => _("Live")
            )
        );

        $form->add_field( 'blank', '' );
        $form->add_field( 'row', '', _('Authorize.net AIM') );

        $aim_login = security::decrypt( base64_decode( $settings['aim-login'] ), PAYMENT_DECRYPTION_KEY );
        $form->add_field( 'text', _('AIM Login'), 'tAIMLogin', $aim_login )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'text', _('AIM Transaction Key'), 'tAIMTransactionKey', security::decrypt( base64_decode( $settings['aim-transaction-key'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'row', '', _('Stripe') );

        if ( $settings['stripe-account'] ) {
            $stripe_account = json_decode($settings['stripe-account'], true);

            $form->add_field( 'text', _('Stripe ID'), 'tStripeId', $stripe_account['stripe_user_id'] )
                ->attribute('disabled', 'disabled');

            $form->add_field( 'text', _('Stripe Publishable Key'), 'tStripePublishableKey', $stripe_account['stripe_publishable_key'] )
                ->attribute('disabled', 'disabled');

            $form->add_field( 'text', _('Stripe Secret Key'), 'tStripeSecretKey', $stripe_account['access_token'] )
                ->attribute('disabled', 'disabled');
        } else {
            $form->add_field('anchor', 'Connect to Stripe')
                ->attribute('href', "http://account.dev.greysuitretail.com/shopping-cart/settings/stripe-connect/?website-id={$this->user->account->id}&user-id={$this->user->id}")
                ->attribute('class', 'btn btn-primary');
        }

        $gateway_options = [];
        $form->add_field( 'select', 'Process Payments With', 'sSelectedGateway', $settings['selected-gateway'] )
            ->options([
                'aim' => 'AIM',
                'stripe' => 'Stripe'
            ]);

        $form->add_field( 'blank', '' );
        $form->add_field( 'row', '', _('PayPal Express Checkout') );

        $form->add_field( 'text', _('Username'), 'tPaypalExpressUsername', security::decrypt( base64_decode( $settings['paypal-express-username'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('Password'), 'tPaypalExpressPassword', security::decrypt( base64_decode( $settings['paypal-express-password'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'text', _('API Signature'), 'tPaypalExpressSignature', security::decrypt( base64_decode( $settings['paypal-express-signature'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 100 );

        $form->add_field( 'checkbox', _('Bill Me Later'), 'cbBillMeLater', $settings['bill-me-later'] );

        $form->add_field( 'anchor', _('Test PayPal Credentials') )
            ->attribute( 'id', 'test-paypal' )
            ->attribute( 'ajax', '1' )
            ->attribute( 'href', '/shopping-cart/settings/test-paypal/?_nonce=' . nonce::create('test_paypal') );

        $form->add_field( 'blank', '' );
        $form->add_field( 'row', '', _('Crest Financial') );

        $form->add_field( 'text', _('Dealer ID'), 'tCrestFinancialDealerId', security::decrypt( base64_decode( $settings['crest-financial-dealer-id'] ), PAYMENT_DECRYPTION_KEY ) )
            ->attribute( 'maxlength', 10 );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                'payment-gateway-status' => $_POST['sStatus']
                , 'aim-login' => base64_encode( security::encrypt( $_POST['tAIMLogin'], PAYMENT_DECRYPTION_KEY ) )
                , 'aim-transaction-key' => base64_encode( security::encrypt( $_POST['tAIMTransactionKey'], PAYMENT_DECRYPTION_KEY ) )
                , 'paypal-express-username' => base64_encode( security::encrypt( $_POST['tPaypalExpressUsername'], PAYMENT_DECRYPTION_KEY ) )
                , 'paypal-express-password' => base64_encode( security::encrypt( $_POST['tPaypalExpressPassword'], PAYMENT_DECRYPTION_KEY ) )
                , 'paypal-express-signature' => base64_encode( security::encrypt( $_POST['tPaypalExpressSignature'], PAYMENT_DECRYPTION_KEY ) )
                , 'bill-me-later' => $_POST['cbBillMeLater']
                , 'crest-financial-dealer-id' => base64_encode( security::encrypt( $_POST['tCrestFinancialDealerId'], PAYMENT_DECRYPTION_KEY ) )
                , 'selected-gateway' => $_POST['sSelectedGateway']
            ) );

            $this->notify( _('Your settings have been successfully saved.') );
            $this->log( 'update-payment-settings', $this->user->contact_name . ' updated payment settings on ' . $this->user->account->title );

            return new RedirectResponse( '/shopping-cart/settings/payment-settings/' );
        }

        $form = $form->generate_form();

        return $this->get_template_response( 'payment-settings' )
            ->kb( 132 )
            ->set( compact( 'form' ) )
            ->menu_item( 'shopping-cart/settings/payment-settings' )
            ->add_title( _('Payment Settings') );
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
            $this->log( 'update-taxes', $this->user->contact_name . ' updated tax settings on ' . $this->user->account->title );

            return new RedirectResponse('/shopping-cart/settings/taxes/');
        }

        $this->resources
            ->css( 'shopping-cart/settings/taxes' )
            ->javascript( 'shopping-cart/settings/taxes' );

        return $this->get_template_response( 'taxes' )
            ->kb( 133 )
            ->menu_item( 'shopping-cart/settings/taxes' )
            ->set( compact( 'taxes', 'states' ) );
    }

    /**
     * Test PayPal
     * @return AjaxResponse
     */
    public function test_paypal() {
        $response = new AjaxResponse( $this->verified() );

        $settings = $this->user->account->get_settings(
            'payment-gateway-status'
            , 'paypal-express-username'
            , 'paypal-express-password'
            , 'paypal-express-signature'
        );

        $paypal_user = security::decrypt( base64_decode( $settings['paypal-express-username'] ), PAYMENT_DECRYPTION_KEY );
        $paypal_password = security::decrypt( base64_decode( $settings['paypal-express-password'] ), PAYMENT_DECRYPTION_KEY );
        $paypal_signature = security::decrypt( base64_decode( $settings['paypal-express-signature'] ), PAYMENT_DECRYPTION_KEY );
        $url = $settings['payment-gateway-status'] ? "https://api-3t.paypal.com/nvp" : "https://api-3t.sandbox.paypal.com/nvp";

        $response_str = curl::post(
            $url,
            [
                "USER" => $paypal_user,
                "PWD" => $paypal_password,
                "SIGNATURE" => $paypal_signature,
                "VERSION" => "104",
                "METHOD" => "GetPalDetails"
            ]
        );
        $paypal_response = [];
        parse_str($response_str, $paypal_response);

        if ( $paypal_response['ACK'] == 'Success' ) {
            $response->notify("PayPal Credentials Successful. Your PAL ID is: {$paypal_response['PAL']}");
        } else {
            $response->notify("PayPal Credentials Failed: {$paypal_response['L_LONGMESSAGE0']}", false);
        }

        return $response;
    }

    /**
     * Stripe Connect
     */
    public function stripe_connect() {
        $stripe_client_id = Config::key('stripe-client-id');
        $url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$stripe_client_id}&scope=read_write";
        url::redirect($url);
    }

    /**
     * Stripe Callback
     * @return RedirectResponse
     */
    public function stripe_callback() {
        $url = "https://connect.stripe.com/oauth/token";
        $auth_code = $_GET['code'];
        $stripe_client_id = Config::key('stripe-client-id');
        $stripe_secret_key = Config::key('stripe-secret-key');
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $stripe_client_id,
            'client_secret' => $stripe_secret_key,
            'code' => $auth_code
        ];
        $response_str = curl::post($url, $data);
        $response = json_decode($response_str, true);

        if ( $response['token_type'] != 'bearer' ) {
            $this->notify('There was an error connecting with Stripe, please try again.', false);
            url::redirect( $_SESSION['callback-referer'] );
        }

        $this->user->account->id = $_SESSION['callback-website-id'];
        $this->user->account->set_settings([
            'stripe-account' => $response_str
        ]);

        url::redirect( $_SESSION['callback-referer'] );
    }

    /**
     * Get Logged In User
     * @return bool
     */
    protected function get_logged_in_user() {
        // connect_* are public, but need a referer and a website-id
        $connect_url = strpos( $_SERVER['REQUEST_URI'], '/shopping-cart/settings/stripe-connect/' ) !== FALSE;

        if ( $connect_url ) {

            if ( !$_REQUEST['website-id'] || !$_SERVER['HTTP_REFERER'] ) {
                return false;
            }

            $_SESSION['callback-website-id'] = $_REQUEST['website-id'];
            $_SESSION['callback-referer'] = $_SERVER['HTTP_REFERER'];
            $_SESSION['callback-user-id'] = $_REQUEST['user-id'];
            // for notifications
            $this->user = new stdClass;
            $this->user->user_id = $this->user->id = $_REQUEST['user-id'];
            $this->user->account = new Account();
            $this->user->account->get($_REQUEST['website-id']);

            return true;
        }

        $callback_url = strpos( $_SERVER['REQUEST_URI'], '/shopping-cart/settings/stripe-callback/' ) !== FALSE;

        if ( $callback_url ) {
            if ( !$_SESSION['callback-website-id'] || !$_SESSION['callback-referer'] || !$_SESSION['callback-user-id'] ) {
                return false;
            }
            // for notifications
            $this->user = new stdClass;
            $this->user->user_id = $this->user->id = $_SESSION['callback-user-id'];
            $this->user->account = new Account();
            $this->user->account->get($_REQUEST['website-id']);

            return true;
        }

        return parent::get_logged_in_user();

    }


}


