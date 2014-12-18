<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 26/11/14
 * Time: 15:55
 */

class LocationsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->title = _('Locations | GeoMarketing');
    }

    /**
     * Index
     * @return $this
     */
    public function index() {
        return $this->get_template_response( 'geo-marketing/locations/index' )
            ->menu_item('geo-marketing/locations/list')
            ->kb( 147 );
    }

    /**
     * List All
     * @return DataTableResponse
     */
    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( '`name`', '`address`', '`last_update`', '`status`' );
        $dt->search( array( '`name`' => false, '`address`' => false, '`status`' => false ) );
        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );

        // Get Locations
        $location = new WebsiteYextLocation();
        $locations = $location->list_all( $dt->get_variables() );
        $dt->set_row_count( $location->count_all( $dt->get_count_variables() ) );

        $data = [];
        foreach ( $locations as $location ) {
            $data[] = [
                $location->name .
                '<br><a href="/geo-marketing/locations/add-edit/?id=' . $location->id . '">Edit</a>'
                , $location->address
                , $location->status
            ];

        }

        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     * @return AjaxResponse
     */
    public function delete() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $website_yext_location = new WebsiteYextLocation();
        $website_yext_location->get( $_GET['id'], $this->user->account->id );

        if ( $website_yext_location->id ) {
            library('yext');
            $yext = new YEXT( $this->user->account );
            $response = $yext->delete( "locations/{$_GET['id']}" );
            if ( isset( $response->errors ) ) {
                $response->notify( 'Your Location could not be deleted. ' . $response->errors[0]->message , false );
            } else {
                $website_yext_location->remove();
                $response->add_response( 'reload_datatable', 'reload_datatable' );
            }
        }

        return $response;
    }

    /**
     * Add Edit
     * @return TemplateResponse
     */
    public function add_edit() {

        $status_codes = [
            200 => 'APPROVED',
            201 => 'APPROVED',
            202 => 'SUBMITTED'
        ];

        $payment_options = [
            'AMERICANEXPRESS' => 'American Express'
            ,'CASH' => 'Cash'
            ,'CHECK' => 'Check'
            ,'DINERSCLUB' => 'Diners Club'
            ,'DISCOVER' => 'Discover'
            ,'FINANCING' => 'Financing'
            ,'INVOICE' => 'Invoice'
            ,'MASTERCARD' => 'Mastercard'
            ,'TRAVELERSCHECK' => 'Travelers Check'
            ,'VISA' => 'Visa'
        ];

        $yext_category_id = $this->user->account->get_settings( 'yext-category' );
        if ( !$yext_category_id ) {
            $this->notify( 'Your account has no Geomarketing Category configured. Please contact your Online Specialist', false );
            return new RedirectResponse( '/geo-marketing/locations' );
        }

        $yext_website_subscription_id = $this->user->account->get_settings( 'yext-subscription-id' );

        // Check if they can add more Locations
        if ( !isset( $_REQUEST['id'] ) ) {
            $yext_max_locations = $this->user->account->get_settings( 'yext-max-locations' );
            if ( !$yext_max_locations ) {
                $yext_max_locations = 1;
                $this->user->account->set_settings( [ 'yext-max-locations' => $yext_max_locations ] );
            }
            $website_yext_location = new WebsiteYextLocation();
            $locations = $website_yext_location->get_all( $this->user->account->id );
            if ( count( $locations ) >= $yext_max_locations ) {
                $this->notify( 'You have reached the maximum number of locations for you account. Please contact your Online Specialist', false );
                return new RedirectResponse( '/geo-marketing/locations' );
            }
        }

        library('yext');
        $yext = new YEXT( $this->user->account );

        $website_yext_location = new WebsiteYextLocation();
        $location = [];

        if ( isset( $_GET['id'] ) ) {
            $website_yext_location->get( $_GET['id'], $this->user->account->id );
            $location = (array) $yext->get( "locations/{$_GET['id']}" );
        }

        $location['synchronize-products'] = $website_yext_location->synchronize_products;
        $location['logo'] = (array) $location['logo'];
        // if have 1 photo - it's store photo
        // if have 5 photos - first one is store photo
        // ignore if have from 2-4
        if ( isset( $location['photos'] ) && ( count( $location['photos'] ) == 1 ||  count( $location['photos'] ) == 5 ) ) {
            $location['store-photo'] = $location['photos'][0]->url;
        }

        if ( $this->verified() ) {

            $post = $_POST;

            $website_yext_location->synchronize_products = (int) isset( $post['synchronize-products'] );
            $website_yext_location->name = $post['locationName'];
            $website_yext_location->address = "{$post['address']}<br>{$post['city']}, {$post['state']} {$post['zip']}";
            $website_yext_location->website_id = $this->user->account->id;

            if ( $post['logo-url'] ) {
                $post['logo'] = [
                    'url' => $post['logo-url']
                    , 'description' => ''
                ];
                unset($post['logo-url']);
            }

            if ( $post['store-photo'] ) {
                if ( isset( $location['photos'] ) && $location['photos'] ) {
                    $post['photos'] = $location['photos'];
                    // if have 1 photo - it's store photo
                    // if have 5 photos - first one is store photo
                    // unshift if have 4 photos
                    // ignore if have 2-3 photos
                    // See GSRA-341 and GSRA-342
                    if ( count( $post['photos'] ) == 1 ||  count( $post['photos'] ) == 5 ) {
                        $post['photos'][0]->url = $post['store-photo'];
                    } else if ( count( $post['photos'] ) == 4 ) {
                        array_unshift( $post['photos'], [
                            'url' => $post['store-photo']
                        ] );
                    }
                } else {
                    $post['photos'] = [[
                        'url' => $post['store-photo']
                    ]];
                }
                unset( $post['store-photo'] );
            }

            // remove unwanted fields
            unset( $post['_nonce'] );
            unset( $post['synchronize-products'] );
            if ( !$post['services'] ) {
                unset( $post['services'] );
            }
            if ( !$post['brands'] ) {
                unset( $post['brands'] );
            }
            if ( !$post['languages'] ) {
                unset( $post['languages'] );
            }
            if ( !$post['keywords'] ) {
                unset( $post['keywords'] );
            }
            if ( !$post['logo-url'] ) {
                unset( $post['logo-url'] );
            }
            if ( !$post['store-photo'] ) {
                unset( $post['store-photo'] );
            }

            // TODO: Get from Config
            $post['categoryIds'] = [ $yext_category_id ];

            if ( !$website_yext_location->id ) {
                // Create
                $website_yext_location->create();
                $post['id'] = $website_yext_location->id;
                $response = $yext->post( 'locations', $post );
                if ( isset( $response->errors ) ) {
                    $this->notify( 'Your Location could not be created. ' . $response->errors[0]->message , false );
                    $website_yext_location->remove();
                    return new RedirectResponse( '/geo-marketing/locations' );
                } else {
                    $website_yext_location->status = isset( $status_codes[$yext->last_response_code] ) ? $status_codes[$yext->last_response_code] : 'REJECTED';
                    $website_yext_location->save();
                }

                // Add add Location to Subscription
                if ( $yext_website_subscription_id ) {
                    $subscription = $yext->get( "subscriptions/{$yext_website_subscription_id}" );
                    $subscription->locationIds[] = $website_yext_location->id;
                    $yext->put( "subscriptions/{$yext_website_subscription_id}", $subscription );
                } else {
                    // If we don't have a Subscription ID, create it!
                    $subscription = $yext->post( 'subscriptions', [
                        'offerId' => YEXT::OFFER_ID
                        , 'locationIds' => [ $website_yext_location->id ]
                    ]);
                    $this->user->account->set_settings( [ 'yext-subscription-id' => $subscription->id ] );
                }
            } else {
                // Update
                $website_yext_location->save();
                $response = $yext->put( "locations/{$website_yext_location->id}", $post );
                if ( isset( $response->errors ) ) {
                    $this->notify( 'Your Location could not be updated. ' . $response->errors[0]->message , false );
                    return new RedirectResponse( '/geo-marketing/locations' );
                } else {
                    $website_yext_location->status = isset( $status_codes[$yext->last_response_code] ) ? $status_codes[$yext->last_response_code] : 'REJECTED';
                    $website_yext_location->save();
                }
            }
            return new RedirectResponse( '/geo-marketing/locations' );

        }

//        $form_html = $form->generate_form();

        $this->resources->css( 'geo-marketing/locations/add-edit', 'media-manager' )
            ->javascript( 'geo-marketing/locations/add-edit', 'media-manager' );

        return $this->get_template_response( 'geo-marketing/locations/add-edit' )
            ->menu_item('geo-marketing/locations/add-edit')
            ->set( compact( 'location', 'payment_options' ) )
            ->kb( 148 );
    }

} 