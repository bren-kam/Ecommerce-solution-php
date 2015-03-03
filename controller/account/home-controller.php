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

        $today = date("Y-m-d");
        $oneWeekAgo = strtotime ( '-1 week' ) ;

        // Get analytics - Visitors
        $date_start_visitors = ( isset( $_GET['dsv'] ) ) ? $_GET['dsv'] : date( 'Y-m-d' , $oneWeekAgo );
        $date_end_visitors = ( isset( $_GET['dev'] ) ) ? $_GET['dev'] : $today;

        // Setup analytics
        $analytics = new Analytics( $date_start_visitors, $date_end_visitors );

        try {
            $analytics->setup( $this->user->account );
            // Get all the data
            $visitors_data = $analytics->get_metric_by_date( 'visits' );
            if ( !$visitors_data ) {
                return new RedirectResponse( '/analytics/oauth2/' );
            }

            $visitors = array();
            if ( is_array( $visitors_data ) ){
                foreach ( $visitors_data as $r_date => $r_value ) {
                    $visitors[] = array( $r_date, $r_value );
                }
            }

        } catch ( GoogleAnalyticsOAuthException $e ) {
            $_SESSION['google-analytics-callback'] = '/analytics/';
            return new RedirectResponse( '/analytics/oauth2/' );
        } catch ( ModelException $e ) {
            $this->notify( _('Please contact your online specialist in order to view analytics.'), false );
            return new RedirectResponse('/');
        }

        $date_start_visitors = new DateTime( $date_start_visitors );
        $date_start_visitors = $date_start_visitors->format('n/j/Y');
        $date_end_visitors = new DateTime( $date_end_visitors );
        $date_end_visitors = $date_end_visitors->format('n/j/Y');
        
        // Get analytics - Email Signups
        $date_start_signups = ( isset( $_GET['dss'] ) ) ? $_GET['dss'] : date ( 'Y-m-d' , $oneWeekAgo );
        $date_end_signups = ( isset( $_GET['des'] ) ) ? $_GET['des'] : $today;

        $email = new Email();
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'e.`date_created`' );
        $dt->add_where( ' AND  e.`status` = 1 AND e.`date_created` >= "' . $date_start_signups . '" AND e.`date_created` < "'. $date_end_signups .'"' );
        $dt->add_where( ' AND e.`website_id` = ' . (int) $this->user->account->id );

        // Get items
        $signups_data = $email->aggregate_by_date( $dt->get_variables() );

        $signups = array();
        if ( is_array( $signups_data ) ){
            foreach ( $signups_data as $index => $signup ) {
                $signups[] = array( strtotime($signup['date']) . "000" , $signup['total'] );
            }
        }

        $date_start_signups = new DateTime( $date_start_signups );
        $date_start_signups = $date_start_signups->format('n/j/Y');
        $date_end_signups = new DateTime( $date_end_signups );
        $date_end_signups = $date_end_signups->format('n/j/Y');

        $kbh_article = new KnowledgeBaseArticle();
        $kbh_home_articles = $kbh_article->get_by_ids([124, 48, 92, 137, 53, 120]);

        $this->resources
                ->css( 'home/home' )
                ->javascript( 'jquery.flot/jquery.flot', 'jquery.flot/curvedLines', 'jquery.flot/excanvas', 'swfobject', 'home/home', 'bootstrap-datepicker' )
                ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'index' )
            ->select('dashboard')
            ->set( compact('website_orders', 'website_reaches', 'visitors', 'signups', 'date_start_visitors', 'date_end_visitors', 'date_start_signups', 'date_end_signups', 'kbh_home_articles') );
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


