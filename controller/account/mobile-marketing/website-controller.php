<?php
class WebsiteController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'mobile-marketing/website/';
        $this->section = 'mobile-marketing';
        $this->title = _('Website | Mobile Marketing' );
    }

    /**
     * Allow them to login to mobile marketing
     *
     * @return TemplateResponse
     */
    protected function index() {
        $response = $this->get_template_response( 'index' )
            ->select( 'mobile-pages', 'view' );

        return $response;
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Determine if we're adding or editing the user
        $mobile_page_id = ( isset( $_GET['mpid'] ) ) ? (int) $_GET['mpid'] : false;

        $page = new MobilePage();

        if ( $mobile_page_id )
            $page->get( $mobile_page_id, $this->user->account->id );

        $v = new Validator('fAddEditPage' );

        $v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
        $v->add_validation( 'tTitle', '!val=Page Title...', _('The "Title" field is required') );

        if ( 'home' != $page->slug )
            $v->add_validation( 'tSlug', 'req', _('The "Link" field is required') );

        $errs = '';

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                if ( $mobile_page_id ) {
                    $page->title = $_POST['tTitle'];
                    $page->slug = ( 'home' == $page->slug ) ? 'home' : $_POST['tSlug'];
                    $page->content = $_POST['taContent'];
                    $page->save();

                    $this->notify( _('Your page has been updated successfully!') );
                } else {
                    $page->website_id = $this->user->account->id;
                    $page->title = $_POST['tTitle'];
                    $page->slug = $_POST['tSlug'];
                    $page->content = $_POST['taContent'];
                    $page->create();

                    $this->notify( _('Your page has been added successfully!') );
                }

                return new RedirectResponse('/mobile-marketing/website/');
            }
        }

        $js_validation = $v->js_validation();

        $this->resources
            ->css( 'mobile-marketing/website/add-edit' )
            ->javascript( 'mobile-marketing/website/add-edit' );

        $response = $this->get_template_response( 'add-edit' )
            ->select( 'mobile-pages', 'add' )
            ->set( compact( 'page', 'errs', 'js_validation' ) )
            ->add_title( ( ( $mobile_page_id ) ? _('Edit') : _('Add') ) );

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
        $delete_page_nonce = nonce::create( 'delete' );

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


