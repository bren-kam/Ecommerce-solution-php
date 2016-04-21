<?php

class WebsiteYextAnalytics extends ActiveRecordBase {

    public $account;

    /**
     * Construct
     * @param Account $account
     */
    public function __construct( Account $account ) {
        parent::__construct( 'website_yext_analytics' );
        $this->account = $account;
    }

    /**
     * Fetch Analytics
     * @param  DateTime $start_date [description]
     * @param  DateTime $end_date   [description]
     */
    public function fetch_analytics( DateTime $start_date, DateTime $end_date ) {
        library('yext');
        $yext = new YEXT( $this->account );

        $report = $yext->report(
            ['SEARCHES', 'PROFILEVIEWS', 'SPECIALOFFERCLICKS', 'FOURSQUAREDAILYCHECKINS', 'YELPPAGEVIEWS']
            , ['DAYS', 'LOCATION_IDS']
            , $start_date->format('Y-m-d')
            , $end_date->format('Y-m-d')
        );

        if ( empty( $report ) ) {
            return;
        }

        $this->begin_transaction();
        foreach ( $report as $row ) {
            if(isset($row->yelp_page_views)){
                $this->query(
                    "REPLACE website_yext_analytics( `location_id`, `date`, `searches`, `profile_views`, `special_offer_clicks`, `foursquare_checkins`, `facebook_likes`, `facebook_talking_about`, `facebook_where_here`, `yelp_views` ) VALUES( {$row->location_id}, '{$row->day}', {$row->searches}, {$row->profile_views}, {$row->featured_message_clicks}, 0, 0, 0, 0, {$row->yelp_page_views} )"
                              );
            }
        }
        $this->commit();
    }

    /**
     * Get Sum
     * @param  int   $location_id
     * @param  DateTime $start_date
     * @param  DateTime $end_date
     * @return array[]
     */
    public function get_sum( $location_id = null, DateTime $start_date, DateTime $end_date ) {
        $where = '';
        if ( $location_id ) {
            $where = " AND a.location_id = {$location_id} ";
        }

        return $this->get_results(
            "SELECT a.`date`, SUM(a.searches) as searches, SUM(a.profile_views) as profile_views, SUM(a.special_offer_clicks) as special_offer_clicks, SUM(foursquare_checkins) as foursquare_checkins, SUM(facebook_likes) as facebook_likes, SUM(facebook_talking_about) as facebook_talking_about, SUM(facebook_where_here) as facebook_where_here
             FROM website_yext_analytics a
             LEFT JOIN website_yext_location l ON l.website_yext_location_id = a.location_id
             WHERE l.`website_id` = ". $this->account->id ." $where AND `date` BETWEEN '". $start_date->format('Y-m-d') ."' AND '". $end_date->format('Y-m-d') ."'
             GROUP BY `date`
             ORDER BY `date`"
            , PDO::FETCH_ASSOC
        );
    }

}
