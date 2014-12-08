<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 04/12/14
 * Time: 14:59
 */

class ListingsController extends BaseController {

    /**
     * Index
     * @return TemplateResponse
     */
    public function index() {
        $locations = new WebsiteYextLocation();
        $locations = $locations->get_all( $this->user->account->id );

        $this->resources->javascript( 'geo-marketing/listings/index' );

        return $this->get_template_response( 'geo-marketing/listings/index' )
            ->menu_item('geo-marketing/listings/list')
            ->set( compact( 'locations' ) );
    }

    /**
     * List All
     * @return DataTableResponse
     */
    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        // Get data from YEXT
        $delete_nonce = nonce::create( 'delete' );
        $data = [
            [
                'My Location'
                , '2FINDLOCAL'
                , "LIVE"
                , '<a href="http://google.com">http://google.com</a>'
            ]
            , [
                'My Location'
                , '2FINDLOCAL'
                , "LIVE"
                , '<a href="http://google.com">http://google.com</a>'
            ]
            , [
                'My Location'
                , '2FINDLOCAL'
                , "LIVE"
                , '<a href="http://google.com">http://google.com</a>'
            ]
        ];
        $dt->set_data($data);

        return $dt;
    }

} 