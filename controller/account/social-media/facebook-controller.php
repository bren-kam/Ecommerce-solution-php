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
        $delete_page_nonce = nonce::create( 'delete-facebook-page' );
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
                '<a href="' . url::add_query_arg( array( 'smfbpid' => $fb_page->id, '_nonce' => $delete_page_nonce ), '/social-media/facebook/choose/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' .
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
}


