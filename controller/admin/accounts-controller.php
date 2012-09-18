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

        $template_response = $this->get_template_response( 'index' )
            ->select( 'accounts', 'view' );

        $this->resources->javascript('accounts/list');
        $this->resources->css_url( Config::resource('jquery-ui') );

        return $template_response;
    }

    /**
     * Add account
     *
     * @return TemplateResponse
     */
    protected function add() {
        $template_response = $this->get_template_response( 'add' )
            ->select( 'accounts', 'add' )
            ->add_title( _('Add') );

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
            ->options( $users )
            ->add_validation( 'req', _('The "User" field is required') );

        $ft->add_field( 'select', _('Online Specialist'), 'sOnlineSpecialistID' )
            ->options( $os_users )
            ->add_validation( 'req', _('The "Online Specialist" field is required') );

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

            $this->notify( _('Your account was successfully created!') );

            return new RedirectResponse('/accounts/');
        }

        $template_response->set( 'form', $ft->generate_form() );

        return $template_response;
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
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $v = new Validator( _('fEditAccount') );
        $v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
        $v->add_validation( 'tProducts', 'req', _('The "Products" field is required') );
        $v->add_validation( 'tProducts', 'num', _('The "Products" field must contain a number') );

        $errs = false;

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
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

                $account->update();
                $this->notify( _('This account has been successfully updated!') );
            }
        }

        // Define fields
        $fields = array( 'account_title', 'users', 'phone', 'products', 'os_users', 'plan', 'plan_description' );

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
        $this->resources->javascript('accounts/edit');
        $this->resources->css('accounts/edit');

        $template_response = $this->get_template_response('edit')
            ->select( 'accounts' )
            ->add_title('Edit')
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
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
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
            $industry_list[$i->id] = $i->name;
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
            $account->live = (int) isset( $_POST['cbLive'] ) && $_POST['cbLive'];
            $account->update();

            // Update the settings
            $account->set_settings( array( 'custom-image-size' => $_POST['tCustomImageSize'] ));

            // Add the industries
            $account->add_industries( $_POST['sIndustries'] );

            // Let them know it was done
            $this->notify( _('The Website Settings have been updated!') );

            // Redirect to main page
            return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/edit/' ) );
        }

        // Create Form
        $form = $ft->generate_form();

        $template_response = $this->get_template_response('website-settings')
            ->select('accounts')
            ->set( compact( 'account', 'form' ) );

        $this->resources->css('accounts/edit');

        return $template_response;
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
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        // Setup objects
        $ft = new FormTable( _('fOtherSettings') );

        // Get variables
        $settings = $account->get_settings(
            'ga-username'
            , 'ga-password'
            , 'ashley-ftp-username'
            , 'ashley-ftp-password'
            , 'ashley-alternate-folder'
            , 'facebook-url'
            , 'advertising-url'
            , 'trumpia-api-key'
            , 'facebook-pages'
        );

        // Start adding fields
        $ft->add_field( 'text', _('FTP Username'), 'tFTPUsername', security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Username'), 'tGAUsername', security::decrypt( base64_decode( $settings['ga-username'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Password'), 'tGAPassword', security::decrypt( base64_decode( $settings['ga-password'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Google Analytics Profile ID'), 'tGAProfileID', $account->ga_profile_id );
        $ft->add_field( 'text', _('Google Analytics Tracking Key'), 'tGATrackingKey', $account->ga_tracking_key );
        $ft->add_field( 'text', _('WordPress Username'), 'tWPUsername', security::decrypt( base64_decode( $account->wordpress_username ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('WordPress Password'), 'tWPPassword', security::decrypt( base64_decode( $account->wordpress_password ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Ashley FTP Username'), 'tAshleyFTPUsername', security::decrypt( base64_decode( $settings['ashley-ftp-username'] ), ENCRYPTION_KEY ) );
        $ft->add_field( 'text', _('Ashley FTP Password'), 'tAshleyFTPPassword', htmlspecialchars( security::decrypt( base64_decode( $settings['ashley-ftp-password'] ), ENCRYPTION_KEY ) ) );
        $ft->add_field( 'checkbox', _('Ashley - Alternate Folder'), 'cbAshleyAlternateFolder', $settings['ashley-alternate-folder'] );
        $ft->add_field( 'text', _('Facebook Pages'), 'tFacebookPages', $settings['facebook-pages'] );
        $ft->add_field( 'text', _('Facebook Page Insights URL'), 'tFacebookURL', $settings['facebook-url'] );
        $ft->add_field( 'text', _('Advertising URL'), 'tAdvertisingURL', $settings['advertising-url'] );
        $ft->add_field( 'text', _('Mailchimp List ID'), 'tMCListID', $account->mc_list_id );
        $ft->add_field( 'text', _('Trumpia API Key'), 'tTrumpiaAPIKey', $settings['trumpia-api-key'] );

        if ( $ft->posted() ) {
            $account->ftp_username = security::encrypt( $_POST['tFTPUsername'], ENCRYPTION_KEY, true );
            $account->ga_profile_id = $_POST['tGAProfileID'];
            $account->ga_tracking_key = $_POST['tGATrackingKey'];
            $account->wordpress_username = security::encrypt( $_POST['tWPUsername'], ENCRYPTION_KEY, true );
            $account->wordpress_password = security::encrypt( $_POST['tWPPassword'], ENCRYPTION_KEY, true );
            $account->mc_list_id = $_POST['tMCListID'];

            $account->update();

            // Update settings
            $account->set_settings( array(
                'ga-username' => security::encrypt( $_POST['tGAUsername'], ENCRYPTION_KEY, true )
                , 'ga-password' => security::encrypt( $_POST['tGAPassword'], ENCRYPTION_KEY, true )
                , 'ashley-ftp-username' => security::encrypt( $_POST['tAshleyFTPUsername'], ENCRYPTION_KEY, true )
                , 'ashley-ftp-password' => security::encrypt( $_POST['tAshleyFTPPassword'], ENCRYPTION_KEY, true )
                , 'ashley-alternate-folder' => (int) isset( $_POST['cbAshleyAlternateFolder'] ) && $_POST['cbAshleyAlternateFolder']
                , 'facebook-pages' => $_POST['tFacebookPages']
                , 'facebook-url' => $_POST['tFacebookURL']
                , 'advertising-url' => $_POST['tAdvertisingURL']
                , 'trumpia-api-key' => $_POST['tTrumpiaAPIKey']
            ));

            $this->notify( _('This account\'s "Other Settings" has been updated!') );

            // Redirect to main page
            return new RedirectResponse( url::add_query_arg( 'aid', $account->id, '/accounts/edit/' ) );
        }

        // Create Form
        $form = $ft->generate_form();

        $template_response = $this->get_template_response('other-settings')
            ->select('accounts')
            ->set( compact( 'account', 'form' ) );

        $this->resources->css('accounts/edit');

        return $template_response;
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

        // Make sure he has permission
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $template_response = $this->get_template_response('actions')
            ->select('accounts');
        $this->resources->css('accounts/edit');

        $template_response->set( compact( 'account' ) );

        return $template_response;
    }

    /**
     * Edit DNS for an account
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function dns() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure he has permission
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        if ( !$this->user->has_permission(10) )
            return new RedirectResponse('/accounts/edit/?aid=' . $_GET['aid']);

        // Make sure they have permission
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
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
        $this->resources->javascript('accounts/dns');
        $this->resources->css('accounts/edit', 'accounts/dns');

        $template_response = $this->get_template_response('dns')
            ->select( 'accounts', 'edit' )
            ->set( compact( 'account', 'zone_id', 'errs', 'domain_name', 'full_domain_name', 'records' ) );

        return $template_response;
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
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $account_note = new AccountNote();

        // More setup
        $v = new Validator( _('fAddNote') );
        $v->add_validation( 'taNote', 'req', _('The note may not be empty') );

        if ( $this->verified() ) {
            $account_note->website_id = $_GET['aid'];
            $account_note->user_id = $this->user->id;
            $account_note->message = $_POST['taNote'];
            $account_note->create();
        }

        // Get notes
        $notes = $account_note->get_all( $_GET['aid'] );

        $template_response = $this->get_template_response('notes')
            ->select( 'accounts' )
            ->set( compact( 'account', 'notes', 'v' ) );

        $this->resources->css('accounts/notes');

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

        set_cookie( 'aid', $_GET['aid'], 172800 ); // 2 days
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
		if ( '0' != $account->version )
            return new RedirectResponse('/accounts/');

        // Create website industry (furniture)
        $account->add_industries( array( 1 ) );

        // Get Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Copy files
        ssh2_exec( $ssh_connection, "cp -R /gsr/platform/copy/. /home/$username/public_html" );

        // Update config & .htaccess file
        $document_root = '\/home\/' . $username . '\/public_html';
        
        ssh2_exec( $ssh_connection, "sed -i 's/\[document_root\]/$document_root/g' /home/$username/public_html/config.php" );
        ssh2_exec( $ssh_connection, "sed -i 's/\[website_id\]/" . $account->id . "/g' /home/$username/public_html/config.php" );

        // Must use FTP to assign folders under the right user
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/" . $account->theme );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache/css" );
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/cache/js" );

        ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Updated website version
        $account->version = 1;
        $account->update();

        // Insert pages
        $pages = array(
            'about-us' => array(
                'title' => 'About Us'
                , 'content' => '&lt;h2&gt;&lt;img class=&quot;alignright&quot; title=&quot;Family shot&quot; src=&quot;http://www.concurringopinions.com/archives/images/family.jpg&quot; alt=&quot;&quot; width=&quot;189&quot; height=&quot;164&quot; style=&quot;float:right; padding-left:10px; padding-bottom:10px;&quot; /&gt;We&#039;ll Make Your House�A Home!&lt;/h2&gt; &lt;p&gt;ABC Home Furnishings family has been in business for over 30 years in Big Town, Louisiana. We originally started as Waterbed Sleep Shoppe and in 1988 we diversified our product line to carry a wide selection of bedroom, living room, and dining room furniture, in our beautifully decorated 33,000 square foot showroom.&lt;/p&gt; &lt;p&gt;We carry some of the most recognized names in furniture and mattresses: Ashley, Berkline, Broyhill, Coaster, and Sealy Mattresses.&lt;/p&gt; &lt;p&gt;Our family buyers continue to always search for the best buys and values in the furniture market. We shop during four international shows each year. Making certain to always find products coming from around the world. Today&#039;s fine furniture is built in The United States, Indonesia, South America, Canada, and China.&lt;/p&gt; &lt;p&gt;Count on us for:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Family service&lt;/li&gt; &lt;li&gt;Fast and friendly delivery&lt;/li&gt; &lt;li&gt;Great customer service&lt;/li&gt; &lt;li&gt;Knowledgeable and trained sales people&lt;/li&gt; &lt;li&gt;Guaranteed low prices on brand name furniture&lt;/li&gt; &lt;/ul&gt;'
            )
            , 'contact-us' => array(
                'title' => 'Contact Us'
                , 'content' => '&lt;p&gt;We love to hear from you! Please call, click or come on over.&lt;/p&gt;'
            )
            , 'current-offer' => array(
                'title' => 'Yes! Email My Special Coupon Offer Now'
                , 'content' => '&lt;p&gt;Receive Exclusive Tips, Trends, Special Offers and Online Only Sales from ABC Furniture.&lt;/p&gt;'
            )
            , 'financing' => array(
                'title' => 'Financing'
                , 'content' => '&lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card gives you the flexibility to pay for your in-store purchases over time while you enjoy your new furniture now.&lt;/p&gt; &lt;h3&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot;&gt;Apply online for instant pre-approval before you shop!&lt;/a&gt;&lt;/h3&gt; &lt;p&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot; title=&quot;Apply Now&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;/theme1/wp-content/uploads/2009/11/apply.gif&quot; alt=&quot;apply&quot; title=&quot;Apply Now&quot; width=&quot;146&quot; height=&quot;39&quot; /&gt;&lt;/a&gt;&lt;/p&gt; &lt;p&gt;As an &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; cardholder, you&#039;ll enjoy these benefits:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Convenient monthly payments&lt;/li&gt; &lt;li&gt;A revolving line of credit for your future furniture needs&lt;/li&gt; &lt;li&gt;Special promotional offers where available, including no-interest and reduced rate interest plans&lt;/li&gt; &lt;li&gt;No annual fee and no prepayment penalties&lt;/li&gt; &lt;li&gt;An easy-to-use online bill payment option&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card is provided by Wells Fargo Financial National Bank, a subsidiary of &lt;a title=&quot;Wells Fargo Financial&quot; href=&quot;http://financial.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Financial&lt;/a&gt;. Wells Fargo Financial is an affiliate of &lt;a title=&quot;Wells Fargo Bank, N.A&quot; href=&quot;http://www.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Bank, N.A&lt;/a&gt;&lt;/p&gt;'
            )
            , 'home' => array(
                'title' => 'Home'
                , 'content' => '&lt;p&gt;ABC Home Furnishings is family-owned and family-operated and has served Big Town, USA for over 30 years. &lt;a title=&quot;About Us&quot; href=&quot;http://furniture.imagineretailer.com/theme1/about-us/&quot;&gt;We have built our company by providing beautiful furniture, great service, low prices and hometown relationships from our family to yours.&lt;/a&gt;&lt;/p&gt; &lt;p&gt;ABC always offers simple to get, &lt;a title=&quot;Financing&quot; href=&quot;http://furniture.imagineretailer.com/theme1/financing/&quot;&gt;simple to use financing&lt;/a&gt;. Our programs often allow you to make payments while deferring interest, and always provide you benefits when shopping with us.&lt;/p&gt; &lt;p&gt;As a ABC Furniture cardholder, you&#039;ll enjoy benefits such as:&lt;br /&gt; &amp;bull; Convenient monthly payments&lt;br /&gt; &amp;bull; A revolving line of credit for all your purchasing needs&lt;br /&gt; &amp;bull; Special promotional offers where available, including no-interest and reduced rate interest plans&lt;br /&gt; &amp;bull; No annual fee and no prepayment penalties&lt;/p&gt; &lt;p&gt;Step inside our beautifully decorated showroom to browse a wide selection of&lt;a title=&quot;bedroom furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/bedrooms/&quot;&gt; bedroom,&lt;/a&gt; &lt;a title=&quot;living room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/living-rooms/&quot;&gt;living room,&lt;/a&gt; &lt;a title=&quot;dining room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/dining-rooms/&quot;&gt;and dining room furniture,&lt;/a&gt; &lt;a title=&quot;leather furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/leather/&quot;&gt;leather,&lt;/a&gt; &lt;a title=&quot;home office furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/home-office/&quot;&gt;home office,&lt;/a&gt; &lt;a title=&quot;kids furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/youth/&quot;&gt;kids furniture &lt;/a&gt;and the area&#039;s largest selection of brand name mattresses and box spring sets. You&#039;ll find brands you recognize and trust including &lt;a title=&quot;Ashley Furniture&quot; href=&quot;http://www.ashleyfurniture.com/&quot;&gt;Ashley&lt;/a&gt;, &lt;a title=&quot;Berkline Furniture&quot; href=&quot;http://www.berkline.com/&quot;&gt;Berkline&lt;/a&gt;, &lt;a title=&quot;Broyhill Furniture&quot; href=&quot;http://www.broyhillfurniture.com/&quot;&gt;Broyhill&lt;/a&gt;, &lt;a title=&quot;Coaster Furniture&quot; href=&quot;http://coastercompany.com/&quot;&gt;Coaster&lt;/a&gt;, and &lt;a title=&quot;Sealy Bedding&quot; href=&quot;http://www.sealy.com/&quot;&gt;Sealy Mattresses&lt;/a&gt;.&lt;/p&gt; &lt;p&gt;Make your house a home at ABC Home Furnishings!&lt;/p&gt;'
            )
            , 'sidebar' => array(
                'title' => 'Sidebar'
                , 'content' => ''
            )
            , 'products' => array(
                'title' => 'Products'
                , 'content' => ''
            )
            , 'brands' => array(
                'title' => 'Brands'
                , 'content' => ''
            )
        );

        foreach ( $pages as $slug => $page ) {
            $account_page = new AccountPage();
            $account_page->website_id = $account->id;
            $account_page->slug = $slug;
            $account_page->title = $page['title'];
            $account_page->content = $page['content'];
            $account_page->create();

            // Need to keep sidebar page
            if ( 'sidebar' == $slug )
                $sidebar_page = $account_page;
        }

        // Insert static sidebar elements
        $attachments = array( 'search', 'video', 'email', 'room-planner' );
        $sequence = 0;

        /**
         * @var AccountPage $sidebar_page
         */
        foreach ( $attachments as $key ) {
            $account_page_attachment = new AccountPageAttachment();
            $account_page_attachment->website_page_id = $sidebar_page->id;
            $account_page_attachment->key = $key;
            $account_page_attachment->value = '';
            $account_page_attachment->sequence = $sequence;
            $account_page_attachment->create();

            $sequence++;
        }

        // Create default email list
        $email_list = new EmailList;
        $email_list->website_id = $account->id;
        $email_list->name = 'Default';
        $email_list->create();

        // Create default email autoresponder
        $email_autoresponder = new EmailAutoresponder;
        $email_autoresponder->website_id = $account->id;
        $email_autoresponder->email_list_id = $email_list->id;
        $email_autoresponder->name = 'Default';
        $email_autoresponder->subject = $account->title . ' - Current Offer';
        $email_autoresponder->message = '<p>Thank you for signing up for the latest tips, trends and special offers. Here is the current offer from our store.<p><br /><br />';
        $email_autoresponder->current_offer = 1;
        $email_autoresponder->default = 1;
        $email_autoresponder->create();

        // Create default email template
        $email_template = new EmailTemplate();
        $email_template->name = 'Default';
        $email_template->template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>[subject]</title><style type="text/css">body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }p { line-height: 21px; padding-bottom: 7px; }h2{ padding:0; margin:0; }td{ font-size: 13px; padding-right: 10px; }li { padding-top: 7px; }</style></head><body>[message]</body></html>';
        $email_template->type = 'default';
        $email_template->create();

        // Create email template association
        $email_template->add_association( $account->id, 'website' );

        // Create default settings
        $account->set_email_settings( array( 'timezone' => Config::setting('default-timezone') ) );

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

        // Get company package
        $company_package = new CompanyPackage();
        $company_package->get( $account->company_package_id );

        // Get template account
        $template_account = new Account();
        $template_account->get( $company_package->website_id );

        // Update theme and logo
        $account->theme = $template_account->theme;
        $account->logo = $template_account->logo;
        $account->update();

        // Get FTP Username
        $username = security::decrypt( base64_decode( $account->ftp_username ), ENCRYPTION_KEY );
        $template_username = security::decrypt( base64_decode( $template_account->ftp_username ), ENCRYPTION_KEY );

        // SSH Connection
        $ssh_connection = ssh2_connect( Config::setting('server-ip'), 22 );
        ssh2_auth_password( $ssh_connection, Config::setting('server-username'), Config::setting('server-password') );

        // Make The new theme directory
        ssh2_exec( $ssh_connection, "mkdir /home/$username/public_html/custom/" . $template_account->theme );

        // Copy over all the theme files
        ssh2_exec( $ssh_connection, "cp -Rf /home/$template_username/public_html/custom/. /home/$username/public_html/custom" );

		// Copy over config file
        ssh2_exec( $ssh_connection, "yes | cp -rf /home/$template_username/public_html/config.php /home/$username/public_html/config.php" );

		ssh2_exec( $ssh_connection, "sed -i 's/$template_username/$username/g' /home/$username/public_html/config.php" );
		ssh2_exec( $ssh_connection, "sed -i 's/" . $template_account->id . "/" . $account->id . "/g' /home/$username/public_html/config.php" );

        ssh2_exec( $ssh_connection, "chmod -R 0777 /home/$username/public_html/custom/cache" );
        ssh2_exec( $ssh_connection, "chown -R $username:$username /home/$username/public_html/" );

        // Make sure the public_html directory has the correct group
        ssh2_exec( $ssh_connection, "chown $username:nobody /home/$username/public_html" );

        // Copy account pages
        $account_page = new AccountPage();
        $account_page->copy_by_account( $template_account->id, $account->id );

        // Get account pages by slug
        $account_pages = ar::assign_key( $account_page->get_all( $account->id ), 'slug', true );
        $template_account_pages = ar::assign_key( $account_page->get_all( $template_account->id ), 'website_page_id', true );

        // Get attachments
        $account_page_attachment = new AccountPageAttachment();
        $template_account_attachments = $account_page_attachment->get_by_account_page_ids( array_keys( $template_account_pages ) );

        // Delete certain sidebar elements that you can only have one of
        $account_page_attachment->delete_unique_attachments( array_values( $account_pages ) );

        // Declare file which will be used to copy files
        $file = new File();

        /**
         * @var AccountPageAttachment $taa
         */
        if ( is_array( $template_account_attachments )  )
        foreach ( $template_account_attachments as $taa ) {
            $value = $file->copy_file( $account->id, $taa->value, 'websites' );

            if ( $value ) {
                // Create the link in website files
                $account_file = new AccountFile();
                $account_file->website_id = $account->id;
                $account_file->file_path = $value;
                $account_file->create();
            }

            $new_account_page_attachment = new AccountPageAttachment();
            $new_account_page_attachment->website_page_id = $account_pages[$template_account_pages[$taa->website_page_id]];
            $new_account_page_attachment->key = $taa->key;
            $new_account_page_attachment->value = $value;
            $new_account_page_attachment->extra = $taa->extra;
            $new_account_page_attachment->meta = $taa->meta;
            $new_account_page_attachment->sequence = $taa->sequence;
            $new_account_page_attachment->create();
        }

        // Copy Account industries
        $account->copy_industries_by_account( $template_account->id, $account->id );

        // Copy Account Pagemeta
        $account_pagemeta = new AccountPagemeta();

        $pagemeta_keys = array( 'display-coupon', 'email-coupon', 'hide-all-maps' );
		$template_account_page_ids = implode( ', ', array_keys( $template_account_pages ) );

        $template_pagemeta = $account_pagemeta->get_by_keys( $template_account_page_ids, $pagemeta_keys );

        $new_pagemeta = array();

        /**
         * @var AccountPagemeta $tpm
         */
        if ( is_array( $template_pagemeta )  )
        foreach ( $template_pagemeta as $tpm ) {
             $website_page_id = (int) $account_pages[$template_account_pages[$tpm->website_page_id]];

            $new_pagemeta[] = array( 'website_page_id' => $website_page_id, 'key' => $tpm->key, 'value' => $tpm->value );
        }

        if ( 0 != count( $new_pagemeta ) )
            $account_pagemeta->add_bulk( $new_pagemeta );

        // Copy top brands
        $account->copy_top_brands_by_account( $template_account->id, $account->id );

        // Copy products
        $account_product = new AccountProduct();
        $account_product->copy_by_account( $template_account->id, $account->id );

        // Reorganize Categories
        $account_category = new AccountCategory();
        $account_category->reorganize_categories( $account->id, new Category() );

        // Copy Website Settings
		$account->copy_settings_by_account( $template_account->id, $account->id, array( 'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-effect', 'banner-hide-scroller', 'sidebar-image-width' ) );

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
        if ( !isset( $_GET['aid'] ) && $this->user->has_permission(7) )
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
        if ( !isset( $_GET['aid'] ) && $this->user->has_permission(10) )
            return new RedirectResponse('/accounts/');

        // Get the account
        $account = new Account();
        $account->get( $_GET['aid'] );

        // Deactivate account
        $account->status = 0;
        $account->update();

        // Give them a notification
        $this->notify( _('The account, "' . $account->title . '", has been deactivated.' ) );

        // Redirect them to accounts page
        return new RedirectResponse('/accounts/');
    }

    /***** AJAX *****/

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

        if ( 251 == $this->user->id ) {
            $dt->add_where( ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $this->user->company_id . ' )' );
        } else {
            // If they are below 8, that means they are a partner
            if ( !$this->user->has_permission(8) )
                $dt->add_where( ' AND b.`company_id` = ' . $this->user->company_id );
        }

		// What other sites we might need to omit
		$omit_sites = ( !$this->user->has_permission(8) ) ? ', 96, 114, 115, 116' : '';

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
            $title .= '<span class="web-actions" style="display: block"><a href="/accounts/edit/?aid=' . $a->id . '" title="' . _('Edit') . ' ' . $a->title . '">' . _('Edit') . '</a> | ';
            $title .= '<a href="/accounts/control/?aid=' . $a->id . '" title="' . _('Control') . ' ' . $a->title . '" target="_blank">' . _('Control Account') . '</a> | ';
            $title .= '<a href="/users/control/?uid=' . $a->user_id . '" title="' . _('Control User') . '" target="_blank">' . _('Control User') . '</a> | ';
            $title .= '<a href="/accounts/notes/?aid=' . $a->id . '" title="' . _('Notes') . '" target="_blank">' . _('Notes') . '</a>';

            if ( isset( $incomplete_checklists[$a->id] ) )
                $title .= ' | <a href="/checklists/view/?cid=' . $incomplete_checklists[$a->id] . '" title="' . _('Checklists') . '" target="_blank">' . _('Checklist') . '</a>';

            $title .= '</span>';

            $data[] = array(
                $image
                , $title
                , '<a href="/users/edit/?uid=' . $a->user_id . '" title="' . $contact_title . '">' . $a->contact_name . '</a>'
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

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['anid'] ) )
            return $response;

        // Get the user
        $account_note = new AccountNote();
        $account_note->get( $_GET['anid'] );

        // Deactivate user
        if ( $account_note->id ) {
            // Delete the note
            jQuery('#dNote' . $account_note->id )->remove();
            jQuery('#dNotes .note.first')->removeClass('first');
            jquery('#dNotes .note:first')->addClass('first');

            $account_note->delete();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

    /**
     * AutoComplete
     *
     * @return AjaxResponse
     */
    public function autocomplete() {
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
}