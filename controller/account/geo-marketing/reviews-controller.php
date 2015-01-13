<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 04/12/14
 * Time: 14:59
 */

class ReviewsController extends BaseController {

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->title = _('Customer Reviews | GeoMarketing');
    }

    /**
     * Index
     * @return TemplateResponse
     */
    public function index() {
        if ( !$this->user->account->get_settings( 'yext-customer-reviews' ) ) {
            return new RedirectResponse('/geo-marketing/locations');
        }

        $location = new WebsiteYextLocation();
        $location_list = $location->get_all( $this->user->account->id );

        $locations = [];
        foreach ( $location_list as $location ) {
            $locations[ $location->id ] = $location;
        }

        if ( !$locations ) {
            return new RedirectResponse( '/geo-marketing/locations' );
        }

        library('yext');
        $yext = new YEXT( $this->user->account );
        $reviews = $yext->get(
            'reviews'
            , [ 'locationIds' => array_keys( $locations ) ]
        )->reviews;

        // Get Site IDs
        $sites = [];

        // They were returnings Reviews without Location ID
        // So we make sure they belong to an Account's location
        foreach ( $reviews as $k => $r ) {
            if ( !isset( $locations[ $r->locationId ] ) ) {
                unset( $reviews[$k] );
                continue;
            }

            $sites[$r->siteId] = $r->siteId;
        }

        $website_yext_review = new WebsiteYextReview();
        $website_yext_review->remove_by_account_id( $this->user->account->id );
        $website_yext_review->insert_bulk( $reviews, $this->user->account->id );

        $this->resources->javascript( 'geo-marketing/reviews/index' );

        return $this->get_template_response( 'geo-marketing/reviews/index' )
            ->menu_item('geo-marketing/reviews')
            ->kb(153)
            ->set( compact( 'locations', 'sites' ) );
    }

    /**
     * List All
     * @return DataTableResponse
     */
    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        $location = new WebsiteYextLocation();
        $location_list = $location->get_all( $this->user->account->id );

        $locations = [];
        foreach ( $location_list as $location ) {
            $locations[ $location->id ] = $location;
        }

        // Set Order by
        $dt->order_by( '`location_id`', '`site_id`', '`title`', '`url`' );
        $dt->search( array( '`location_id`' => false, '`site_id`' => false, '`title`' => false, '`url`' => false, '`content`' => false,'`date_created`' => false ) );

        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );
        $location_id = $_SESSION['reviews']['location-id'];
        if ( $location_id ) {
            $dt->add_where( " AND `location_id` = " . $_SESSION['reviews']['location-id'] );
        }
        $site_id = $_SESSION['reviews']['site-id'];
        if ( $site_id ) {
            $dt->add_where( " AND `site_id` = '" . $_SESSION['reviews']['site-id'] . "'" );
        }

        // Get Reviews
        $review = new WebsiteYextReview();
        $reviews = $review->list_all( $dt->get_variables() );
        $dt->set_row_count( $review->count_all( $dt->get_count_variables() ) );

        $data = [];
        foreach ( $reviews as $review ) {

            $data[] = [
                $locations[ $review->location_id ]->name
                . '<br><a href="'. $review->url .'" title="See review" target="_blank">See Review</a>'
                , $review->site_id
                , $review->author_name
                , $review->content
                , $review->rating
                , $review->date_created
            ];
        }

        $dt->set_data( $data );

        return $dt;
    }

}
