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
        $delete_nonce = nonce::create( 'delete' );

        $data = [];
        foreach ( $locations as $location ) {
            $data[] = [
                $location->name .
                '<br><a href="/geo-marketing/locations/add-edit/?id=' . $location->id . '">Edit</a>' .
                ' | <a href="/geo-marketing/locations/delete/?id=' . $location->id . '&_nonce=' . $delete_nonce . '" ajax="1" confirm="Do you want to remove this Location? Cannot be undone">Delete</a>' .
                ' | <a href="/geo-marketing/locations/import-products/?id=' . $location->id . '" >Import Products</a>'
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
            $website_yext_location->remove();

            // There is no DELETE location for YEXT - But remove it's subscriptions
            $yext_website_subscription_id = YEXT::$SUBSCRIPTION_ID; // $this->user->account->get_settings( 'yext-subscription-id' );
            if ( $yext_website_subscription_id ) {
                $subscription = $yext->get( "subscriptions/{$yext_website_subscription_id}" );
                if ( $subscription && ($key = array_search((int)$_GET['id'], $subscription->locationIds)) !== false ) {
                    unset( $subscription->locationIds[$key] );
                    if ( empty( $subscription->locationIds ) ) {
                        $subscription->status = 'CANCELED';
                        $this->user->account->set_settings( [ 'yext-subscription-id' => null ] );
                    }
                    $yext->put( "subscriptions/{$yext_website_subscription_id}", $subscription );
                }
            }

            $response->add_response( 'reload_datatable', 'reload_datatable' );
        }

        return $response;
    }

    /**
     * Add Edit
     * @return TemplateResponse
     */
    public function add_edit() {

        library('yext');

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

        $yext_category_list = ( new WebsiteYextCategory() )->get_all();
        $yext_categories = [];
        foreach ( $yext_category_list as $category ) {
            $yext_categories[ $category->id ] = $category->name;
        }

        $yext_website_subscription_id = YEXT::$SUBSCRIPTION_ID;  // $this->user->account->get_settings( 'yext-subscription-id' );

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

        $yext = new YEXT( $this->user->account );

        $website_yext_location = new WebsiteYextLocation();
        $location = [];

        if ( isset( $_GET['id'] ) ) {
            $website_yext_location->get( $_GET['id'], $this->user->account->id );
            $location = (array) $yext->get( "locations/{$_GET['id']}" );
            if ( isset( $location['errors'] ) ) {
                $this->notify( "Location {$_GET['id']} not found", false );
                return new RedirectResponse('/geo-marketing/locations' );
            }
        }

        $location['synchronize-products'] = $website_yext_location->synchronize_products;
        $location['logo'] = (array) $location['logo'];

        if ( $location['photos'] ) {
            $location['custom-photos'] = [];
            foreach( $location['photos'] as $cp ) {
                $location['custom-photos'][] = (array) $cp;
            }
        } else {
            $location['custom-photos'] = [ [], [], [], [], [] ];
        }

        $hours = explode( ',', $location['hours'] );
        $location['hours-array'] = [];
        foreach( $hours as $hour ) {
            $hour_pieces = explode( ':', $hour );
            $location[ (int)$hour_pieces[0] ] = [
                'open' => "{$hour_pieces[1]}:{$hour_pieces[2]}"
                , 'close' => "{$hour_pieces[3]}:{$hour_pieces[4]}"
            ];
        }

        $period = new DatePeriod(
             new DateTime('2015-01-01 00:00:00'),
             new DateInterval('PT15M'),
             new DateTime('2015-01-02 00:00:00')
        );
        $days = [
            1 => 'Sunday'
            , 2 => 'Monday'
            , 3 => 'Tuesday'
            , 4 => 'Wednesday'
            , 5 => 'Thursday'
            , 6 => 'Friday'
            , 7 => 'Saturday'
        ];
        $hour_options = [];
        foreach ( $period as $time ) {
            $hour_options[$time->format('H:i')] = $time->format('g:i A');
        }

        if ( $this->verified() ) {

            $post = $_POST;

            $post['locationName'] = ucwords( strtolower( $post['locationName'] ) );
            $post['address'] = ucwords( strtolower( $post['address'] ) );
            $post['address2'] = ucwords( strtolower( $post['address2'] ) );
            $post['city'] = ucwords( strtolower( $post['city'] ) );

            // Some Custom Valiations
            // All of this element can't have more than 10 lines
            $error_messages = [];
            foreach( [ 'videoUrls', 'emails', 'specialties', 'services', 'brands', 'languages', 'keywords' ] as $element ) {
                $post[$element] = explode( "\n", $post[$element] );
                if ( count( $post[$element] ) > 10 ) {
                    $error_messages[] = "The field '$element' can not have more than 10 lines";
                }

                // Except for emails, each line can't have more than 50 characters
                if ( $element != 'emails' ) {
                    foreach ( $post[$element] as $line ) {
                        if ( strlen( $line ) > 50 )  {
                            $error_messages[] = "The each line in '$element' can't exceed 50 characters";
                        }
                    }
                }
            }

            if ( $error_messages ) {
                $error_messages = '<div class="alert alert-danger">'. implode( '<br>', $error_messages ) .'</div>';
                $location = $post;
            } else {

                $website_yext_location->synchronize_products = (int) isset( $post['synchronize-products'] );
                $website_yext_location->name = $post['locationName'];
                $website_yext_location->address = "{$post['address']} {$post['address2']}<br>{$post['city']}, {$post['state']} {$post['zip']}";
                $website_yext_location->website_id = $this->user->account->id;

                $posted_fields = $post;

                if ( $post['logo-url'] ) {
                    $post['logo'] = [
                        'url' => $post['logo-url']
                        , 'description' => ''
                    ];
                    unset($post['logo-url']);
                }

                if ( $post['custom-photos'] ) {
                    foreach( $post['custom-photos'] as $k => $custom_photo ) {
                        if ( $custom_photo ) {
                            $photo_index = $k;
                            $post['photos'][$photo_index] = [
                                'url' => $custom_photo
                            ];
                        }
                    }
                }

                $post['photos'] = array_values( $post['photos'] );

                $hours = [];
                foreach ( $post['hours-array'] as $day_number => $day_hours ) {
                    if ( !$day_hours['open'] || !$day_hours['close'] )
                        continue;
                    $hours[] = implode( ':', [$day_number, $day_hours['open'], $day_hours['close'] ] );
                }
                $post['hours'] = implode( ',', $hours );

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
                unset( $post['custom-photos'] );
                unset( $post['hours-array'] );

                if ( !$website_yext_location->id ) {
                    // Create
                    $website_yext_location->create();
                    $post['id'] = $website_yext_location->id;
                    $response = $yext->post( 'locations', $post );
                    if ( isset( $response->errors ) ) {
                        $error_messages = '<div class="alert alert-danger">Your Location could not be created. ' . $response->errors[0]->message.'</div>';
                        $website_yext_location->remove();
                        $location = $posted_fields;
                        // $this->notify( 'Your Location could not be created. ' . $response->errors[0]->message , false );
                        // return new RedirectResponse( '/geo-marketing/locations' );
                    } else {
                        $website_yext_location->status = isset( $status_codes[$yext->last_response_code] ) ? $status_codes[$yext->last_response_code] : 'REJECTED';
                        $website_yext_location->save();

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
                        return new RedirectResponse( '/geo-marketing/locations' );
                    }
                } else {
                    // Update
                    $response = $yext->put( "locations/{$website_yext_location->id}", $post );
                    if ( isset( $response->errors ) ) {
                        $error_messages = '<div class="alert alert-danger">Your Location could not be updated. ' . $response->errors[0]->message.'</div>';
                        $location = $posted_fields;
                        // $this->notify( 'Your Location could not be updated. ' . $response->errors[0]->message , false );
                        // return new RedirectResponse( '/geo-marketing/locations' );
                    } else {
                        $website_yext_location->status = isset( $status_codes[$yext->last_response_code] ) ? $status_codes[$yext->last_response_code] : 'REJECTED';
                        $website_yext_location->save();
                        return new RedirectResponse( '/geo-marketing/locations' );
                    }
                }

            }

        }

        $this->resources->css( 'geo-marketing/locations/add-edit', 'media-manager' )
            ->javascript( 'geo-marketing/locations/add-edit', 'media-manager' );

        return $this->get_template_response( 'geo-marketing/locations/add-edit' )
            ->menu_item('geo-marketing/locations/add-edit')
            ->set( compact( 'location', 'payment_options', 'yext_categories', 'error_messages', 'hour_options', 'days' ) )
            ->kb( 148 );
    }

    /**
     * Import Products
     * @return TemplateResponse
     */
    public function import_products() {

        $location = new WebsiteYextLocation();
        $location->get( $_GET['id'], $this->user->account->id );
        if ( !$location->id )
            return new RedirectResponse('/geo-marketing/locations/');

        if ( $this->verified() ) {

            $f = fopen( $_FILES['csv']['tmp_name'], 'r' );

            if ( !$f ) {
                $this->notify( 'A CSV File is Required', false );
                return new RedirectResponse( '/geo-marketing/locations/import-products' );
            }

            // skip first row -- headers
            $headers = fgetcsv( $f );

            $yext_items = [];
            $index = 0;
            $skipped = [];
            while ( ( $values = fgetcsv( $f ) ) !== FALSE ) {

                $index++;
                $row = array_combine( $headers, $values );

                if ( empty($row['ProductName']) ) {
                    $skipped[] = $row;
                    continue;
                }

                $item = [
                    'id' => "{$this->user->account->id}-cp-{$index}"
                    , 'name' => $row['ProductName']
                    , 'description' => $row['ProductDescription']
                    , 'idCode' => $row['IdCode']
                    , 'cost' => [
                        'type' => 'PRICE'
                        , 'price' => $row['ProductPrice']
                        , 'unit' => 'Each'
                    ]
                    , 'url' => $row['Url']
                ];

                $row['ProductOption'] = array_slice( $values, 3, 5 );
                $item_po = [];
                foreach ( $row['ProductOption'] as $po ) {
                    $po_pieces = explode( '|', $po );
                    if ( count($po_pieces) != 2 || !is_numeric( $po_pieces[1] ) )
                        continue;

                    $item_po[] = [
                        'name' => $po_pieces[0]
                        , 'price' => $po_pieces[1]
                    ];
                }
                if ( $item_po ) {
                    $item['cost']['options'] = $item_po;
                }

                $row['Photos'] = explode( '|', $row['Photos'] );
                $item_photos = [];
                foreach ( $row['Photos'] as $photo ) {
                    if ( !$photo )
                        continue;

                    $item_photos[] = [
                        'url' => $photo
                    ];
                }
                if( $item_photos ) {
                    $item['photos'] = $item_photos;
                }

                $yext_items[] = $item;
            }

            fclose( $f );

            $yext_list_id = "{$this->user->account->id}-imported-products";
            $yext_list = [
                'id' => $yext_list_id
                , 'name' => "Products for {$this->user->account->title}. {$yext_list_id}"
                , 'title' => "Products for {$this->user->account->title}"
                , 'type' => 'PRODUCTS'
                , 'publish' => true
                , 'sections' => [[
                    'id' => "{$yext_list_id}-section"
                    , 'name' => "Imported Products"
                    , 'items' => $yext_items
                ]]
            ];

            library('yext');
            $yext = new YEXT( $this->user->account );
            $yext_list_exists = $yext->get( "lists/{$yext_list_id}" );
            if ( isset( $yext_list_exists->id ) ) {
                $response = $yext->put( "lists/{$yext_list_id}", $yext_list );
            } else {
                $response = $yext->post( "lists", $yext_list );
            }

            $success = !isset( $response->errors );

            $yext_location = (array) $yext->get("locations/{$location->id}");
            if ( empty($yext_location['lists']) ) {
                $yext_location['lists'] = [[
                    'id' => $yext_list['id']
                    , 'name' => $yext_list['name']
                    , 'type' => 'PRODUCTS'
                ]];
            } else {
                $found = false;
                foreach( $yext_location['lists'] as $list ) {
                    if ( $list->name == $yext_list['name'] ) {
                        $found = true;
                        break;
                    }
                }
                if ( !$found ) {
                    $yext_location['lists'][] = [
                        'id' => $yext_list['id']
                        , 'name' => $yext_list['name']
                        , 'type' => 'PRODUCTS'
                    ];
                }
            }
            $yext->put("locations/{$location->id}", $yext_location);

            $file = new File( 'websites' . Config::key('aws-bucket-domain') );
            $file->upload_file( $_FILES['csv']['tmp_name'], "yext-products-{$location->id}.csv", $this->user->account->id . '/yext/' );

            // Cleanup
            unset( $yext_items );
            unset( $products );
        }

        $current_product_list = "http://websites.retailcatalog.us/1352/yext/yext-products-{$location->id}.csv";
        if ( !curl::check_file( $current_product_list, 5 ) ) {
            unset( $current_product_list );
        }

        return $this->get_template_response( 'geo-marketing/locations/import-products' )
            ->menu_item('geo-marketing/import-products')
            ->set( compact( 'location', 'skipped', 'success', 'response', 'current_product_list' ) )
            ->kb( 152 );
    }

}