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
            ->javascript('accounts/list')
            ->css_url( Config::resource('jquery-ui') );

        return $this->get_template_response( 'index' )
            ->kb( 1 )
            ->select( 'accounts', 'view' );
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
        $ft = new FormTable( 'fAddAccount' );

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

        $ft->add_field( 'select', _('Type'), 'sType' )
            ->options( array(
                _('Furniture') => _('Furniture')
                , _('RTO') => _('RTO')
                , _('EVR') => _('EVR')
                , _('High Impact') => _('High Impact')
            ));

        // Update the account if posted
        if ( $ft->posted() ) {
            $account->user_id = $_POST['sUserID'];
            $account->os_user_id = $_POST['sOnlineSpecialistID'];
            $account->domain = $_POST['tDomain'];
            $account->title = $_POST['tTitle'];
            $account->type = $_POST['sType'];
            $account->create();

            // Needs to create a checklist
            $checklist = new Checklist();
            $checklist->website_id = $account->id;
            $checklist->type = 'Website Setup';
            $checklist->create();

            // Add checklist website items
            $checklist_website_item = new ChecklistWebsiteItem();
            $checklist_website_item->add_all_to_checklist( $checklist->id );

            $this->notify( _('Your account was successfully created!') );

            return new RedirectResponse('/accounts/');
        }

        return $this->get_template_response( 'add' )
            ->kb( 2 )
            ->add_title( _('Add') )
            ->select( 'accounts', 'add' )
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

        $v = new Validator( 'fEditAccount' );
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
                    $account->phone = $_POST['tPhone'];
                    $account->products = $_POST['tProducts'];
                    $account->plan_name = $_POST['tPlan'];
                    $account->plan_description = $_POST['taPlanDescription'];
                    $account->pages = (int) isset( $_POST['cbPages'] );
                    $account->shopping_cart = (int) isset( $_POST['cbShoppingCart'] );
                    $account->product_catalog = (int) isset( $_POST['cbProductCatalog'] );
                    $account->room_planner = (int) isset( $_POST['cbRoomPlanner'] );
                    $account->blog = (int) isset( $_POST['cbBlog'] );
                    $account->craigslist = (int) isset( $_POST['cbCraigslist'] );
                    $account->email_marketing = (int) isset( $_POST['cbEmailMarketing'] );
                    $account->domain_registration = (int) isset( $_POST['cbDomainRegistration'] );
                    $account->mobile_marketing = (int) isset( $_POST['cbMobileMarketing'] );
                    $account->additional_email_Addresses = (int) isset( $_POST['cbAdditionalEmailAddresses'] );
                    $account->social_media = (int) isset( $_POST['cbSocialMedia'] );

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

        // Create form elements
        $account_title = new FormTable_Text( false, _('tTitle'), $account->title );

        $fts = new FormTable_Select( false, 'sUserID', $account->user_id );
        $users = $fts->options( $users );

        $phone = new FormTable_Text( false, _('tPhone'), $account->phone );

        $products = new FormTable_Text( false, _('tProducts'), $account->products );

        $fts = new FormTable_Select( false, 'sOSUserID', $account->os_user_id );
        $os_users = $fts->options( $os_users );

        $plan = new FormTable_Text( _('Plan'), 'tPlan', $account->plan_name );
        $plan_description = new FormTable_Textarea( _('Plan Description'), 'taPlanDescription', $account->plan_description );

        // Address
        $address_settings = $account->get_settings( 'address', 'city', 'state', 'zip' );

        $address = new FormTable_Text( _('Address'), 'tAddress', $address_settings['address'] );
        $address->attribute( 'placeholder', _('Address') );

        $city = new FormTable_Text( _('City'), 'tCity', $address_settings['city'] );
        $city->attribute( 'placeholder', _('City') );

        $state = new FormTable_Select( _('State'), 'sState', $address_settings['state'] );
        $state->options( data::states( false ) );

        $zip = new FormTable_Text( _('Zip'), 'tZip', $address_settings['zip'] );
        $zip->attribute( 'placeholder', _('Zip') );

        // Validation
        foreach ( $fields as $field ) {
            $$field->validation( $v );
        }

        $owner = new User();
        $owner->get( $account->user_id );

        // Features
        $features = array(
            'pages'
            , 'shopping_cart'
            , 'product_catalog'
            , 'room_planner'
            , 'blog'
            , 'craigslist'
            , 'email_marketing'
            , 'domain_registration'
            , 'mobile_marketing'
            , 'additional_email_addresses'
            , 'social_media'
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
                , 'checkbox' => '<input type="checkbox" name="' . $checkbox_form_name . '" id="' . $checkbox_form_name . '" class="hidden" value="1"' . $checked . ' />'
                , 'selected' => $selected
            );
        }

        // Include Resources
        $this->resources
            ->javascript('accounts/edit')
            ->css('accounts/edit');

        $template_response = $this->get_template_response('edit')
            ->kb( 4 )
            ->select( 'accounts' )
            ->add_title( _('Edit') )
            ->set( compact( 'account', 'owner', 'checkboxes', 'errs' ) );

        foreach ( $fields as $field ) {
            $template_response->set( $field, $$field->generate() );
        }

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
        $ft = new FormTable( 'fWebsiteSettings' );

        // Get variables
        $industries = $industry->get_all();
        $account_industries = $account->get_industries();

        $company_packages = $cp->get_all( $account->id );
        $packages = array( '' => _('Select a Package') );

        $custom_image_size = $account->get_settings( 'custom-image-size' );

        // Start adding fields
        $ft->add_field( 'text', _('Domain'), 'tDomain', $account->domain )
            ->add_validation( 'tDomain', 'req', _('The "Domain" field is required') );

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
            ->attribute( 'multiple', 'multiple')
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

            if ( !$account->live && isset( $_POST['cbLive'] ) && $_POST['cbLive'] ) {
                // SSH Connection
                $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
                ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

                $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );
                $domain = url::domain( $account->domain, false );

                ssh2_exec( $ssh_connection, "sed -i 's/\[domain\]/$domain/g' /home/$username/public_html/.htaccess" );
                ssh2_exec( $ssh_connection, "sed -i 's/#Rewrite/Rewrite/g' /home/$username/public_html/.htaccess" );

                $account->live = 1;
            } else {
                $account->live = isset( $_POST['cbLive'] );
            }

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
            ->select('accounts')
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
        $ft = new FormTable( _('fOtherSettings') );

        // Get variables
        $settings = $account->get_settings(
            'ga-username'
            , 'ga-password'
            , 'gm-api-key'
            , 'gm-contact-page'
            , 'ashley-ftp-username'
            , 'ashley-ftp-password'
            , 'ashley-alternate-folder'
            , 'facebook-url'
            , 'advertising-url'
            , 'trumpia-username'
            , 'trumpia-password'
            , 'zopim'
            , 'facebook-pages'
            , 'responsive-web-design'
        );

        $test_ashley_feed_url = url::add_query_arg( 'aid', $account->id, '/accounts/test-ashley-feed/' );
        $test_ashley_feed = ' (<a href="' . $test_ashley_feed_url . '#dTestAshleyFeed" title="' . _('Test') . '" rel="dialog" ajax="1">' . _('Test') . '</a>)';

        // Start adding fields
        $ft->add_field( 'text', _('FTP Username'), 'tFTPUsername', security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY ) );
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
        $ft->add_field( 'text', _('Facebook Pages'), 'tFacebookPages', $settings['facebook-pages'] );
        $ft->add_field( 'text', _('Facebook Page Insights URL'), 'tFacebookURL', $settings['facebook-url'] );
        $ft->add_field( 'text', _('Advertising URL'), 'tAdvertisingURL', $settings['advertising-url'] );
        $ft->add_field( 'text', _('Trumpia Username'), 'tTrumpiaUsername', $settings['trumpia-username'] );
        $ft->add_field( 'text', _('Trumpia Password'), 'tTrumpiaPassword', $settings['trumpia-password'] );
        $ft->add_field( 'text', _('Zopim'), 'tZopim', $settings['zopim'] );
        $ft->add_field( 'checkbox', _('Responsive Web Design'), 'cbResponsiveWebDesign', $settings['responsive-web-design'] );

        if ( $ft->posted() ) {
            $account->ftp_username = security::encrypt( $_POST['tFTPUsername'], ENCRYPTION_KEY, true );
            $account->ga_profile_id = $_POST['tGAProfileID'];
            $account->ga_tracking_key = $_POST['tGATrackingKey'];
            $account->wordpress_username = security::encrypt( $_POST['tWPUsername'], ENCRYPTION_KEY, true );
            $account->wordpress_password = security::encrypt( $_POST['tWPPassword'], ENCRYPTION_KEY, true );

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
                , 'facebook-pages' => $_POST['tFacebookPages']
                , 'facebook-url' => $_POST['tFacebookURL']
                , 'advertising-url' => $_POST['tAdvertisingURL']
                , 'trumpia-username' => $_POST['tTrumpiaUsername']
                , 'trumpia-password' => $_POST['tTrumpiaPassword']
                , 'zopim' => $_POST['tZopim']
                , 'responsive-web-design' => (int) isset( $_POST['cbResponsiveWebDesign'] ) && $_POST['cbResponsiveWebDesign']
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

        // Get trumpia api key
        $settings = $account->get_settings( 'trumpia-username', 'craigslist-customer-id', 'sendgrid-username' );

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
     * Craigslist
     *
     * @return TemplateResponse
     */
    protected function craigslist() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Load the library
        library( 'craigslist-api' );

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        $craigslist_market_link = new CraigslistMarketLink();
        $craigslist_market = new CraigslistMarket();
        $craigslist_api = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        // Get variables
        $craigslist_markets = $craigslist_market->get_all();
        $account_market_links = array();

        // Add validation
        $v = new Validator( 'fLinkMarket' );

        $v->add_validation( 'sMarketId', 'req', _('The "Market" field is required') );
        $v->add_validation( 'sCLCategoryId', 'req', _('The "Category" field is required') );

        $errs = false;

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                // Get the craigslist market
                $craigslist_market->get( $_POST['sMarketId'] );

                // Get addresses
                $account_pagemeta = new AccountPagemeta();
                $addresses = @unserialize( $account_pagemeta->get_by_account_and_keys( $account->id, 'addresses' ) );

                // Create as many locations as we can, up to 10;
                if ( is_array( $addresses ) )
                foreach ( $addresses as $addr ) {
                    $locations[] = $addr['city'] . ', ' . $addr['state'];
                }

                $locations[] = $craigslist_market->market;

                if ( !empty( $craigslist_market->area ) )
                    $locations[] = $craigslist_market->city . ', ' . $craigslist_market->state;

                $locations[] = $craigslist_market->city;
                $locations[] = $craigslist_market->city . ' Area';

                // Finalize the locations to 10 at the max
                $locations = array_slice( array_unique( $locations ), 0, 10 );

                // Add the store link
                if ( 1 == $account->pages && !empty( $account->domain ) ) {
                    $url = 'http://' . $account->domain . '/';
                    $store['storelink'] = $url;
                }

                // See if they have a remote logo
                $remote_logo = stristr( $account->logo, 'http' );

                /**
                 * @var string $url
                 */
                // Add a store logo if they have one
                if ( !empty( $website['logo'] ) && ( $remote_logo || isset( $url ) ) ) {
                    $logo = ( $remote_logo ) ? $website['logo'] :  "{$url}custom/uploads/images/" . $website['logo'];
                    $store['storelogo'] = $logo;
                }

                // Set the store name -- everyone should have one of these
                $store['storename'] = $account->title;

                // Set the phone if they have one
                if ( !empty( $account->phone ) )
                    $store['storephone'] = $account['phone'];

                $craigslist_customer_id = $account->get_settings('craigslist_customer_id');

                if ( empty( $craigslist_customer_id ) ) {
                    // Create the customer
                    $craigslist_customer_id = $craigslist_api->add_customer( $account->title );

                    if ( $craigslist_customer_id )
                        $account->set_settings( array( 'craigslist-customer-id' => $craigslist_customer_id ) );
                }

                // Get the market id
                $market_id = $craigslist_api->add_market( $craigslist_customer_id, $craigslist_market->cl_market_id, $locations, $_POST['sCLCategoryId'], $store );

                // Link it in our database
                if ( $market_id ) {
                    $craigslist_market_link->website_id = $account->id;
                    $craigslist_market_link->craigslist_market_id = $_POST['sMarketId'];
                    $craigslist_market_link->market_id = $market_id;
                    $craigslist_market_link->cl_category_id = $_POST['sCLCategoryId'];
                    $craigslist_market_link->create();

                    $this->notify( _('Your account has been successfully linked to the market and category!') );
                } else {
                    $errs .= _('An error occurred while trying to link your market. Please contact a system administrator.');
                }
            }
        }

        // Get account market links
        $account_craigslist_market_links = $craigslist_market_link->get_by_account( $account->id );

        /**
         * @var CraigslistMarketLink $acml
         */
        if ( is_array( $account_craigslist_market_links ) ) {
            $category_markets = array();

            foreach ( $account_craigslist_market_links as $acml ) {
                if ( !isset( $category_markets[$acml->cl_market_id] ) )
                    $category_markets[$acml->cl_market_id] = $craigslist_api->get_cl_market_categories( $acml->cl_market_id );

                $category = '(No Category)';

                if ( is_array( $category_markets[$acml->cl_market_id] ) )
                foreach ( $category_markets[$acml->cl_market_id] as $cm ) {
                    if ( $cm->cl_category_id == $acml->cl_category_id ) {
                        $category = $cm->name;
                        break;
                    }
                }

                $account_market_links[] = $acml->market . ' / ' . $category;
            }
        }

        $template_response = $this->get_template_response('craigslist')
            ->set( compact( 'account', 'account_market_links', 'craigslist_markets', 'errs' ) )
            ->select('accounts');

        $this->resources
            ->css('accounts/edit')
            ->javascript('accounts/craigslist');

        return $template_response;
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

        // Make sure they have permission
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

        $v = new Validator( 'fEditDNS' );

        // Declare variables
        $domain_name = url::domain( $account->domain, false );
        $full_domain_name = $domain_name . '.';
        $zone_id = $account->get_settings( 'r53-zone-id' );
        $errs = false;

        // Handle form actions
        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) && isset( $_POST['changes'] ) && is_array( $_POST['changes'] ) ) {
                $changes = array();
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

        // Do an action like create DNS
        if ( isset( $_GET['a'] ) )
        switch ( $_GET['a'] ) {
            case 'create':
                // We already have something, stop!
                if ( !empty( $zone_id ) )
                    break;

                // Create the zone file
                $zone = $r53->createHostedZone( $domain_name, md5(microtime()) );

                $zone_id = $zone['HostedZone']['Id'];

                // Set the settings
                $account->set_settings( array( 'r53-zone-id' => $zone_id ) );

                // Defaults
                $changes = array(
                    $r53->prepareChange( 'CREATE', $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', $full_domain_name, 'MX', '14400', '0 mail.' . $full_domain_name )
                    , $r53->prepareChange( 'CREATE', $full_domain_name, 'TXT', '14400', '"v=spf1 a mx ip4:199.79.48.137 ~all"' )
                    , $r53->prepareChange( 'CREATE', 'mail.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
                    , $r53->prepareChange( 'CREATE', 'www.' . $full_domain_name, 'CNAME', '14400', $full_domain_name )
                    , $r53->prepareChange( 'CREATE', 'ftp.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
                    , $r53->prepareChange( 'CREATE', 'cpanel.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'whm.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'webmail.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'webdisk.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                );

                $response = $r53->changeResourceRecordSets( $zone_id, $changes );
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
        }

        if ( !empty( $zone_id ) ) {
            $r53->getHostedZone( $zone_id );
            $records = $r53->listResourceRecordSets( $zone_id );

            if ( is_array( $records['ResourceRecordSets'] ) ) {
                lib( 'misc/dns-sort' );
                new DNSSort( $records['ResourceRecordSets'] );
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
            ->javascript('accounts/dns')
            ->css('accounts/edit', 'accounts/dns');

        return $this->get_template_response('dns')
            ->kb( 8 )
            ->select( 'accounts', 'edit' )
            ->set( compact( 'account', 'zone_id', 'errs', 'domain_name', 'full_domain_name', 'records' ) );
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
        $v = new Validator( 'fAddNote' );
        $v->add_validation( 'taNote', 'req', _('The note may not be empty') );

        if ( $this->verified() ) {
            $account_note->website_id = $_GET['aid'];
            $account_note->user_id = $this->user->id;
            $account_note->message = $_POST['taNote'];
            $account_note->create();
        }

        // Get notes
        $notes = $account_note->get_all( $_GET['aid'] );

        $this->resources->css('accounts/notes');

        return $this->get_template_response('notes')
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

        $form = new FormTable( 'fAddEmailTemplate' );
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
        $install_service->install_website( $account );

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
        $install_service->install_package( $account );

        // Let them know it's been installed
        $this->notify( _('The website package has been successfully installed') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/edit/' ) );
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
        $account->status = 0;
        $account->save();

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

            fn::mail( $company->email, $subject, $message );
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

        // Give them a notification
        $this->notify( _('The Ashley Feed has been successfully run!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/actions/' ) );
    }

    /**
     * Test Ashley Feed
     *
     * @return HtmlResponse
     */
    protected function test_ashley_feed() {
        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Get the files
        $ashley_specific_feed = new AshleySpecificFeedGateway();

        $ftp = $ashley_specific_feed->get_ftp( $account );

        // Yay
        $files = $ftp->raw_list();
        $file_count = count( $files );

        // Create response
        $response = new CustomResponse( $this->resources, 'accounts/ashley-files' );

        $response->set( compact( 'files', 'file_count' ) );

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

        // Give them a notification
        $this->notify( _('The categories have been successfully reorganized!') );

        // Redirect them to accounts page
        return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/actions/' ) );
    }

    /**
     * Create craigslist account
     *
     * @return RedirectResponse
     */
    protected function create_craigslist_account() {
        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist_api = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        // Create the customer
        $customer_id = $craigslist_api->add_customer( $account->title );

        if ( $customer_id ) {
            $account->set_settings( array( 'craigslist-customer-id' => $customer_id ) );
            $message = _('Craigslist account has been successfully created!');
            $url = url::add_query_arg( 'aid', $account->id, '/accounts/craigslist/' );
        } else {
            $message = _('Craigslist account failed to get created. Please contact support.');
            $url = url::add_query_arg( 'aid', $account->id, '/accounts/actions/' );
        }

        $this->notify( $message );

        return new RedirectResponse( $url );
    }

    /***** AJAX *****/

    /**
     * Create trumpia account
     *
     * @return CustomResponse|AjaxResponse
     */
    protected function create_trumpia_account() {
        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Get mobile plans
        $mobile_plan = new MobilePlan();
        $mobile_plans = $mobile_plan->get_all();

        $mobile_plan_options = array();

        /**
         * @var MobilePlan $mp
         */
        foreach ( $mobile_plans as $mp ) {
            $mobile_plan_options[$mp->id] = $mp->name;
        }

        // Create new form table
        $ft = new FormTable( 'fCreateTrumpiaAccount' );

        $ft->submit( _('Create') )
            ->attribute( 'ajax', '1' )
            ->set_action( url::add_query_arg( 'aid', $account->id, '/accounts/create-trumpia-account/' ) );

        $ft->add_field( 'select', _('Mobile Marketing Plan'), 'sMobilePlanId' )
            ->add_validation( 'req', _('The "Mobile Marketing Plan" field is required') )
            ->options( $mobile_plan_options );

        $form = $ft->generate_form();

        // Create the account
        if ( $ft->posted() ) {
            $response = new AjaxResponse(true);

            // Get install service
            $install_service = new InstallService();

            // Alert them that there was a problem
            try {
                $install_service->install_trumpia_account( $mobile_plan, $account );
            } catch ( ModelException $e ) {
                $response->check( false, $e->getMessage() );
            }

            if ( $response->has_error() )
                return $response;

            // Add notification
            $this->notify( _('Trumpia account successfully created') );

            // Redirect to next page
            jQuery('body')->redirect( url::add_query_arg( 'aid', $account->id, '/accounts/other-settings/' ) );

            // Add jquery
            $response->add_response( 'jquery', jQuery::getResponse() );

            return $response;
        }

        $response = new CustomResponse( $this->resources, 'accounts/create-trumpia-account' );
        $response->set( compact( 'form' ) );

        return $response;
    }

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

        if ( User::KEVIN_DORAN == $this->user->id ) {
            $dt->add_where( ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $this->user->company_id . ' )' );
        } else {
            // If they are below 8, that means they are a partner
            if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
                $dt->add_where( ' AND b.`company_id` = ' . $this->user->company_id );
        }

		// What other sites we might need to omit
		$omit_sites = ( !$this->user->has_permission( User::ROLE_ADMIN ) ) ? ', 96, 114, 115, 116' : '';

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

            $title = '<a href="http://' . $a->domain . '/" target="_blank"><strong title="' . $a->domain . ' - ' . $a->online_specialist . '">' . $a->title . $store_name . '</strong></a><br />';
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
            // Delete the note
            jQuery('#dNote' . $account_note->id )->remove();
            jQuery('#dNotes .note.first')->removeClass('first');
            jquery('#dNotes .note:first')->addClass('first');

            $account_note->remove();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

    /**
     * Get craigslist market categories
     *
     * @return AjaxResponse
     */
    protected function get_craigslist_market_categories() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['clmid'] ) && isset( $_POST['aid'] ), _('Failed to get craigslist market categories') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get the account
        $account = new Account();
        $account->get( $_POST['aid'] );

        // Get the craigslist market id
        $craigslist_market_link = new CraigslistMarketLink();
        $cl_category_ids = $craigslist_market_link->get_cl_category_ids_by_account( $account->id, $_POST['clmid'] );

        // Load Craigslist API
        library('craigslist-api');

        $craigslist_api = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );
        $market_categories = $craigslist_api->get_cl_market_categories( $_POST['clmid'] );

        // Need to create new options
        $options_html = '<option value="">-- ' . _('Select Category') . ' --</option>';

        if ( is_array( $market_categories ) )
        foreach ( $market_categories as $mc ) {
            if ( in_array( $mc->cl_category_id, $cl_category_ids ) )
                continue;

            $options_html .= '<option value="' . $mc->cl_category_id . '">' . $mc->name . '</option>';
        }

        // Replace old ones
        jQuery('#sCLCategoryId')->empty()->append( $options_html );

        $response->add_response( 'jquery', jQuery::getResponse() );

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
        switch ( $_POST['type'] ) {
            case 'domain':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_POST['term'], 'domain', $this->user, $status );
            break;

            case 'store_name':
                $results = $this->user->autocomplete( $_POST['term'], 'store_name' );

                if ( is_array( $results ) )
                foreach ( $results as &$result ) {
                    $result['store_name'] = $result['store_name'];
                }
            break;

            case 'title':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_POST['term'], 'title', $this->user, $status );
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

        $this->notify( _('All account prices has been reseted.') );

        return new RedirectResponse( "/accounts/actions/?aid={$_GET['aid']}" );
    }

}