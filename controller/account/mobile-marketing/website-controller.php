<?php
class WebsiteController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'mobile-marketing/website/';
        $this->title = _('Website | Mobile Marketing' );
    }

    /**
     * Allow them to login to mobile marketing
     *
     * @return TemplateResponse
     */
    protected function index() {
        $response = $this->get_template_response( 'index' )
            ->select( 'mobile-marketing', 'website' );

        return $response;
    }

    /***** AJAX *****/

    /**
     * List Mobile Pages
     *
     * @return DataTableResponse
     */
    protected function list_pages() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $mobile_page = new MobilePage();

        // Set Order by
        $dt->order_by( '`title`', '`status`', '`date_updated`' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`title`' => false ) );

        // Get items
        $mobile_pages = $mobile_page->list_all( $dt->get_variables() );
        $dt->set_row_count( $mobile_page->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this page? This cannot be undone.');
        $delete_page_nonce = nonce::create( 'delete_mobile_page' );

        /**
         * @var MobilePage $mobile_page
         */
        if ( is_array( $mobile_pages ) )
        foreach ( $mobile_pages as $mobile_page ) {
            $date_update = new DateTime( $mobile_page->date_updated );

            $data[] = array(
                $mobile_page->title . '<div class="actions">' .
                    '<a href="http://m.' . str_replace( 'www.', '', url::domain( $this->user->account->domain ) ) . '/' . $mobile_page->slug . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'mpid', $mobile_page->id, '/mobile-marketing/website/add-edit/') . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'mpid' => $mobile_page->id, '_nonce' => $delete_page_nonce ), '/mobile-marketing/website/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>' .
                    '</div>'
                , ( $mobile_page->status ) ? _('Visible') : _('Not Visible')
                , $date_update->format('F jS, Y')
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
        $response->check( isset( $_GET['mpid'] ), _('You cannot delete this page') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $mobile_page = new MobilePage();
        $mobile_page->get( $_GET['mpid'], $this->user->account->id );
        $mobile_page->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


