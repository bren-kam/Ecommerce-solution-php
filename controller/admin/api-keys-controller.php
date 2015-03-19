<?php

class ApiKeysController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        return $this->get_template_response( 'api-keys/index' )
            ->add_title( 'API Keys' )
            ->menu_item( 'api-keys/index' );
    }

    public function list_all() {
        $dt = new DataTableResponse( $this->user );
        $dt->order_by( 'a.`api_key_id`', 'c.`name`', 'c.`domain`', 'a.`status`' );
        $dt->search( array( 'a.`api_key_id`' => true, 'c.`name`' => true, 'c.`domain`' => true, 'a.`status`' => true ) );

        $api_key = new ApiKey();
        $api_keys = $api_key->list_all( $dt->get_variables() );
        $count = $api_key->count_all( $dt->get_count_variables() );

        $data = array();
        foreach ( $api_keys as $row ) {
            $data[] = array(
                "<a href=\"/api-keys/manage/?id={$row->api_key_id}\">{$row->api_key_id}</a>"
                , $row->company
                , $row->domain
                , $row->status == 1 ? 'Active' : 'Disabled'
            );
        }

        $dt->set_data( $data );
        $dt->set_row_count( $count );

        return $dt;
    }

    public function manage() {
        $api_key = new ApiKey();
        $api_key->get( $_GET['id'] );

        if ( $this->verified() ) {
            $api_key->status = $_POST['status'];
            $api_key->company_id = $_POST['company-id'];
            if ( !$api_key->id ) {
                $api_key->key = md5(rand());
                $api_key->create();
            } else {
                $api_key->save();
            }

            $api_key->set_brands( $_POST['brands'] );
            $api_key->set_ashley_accounts( $_POST['ashley-accounts'] );
            $this->notify( 'API Key settings updated!' );
            return new RedirectResponse('/api-keys/manage/?id=' . $api_key->id);
        }

        $brands = array();
        $selected_brands = array();
        $api_key_brands = $api_key->get_brand_ids();
        $brand_objects = ( new Brand() )->get_all();
        foreach ( $brand_objects as $brand ) {
            $brands[] = array( 'id' => $brand->brand_id, 'name' => $brand->name );
            if ( in_array( $brand->brand_id, $api_key_brands ) ) {
                $selected_brands[] = array( 'id' => $brand->brand_id, 'name' => $brand->name );
            }
        }

        $ashley_accounts = array();
        $selected_ashley_accounts = array();
        $api_key_ashley_accounts = $api_key->get_ashley_accounts();
        $accounts = ( new AshleySpecificFeedGateway() )->get_feed_accounts();
        foreach ( $accounts as $account ) {
            $encrypted = $account->get_settings( 'ashley-ftp-username' );
            if ( isset( $ashley_accounts[$encrypted] ) )
                continue;

            $decrypted = security::decrypt( base64_decode( $encrypted ), ENCRYPTION_KEY );
            $ashley_accounts[$encrypted] = array( 'id' => $encrypted, 'name' => preg_replace( '/[^0-9]/', '', $decrypted ));
            if ( in_array( $encrypted, $api_key_ashley_accounts ) ) {
                $selected_ashley_accounts[] = array( 'id' => $encrypted, 'name' => preg_replace( '/[^0-9]/', '', $decrypted ) );
            }
        }

        $companies = (new Company())->get_all();

        // remove string keys to make it a json array
        foreach( $ashley_accounts as $k => $aa ) {
            unset( $ashley_accounts[$k] );
            $ashley_accounts[] = $aa;
        }

        $this->resources
            ->javascript_url( Config::resource( 'typeahead-js' ) )
            ->javascript( 'api-keys/manage' );

        return $this->get_template_response('api-keys/manage')
            ->add_title('Manage API Key')
            ->set( compact( 'api_key', 'brands', 'selected_brands', 'ashley_accounts', 'selected_ashley_accounts', 'companies' ) )
            ->menu_item( 'api-keys/index' );
    }

}