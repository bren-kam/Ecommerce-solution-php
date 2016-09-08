<?php
class CustomizeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/customize/';
        $this->section = 'accounts';
        $this->title = 'Customize';
    }

    /**
     * List Companies
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
            return new RedirectResponse('/accounts/');

        return $this->get_template_response( 'index' )
            ->kb( 9 )
            ->select( 'customize', 'view' );
    }

    /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function stylesheet() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) || !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Get Account
        $account = new Account();
        $account->get( $_GET['aid'] );
        $unlocked_less = false;
        
        if ( $_GET['aid'] != Account::TEMPLATE_UNLOCKED ) {
            $unlocked = new Account();
            $unlocked->get( Account::TEMPLATE_UNLOCKED );
            $unlocked_less = $unlocked->get_settings('less');
        } else {
            $unlocked_less = true;
        }

        $less = $account->get_settings('less');

        $this->resources
            ->css('accounts/customize/css')
            ->javascript('accounts/customize/css')
            ->javascript_url( Config::resource('ace-js') );

        return $this->get_template_response( 'css' )
            ->kb( 10 )
            ->select( 'customize', 'stylesheet' )
            ->set( compact( 'less', 'account', 'unlocked_less' ) )
            ->add_title( _('LESS CSS') );
    }

    /**
     * Settings
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function settings() {
        if ( !$this->user->has_permission( User::ROLE_ADMIN ) || !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Setup objects
        $ft = new BootstrapForm( _('fCustomizeSettings') );

        // Get variables
        $settings = $account->get_settings(
            'slideshow-fixed-width'
            , 'slideshow-categories'
            , 'sidebar-left'
            , 'header-full-width'
            , 'dropdown-hover'
        );

        // Start adding fields
        $ft->add_field( 'checkbox', _('Fixed-width Slideshow'), 'cbFixedWidthSlideshow', $settings['slideshow-fixed-width'] );
        $ft->add_field( 'checkbox', _('Slideshow w/ Categories'), 'cbSlideshowCategories', $settings['slideshow-categories'] );
        $ft->add_field( 'checkbox', _('Left-hand-side Sidebar'), 'cbSidebarLeft', $settings['sidebar-left'] );
        $ft->add_field( 'checkbox', _('Full Width Header'), 'cbHeaderFullWidth', $settings['header-full-width'] );
        $ft->add_field( 'checkbox', _('Dropdown Hover'), 'cbDropdownHover', $settings['dropdown-hover'] );                

        if ( $ft->posted() ) {
            // Update settings
            $account->set_settings( array(
                'slideshow-fixed-width' => (int) isset( $_POST['cbFixedWidthSlideshow'] ) && $_POST['cbFixedWidthSlideshow']
                , 'slideshow-categories' => (int) isset( $_POST['cbSlideshowCategories'] ) && $_POST['cbSlideshowCategories']
                , 'sidebar-left' => (int) isset( $_POST['cbSidebarLeft'] ) && $_POST['cbSidebarLeft']
                , 'header-full-width' => (int) isset( $_POST['cbHeaderFullWidth'] ) && $_POST['cbHeaderFullWidth']
                , 'dropdown-hover' => (int) isset( $_POST['cbDropdownHover'] ) && $_POST['cbDropdownHover']                                
            ));

            $this->notify( _('Settings have been updated!') );

            return new RedirectResponse( url::add_query_arg( 'aid', $_GET['aid'], '/accounts/customize/settings/' ) );
        }

        // Create Form
        $form = $ft->generate_form();

        return $this->get_template_response('settings')
            ->kb( 0 )
            ->add_title( _('Settings') )
            ->select('customize', 'settings')
            ->set( compact( 'account', 'form' ) );
    }

     /**
     * Add/Edit Favicon
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function favicon() {
         if (!$this->user->has_permission(User::ROLE_ADMIN) || !isset($_GET['aid']))
            return new RedirectResponse('/accounts/');

        // Get Account
        $account = new Account();
        $account->get($_GET['aid']);

        $favicon = $account->get_settings("favicon");
        $this->resources->javascript('fileuploader', 'accounts/customize/favicon')
            ->css( 'accounts/customize/favicon' );

        return $this->get_template_response('favicon')
            ->select('customize', 'favico')
            ->set(compact('favicon', 'account'))
            ->add_title(_('Favicon'));
    }

    /***** AJAX *****/

    /**
     * Save LESS
     *
     * @return AjaxResponse
     */
    protected function save_less() {
        set_time_limit(3600);

        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());

        // Get account
        if ( $_GET['aid'] == Account::TEMPLATE_UNLOCKED ) {
            $less_css = $_POST['less'];
        } else {
            $unlocked = new Account();
            $unlocked->get( Account::TEMPLATE_UNLOCKED );
            $unlocked_less = $unlocked->get_settings('less');
            $less_css = $unlocked_less . $_POST['less'];
        }

        $account = new Account();
        $account->get($_GET['aid']);

        // If there is an error or now user id, return
        if ( $response->has_error() )
             return $response;

        library('lessc.inc');
        $less = new lessc;
        $less->setFormatter("compressed");

        try {
            $css = $less->compile( $less_css );
        } catch (exception $e) {
            $response->notify( 'Error: ' . $e->getMessage(), false );
            return $response;
        }

        $account->set_settings( array( 'less' => $_POST['less'], 'css' => $css ) );

        $response->notify( 'LESS/CSS has been successfully updated!' );

        // Update all other LESS sites
        if ( $_GET['aid'] == Account::TEMPLATE_UNLOCKED ) {
            $less_accounts = $account->get_less_sites();

            /**
             * @var Account $less_account
             * @var string $unlocked_less
             */
            if ( !empty( $less_accounts ) )
            foreach ( $less_accounts as $less_account ) {
                if ( $less_account->id == Account::TEMPLATE_UNLOCKED )
                    continue;

                $less = new lessc;
                $less->setFormatter("compressed");
                $site_less = $less_account->get_settings('less');

                $less_account->set_settings( array(
                    'css' => $less->compile( $less_css . $site_less )
                ));

                unset( $less );
                unset( $site_less );
                unset( $less_account );
                gc_collect_cycles();
            }
        }

        return $response;
    }

    /**
     * Upload Favicon
     *
     * @return AjaxResponse
     */
    protected function upload_favicon() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());

        // Get account
        $account = new Account();
        $account->get($_GET['aid']);

        // If there is an error or now user id, return
        if ( $response->has_error() )
             return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $account_file = new AccountFile();

        // Create uploader
        $uploader = new qqFileUploader( array( 'ico' ), 6144000 );

        // Upload file
        $result = $uploader->handleUpload('gsr_');

        $response->check( $result['success'], _('Failed to upload favicon') );

        // If there is an error or now user id, return
        if ( $response->has_error() ) {
             $response->add_response( "error", $result["error"] );
             return $response;
        }

        //Create favicon name
        $favicon_name =  'favicon.ico';

        // Create the different versions we need
        $favicon_dir = $account->id . '/favicon/';

        // Normal and large
        $file_url =  $file->upload_file( $result['file_path'], $favicon_name, $favicon_dir );


        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create account file
        $account_file->website_id = $account->id;
        $account_file->path = $file_url;
        $account_file->create();

        $account->set_settings( array( 'favicon' => $file_url ) );

        $response->add_response( 'refresh', true );

        return $response;
    }

    public function ashley_express_shipping_prices() {

        $account = new Account();
        $account->get( $_GET['aid'] );

        if ( $this->verified() ) {

            $file = $_FILES['file'];
            $filename = tempnam( sys_get_temp_dir(), 'ae_' ) . '.' . f::extension( $file['name'] );

            move_uploaded_file( $file['tmp_name'], $filename );
            $ae = new AshleyExpressFeedGateway();
            try {
                $updated = $ae->run_shipping_prices( $account, $filename );
                $this->notify( "Updated $updated products" );
            } catch( Exception $e ) {
                $this->notify( $e->getMessage(), false );
            }

        }

        return $this->get_template_response( 'ashley-express-shipping-prices' )
            ->set( compact( 'account' ) )
            ->kb( 0 );
    }
}