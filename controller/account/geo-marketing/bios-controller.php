<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 04/12/14
 * Time: 14:59
 */

class BiosController extends BaseController {

    /**
     * Index
     * @return TemplateResponse
     */
    public function index() {
        return $this->get_template_response( 'geo-marketing/bios/index' )
            ->menu_item('geo-marketing/bios/list');
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
                '12
                    <br><a href="/geo-marketing/bios/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/bios/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Bio? Cannot be Undone.">Delete</a>
                '
                , "Name 12"
                , "Last Updated"
            ]
            , [
                '12
                    <br><a href="/geo-marketing/bios/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/bios/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Bio? Cannot be Undone.">Delete</a>
                '
                , "Name 12"
                , "Last Updated"
            ]
            , [
                '12
                    <br><a href="/geo-marketing/bios/add-edit/?id=12">Edit</a>
                    | <a href="/geo-marketing/bios/delete/?id=12&_nonce='.$delete_nonce.'" ajax="1" confirm="Do you want to Delete this Bio? Cannot be Undone.">Delete</a>
                '
                , "Name 12"
                , "Last Updated"
            ]
        ];
        $dt->set_data($data);

        return $dt;
    }

    /**
     * Add Edit
     * @return TemplateResponse
     */
    public function add_edit() {
        if ( isset( $_GET['id'] ) ) {
            // $website_yext_location->get( $_GET['id'], $this->user->account->id );
            $bio = [];  // GET FROM YEXT
        }

        $form = new BootstrapForm( 'add-edit-bio' );

        $form->add_field( 'hidden', 'id', $bio['id'] );
        $form->add_field( 'text', 'Name', 'name', $bio['name'] );
        $form->add_field( 'textarea', 'Description', 'description', $bio['description'] );
        // TODO: Media Manager Field Type
        // $form->add_field( 'image', 'photo', 'photo', $bio['photo'] );
        $form->add_field( 'textarea', 'Education', 'education', $bio['education'] );
        $form->add_field( 'textarea', 'Certifications', 'certifications', $bio['certifications'] );
        $form->add_field( 'textarea', 'Services', 'services', $bio['services'] );
        $form->add_field( 'text', 'URL', 'url', $bio['url'] );

        $form_html = $form->generate_form();

        return $this->get_template_response( 'geo-marketing/bios/add-edit' )
            ->menu_item( 'geo-marketing/bios/add-edit' )
            ->set( compact( 'form_html' ) );
    }

} 