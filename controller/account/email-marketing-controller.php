<?php
class EmailMarketingController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/';
        $this->section = 'email-marketing';
        $this->title = _('Email Marketing');
    }

    /**
     * Show dashboard
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        $email_message = new EmailMessage();
        $messages = $email_message->get_dashboard_messages_by_account( $this->user->account->id );

        $email = new Email();
        $subscribers = $email->get_dashboard_subscribers_by_account( $this->user->account->id );

        // Setup variables
        $email = new AnalyticsEmail();
        $email_count = count( $messages );
        $i = 0;

        if ( is_array( $messages ) ) {
        	// Get the analytics data
        	while ( $i < $email_count && !$email->mc_campaign_id ) {
                $message = $messages[$i];
                $email->get_complete( $message->mc_campaign_id, $this->user->account->id );

        		$this->notify( _('An error occurred while trying to get your email') . ', "' . $message->subject . '". ' . _('Please contact an online specialist for assistance.'), false );
        		$i++;
        	}
        }

        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'email-marketing/dashboard' )
            ->javascript( 'swfobject', 'email-marketing/dashboard');

        return $this->get_template_response( 'index' )
            ->add_title( _('Dashboard') )
            ->select( 'dashboard' )
            ->set( compact( 'messages', 'subscribers', 'email', 'bar_chart', 'email_count' ) );
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


