<?php

class AnalyticsController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->title = 'Analytics | GeoMarketing';
    }

    public function index() {

        // Get analytics
        $start_date_str = ( isset( $_GET['ds'] ) ) ? $_GET['ds'] : '-6 week';
        $end_date_str = ( isset( $_GET['de'] ) ) ? $_GET['de'] : '';
        $location_id = isset( $_GET['location_id'] ) ? $_GET['location_id'] : null;

        // Locations
        $website_yext_location = new WebsiteYextLocation();
        $locations = $website_yext_location->get_all( $this->user->account->id );

        // Get the dates
        $start_date = new DateTime( $start_date_str );
        $end_date= new DateTime( $end_date_str );

        $analytics = new WebsiteYextAnalytics( $this->user->account );
        // $analytics->fetch_analytics( $start_date, $end_date );
        $analytics_all = $analytics->get_sum( $location_id, $start_date, $end_date );
        $analytics_all = ar::assign_key( $analytics_all, 'date' );

        $period = new DatePeriod(
             $start_date,
             new DateInterval('P1D'),
             $end_date
        );

        $searches = $profile_views = $special_offer_clicks = $foursquare_checkins = $facebook_likes = $facebook_talking_about = $facebook_werehere = [];
        foreach ( $period as $date ) {
            $date_str = $date->format( 'Y-m-d' );
            $ms_time = $date->getTimestamp() * 1000;
            $row = isset( $analytics_all[ $date_str ] ) ? $analytics_all[ $date_str ] : null;
            if ( $row ) {
                $searches[] = [ $ms_time, $row['searches'] ];
                $profile_views[] = [ $ms_time, $row['profile_views'] ];
                $special_offer_clicks[] = [ $ms_time, $row['special_offer_clicks'] ];
                $foursquare_checkins[] = [ $ms_time, $row['foursquare_checkins'] ];
                $facebook_likes[] = [ $ms_time, $row['facebook_likes'] ];
                $facebook_talking_about[] = [ $ms_time, $row['facebook_talking_about'] ];
                $facebook_werehere[] = [ $ms_time, $row['facebook_werehere'] ];
            } else {
                $searches[] = [ $ms_time, 0 ];
                $profile_views[] = [ $ms_time, 0 ];
                $special_offer_clicks[] = [ $ms_time, 0 ];
                $foursquare_checkins[] = [ $ms_time, 0 ];
                $facebook_likes[] = [ $ms_time, 0 ];
                $facebook_talking_about[] = [ $ms_time, 0 ];
                $facebook_werehere[] = [ $ms_time, 0 ];
            }
        }
        $reports = compact( 'searches', 'profile_views', 'special_offer_clicks', 'foursquare_checkins', 'facebook_likes', 'facebook_talking_about', 'facebook_werehere' );

        $this->resources
            ->css( 'geo-marketing/analytics/analytics' )
            ->javascript( 'geo-marketing/analytics/analytics', 'jquery.flot/jquery.flot', 'jquery.flot/excanvas', 'bootstrap-datepicker' )
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'geo-marketing/analytics/index' )
            ->kb( 0 )
            ->add_title( _('Dashboard') )
            ->menu_item( 'geo-marketing/analytics' )
            ->set( compact( 'start_date', 'end_date', 'reports', 'locations', 'location_id' ) );

    }

}