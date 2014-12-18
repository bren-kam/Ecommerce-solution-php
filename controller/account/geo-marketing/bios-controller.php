<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 04/12/14
 * Time: 14:59
 */

class BiosController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->title = _('Bios | GeoMarketing');
    }


    /**
     * Index
     * @return TemplateResponse
     */
    public function index() {

        $location = new WebsiteYextLocation();
        $locations = $location->get_all( $this->user->account->id );

        library('yext');
        $yext = new YEXT( $this->user->account );

        $website_yext_bio = new WebsiteYextBio();
        $website_yext_bio->remove_by_account_id( $this->user->account->id );

        foreach ( $locations as $location ) {
            $list_id = "{$this->user->account->id}-{$location->id}-bios";
            $response = $yext->get( "lists/{$list_id}" );
            if ( !isset( $response->errors ) ) {
                $website_yext_bio->insert_bulk( $response->sections[0]->items, $location->id, $this->user->account->id );
            }
        }

        return $this->get_template_response( 'geo-marketing/bios/index' )
            ->menu_item('geo-marketing/bios/list')
            ->kb( 148 );
    }

    /**
     * List All
     * @return DataTableResponse
     */
    public function list_all() {
        $dt = new DataTableResponse( $this->user );

        $dt->order_by( 'b.`website_yext_bio_id`', 'b.`name`' );
        $dt->search( array( '`website_yext_bio_id`' => false, 'b.`name`' => false ) );
        $dt->add_where( " AND b.`website_id` = " . (int) $this->user->account->id );

        // Get Bios
        $bio = new WebsiteYextBio();
        $bios = $bio->list_all( $dt->get_variables() );
        $dt->set_row_count( $bio->count_all( $dt->get_count_variables() ) );

        $data = [];
        $delete_nonce = nonce::create( 'delete' );
        foreach ( $bios as $bio ) {
            $data[] = [
                $bio->name .
                '<br><a href="/geo-marketing/bios/add-edit/?id=' . $bio->website_yext_bio_id . '">Edit</a>
                | <a href="/geo-marketing/bios/delete/?id=' . $bio->website_yext_bio_id . '&_nonce='.$delete_nonce.'" confirm="Do you want to Delete this Bio? Cannot be Undone.">Delete</a>'
                , $bio->location
            ];
        }

        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Add Edit
     * @return TemplateResponse
     */
    public function add_edit() {

        $website_yext_location = new WebsiteYextLocation();
        $location_list = $website_yext_location->get_all( $this->user->account->id );
        $locations = [];
        foreach( $location_list as $location ) {
            $locations[ $location->id ] = $location->name;
        }

        $website_yext_bio = new WebsiteYextBio();
        $website_yext_bio->get( $_REQUEST['id'], $this->user->account->id );

        $location_id = $website_yext_bio->website_yext_location_id ? $website_yext_bio->website_yext_location_id : $_REQUEST['location-id'];

        library('yext');
        $bio = [];
        $yext = new YEXT( $this->user->account );
        $list_id = "{$this->user->account->id}-{$location_id}-bios";
        $list = $yext->get( "lists/{$list_id}" );
        if ( isset( $list->sections[0]->items ) ) {
            $list_item_index = null;  // will be use to know if update
            if ( isset( $_REQUEST['id'] ) ) {
                foreach ($list->sections[0]->items as $i => $yext_bio) {
                    if ($yext_bio->id == $_REQUEST['id']) {
                        $bio = (array)$yext_bio;
                        $list_item_index = $i;
                        break;
                    }
                }
            }
            $create_list = false;
        } else {
            $create_list = true;
        }

        $form = new BootstrapForm( 'add-edit-bio' );

        $form->add_field( 'hidden', 'id', $bio['id'] );

        $select = $form->add_field( 'select', 'Location', 'location-id', $website_yext_bio->website_yext_location_id )
            ->options( $locations );
        if ( $website_yext_bio->website_yext_location_id ) {
            $select->attribute( 'disabled', 'disabled' );
            $form->add_field( 'hidden', 'location-id', $website_yext_bio->website_yext_location_id );
        }

        $form->add_field( 'text', 'Name', 'name', $bio['name'] )
            ->add_validation( 'req', 'A Name is required' );
        $form->add_field( 'textarea', 'Description', 'description', $bio['description'] )
            ->add_validation( 'req', 'A Description is required' );
        // TODO: Media Manager Field Type
        // $form->add_field( 'image', 'photo', 'photo', $bio['photo'] );
        $form->add_field( 'textarea', 'Education', 'education', is_array( $bio['education'] ) ? implode( "\n", $bio['education'] ) : '' );
        $form->add_field( 'textarea', 'Certifications', 'certifications', is_array( $bio['certifications'] ) ? implode( "\n", $bio['certifications'] ) : '' );
        $form->add_field( 'textarea', 'Services', 'services', is_array( $bio['services'] ) ? implode( "\n", $bio['services'] ) : '' );
        $form->add_field( 'text', 'URL', 'url', $bio['url'] );

        if ( $form->posted() ) {
            $list_id = "{$this->user->account->id}-{$location_id}-bios";
            $bio = $_POST;
            unset( $bio['_nonce'] );
            unset( $bio['location-id'] );
            if ( !$bio['id'] ) {
                $bio['id'] = time();
            }

            if ( $create_list ) {
                $list = [
                    'id' => $list_id
                    , 'name' => "{$this->user->account->title} - {$locations[$location_id]} Bios"
                    , 'title' => "{$this->user->account->title} - {$locations[$location_id]} Bios"
                    , 'type' => 'BIOS'
                    , 'publish' => true
                    , 'sections' => [
                        'id' => $list_id . '-section'
                        , 'name' => "{$this->user->account->title} - {$locations[$_POST['location-id']]} Bios"
                        , 'items' => [
                            $bio
                        ]
                    ]
                ];
                $response = $yext->post( 'lists', $list );
            } else {
                if ( $list_item_index !== NULL) {
                    $list->sections[0]->items[$list_item_index] = (object) $bio;
                } else {
                    $list->sections[0]->items[] = (object) $bio;
                }
                $response = $yext->put( "lists/{$list_id}", $list );
            }

            if ( isset( $response->errors ) ) {
                $this->notify( 'Your Bio could not be processed. ' . $response->errors[0]->message , false );
            }

            return new RedirectResponse( '/geo-marketing/bios' );
        }

        $form_html = $form->generate_form();

        return $this->get_template_response( 'geo-marketing/bios/add-edit' )
            ->menu_item( 'geo-marketing/bios/add-edit' )
            ->set( compact( 'form_html' ) )
            ->kb( 151 );
    }

    public function delete() {

        if ( !$this->verified() ) {
            $this->notify( 'An error happened, please try again.', true );
            return new RedirectResponse( '/geo-marketing/bios' );
        }

        $website_yext_bio = new WebsiteYextBio();
        $website_yext_bio->get( $_REQUEST['id'], $this->user->account->id );

        library('yext');
        $yext = new YEXT( $this->user->account );
        $list_id = "{$this->user->account->id}-{$website_yext_bio->website_yext_location_id}-bios";
        $list = $yext->get( "lists/{$list_id}" );
        if ( isset( $list->sections[0]->items ) ) {
            if ( isset( $_REQUEST['id'] ) ) {
                foreach ($list->sections[0]->items as $i => $yext_bio) {
                    if ($yext_bio->id == $_REQUEST['id']) {
                        // we found it! remove from the list and update
                        array_splice( $list->sections[0]->items, $i, 1 );
                        $response = $yext->put( "lists/{$list_id}", $list );
                        if ( isset( $response->errors ) ) {
                            $this->notify( 'Your Bio could not be deleted. ' . $response->errors[0]->message , false );
                        }
                        break;
                    }
                }
            }
        }

        return new RedirectResponse( '/geo-marketing/bios' );
    }

} 