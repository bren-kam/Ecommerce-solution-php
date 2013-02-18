<?php
class FacebookController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'social-media/facebook/';
        $this->section = 'social-media';
        $this->title = _('Facebook') . ' | ' . _('Social Media');
    }

    /**
     * Redirect to Facebook
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'facebook-pages', 'view' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        $sm_facebook_page_id = ( isset( $_GET['smfbpid'] ) ) ? $_GET['smfbpid'] : false;

        $page = new SocialMediaFacebookPage();

        if ( $sm_facebook_page_id )
            $page->get( $sm_facebook_page_id, $this->user->account->id );

        $facebook_page_limit = $this->user->account->get_settings( 'facebook-pages' );
        $facebook_page_count = $page->count_all( array( ' AND `website_id` = ' . (int) $this->user->account->id, '' ) );
        $has_permission = $page->id || $facebook_page_count < $facebook_page_limit || empty( $facebook_page_count );

        $form = new FormTable( 'fAddEditFacebookPage' );
        $submit_text = ( $page->id ) ? _('Save') : _('Add');
        $form->submit( $submit_text );

        $form->add_field( 'text', _('Name'), 'tName', $page->name )
            ->attribute( 'maxlength', 100 )
            ->add_validation( 'req', _('The "Name" field is required' ) );

        if ( $form->posted() ) {
            $page->website_id = $this->user->account->id;
            $page->name = $_POST['tName'];
            $page->status = 1;

            if ( $page->id ) {
                $page->save();
                $this->notify( _('Your facebook page has been updated successfully!') );
            } else {
                $page->create();
                $this->notify( _('Your facebook page has been added successfully!') );
            }

            return new RedirectResponse('/social-media/facebook/');
        }

        $form = $form->generate_form();

        return $this->get_template_response( 'add-edit' )
            ->select( 'facebook-pages', 'add' )
            ->set( compact( 'page', 'has_permission', 'form' ) );
    }

    /**
     * Choose
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function choose() {
        // Make Sure they can only get here when they select a page
        if ( !isset( $_GET['smfbpid'] ) )
            return new RedirectResponse('/social-media/facebook/');

        // Get the page
        $page = new SocialMediaFacebookPage();
        $page->get( $_GET['smfbpid'], $this->user->account->id );

        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        // Set the session
        $_SESSION['sm_facebook_page_id'] = $page->id;

        // Get settings
        $settings = $this->user->account->get_settings( 'facebook-url', 'social-media-add-ons' );

        $this->resources->css( 'social-media/facebook/choose' );

        return $this->get_template_response( 'choose' )
            ->select( 'facebook-pages' )
            ->set( compact( 'settings' ) );
    }

    /**
     * About Us
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function about_us() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        $about_us = new SocialMediaAboutUs();
        $about_us->get( $page->id );

        // Make sure it's created
        if ( !$about_us->key ) {
            $about_us->sm_facebook_page_id = $page->id ;

            if ( $this->user->account->pages ) {
                $account_page = new AccountPage();
                $account_page->get_by_slug( $this->user->account->id, 'about-us' );

                $about_us->website_page_id = (int) $account_page->id;
            } else {
                $about_us->website_page_id = 0;
            }

            $about_us->key = md5( $this->user->id . microtime() . $page->id );
            $about_us->create();
        }

        if ( $this->user->account->pages ) {
            $files = array();
        } else {
            $account_file = new AccountFile();
            $files = $account_file->get_by_account( $this->user->account->id );

            if ( $this->verified() ) {
                $about_us->content = $_POST['taContent'];
                $about_us->save();

                $this->notify( _('Your About Us page has been successfully updated!') );
            }
        }

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'website/pages/page' );

        return $this->get_template_response( 'about-us' )
            ->add_title( _('About Us') )
            ->select( 'about-us' )
            ->set( compact( 'about_us', 'page', 'files' ) );
    }

    /**
     * Contact Us
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function contact_us() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        $contact_us = new SocialMediaContactUs();
        $contact_us->get( $page->id );

        // Make sure it's created
        if ( !$contact_us->key ) {
            $contact_us->sm_facebook_page_id = $page->id ;

            if ( $this->user->account->pages ) {
                $account_page = new AccountPage();
                $account_page->get_by_slug( $this->user->account->id, 'contact-us' );

                $contact_us->website_page_id = (int) $account_page->id;
            } else {
                $contact_us->website_page_id = 0;
            }

            $contact_us->key = md5( $this->user->id . microtime() . $page->id );
            $contact_us->create();
        }

        if ( $this->user->account->pages ) {
            $files = array();
        } else {
            $account_file = new AccountFile();
            $files = $account_file->get_by_account( $this->user->account->id );

            if ( $this->verified() ) {
                $contact_us->content = $_POST['taContent'];
                $contact_us->save();

                $this->notify( _('Your Contact Us page has been successfully updated!') );
            }
        }

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'website/pages/page' );

        return $this->get_template_response( 'contact-us' )
            ->add_title( _('Contact Us') )
            ->select( 'contact-us' )
            ->set( compact( 'contact_us', 'page', 'files' ) );
    }

    /**
     * Email Sign Up
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function email_sign_up() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        $email_sign_up = new SocialMediaEmailSignUp();
        $email_sign_up->get( $page->id );

        $v = new Validator( 'fEmailSignUp' );

        // Add validation
        $v->add_validation( 'sEmailList', '!val=0', _('You must select an email list.') );

        // Make sure it's created
        if ( !$email_sign_up->key ) {
            $email_sign_up->sm_facebook_page_id = $page->id ;
            $email_sign_up->key = md5( $this->user->id . microtime() . $page->id );
            $email_sign_up->create();
        }

        // Check for errs
        $errs = '';

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $email_sign_up->email_list_id = $_POST['sEmailList'];
                $email_sign_up->tab = $_POST['taTab'];
                $email_sign_up->save();

                $this->notify( _('Your Email Sign Up page has been successfully updated!') );
            }
        }

        // Get files
        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        // Get email lists
        $email_list = new EmailList();
        $email_lists = $email_list->get_by_account( $this->user->account->id );

        // Setup validation
        $js_validation = $v->js_validation();

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'website/pages/page' );

        return $this->get_template_response( 'email-sign-up' )
            ->add_title( _('Email Sign Up') )
            ->select( 'email-sign-up' )
            ->set( compact( 'email_sign_up', 'page', 'js_validation', 'errs', 'files', 'email_lists' ) );
    }

    /**
     * Products
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function products() {
        // Make Sure they chose a facebook page
        if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
            return new RedirectResponse('/social-media/facebook/');

        $page = new SocialMediaFacebookPage();
        $page->get( $_SESSION['sm_facebook_page_id'], $this->user->account->id );

        // Make Sure they chose a facebook page
        if ( !$page->id )
            return new RedirectResponse('/social-media/facebook/');

        $products = new SocialMediaProducts();
        $products->get( $page->id );

        // Make sure it's created
        if ( !$products->key ) {
            $products->sm_facebook_page_id = $page->id ;
            $products->key = md5( $this->user->id . microtime() . $page->id );
            $products->create();
        }

        if ( $this->user->account->product_catalog ) {
            $files = array();
        } else {
            $account_file = new AccountFile();
            $files = $account_file->get_by_account( $this->user->account->id );

            if ( $this->verified() ) {
                $products->content = $_POST['taContent'];
                $products->save();

                $this->notify( _('Your Products page has been successfully updated!') );
            }
        }

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'website/pages/page' );

        return $this->get_template_response( 'products' )
            ->add_title( _('Products') )
            ->select( 'products' )
            ->set( compact( 'products', 'page', 'files' ) );
    }

    /**
     * Settings
     *
     * @return TemplateResponse
     */
    protected function settings() {
        // Instantiate classes
        $form = new FormTable( 'fSettings' );

        // Get settings
        $settings_array = array( 'timezone' );
        $settings = $this->user->account->get_settings( $settings_array );

        $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
            ->options( data::timezones( false, false, true ) );

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }

            $this->user->account->set_settings( $new_settings );

            $this->notify( _('Your settings have been successfully saved!') );

            // Refresh to get all the changes
            return new RedirectResponse('/social-media/facebook/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->add_title( _('Settings') )
            ->select( 'settings' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /***** AJAX *****/

    /**
     * List
     *
     * @return DataTableResponse
     */
    protected function list_pages() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set variables
        $dt->order_by( '`name`', '`date_created`' );
        $dt->add_where( " AND `website_id` = " . $this->user->account->id );
        $dt->search( array( '`name`' => false ) );

        $facebook_page = new SocialMediaFacebookPage();

        // Get autoresponder
        $facebook_pages = $facebook_page->list_all( $dt->get_variables() );
        $dt->set_row_count( $facebook_page->count_all( $dt->get_where() ) );

        // Setup variables
        $confirm = _('Are you sure you want to delete this post? This will disable all related apps and it cannot be undone.');
        $delete_page_nonce = nonce::create( 'delete' );
        $timezone = $this->user->account->get_settings( 'timezone' );
        $server_timezone = Config::setting('server-timezone');
        $data = array();

        // Create output
        if ( is_array( $facebook_pages ) )
        foreach ( $facebook_pages as $fb_page ) {
            // Set the actions
            $actions = '<br />' .
            '<div class="actions">' .
                '<a href="' . url::add_query_arg( 'smfbpid', $fb_page->id, '/social-media/facebook/choose/' ) . '" title="' . _('Select') . '">' . _('Select') . '</a> | ' .
                '<a href="' . url::add_query_arg( 'smfbpid', $fb_page->id, '/social-media/facebook/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                '<a href="' . url::add_query_arg( array( 'smfbpid' => $fb_page->id, '_nonce' => $delete_page_nonce ), '/social-media/facebook/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' .
            '</div>';

            $data[] = array(
                $fb_page->name . $actions
                , dt::adjust_timezone( $fb_page->date_created, $server_timezone, $timezone, 'F jS, Y g:i a' )
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
        $response->check( isset( $_GET['smfbpid'] ), _('You cannot delete this facebook page') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $page = new SocialMediaFacebookPage();
        $page->get( $_GET['smfbpid'], $this->user->account->id );
        $page->status = 0;
        $page->save();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


