<?php
class CraigslistController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'craigslist/';
        $this->section = 'craigslist';
        $this->title = _('Craigslist');
    }

    /**
     * List ads
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get the Craigslist Ad Id
        $craigslist_ad_id = ( isset( $_GET['caid'] ) ) ? $_GET['caid'] : false;

        $ad = new CraigslistAd();

        if ( $craigslist_ad_id )
            $ad->get_complete( $craigslist_ad_id, $this->user->account->id );

        $v = new Validator('fAddCraigslistTemplate');
        $v->add_validation( 'taDescription', 'req', _('The "Description" field is required') );
        $v->add_validation( 'taDescription', 'maxlen=30000', _('The "Description" field must be 30,000 characters or less') );

        $v->add_validation( 'tPrice', 'req', _('The "Price" field is required') );
        $v->add_validation( 'tPrice', 'float', _('The "Price" field may only contain numbers and a decimal point') );

        // Setup for validation
        $js_validation = $v->js_validation();
        $errs = '';

        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist_api = new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') );

        if ( $this->verified() ) {
            $errs = $v->validate();

            // Validation for the headlines
            $i = 1;
            foreach ( $_POST['tHeadlines'] as $hl ) {
                if ( empty( $hl ) )
                    $errs .= _('Headline') . ' #' . $i . ' is required<br />';

                $i++;
            }

            if ( empty( $errs ) ) {
                $ad->product_id = $_POST['hProductID'];
                $ad->text = $_POST['taDescription'];
                $ad->price = $_POST['tPrice'];

                if ( $ad->id ) {
                    $ad->save();
                } else {
                    $ad->website_id = $this->user->account->id;
                    $ad->create();
                }

                // Reset headlines
                $ad->delete_headlines();
                $ad->add_headlines( $_POST['tHeadlines'] );

                // Set markets
                $ad->set_markets( $_POST['sCraigslistMarkets'] );

                if ( '1' == $_POST['hPostAd'] ) {
                    $ad->post();
                    $this->notify( _('Your Craiglist Ad has been successfully sent to post!') );
                    return new RedirectResponse('/craigslist/');
                } else {
                    $this->notify( _('Your Craiglist Ad has been successfully created/saved!') );
				}
            }
        }

        // Get markets
        $market = new CraigslistMarket();
        $markets = $market->get_by_account( $this->user->account->id );

        if ( empty( $markets ) )
            $this->notify( _('You have no Craigslist markets connected with your account. Please contact your Online Specialist.'), false );

        $title = ( $ad->id ) ? _('Edit') : _('Add');

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'craigslist/add-edit' );

        return $this->get_template_response( 'add-edit' )
            ->select( 'add' )
            ->add_title( $title )
            ->set( compact( 'ad', 'markets', 'craigslist_api', 'js_validation', 'errs' ) );
    }

    /***** AJAX *****/

    /**
     * List All
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $craigslist_ad = new CraigslistAd();

        // Set Order by
        $dt->order_by( 'cah.`headline`', 'ca.`text`', 'p.`name`', 'p.`sku`', 'ca.`active`', 'ca.`date_created`' );
        $dt->add_where( ' AND ca.`website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( 'cah.`headline`' => false, 'ca.`text`' => true, 'p.`name`' => true, 'p.`sku`' => false ) );

        // Get items
        $ads = $craigslist_ad->list_all( $dt->get_variables() );
        $dt->set_row_count( $craigslist_ad->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete a craigslist ad? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $copy_nonce = nonce::create( 'copy' );

        /**
         * @var CraigslistAd $ad
         */
        if ( is_array( $ads ) )
        foreach ( $ads as $ad ) {
            $status = ( '0000-00-00 00:00:00' == $ad->date_posted ) ? _('Waiting Approval') : _('Posted');
            $date = new DateTime( $ad->date_created );

            $data[] = array(
                $ad->headline . '<br />' .
                '<div class="actions">' .
                    '<a href="' . url::add_query_arg( 'caid', $ad->id, '/craigslist/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit / Post') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'caid' => $ad->id, '_nonce' => $copy_nonce ), '/craigslist/copy/' ) . '" title="' . _('Copy') . '" ajax="1">' . _('Copy') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'caid' => $ad->id, '_nonce' => $delete_nonce ), '/craigslist/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>
                </div>'
                , format::limit_chars( html_entity_decode( str_replace( "\n", '', $ad->text ) ), 100, NULL, TRUE ) . '...'
                , $ad->product_name
                , $ad->sku
                , $status
                , $date->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['caid'] ), _('Cannot delete this Craigslist Ad') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $craigslist_ad = new CraigslistAd();
        $craigslist_ad->get( $_GET['caid'], $this->user->account->id );

        // Remove from Primus
        library( 'craigslist-api' );

        $craigslist_ad->delete_from_primus( new Craigslist_API( Config::key('craigslist-gsr-id'), Config::key('craigslist-gsr-key') ) );

        // Set to inactive
        $craigslist_ad->active = 0;
        $craigslist_ad->save();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Copy
     */
    protected function copy() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['caid'] ), _('Cannot copy this Craigslist Ad') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $craigslist_ad = new CraigslistAd();
        $craigslist_ad->get( $_GET['caid'], $this->user->account->id );

        $response->check( $craigslist_ad->id, _('Cannot copy this Craigslist Ad') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create new one
        $craigslist_ad->create();
        $craigslist_ad->add_headlines( $craigslist_ad->headlines );

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Load Product
     */
    protected function load_product() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['pid'] ), _('Cannot get Craigslist product') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $product = new Product();
        $product->get( $_POST['pid'] );

        $account_product = new AccountProduct();
        $account_product->get( $product->id, $this->user->account->id );

        // Set price
        $price = ( $account_product->product_id ) ? $account_product->price : 0;

        $images = $product->get_images();
        $image_html = '';

        foreach ( $images as &$image ) {
            $image = 'http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/large/' . $image;
            $image_html .= '<img class="hiddenImage" name="hiddenImage" src="' . $image . '" />';
        }

        $specifications = unserialize( html_entity_decode( $product->product_specifications, ENT_QUOTES, 'UTF-8' ) );
        $product_specifications = '';

        if ( is_array( $specifications ) && count( $specifications ) > 0 )
        foreach ( $specifications as $ps ) {
            if ( !empty( $product_specifications ) )
                $product_specifications .= '<br />';

            $name = html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' );
            $value = html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' );

            $product_specifications .= ( empty( $name ) ) ? $value : $name . ' - ' . $value;
        }

        if ( !empty( $product_specifications ) )
            $product_specifications = "<p>$product_specifications</p>";

        jQuery('#hProductDescription')->val( $product->description );
        jQuery('#hProductName')->val( $product->name );
        jQuery('#hProductCategoryID')->val( $product->category_id );
        jQuery('#hProductID')->val( $product->product_id );
        jQuery('#hProductCategoryName')->val( $product->category );
        jQuery('#hProductSKU')->val( $product->sku );
        jQuery('#hProductBrandName')->val( $product->brand );
        jQuery('#hProductSpecifications')->val( $product_specifications );
        jQuery('#tPrice[val=]')->val( $price );

        jQuery('#dProductPhotos')
        	->html( $image_html )
        	->openEditorAndPreview(); // Needs to determine template

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


