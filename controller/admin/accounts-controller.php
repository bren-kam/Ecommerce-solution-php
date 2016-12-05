<?php
class AccountsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/';
        $this->section = 'accounts';
    }

    /**
     * List Accounts
     *
     * @return TemplateResponse
     */
    protected function index() {
        unset( $_SESSION['accounts'] );

        $this->resources
            ->css('accounts/index')
            ->javascript('accounts/index')
            ->javascript_url( Config::resource('typeahead-js') );

        return $this->get_template_response( 'index' )
            ->kb( 1 )
            ->select( 'accounts', 'accounts/index' );
    }

    /**
     * Add account
     *
     * @return TemplateResponse
     */
    protected function add() {
        // Instantiate Objects
        $account = new Account();

        $user_array = $this->user->get_all();
        $os_users[''] = _('-- Select Online Specialist --');
        $users[''] = _('-- Select User --');

        /**
         * @var User $user
         */
        foreach ( $user_array as $user ) {
            if ( $user->role >= 7 )
                $os_users[$user->id] = $user->contact_name;

            $users[$user->id] = $user->contact_name;
        }

        // Create new form table
        $ft = new BootstrapForm( 'fAddAccount' );

        $ft->submit( _('Add') );

        $ft->add_field( 'text', _('Title'), 'tTitle' )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $ft->add_field( 'text', _('Domain'), 'tDomain' )
            ->attribute( 'maxlength', 100 );

        $ft->add_field( 'select', _('User'), 'sUserID' )
            ->add_validation( 'req', _('The "User" field is required') )
            ->options( $users );

        $ft->add_field( 'select', _('Online Specialist'), 'sOnlineSpecialistID' )
            ->add_validation( 'req', _('The "Online Specialist" field is required') )
            ->options( $os_users );

        $industry = new Industry();
        $industry_options = [];
        foreach( $industry->get_all() as $industry ) {
            $industry_options[$industry->id] = ucwords($industry->name);
        }

        $ft->add_field( 'select', _('Industry'), 'sIndustry' )
            ->options( $industry_options );

        // Update the account if posted
        if ( $ft->posted() ) {
            $account->user_id = $_POST['sUserID'];
            $account->os_user_id = $_POST['sOnlineSpecialistID'];
            $account->domain = $_POST['tDomain'];
            $account->title = $_POST['tTitle'];
            $account->type = 'Furniture'; // $_POST['sType'];
            $account->create();

            // Needs to create a checklist
            $checklist = new Checklist();
            $checklist->website_id = $account->id;
            $checklist->type = 'Website Setup';
            $checklist->create();

            // Add checklist website items
            $checklist_website_item = new ChecklistWebsiteItem();
            $checklist_website_item->add_all_to_checklist( $checklist->id );

            $account->add_industries([ $_POST['sIndustry'] ]);

            $this->notify( _('Your account was successfully created!') );

            return new RedirectResponse('/accounts/');
        }

        return $this->get_template_response( 'add' )
            ->kb( 2 )
            ->add_title( _('Add') )
            ->select( 'accounts', 'accounts/add' )
            ->set( 'form', $ft->generate_form() );
    }

    /**
     * Edit Account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get Form Table for the elements
        lib('helpers/form-table');

        // Instantiate
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $v = new BootstrapValidator( 'fEditAccount' );
        $v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
        $v->add_validation( 'tProducts', 'req', _('The "Products" field is required') );
        $v->add_validation( 'tProducts', 'num', _('The "Products" field must contain a number') );

        $errs = false;

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                // Get the username
                $sendgrid_username = $account->get_settings('sendgrid-username');
                $continue = true;

                // We must cancel email marketing
                if ( 1 == $account->email_marketing && 1 != (int) isset( $_POST['cbEmailMarketing'] ) ) {
                    if ( !empty( $sendgrid_username ) ) {
                        library('sendgrid-api');
                        $sendgrid = new SendGridAPI( $account );
                        $sendgrid->setup_subuser();
                        $sendgrid->subuser->delete( $sendgrid_username );
                    }
                } elseif ( 0 == $account->email_marketing && 0 != (int) isset( $_POST['cbEmailMarketing'] ) && empty( $sendgrid_username ) ) {
                    // Not allowed!
                    $this->notify( 'Please contact Technical to create an email marketing account.', false );
                    unset( $_POST['cbEmailMarketing'] );
                    $continue = false;
                }

                if ( $continue ) {
                    $account->title = $_POST['tTitle'];
                    $account->user_id = $_POST['sUserID'];
                    $account->os_user_id = $_POST['sOSUserID'];
                    $account->products = $_POST['tProducts'];
                    $account->plan_name = $_POST['tPlan'];
                    $account->plan_description = $_POST['taPlanDescription'];
                    $account->pages = (int) isset( $_POST['cbPages'] );
                    $account->shopping_cart = (int) isset( $_POST['cbShoppingCart'] );
                    $account->product_catalog = (int) isset( $_POST['cbProductCatalog'] );
                    $account->room_planner = (int) isset( $_POST['cbRoomPlanner'] );
                    $account->blog = (int) isset( $_POST['cbBlog'] );
                    $account->email_marketing = (int) isset( $_POST['cbEmailMarketing'] );
                    $account->domain_registration = (int) isset( $_POST['cbDomainRegistration'] );
                    $account->additional_email_Addresses = (int) isset( $_POST['cbAdditionalEmailAddresses'] );
                    $account->social_media = (int) isset( $_POST['cbSocialMedia'] );
                    $account->remarketing = (int) isset( $_POST['cbRemarketing'] );                    
                    $account->geo_marketing = (int) isset( $_POST['cbGeoMarketing'] );
                    $account->user_id_updated = $this->user->id;

                    $account->save();

                    if ( 1 == $account->social_media )
                    $account->set_settings( array(
                        'social-media-add-ons' => serialize( array(
                            'email-sign-up'
                            , 'fan-offer'
                            , 'sweepstakes'
                            , 'share-and-save'
                            , 'facebook-site'
                            , 'contact-us'
                            , 'about-us'
                            , 'products'
                            , 'current-ad'
                            , 'posting'
                        ) )
                    ) );

                    // Set the account's official address
                    $account->set_settings( array(
                        'address' => $_POST['tAddress']
                        , 'city' => $_POST['tCity']
                        , 'state' => $_POST['sState']
                        , 'zip' => $_POST['tZip']
                        , 'phone' => $_POST['tPhone']
                    ) );

                    $this->notify( _('This account has been successfully updated!') );
                }
            }
        }

        // Define fields
        $fields = array( 'account_title', 'users', 'phone', 'products', 'os_users', 'plan', 'plan_description', 'address', 'city', 'state', 'zip' );

        // Get users
        $user_array = $this->user->get_all();
        $os_users[''] = _('-- Select Online Specialist --');
        $users[''] = _('-- Select User --');

        /**
         * @var User $user
         */
        foreach ( $user_array as $user ) {
            if ( $user->role >= 7 )
                $os_users[$user->id] = $user->contact_name;

            $users[$user->id] = $user->contact_name;
        }

        // Address
        $address = $account->get_settings( 'address', 'city', 'state', 'zip', 'phone' );
        $states = data::states( false );

        $owner = new User();
        $owner->get( $account->user_id );

        // Features
        $features = array(
            'pages'
            , 'shopping_cart'
            , 'product_catalog'
            , 'room_planner'
            , 'blog'
            , 'email_marketing'
            , 'domain_registration'
            , 'additional_email_addresses'
            , 'social_media'
            , 'geo_marketing'
            , 'remarketing'
        );

        foreach ( $features as $feature ) {
            $checkbox_form_name = format::underscore_to_camel_case( 'cb_' . $feature );

            switch ( $feature ) {
                case 'pages':
                    $checkbox_name = _('Website');
                break;

                default:
                    $checkbox_name = ucwords( str_replace( '_', ' ', $feature ) );
                break;
            }

            $selected = isset( $_POST[$checkbox_form_name] ) || ( !isset( $_POST ) || empty( $POST ) ) && $account->$feature;
            $checked = ( $selected ) ? ' checked="checked"' : '';

            $checkboxes[$feature] = array(
                'name' => $checkbox_name
                , 'form_name' => $checkbox_form_name
                , 'selected' => $selected
            );
        }

        // Include Resources
        $this->resources
            ->javascript_url( Config::resource( 'bootstrap-validator-js' ) )
            ->javascript('accounts/edit', 'bootstrap-switch.min')
            ->css('accounts/edit');

        $js_validation = $v->js_validation();

        $template_response = $this->get_template_response('edit')
            ->kb( 4 )
            ->select( 'accounts', 'accounts/index' )
            ->add_title( _('Edit') )
            ->set( compact( 'account', 'address', 'states', 'users', 'os_users', 'checkboxes', 'errs', 'owner', 'checkboxes', 'js_validation' ) );

        return $template_response;
    }

    /**
     * Website Settings
     *
     * @return TemplateResponse
     */
    protected function website_settings() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        // Setup objects
        $cp = new CompanyPackage();
        $industry = new Industry();
        $ft = new BootstrapForm( 'fWebsiteSettings' );

        // Get variables
        $industries = $industry->get_all();
        $account_industries = $account->get_industries();

        $company_packages = $cp->get_all( $account->id );
        $packages = array( '' => _('Select a Package') );

        $custom_image_size = $account->get_settings( 'custom-image-size' );

        // Start adding fields
        $ft->add_field( 'text', _('Domain'), 'tDomain', $account->domain )
            ->add_validation( 'req', _('The "Domain" field is required') );

        $ft->add_field( 'text', _('Theme'), 'tTheme', $account->theme )
            ->add_validation( 'req', _('The "Theme" field is required') );

        // Get company packages

        if ( is_array( $company_packages ) )
        foreach ( $company_packages as $package ) {
            $packages[$package->id] = $package->name;
        }

        $ft->add_field( 'select', _('Package'), 'sPackage', $account->company_package_id )
            ->options( $packages );

        // Setup Industries

        $industry_list = array();

        foreach ( $industries as $i ) {
            $industry_list[$i->id] = ucwords( $i->name );
        }

        $ft->add_field( 'select', _('Industries'), 'sIndustries[]', $account_industries )
            ->attribute( 'multiple', 'multiple' )
            ->options( $industry_list );

        // Max Image Size
        $ft->add_field( 'text', _('Max Image Size For Custom Products'), 'tCustomImageSize', $custom_image_size );

        // Is the site live?
        $ft->add_field( 'checkbox', _('Live'), 'cbLive', $account->live );

        // Update account
        if ( $ft->posted() ) {
            // Update account
            $account->company_package_id = $_POST['sPackage'];
            $account->domain = $_POST['tDomain'];
            $account->theme = $_POST['tTheme'];
            $account->live = isset( $_POST['cbLive'] );

            $account->user_id_updated = $this->user->id;

            $account->save();

            // Update the settings
            $account->set_settings( array( 'custom-image-size' => $_POST['tCustomImageSize'] ));

            // Set the industries
            $account->delete_industries();
            $account->add_industries( $_POST['sIndustries'] );

            // Let them know it was done
            $this->notify( _('The Website Settings have been updated!') );

            // Redirect to main page
            return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/website-settings/' ) );
        }

        // Create Form
        $form = $ft->generate_form();

        $this->resources->css('accounts/edit');

        return $this->get_template_response('website-settings')
            ->kb( 5 )
            ->add_title( _('Website Settings') )
            ->select( 'accounts' )
            ->set( compact( 'account', 'form' ) );
    }

    /**
     * Other Settings
     *
     * @return TemplateResponse
     */
    protected function other_settings() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        // Setup objects
        $ft = new BootstrapForm( _('fOtherSettings') );

        // Get variables
        $settings = $account->get_settings(
            'ga-username'
            , 'ga-password'
            , 'gm-api-key'
            , 'gm-contact-page'
            , 'ashley-ftp-username'
            , 'ashley-ftp-password'
            , 'ashley-alternate-folder'
            , 'ashley-express-buyer-id'
            , 'ashley-express-ship-to'
            , 'facebook-url'
            , 'advertising-url'
            , 'zopim'
            , 'facebook-pages'
            , 'responsive-web-design'
            , 'ashley-express'
            , 'sendgrid-username'
            , 'sendgrid-password'
            , 'arb-subscription-id'
            , 'arb-subscription-amount'
            , 'arb-subscription-gateway'
            , 'yext-max-locations'
            , 'yext-customer-reviews'
            , 'cloudflare-domain'
        );

        $test_ashley_feed_url = "/accounts/test-ashley-feed/?aid={$account->id}&_nonce=" . nonce::create( 'test_ashley_feed' );
        $test_ashley_feed = ' (<a href="' . $test_ashley_feed_url . '" title="' . _('Test') . '" ajax="1">' . _('Test') . '</a>)';

        // Start adding fields
        $ft->add_field( 'text', _('FTP Username'), 'tFTPUsername', security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('FTP Password'), 'tFTPPassword', security::decrypt( base64_decode( $account->ftp_password ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Username'), 'tGAUsername', security::decrypt( base64_decode( $settings['ga-username'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Password'), 'tGAPassword', security::decrypt( base64_decode( $settings['ga-password'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Profile ID'), 'tGAProfileID', $account->ga_profile_id );
        $ft->add_field( 'text', _('Google Analytics Tracking Key'), 'tGATrackingKey', $account->ga_tracking_key );
        $ft->add_field( 'text', _('Google Maps API Key'), 'tGMAPIKey', $settings['gm-api-key'] );
        $ft->add_field( 'checkbox', _('Google Maps Contact Page'), 'cbGoogleMapsContactPage', $settings['gm-contact-page'] );
        $ft->add_field( 'text', _('WordPress Username'), 'tWPUsername', security::decrypt( base64_decode( $account->wordpress_username ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('WordPress Password'), 'tWPPassword', security::decrypt( base64_decode( $account->wordpress_password ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Ashley FTP Username') . $test_ashley_feed, 'tAshleyFTPUsername', security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Ashley FTP Password'), 'tAshleyFTPPassword', htmlspecialchars( security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY ) ) );
        $ft->add_field( 'checkbox', _('Ashley - Alternate Folder'), 'cbAshleyAlternateFolder', $settings['ashley-alternate-folder'] );
        $ft->add_field( 'text', _('Ashley Express - Ashley Account #'), 'tAshleyExpressBuyerCode', $settings['ashley-express-buyer-id'] );
        $ft->add_field( 'text', _('Ashley Express - Ship To'), 'tAshleyExpressShipTo', $settings['ashley-express-ship-to'] );
        $ft->add_field( 'text', _('Facebook Pages'), 'tFacebookPages', $settings['facebook-pages'] );
        $ft->add_field( 'text', _('Facebook Page Insights URL'), 'tFacebookURL', $settings['facebook-url'] );
        $ft->add_field( 'text', _('Advertising URL'), 'tAdvertisingURL', $settings['advertising-url'] );
        $ft->add_field( 'text', _('Zopim'), 'tZopim', $settings['zopim'] );
        $ft->add_field( 'checkbox', _('Responsive Web Design'), 'cbResponsiveWebDesign', $settings['responsive-web-design'] );
        $ft->add_field( 'checkbox', _('Enable Ashley Express Program'), 'cbAshleyExpress', $settings['ashley-express'] );
        $ft->add_field( 'text', _('Sendgrid Username'), 'tSendgridUsername', $settings['sendgrid-username'] );
        $ft->add_field( 'text', _('Sendgrid Password'), 'tSendgridPassword', $settings['sendgrid-password'] );
        $ft->add_field( 'text', _('ARB Subscription ID'), 'tARBSubscriptionID', $settings['arb-subscription-id'] );
        $ft->add_field( 'text', _('ARB Subscription Amount'), 'tARBSubscriptionAmount', $settings['arb-subscription-amount'] );

        if ( $this->user->has_permission( User::ROLE_ADMIN ) ) {
            $ft->add_field( 'select', 'ARB Subscription Gateway', 'sARBSubscriptionGateway', $settings['arb-subscription-gateway'] )
                ->options( array(
                    'gsr' => 'Grey Suit Retail'
                    , 'ir' => 'Imagine Retailer'
                ));
        }

        $ft->add_field( 'text', _('GeoMarketing Max. Locations'), 'tYextMaxLocation', $settings['yext-max-locations'] );
        $ft->add_field( 'text', _('GeoMarketing - Review Services'), 'tYextCustomerReviews', $settings['yext-customer-reviews'] );
        $ft->add_field( 'text', _('CloudFlare Domain'), 'tCloudFlareDomain', $settings['cloudflare-domain'] );

        $server = new Server();
        $servers = $server->get_all();
        $server_array = array( '' => '-- Select Server --' );

        foreach ( $servers as $server ) {
            $server_array[$server->id] = $server->name . ' (' . $server->ip . ')';
        }

        $ft->add_field( 'select', 'Server Host/IP', 'sServerId', $account->server_id )
            ->options( $server_array );

        if ( $ft->posted() ) {
            $account->server_id = $_POST['sServerId'];
            $account->ftp_username = security::encrypt( $_POST['tFTPUsername'], ENCRYPTION_KEY, true );
            $account->ftp_password = security::encrypt( $_POST['tFTPPassword'], ENCRYPTION_KEY, true );
            $account->ga_profile_id = $_POST['tGAProfileID'];
            $account->ga_tracking_key = $_POST['tGATrackingKey'];
            $account->wordpress_username = security::encrypt( $_POST['tWPUsername'], ENCRYPTION_KEY, true );
            $account->wordpress_password = security::encrypt( $_POST['tWPPassword'], ENCRYPTION_KEY, true );
            $account->user_id_updated = $this->user->id;

            $account->save();

            // Update settings
            $account->set_settings( array(
                'ga-username' => security::encrypt( $_POST['tGAUsername'], ENCRYPTION_KEY, true )
                , 'ga-password' => security::encrypt( $_POST['tGAPassword'], ENCRYPTION_KEY, true )
                , 'gm-api-key' => $_POST['tGMAPIKey']
                , 'gm-contact-page' => (int) isset( $_POST['cbGoogleMapsContactPage'] ) && $_POST['cbGoogleMapsContactPage']
                , 'ashley-ftp-username' => security::encrypt( $_POST['tAshleyFTPUsername'], ENCRYPTION_KEY, true )
                , 'ashley-ftp-password' => security::encrypt( $_POST['tAshleyFTPPassword'], ENCRYPTION_KEY, true )
                , 'ashley-alternate-folder' => (int) isset( $_POST['cbAshleyAlternateFolder'] ) && $_POST['cbAshleyAlternateFolder']
                , 'ashley-express-buyer-id' => $_POST['tAshleyExpressBuyerCode']
                , 'ashley-express-ship-to' => $_POST['tAshleyExpressShipTo']
                , 'facebook-pages' => $_POST['tFacebookPages']
                , 'facebook-url' => $_POST['tFacebookURL']
                , 'advertising-url' => $_POST['tAdvertisingURL']
                , 'zopim' => $_POST['tZopim']
                , 'responsive-web-design' => (int) isset( $_POST['cbResponsiveWebDesign'] ) && $_POST['cbResponsiveWebDesign']
                , 'ashley-express' => (int) isset( $_POST['cbAshleyExpress'] ) && $_POST['cbAshleyExpress']
                , 'arb-subscription-id' => $_POST['tARBSubscriptionID']
                , 'arb-subscription-amount' => $_POST['tARBSubscriptionAmount']
                , 'arb-subscription-gateway' => isset($_POST['sARBSubscriptionGateway']) ? $_POST['sARBSubscriptionGateway'] : $settings['arb-subscription-gateway']
                , 'yext-max-locations' => (int) $_POST['tYextMaxLocation']
                , 'yext-customer-reviews' => (int) $_POST['tYextCustomerReviews']
                , 'cloudflare-domain' => $_POST['tCloudFlareDomain']
            ));

            $this->notify( _('This account\'s "Other Settings" has been updated!') );

            // Redirect to main page
            return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/other-settings/' ) );
        }

        // Create Form
        $form = $ft->generate_form();

        $this->resources->css('accounts/edit');

        return $this->get_template_response('other-settings')
            ->kb( 6 )
            ->add_title( _('Other Settings') )
            ->select('accounts')
            ->set( compact( 'account', 'form' ) );
    }

    /**
     * Actions
     *
     * @return TemplateResponse
     */
    protected function actions() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Get api keys
        $settings = $account->get_settings( 'sendgrid-username', 'yext-subscription-id', 'cloudflare-zone-id' );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $this->resources
            ->css('accounts/edit')
            ->javascript('accounts/actions');

        return $this->get_template_response('actions')
            ->kb( 7 )
            ->add_title( _('Actions') )
            ->set( compact( 'account', 'settings' ) )
            ->select('accounts');

    }

    /**
     * Edit DNS for an account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function dns() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        if ( !$this->user->has_permission( User::ROLE_SUPER_ADMIN ) )
            return new RedirectResponse('/accounts/edit/?aid=' . $_GET['aid']);

        // Cloudflare
        library('cloudflare-api');
        $cloudflare = new CloudFlareAPI( $account );
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );
        $zone_id = $account->get_settings( 'r53-zone-id' );

        $v = new Validator( 'fEditDNS' );

        // Declare variables
        $domain_name = url::domain( $account->domain, false );
        $full_domain_name = $domain_name . '.';
        $errs = false;

        // Handle form actions
        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) && isset( $_POST['changes'] ) && is_array( $_POST['changes'] ) ) {
                $changes = array();

                if ( $cloudflare_zone_id ) {
                    foreach ( $_POST['changes'] as $dns_zone_id => $records ) {
                        switch( $_POST['changes'][$dns_zone_id]['action'] ) {
                            default:
                                continue;
                            break;

                            case '1':
                                $cloudflare->create_dns_record( $cloudflare_zone_id, $_POST['changes'][$dns_zone_id]['type'], $_POST['changes'][$dns_zone_id]['name'], $_POST['changes'][$dns_zone_id]['content'], $_POST['changes'][$dns_zone_id]['ttl'] );
                            break;

                            case '2':
                                $cloudflare->update_dns_record( $cloudflare_zone_id, $dns_zone_id, $_POST['changes'][$dns_zone_id]['type'], $_POST['changes'][$dns_zone_id]['name'], $_POST['changes'][$dns_zone_id]['content'], $_POST['changes'][$dns_zone_id]['ttl'] );
                            break;

                            case '0':
                                $cloudflare->delete_dns_record( $cloudflare_zone_id, $dns_zone_id );
                                continue;
                            break;
                        }
                    }
                } else {
                    $change_count = count( $_POST['changes']['name'] );

                    for( $i = 0; $i < $change_count; $i++ ) {
                        // Get the records
                        $records = format::trim_deep( explode( "\n", $_POST['changes']['records'][$i] ) );

                        switch( $_POST['changes']['action'][$i] ) {
                            default:
                                continue;
                            break;

                            case '1':
                                $action = 'CREATE';
                            break;

                            case '0':
                                $action = 'DELETE';
                            break;
                        }

                        /**
                         * @var string $action
                         */
                        $changes[] = $r53->prepareChange( $action, $_POST['changes']['name'][$i], $_POST['changes']['type'][$i], $_POST['changes']['ttl'][$i], $records );
                    }

                    $response = $r53->changeResourceRecordSets( $zone_id, $changes );
                }

            }
        }

        // Do an action like create DNS
        if ( isset( $_GET['a'] ) )
        switch ( $_GET['a'] ) {
            case 'create':
                // Create Cloudflare Account
                $cloudflare_zone_id = $cloudflare->create_zone($domain_name);

                $account->set_settings( array( 'cloudflare-zone-id' => $cloudflare_zone_id ) );

                $server = new Server();
                $server->get( $account->server_id );

                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', $full_domain_name, $server->nodebalancer_ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'MX', $full_domain_name, '0 mail.' . $full_domain_name, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'TXT', $full_domain_name, '"v=spf1 a mx ip4:199.79.48.137 ip4:208.53.48.135 ip4:199.79.48.25 ip4:162.218.139.218 ip4:162.218.139.219 ~all"', '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'mail.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'CNAME', 'www.' . $full_domain_name, $full_domain_name, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'ftp.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'cpanel.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'whm.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'webmail.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'webdisk.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));

                // See if they need to upgrade to pro
                if ( $account->shopping_cart ) {
                    $available_plans = $cloudflare->available_plans( $cloudflare_zone_id );
                    $upgrade_plan = false;

                    foreach ( $available_plans as $plan ) {
                        if ( 'pro' == $plan->legacy_id ) {
                            $upgrade_plan = $plan;
                            break;
                        }
                    }

                    $cloudflare->edit_zone( $cloudflare_zone_id, $upgrade_plan );
                }

                $cloudflare->change_security_level( $cloudflare_zone_id );
                $cloudflare->change_ipv6( $cloudflare_zone_id );
                $cloudflare->change_minify( $cloudflare_zone_id );
                $cloudflare->change_mirage( $cloudflare_zone_id );
                $cloudflare->change_polish( $cloudflare_zone_id );
            break;

            case 'delete':
                // We don't have something, stop!
                if ( empty( $zone_id ) )
                    break;

                // Delete the zone
                if ( !$r53->deleteHostedZone( $zone_id ) ) {
                    $error = $r53->getError();

                    if ( 'NoSuchHostedZone' != $error['code'] ) {
                        $errs = $error['error'];
                        break;
                    }
                }

                // Update the settings
                $account->set_settings( array( 'r53-zone-id' => '' ) );

                $zone_id = '';
            break;

            case 'transfer':
                // Create Cloudflare Account
                $cloudflare_zone_id = $cloudflare->create_zone($domain_name);

                $account->set_settings( array( 'cloudflare-zone-id' => $cloudflare_zone_id ) );

                $server = new Server();
                $server->get( $account->server_id );

                // Get records from Route 53
                $r53->getHostedZone( $zone_id );
                $records = $r53->listResourceRecordSets( $zone_id );

                if ( is_array( $records['ResourceRecordSets'] ) ) {
                    lib( 'misc/dns-sort' );
                    new DNSSort( $records['ResourceRecordSets'] );
                }
                if ( !empty( $records['ResourceRecordSets'] ) ) {
                    foreach ($records['ResourceRecordSets'] as $record) {
                        if (in_array($record['Type'], array('NS', 'SOA')))
                            continue;

                        $cloudflare->create_dns_record($cloudflare_zone_id, $record['Type'], $record['Name'], current($record['ResourceRecords']), $record['TTL'], url::domain($account->domain, false));
                    }
                } else {
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', $full_domain_name, $server->nodebalancer_ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'MX', $full_domain_name, '0 mail.' . $full_domain_name, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'TXT', $full_domain_name, '"v=spf1 a mx ip4:207.97.247.132 ip4:204.232.171.66 ~all"', '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'mail.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'CNAME', 'www.' . $full_domain_name, $full_domain_name, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'ftp.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'cpanel.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'whm.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'webmail.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                    $cloudflare->create_dns_record($cloudflare_zone_id, 'A', 'webdisk.' . $full_domain_name, $server->ip, '14400', url::domain($account->domain, false));
                }

                // See if they need to upgrade to pro
                if ( $account->shopping_cart ) {
                    $available_plans = $cloudflare->available_plans( $cloudflare_zone_id );
                    $upgrade_plan = false;

                    foreach ( $available_plans as $plan ) {
                        if ( 'pro' == $plan->legacy_id ) {
                            $upgrade_plan = $plan;
                            break;
                        }
                    }

                    $cloudflare->edit_zone( $cloudflare_zone_id, $upgrade_plan );
                }

                $cloudflare->change_security_level( $cloudflare_zone_id );
                $cloudflare->change_ipv6( $cloudflare_zone_id );
                $cloudflare->change_minify( $cloudflare_zone_id );
                $cloudflare->change_mirage( $cloudflare_zone_id );
                $cloudflare->change_polish( $cloudflare_zone_id );
            break;
        }

        if ( $cloudflare_zone_id ) {
            $records = $cloudflare->list_dns_records( $cloudflare_zone_id );
        } else {
            if (!empty($zone_id)) {
                $r53->getHostedZone($zone_id);
                $records = $r53->listResourceRecordSets($zone_id);

                if (is_array($records['ResourceRecordSets'])) {
                    lib('misc/dns-sort');
                    new DNSSort($records['ResourceRecordSets']);
                }
            }
        }

        // Put out notifications
        if ( isset( $response ) ) {
            if ( $response ) {
                $this->notify( _('Your DNS Zone file has been successfully updated!') );
            } else {
                $errs .= _('There was an error while trying to update your DNS Zone file. Please try again.');
            }
        }


        // Keep the resources that we need
        $this->resources
            ->javascript('accounts/dns');

        $zone_details = ( $cloudflare_zone_id ) ? $cloudflare->zone_details( $cloudflare_zone_id ) : false;

        return $this->get_template_response('dns')
            ->add_title( 'DNS' )
            ->kb( 8 )
            ->select( 'accounts', 'edit' )
            ->set( compact( 'account', 'zone_id', 'cloudflare_zone_id', 'errs', 'domain_name', 'full_domain_name', 'records', 'zone_details' ) );
    }

    /**
     * Notes page
     *
     * @return RedirectResponse|TemplateResponse
     */
    protected function notes() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Setup
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Make sure they have access
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $account_note = new AccountNote();

        // More setup
        $v = new BootstrapValidator( 'fAddNote' );
        $v->add_validation( 'taNote', 'req', _('The note may not be empty') );

        if ( $this->verified() ) {
            $account_note->website_id = $_GET['aid'];
            $account_note->user_id = $this->user->id;
            $account_note->message = $_POST['taNote'];
            $account_note->create();
            return new RedirectResponse( '/accounts/notes/?aid=' . $_GET['aid'] );
        }

        // Get notes
        $notes = $account_note->get_all( $_GET['aid'] );

        $this->resources
            ->javascript('accounts/notes')
            ->css('accounts/notes');

        return $this->get_template_response('notes')
            ->add_title( 'Notes' )
            ->kb( 3 )
            ->select( 'accounts' )
            ->set( compact( 'account', 'notes', 'v' ) );
    }

    /**
     * Add Email Template
     *
     * @return TemplateResponse
     */
    protected function add_email_template() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        $form = new BootstrapForm( 'fAddEmailTemplate' );
        $form->submit( _('Add Template') );

        $form->add_field( 'text', _('View Product Button'), 'tViewProductButton' )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'req', _('The "View Product Button" field is required') )
            ->add_validation( 'url', _('The "View Product Button" field must contain a valid URL') );

        $form->add_field( 'text', _('Product Color'), 'tProductColor' )
            ->attribute( 'maxlength', 6 )
            ->add_validation( 'req', _('The "Product Color" field is required') )
            ->add_validation( 'custom=[0-9a-fA-F]{3,6}', _('The "Product Color" must contain a valid hex number') );

        $form->add_field( 'text', _('Price Color'), 'tPriceColor' )
            ->attribute( 'maxlength', 6 )
            ->add_validation( 'req', _('The "Price Color" field is required') )
            ->add_validation( 'custom=[0-9a-fA-F]{3,6}', _('The "Price Color" must contain a valid hex number') );

        $form->add_field( 'textarea', _('Default Template'), 'taDefaultTemplate' )
            ->add_validation( 'req', _('The "Default Template" field is required') );

        $form->add_field( 'textarea', _('Product Template'), 'taProductTemplate' )
            ->add_validation( 'req', _('The "Product Template" field is required') );

        $form->add_field( 'text', _('Template Image'), 'tTemplateImage' )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'req', _('The "Template Image" field is required') )
            ->add_validation( 'url', _('The "Template Image" field must contain a valid URL') );

        $form->add_field( 'text', _('Template Image Thumbnail'), 'tTemplateImageThumbnail' )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'req', _('The "Template Image Thumbnail" field is required') )
            ->add_validation( 'url', _('The "Template Image Thumbnail" field must contain a valid URL') );

        if ( $form->posted() ) {
            // Do stuff
            $account->set_settings( array(
                'view-product-button' => $_POST['tViewProductButton']
                , 'product-color' => $_POST['tProductColor']
                , 'product-price-color' => $_POST['tPriceColor']
            ) );

            // Update default template
            $email_template = new EmailTemplate();
            $email_template->get_default( $account->id );

            $email_template->template = $_POST['taDefaultTemplate'];
            $email_template->image = $_POST['tTemplateImage'];
            $email_template->thumbnail = $_POST['tTemplateImageThumbnail'];
            $email_template->save();

            // Create product template
            $product_template = new EmailTemplate();
            $product_template->name = 'Product Offer';
            $product_template->template = $_POST['taProductTemplate'];
            $product_template->image = $_POST['tTemplateImage'];
            $product_template->thumbnail = $_POST['tTemplateImageThumbnail'];
            $product_template->type = 'product';
            $product_template->create();

            // Add association
            $product_template->add_association( $account->id );

            // Once done
            $this->notify( _('Email template has been successfully added!') );
            return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/actions/' ) );
        }

        $template_response = $this->get_template_response('add-email-template')
            ->add_title( _('Add Email Template') )
            ->set( array( 'form' => $form->generate_form() ) )
            ->select('accounts');

        return $template_response;
    }

    /***** REDIRECTS *****/

    /**
     * Control
     *
     * @return RedirectResponse
     */
    protected function control() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        set_cookie( 'wid', $_GET['aid'], 172800 ); // 2 days
        set_cookie( 'action', base64_encode( security::encrypt( 'bypass', ENCRYPTION_KEY ) ), 172800 ); // 2 days

        $url = 'http://' . ( ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? str_replace( 'admin', 'account', $_SERVER['HTTP_X_FORWARDED_HOST'] ) : str_replace( 'admin', 'account', $_SERVER['HTTP_HOST'] ) );

        return new RedirectResponse( $url );
    }

    /**
     * Delete categories and products
     *
     * @return RedirectResponse
     */
    protected function install_website() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Make sure it has enough memory to install
		ini_set('memory_limit', '256M');

        // Get Account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Make sure it's not already installed
		if ( '0' != $account->version && !empty( $account->version ) )
            return new RedirectResponse('/accounts/');

        // Get install service
        $install_service = new InstallService();
        $install_service->install_website( $account, $this->user->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Let them know it's been installed
        $this->notify( _('Website has been successfully installed') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/edit/' ) );
    }

    /**
     * Delete categories and products
     *
     * @return RedirectResponse
     */
    protected function install_package() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get Account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Make sure a package is selected
        if ( 0 == $account->company_package_id )
            return new RedirectResponse('/accounts/');

        // Get install service
        $install_service = new InstallService();
        $install_service->install_package( $account, $this->user->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Let them know it's been installed
        $this->notify( _('The website package has been successfully installed') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/actions/' ) );
    }

    /**
     * Delete categories and products
     *
     * @return RedirectResponse
     */
    protected function delete_categories_and_products() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) && $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) )
            return new RedirectResponse('/accounts/');

        // Get the website products and categories
        $account_product = new AccountProduct();
        $account_category = new AccountCategory();

        $account_product->deactivate_by_account( $_GET['aid'] );
        $account_category->delete_by_account( $_GET['aid'] );

        // Clear CloudFlare Cache
        $account = new Account();
        $account->get( $_GET['aid'] );
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $this->notify( _('All categories and products have been removed.') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/edit/' ) );
    }

    /**
     * Cancel an account
     *
     * @return RedirectResponse
     */
    protected function cancel() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) && $this->user->has_permission( User::ROLE_SUPER_ADMIN ) )
            return new RedirectResponse('/accounts/');

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Get settings
        $sendgrid_username = $account->get_settings( 'sendgrid-username' );

        if ( !empty( $sendgrid_username ) ) {
            library('sendgrid-api');
            $sendgrid = new SendGridAPI( $account );
            $sendgrid->setup_subuser();
            $sendgrid->subuser->delete( $sendgrid_username );
        }

        // Deactivate account
        $account->status = Account::STATUS_INACTIVE;
        $account->user_id_updated = $this->user->id;
        $account->save();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Give them a notification
        $this->notify( _('The account, "' . $account->title . '", has been deactivated.' ) );

        // Send an email to let people know an account cancelled
        $company = new Company();
        $company->get( $account->company_id );

        if ( !empty( $company->email ) ) {
            $subject = $company->name . ' Account Canceled';

            $message = "Billing has been terminated, billing has stopped and $account->title has been removed from the platform.";
            $message .= "\n\n";
            $message .= "-$company->name";

            library('sendgrid-api'); SendgridApi::send( $company->email, $subject, $message );
        }

        // Redirect them to accounts page
        return new RedirectResponse('/accounts/');
    }

    /**
     * Run Ashley Feed
     *
     * @return RedirectResponse
     */
    protected function run_ashley_feed() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Run the feed
        $ashley_specific_feed = new AshleySpecificFeedGateway();
        $ashley_specific_feed->run( $account );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Give them a notification
        $this->notify( _('The Ashley Feed has been successfully run!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/actions/' ) );
    }

    /**
     * Test Ashley Feed
     *
     * @return AjaxResponse
     */
    protected function test_ashley_feed() {
        $response = new AjaxResponse( $this->verified() );
        if ($response->has_error())
            return $response;

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Get the files
        $ashley_specific_feed = new AshleySpecificFeedGateway();

        $ftp = $ashley_specific_feed->get_ftp( $account );

        // Yay
        $files = $ftp->raw_list();
        $file_count = count( $files );

        // FTP Settings
        $settings = $account->get_settings( 'ashley-ftp-username', 'ashley-ftp-password', 'ashley-alternate-folder' );
        $username = urlencode( security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY ) );
        $password = urlencode( security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY ) );
        $folder = str_replace( 'CE_', '', $username );
        if ( '-' != substr( $folder, -1 ) )
            $folder .= '-';
        $subfolder = ( '1' == $settings['ashley-alternate-folder'] ) ? 'Outbound/Items' : 'Outbound';
        $base_path = "/CustEDI/$folder/$subfolder/";

        // Create response
        $message = "Got {$file_count} file(s):";
        foreach ($files as $f) {
            $message .= "<br> {$f['name']} - {$f['size']} <a href=\"ftp://{$username}:{$password}@ftp.ashleyfurniture.com/{$base_path}{$f['name']}\" target=\"_blank\">View</a>";
        }
        $response->notify( $message );

        return $response;
    }

    /**
     * Reorganize categories
     *
     * @return RedirectResponse
     */
    protected function reorganize_categories() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get the account category
        $account_category = new AccountCategory();
        $account_category->reorganize_categories( $_GET['aid'], new Category() );

        // Clear CloudFlare Cache
        $account = new Account();
        $account->get( $_GET['aid'] );
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Give them a notification
        $this->notify( _('The categories have been successfully reorganized!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/actions/' ) );
    }



    /***** AJAX *****/

    /**
     * Create email marketing account
     *
     * @return AjaxResponse
     */
    protected function create_email_marketing_account() {
        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Get email plans
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $account );
        $sendgrid->setup_subuser();

        $username = format::slug( $account->title );

        $user = new User();
        $user->get( $account->user_id );

        $password = substr( $account->id . md5(microtime()), 0, 10 );
        list( $first_name, $last_name ) = explode( ' ', $user->contact_name, 2 );
        if ( empty( $last_name ) ) {
            $response = new AjaxResponse( true );
            $response->notify( _('Please specify user First Name and Last Name before creating an Email Marketin Account'), false );
            return $response;
        }

        $settings = $account->get_settings( 'address', 'city', 'state', 'zip', 'from_email', 'from_name' );

        $settings = $account->get_settings( array('address', 'city', 'state', 'zip') );
        if ( !$settings['address'] || !$settings['city'] || !$settings['state'] || !$settings['zip'] ) {
            $response = new AjaxResponse( true );
            $response->notify( _('Please specify your Address, City, State and ZIP code before creating an Email Marketing Account'), false );
            return $response;
        }

        $phone = ( empty( $user->work_phone ) ) ? $user->cell_phone : $user->work_phone;
        if ( empty( $phone ) )
            $phone = '8185551234';

        $sendgrid->subuser->add( $username, $password, $user->email, $first_name, $last_name, $settings['address'], $settings['city'], $settings['state'], $settings['zip'], 'US', $phone, $account->domain, $account->title );

        // Add IP Address
        $sendgrid->subuser->send_ip( $username );

        // Create identity
        $sendgrid->setup_sender_address();
        $name = ( empty ( $settings['from_name'] ) ) ? $user->contact_name : $settings['from_name'];
        $email = ( empty( $settings['from_email'] ) ) ? 'noreply@' . url::domain( $account->domain, false ) : $settings['from_email'];

        // Add sender address
        $sendgrid->sender_address->add( $account->id, $name, $email, $settings['address'], $settings['city'], $settings['state'], $settings['zip'], 'US' );

        $account->set_settings( array( 'sendgrid-username' => $username, 'sendgrid-password' => $password ) );

        // Now add all email lists
        $sendgrid = new SendGridAPI( $account, $username, $password );
        $sendgrid->setup_list();
        $sendgrid->setup_email();
        $email_list = new EmailList();
        $email_lists = $email_list->get_by_account( $account->id );

        foreach ( $email_lists as $email_list ) {
            $sendgrid->list->add( $email_list->name );

            // Now import subscribers

            $email = new Email();
            $emails = $email->get_by_email_list( $email_list->id );

            $email_chunks = array_chunk( $emails, 1000 );

            foreach ( $email_chunks as $emails ) {
                $sendgrid->email->add( $email_list->name, $emails );
            }
        }

        $sendgrid->setup_filter();
        $sendgrid->filter->event_notify( 0, 0, 0, 0, 0, 0, 0, 1, 0, 'https://api.imagineretailer.com/?api_key=0e63566eb07d3369836e2b59e85b9845&method=sendgrid_event_callback&aid=' . $account->id );

        // Create response
        $response = new AjaxResponse( true );

        // Add notification
        $response->notify( _('Email Marketing account successfully created') );

        return $response;
    }

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get Models
        $account = new Account();
        $checklist = new Checklist();

        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );

        // Add Where's
        if ( isset( $_SESSION['accounts']['state'] ) && 'all' != $_SESSION['accounts']['state'] ) {
            switch ( $_SESSION['accounts']['state'] ) {
                default:
                case 'live':
                    $state = 1;
                break;

                case 'staging':
                    $state = 0;
                break;

                case 'inactive':
                    $state = -1;
                break;
            }

            // Live accounts
            $dt->add_where( ( -1 == $state ) ? ' AND a.`status` = 0' : ' AND a.`status` = 1 AND a.`live` = ' . $state );
        } else {
            $dt->add_where( ' AND a.`status` = 1' );
        }

        // Add search
        if ( isset( $_SESSION['accounts']['search'] ) ) {
            $_GET['sSearch'] = $_SESSION['accounts']['search'];
            $dt->search( array( 'a.`title`' => false, 'a.`domain`' => false, 'b.`contact_name`' => false, 'c.`contact_name`' => false ) );
        }

        // If they are below 8, that means they are a partner
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            $dt->add_where( ' AND b.`company_id` = ' . $this->user->company_id );

		// What other sites we might need to omit
		$omit_sites = ( !$this->user->has_permission( User::ROLE_ADMIN ) ) ? ', 96, 114, 115, 116' : '';

		// Omitting all demo sites for non super-admins
		//if(!$this->user->has_permission( User::ROLE_SUPER_ADMIN )){
		//	$demo_websites = $account->get_demo_websites();
		//	$omit_sites .=', '.$demo_websites;
		//}

		// Form the where
		$dt->add_where( " AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" );


        // Get accounts
        $accounts = $account->list_all( $dt->get_variables() );
        $dt->set_row_count( $account->count_all( $dt->get_count_variables() ) );

        // Get account ids with incomplete checklists
        $incomplete_checklists = $checklist->get_incomplete();

        // Set initial data
        $data = false;

        if ( is_array( $accounts ) )
        foreach ( $accounts as $a ) {
            $image = '<img src="/images/icons/companies/' . $a->company_id . '.gif" alt="" width="24" height="24" />';

            // Get the store name if necessary
            $store_name = ( $a->title == $a->store_name || empty( $a->store_name ) ) ? '' : ' (' . $a->store_name . ')';

            // Get the phone
            $contact_title = ( empty( $a->phone ) ) ? _('No Phone') : $a->phone;

            $title = '<a href="http://' . $a->domain . '/" target="_blank"><strong title="' . $a->domain . ' - ' . $a->online_specialist . '">' . stripslashes($a->title) . stripslashes($store_name) . '</strong></a><br />';
            $title .= '<span class="web-actions" style="display: block">';

            if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) )
                $title .= '<a href="/accounts/edit/?aid=' . $a->id . '" title="' . _('Edit') . ' ' . $a->title . '">' . _('Edit') . '</a> | ';

            $title .= '<a href="/accounts/control/?aid=' . $a->id . '" title="' . _('Control') . ' ' . $a->title . '" target="_blank">' . _('Control Account') . '</a>';

            if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) {
                $title .= ' | <a href="/users/control/?uid=' . $a->user_id . '" title="' . _('Control User') . '" target="_blank">' . _('Control User') . '</a> | ';
                $title .= '<a href="/accounts/notes/?aid=' . $a->id . '" title="' . _('Notes') . '" target="_blank">' . _('Notes') . '</a>';

                if ( isset( $incomplete_checklists[$a->id] ) )
                    $title .= ' | <a href="/checklists/checklist/?cid=' . $incomplete_checklists[$a->id] . '" title="' . _('Checklists') . '" target="_blank">' . _('Checklist') . '</a>';
            }

            $title .= '</span>';

            $data[] = array(
                $image
                , $title
                , '<a href="/users/add-edit/?uid=' . $a->user_id . '" title="' . $contact_title . '">' . $a->contact_name . '</a>'
                , $a->online_specialist
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete a note
     *
     * @return AjaxResponse
     */
    protected function delete_note() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['anid'] ), _('Failed to delete note') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get the note
        $account_note = new AccountNote();
        $account_note->get( $_GET['anid'] );

        // Deactivate user
        if ( $account_note->id ) {
            $account_note->remove();
        }

        return $response;
    }

    /**
     * AutoComplete
     *
     * @return AjaxResponse
     */
    protected function autocomplete() {
        $ajax_response = new AjaxResponse( $this->verified() );

        // Get the right suggestions for the right type
        switch ( $_GET['type'] ) {
            case 'domain':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_GET['term'], 'domain', $this->user, $status );
            break;

            case 'store_name':
                $results = $this->user->autocomplete( $_GET['term'], 'store_name' );

                if ( is_array( $results ) )
                foreach ( $results as &$result ) {
                    $result['store_name'] = $result['store_name'];
                }
            break;

            case 'title':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_GET['term'], 'title', $this->user, $status );
            break;
        }

        $ajax_response->add_response( 'objects', $results );

        return $ajax_response;
    }

    /**
     * Reset product prices
     *
     * Set all AccountProducts prices to 0 for a specific Account
     *
     * @return RedirectResponse
     */
    protected function reset_product_prices() {
            // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) && $this->user->has_permission( User::ROLE_SUPER_ADMIN ) )
            return new RedirectResponse( '/accounts/' );

        $account_product = new AccountProduct();
        $account_product->reset_price_by_account( $_GET['aid'] );

        // Clear CloudFlare Cache
        $account = new Account;
        $account->get( $_GET['aid'] );
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $this->notify( _('All account prices has been reseted.') );

        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

    /**
     * Run Ashley Express Feed
     *
     * @return RedirectResponse
     */
    protected function run_ashley_express_feed() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Run the feed
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_flag_products( $account );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Give them a notification
        $this->notify( _('The Ashley Express Feed has been successfully run!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/actions/' ) );
    }

    /**
     * Run Ashley Express Order Status Feed
     *
     * @return RedirectResponse
     */
    protected function run_ashley_express_order_status() {
        // Make sure it was a valid request
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Run the feed
        // Ashley Express Feed - Order Acknowledgement
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_order_acknowledgement( $account );
        unset( $ashley_express_feed );
        gc_collect_cycles();

        // Ashley Express Feed - Order ASN
        $ashley_express_feed = new AshleyExpressFeedGateway();
        $ashley_express_feed->run_order_asn( $account );
        unset( $ashley_express_feed );
        gc_collect_cycles();

        // Give them a notification
        $this->notify( _('The Ashley Express Feed Orders Check has been successfully run!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/actions/' ) );
    }

    /**
     * Install New Theme
     *
     * @throws Exception
     */
    public function install_new_theme() {
        $sources = array(
            'unlocked' => 1352
            , 'upgrade' => 1415
            , 'upgrade2' => 1422
        );

        // Template account
        $source_account = new Account();
        $source_account->get( $sources[$_GET['source']] );

        // Where are we installing the new Theme
        $account = new Account();
        $account->get( $_GET['aid'] );

        if ( !$source_account->id || !$account->id ) {
            throw new Exception( "Account #{$_GET['aid']} or Source Account #{$_GET['source']} not found." );
        }

        // Copy LESS
        $less = $source_account->get_settings( 'css' );
        $account->set_settings( array( 'css' => $less ) );

        // If they have a custom config for Home Page Layout
        // We need to install Trending Products Feature
        $layout = $account->get_settings( 'layout' );
        $layout = json_decode( $layout );
        if ( $layout ) {
            $found = false;
            foreach ( $layout as $element ) {
                if ( $element->name == 'popular-items' ) {
                    $found = true;
                }
            }
            if ( !$found ) {
                $layout[] = (object) array( 'name' => 'popular-items', 'disabled' => 0 );
                $account->set_settings( array(
                    'layout' => json_encode( $layout )
                ) );
            }
        }

        // Add Index
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );
        if ( !$username )
            throw new Exception( "Can't find username for this account. You will need to set new index.php manually." );

        // Get where is hosted
        $server = new Server;
        $server->get( $account->server_id );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
        ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
        ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

        // Setup as root
        ssh2_exec( $ssh_connection, "sudo su -" );

        ssh2_exec( $ssh_connection, "sudo mv /home/$username/public_html/index.php /home/$username/public_html/index.php.old" );
        ssh2_exec( $ssh_connection, "sudo cp /gsr/systems/gsr-site/copy/index.php /home/$username/public_html/index.php" );
        ssh2_exec( $ssh_connection, "sudo sed -i 's/\\[website_id\\]/{$account->id}/g' /home/$username/public_html/index.php" );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        echo 'Finished!'; die;
    }

    /**
     * Reactivate account (ghosted or canceled)
     *
     * @return RedirectResponse
     */
    public function reactivate() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/accounts/' );

        $account = new Account();
        $account->get( $_GET['aid'] );

        if (  $account->id ) {
            $account->status = Account::STATUS_ACTIVE;
            $account->save();

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $account );
                $cloudflare->purge( $cloudflare_zone_id );
            }
        }

        $this->notify( _("Account reactivated") );

        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

    /**
     * Resynchronize Email Lists (Sendgrid and others)
     *
     * @return RedirectResponse
     */
    public function resync_email_lists() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/accounts/' );

        $account = new Account();
        $account->get( $_GET['aid'] );

        if (  $account->id )
            $account->resync_sendgrid_lists();

        $this->notify( _("Email Lists Synced") );

        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

    /**
     * Created Index for products
     *
     * @return RedirectResponse
     */
    public function index_products() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/accounts/' );

        $account = new Account();
        $account->get( $_GET['aid'] );

        if (  $account->id ) {
            $index = new IndexProducts();
            $index->index_website( $account->id );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $account );
                $cloudflare->purge( $cloudflare_zone_id );
            }
        }

        $this->notify( _("All Products Indexed!") );
        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

    /**
     * Cancel YEXT Subscription
     *
     * @return RedirectResponse
     */
    public function cancel_yext_subscription() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/accounts/' );

        $account = new Account();
        $account->get( $_GET['aid'] );

        if ( !$account->id )
            return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );

        library('yext');
        $yext = new YEXT( $account );

        $yext_website_subscription_id = $account->get_settings( 'yext-subscription-id' );
        if ( !$yext_website_subscription_id )
            return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );

        $subscription = $yext->get( "subscriptions/{$yext_website_subscription_id}" );
        if ( !isset($subscription->id) )
            return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );

        $subscription->status = 'CANCELED';
        $yext->put( "subscriptions/{$yext_website_subscription_id}", $subscription );

        $account->set_settings( array( 'yext-subscription-id' => '' ) );

        $this->notify( _("Subscription cancelled.") );
        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

    /**
     * Purge Cloudflare Cache
     *
     * @return RedirectResponse
     */
    public function purge_cloudflare_cache() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/accounts/' );

        $account = new Account();
        $account->get( $_GET['aid'] );

        $cloudflare_zone_id = $account->get_settings('cloudflare-zone-id');

        if ( !$cloudflare_zone_id )
            return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );

        library('cloudflare-api');
        $cloudflare = new CloudFlareAPI( $account );
        $cloudflare->purge( $cloudflare_zone_id );

        $this->notify( _("CloudFlare cache purged.") );
        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }
}