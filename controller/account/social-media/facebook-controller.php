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


