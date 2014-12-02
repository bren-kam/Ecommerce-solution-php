<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 26/11/14
 * Time: 15:55
 */

class LocationsController extends BaseController {

    public function index() {
        return $this->get_template_response( 'geo-marketing/locations/index' )
            ->menu_item('geo-marketing/locations/list');
    }

    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        // Get data from YEXT
        $delete_nonce = nonce::create( 'delete' );
        $data = [
            [
                '12
                    <br><a href="/geo-marketing/locations/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/locations/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Location? Cannot be Undone.">Delete</a>
                '
                , "Location Name 12"
                , "Address_1<br>City, State, ZIP "
                , "Last Updated"
            ]
            , [
            '12
                    <br><a href="/geo-marketing/locations/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/locations/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Location? Cannot be Undone.">Delete</a>
                '
                , "Location Name 12"
                , "Address_1<br>City, State, ZIP "
                , "Last Updated"
            ]
            , [
                '12
                    <br><a href="/geo-marketing/locations/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/locations/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Location? Cannot be Undone.">Delete</a>
                '
                , "Location Name 12"
                , "Address_1<br>City, State, ZIP "
                , "Last Updated"
            ]
        ];
        $dt->set_data($data);

        return $dt;
    }

    public function delete() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $website_yext_location = new WebsiteYextLocation();
        $website_yext_location->get( $_GET['id'], $this->user->account->id );
        if ( $website_yext_location->yext_id ) {
            //TODO: DELETE FROM YEXT
            $website_yext_location->delete();
        }

        $response->add_response( 'reload_datatable', 'reload_datatable' );
        return $response;
    }

    public function add_edit() {

        $website_yext_location = new WebsiteYextLocation();
        $location = [];

        if ( isset( $_GET['id'] ) ) {
            $website_yext_location->get( $_GET['id'], $this->user->account->id );
            $location = [];  // GET FROM YEXT
        }

        $form = new BootstrapForm( 'add-edit-location' );

        $form->add_field( 'hidden', 'id', $location['id'] );

        $form->add_field( 'text', 'Name', 'name', $location['name'] );
        $form->add_field( 'text', 'Address Line 1', 'address', $location['address'] );
//        $form->add_field( 'text', 'Address Line 2', 'address2', $location['address2'] );
//        $form->add_field( 'checkbox', 'Suppress Address', 'suppressAddress', $location['suppressAddress'] );
        $form->add_field( 'text', 'City', 'city', $location['city'] );
        $form->add_field( 'text', 'State', 'state', $location['state'] );
        $form->add_field( 'text', 'ZIP', 'zip', $location['zip'] );
        $form->add_field( 'text', 'Phone', 'phone', $location['phone'] );
        $form->add_field( 'checkbox', 'Is Phone Tracked', 'isPhoneTracked', $location['isPhoneTracked'] );
        $form->add_field( 'text', 'Fax Phone', 'faxPhone', $location['faxPhone'] );
        $form->add_field( 'text', 'Mobile Phone', 'mobilePhone', $location['mobilePhone'] );
        $form->add_field( 'text', 'Toll Free Phone', 'tollFreePhone', $location['tollFreePhone'] );
        $form->add_field( 'text', 'TTY Phone', 'ttyPhone', $location['ttyPhone'] );
        // TODO: Get Cateogory IDs from YEXT
//        $form->add_field( 'select', 'Category', 'categories[]', $location['categories'] )
//            ->attribute( 'multiple', 'multiple' )
//            ->options( [] );
        $form->add_field( 'text', 'Special Offer', 'specialOffer', $location['specialOffer'] );
        $form->add_field( 'text', 'Special Offer URL', 'specialOfferUrl', $location['specialOfferUrl'] );
        $form->add_field( 'text', 'Website URL', 'websiteUrl', $location['websiteUrl'] );
        $form->add_field( 'text', 'Reservations URL', 'reservationsUrl', $location['reservationsUrls'] );
        $form->add_field( 'text', 'Hours', 'hours', $location['hours'] );
        $form->add_field( 'text', 'Additional Hours Text', 'additionalHoursText', $location['additionalHoursText'] );
        // TODO: Holiday Hour Arrays
        // $form->add_field( 'text', 'Holiday Hours', 'specialOffer', $location['specialOffer'] );
        $form->add_field( 'textarea', 'Description', 'description', $location['description'] )
            ->attribute( 'rte', '1' );
        $form->add_field( 'select', 'Payment Options', 'paymentOptions[]', $location['paymentOptions'] )
            ->attribute( 'multiple', 'multiple' )
            ->options( [
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
            ] );
        // TODO: Media Manager Field Type
        // $form->add_field( 'image', 'Logo', 'specialOffer', $location['specialOffer'] );
        $form->add_field( 'textarea', 'Video URLs (one per line)', 'videoUrls', $location['specialOffer'] ? implode( "\n", $location['specialOffer'] ) : '' );
        $form->add_field( 'text', 'Twitter Handle', 'twitterHandle', $location['twitterHandle'] );
        $form->add_field( 'text', 'Facebook Page URL', 'facebookPageUrl', $location['facebookPageUrl'] );
        // TODO: Media Manager Field Type
        // $form->add_field( 'image', 'Facebook Cover Photo', 'specialOffer', $location['specialOffer'] );
        // $form->add_field( 'image', 'Facebook Profile Picture', 'specialOffer', $location['specialOffer'] );
        $form->add_field( 'text', 'Year Stabilished', 'yearEstabilished', $location['yearEstabilished'] );
//        $form->add_field( 'text', 'Display Latitude', 'displayLat', $location['displayLat'] );
//        $form->add_field( 'text', 'Display Longutude', 'displayLon', $location['displayLon'] );
//        $form->add_field( 'text', 'Routable Latitude', 'routableLat', $location['routableLat'] );
//        $form->add_field( 'text', 'Routable Longitude', 'routableLon', $location['routableLon'] );
        $form->add_field( 'textarea', 'Emails (one per line)', 'emails', $location['emails'] ? implode( "\n", $location['emails'] ) : '' );
        $form->add_field( 'textarea', 'Specialities (one per line)', 'specialities', $location['specialities'] ? implode( "\n", $location['specialities'] ) : '' );
        $form->add_field( 'textarea', 'Services (one per line)', 'services', $location['services'] ? implode( "\n", $location['services'] ) : '' );
        $form->add_field( 'textarea', 'Brands (one per line)', 'brands', $location['brands'] ? implode( "\n", $location['brands'] ) : '' );
        $form->add_field( 'textarea', 'Languages (one per line) ', 'languages', $location['languages'] ? implode( "\n", $location['languages'] ) : '' );
        $form->add_field( 'textarea', 'Keywords (one per line)', 'keywords', $location['keywords'] ? implode( "\n", $location['keywords'] ) : '' );
//        $form->add_field( 'textarea', 'Lists (one per line)', 'lists', $location['lists'] ? implode( "\n", $location['lists'] ) : '' );

        $form->add_field( 'checkbox', 'List top 100 products on location', 'synchronize-products', $website_yext_location->synchronize_products );

        if ( $this->verified() ) {

            $post = $_POST;

            $website_yext_location->synchronize_products = (int) isset( $post['synchronize-products'] );

            // remove unwanted fields
            unset( $post['_nonce'] );
            unset( $post['synchronize-products'] );

            // TODO: SEND TO YEXT

            // save
            if ( $website_yext_location->yext_id ) {
                $website_yext_location->save();
            } else {
                $website_yext_location->yext_id = rand(1, 9999);  // TODO: Get from YEXT RESPONSE
                $website_yext_location->create();
            }

        }

        $form_html = $form->generate_form();

        return $this->get_template_response( 'geo-marketing/locations/add-edit' )
            ->set( compact( 'form_html' ) );
    }

} 