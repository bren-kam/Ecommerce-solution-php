<?php
class HomeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'home/';
        $this->title = 'Dashboard';
    }

    /**
     * Setup a new account
     * @return TemplateResponse
     */
    protected function index() {

        $website_order = new WebsiteOrder();
        $website_order_item = new WebsiteOrderItem();

        $website_orders = $website_order->list_all([ " AND `website_id` = " . $this->user->account->id, '', 'ORDER BY website_order_id DESC', 5 ]);
        foreach ( $website_orders as $wo ) {
            $wo->items = $website_order_item->get_all( $wo->website_order_id );
        }

        $website_reach = new WebsiteReach();
        $website_reaches = $website_reach->list_all( [ " AND wr.`website_id` = " . $this->user->account->id, '', 'ORDER BY website_reach_id DESC', 5] );
        foreach( $website_reaches as $wr ) {
            $wr->get_info();
        }

        return $this->get_template_response( 'index' )
            ->select('dashboard')
            ->set( compact('website_orders', 'website_reaches') );
    }

    /**
     * List Accounts to select
     *
     * @return TemplateResponse
     */
    protected function select_account() {
        return $this->get_template_response( 'select-account' )
            ->add_title( _('Select Account') );
    }

    /**
     * Change Account
     * @return RedirectResponse
     */
    protected function change_account() {
        if ( empty( $_GET['aid'] ) )
            return new RedirectResponse('/');

        /**
         * @var Account $account
         */
        foreach ( $this->user->accounts as $account ) {
            // If it's amongst the user's accounts, redirect him
            if ( $account->id == $_GET['aid'] ) {
                set_cookie( 'wid', $account->id, 172800 ); // 2 Days
                break;
            }
        }

        // Either redirect to home or logout if he's trying to control someone else's site
        return new RedirectResponse( '/' );
    }
}


