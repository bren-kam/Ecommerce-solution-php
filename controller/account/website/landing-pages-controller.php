<?php

class LandingPagesController extends BaseController{

    public function __construct(){
        parent::__construct();

        $this->view_base = 'website/landing-pages/';
        $this->section = _('Landing Page Builder');
    }


    protected function index(){
        // Make sure they can be here
        $this->resources
            ->css( 'website/landing-pages/index' );

        return $this->get_template_response( 'index' )
            ->menu_item( 'website/landing-pages/list' );
    }

    protected function list_pages(){
                // Get response
        $dt = new DataTableResponse( $this->user );
        $account_page = new AccountPage();

        $dont_show = array( 'sidebar', 'furniture', 'brands' );
        $standard_pages = array( 'home', 'financing', 'current-offer', 'contact-us', 'about-us', 'products' );
        
        // Set Order by
        $dt->order_by( '`title`', '`date_updated`' );
        $dt->search( array( '`title`' => false ) );
        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );
        foreach($dont_show as $title) {
            $dt->add_where( " AND `title` != '" . $title ."'");
            $dt->add_where( " AND `landing_page` = 1" );
        }

            
        // Get account pages
        $account_pages = $account_page->list_all( $dt->get_variables(), $exclude );
        $dt->set_row_count( $account_page->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $can_delete = $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST );

        if ( $can_delete ) {
            $confirm = _('Are you sure you want to delete this page? This cannot be undone.');
            $delete_page_nonce = nonce::create( 'delete_page' );
        }



        /**
         * @var AccountPage $page
         * @var string $confirm
         * @var string $delete_page_nonce
         */
        if ( is_array( $account_pages ) )
        foreach ( $account_pages as $page ) {


            $actions = '';

            if ( $can_delete && !in_array( $page->slug, $standard_pages ) ) {
                $url = url::add_query_arg( array(
                    '_nonce' => $delete_page_nonce
                    , 'apid' => $page->id
                ), '/website/delete-page/' );

               $actions = ' | <a href="' .  $url . '" title="' . _('Delete Page') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
            }

            $title = ( empty( $page->title ) ) ? format::slug_to_name( $page->slug ) . ' (' . _('No Name') . ')' : $page->title;

            $updated = DateTime::createFromFormat('Y-m-d H:i:s', $page->date_updated ? $page->date_updated : $page->date_created);

            $data[] = array(
                $title . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . '/' . $page->slug . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'apid', $page->id, '/website/landing-pages/edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions .
                    '</div>'
                , $updated->format('F jS, Y h:ia')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;

        
    }

        /**
     * Add Page
     *
     * @return CustomResponse|RedirectResponse
     */
    protected function add() {
        $form = new BootstrapForm( 'fAddPage' );
        $form->submit( _('Add') );

        $form->add_field( 'text', _('Title'), 'tTitle' )
            ->add_validation( 'req', _('The "Title" field is required') );

        $form->add_field( 'text', _('URL'), 'tSlug' )
            ->add_validation( 'req', _('The "URL" field is required') );
        
        
        $form->add_field('hidden', 'hLandingPage', '1');
        if ( $form->posted() ) {
            $page = new AccountPage();
            $page->website_id = $this->user->account->id;
            $page->title = $_POST['tTitle'];
            $page->slug = $_POST['tSlug'];
            $page->landing_page = $_POST['hLandingPage'];
            $page->create();

            $this->notify( _('Your page has been successfully added!') );
            $this->log( 'create-website-page', $this->user->contact_name . ' has created a landing page on ' . $this->user->account->title, $page->id );

            return new RedirectResponse('/website/landing-pages/');
        }

        $form = $form->generate_form();

        $this->resources->javascript('website/landing-pages/add');
        $response = new CustomResponse( $this->resources, 'website/landing-pages/add' );
        $response->set( compact( 'form' ) );
        return $response;
    }

    protected function builder(){
        // Make sure they can be here
        if ( !isset( $_GET['apid'] ) )
            return new RedirectResponse('/website/');

        $account_page = new AccountPage();
        $account_page->get($_GET['apid'],$this->user->account->id );
        $this->resources
            ->javascript_url( 
                Config::resource('typeahead-js'),
                 '/resources/js_single/?f=PageBuilder/js/builder',

                '/resources/json_single/?f=PageBuilder/elements',

                '/resources/js_single/?f=PageBuilder/js/src-min-noconflict/ace',
                '/resources/js_single/?f=PageBuilder/js/redactor/bufferButtons',
                '/resources/js_single/?f=PageBuilder/js/redactor/table',
                '/resources/js_single/?f=PageBuilder/js/redactor/table',
                '/resources/js_single/?f=PageBuilder/js/redactor/redactor.min',
                '/resources/js_single/?f=PageBuilder/js/chosen.jquery.min',
                '/resources/js_single/?f=PageBuilder/js/spectrum',
                '/resources/js_single/?f=PageBuilder/js/application',
                '/resources/js_single/?f=PageBuilder/js/jquery.zoomer',
                '/resources/js_single/?f=PageBuilder/js/jquery.placeholder',
                '/resources/js_single/?f=PageBuilder/js/flatui-fileinput',
                '/resources/js_single/?f=PageBuilder/js/jquery.tagsinput',
                '/resources/js_single/?f=PageBuilder/js/flatui-radio',
                '/resources/js_single/?f=PageBuilder/js/flatui-checkbox',
                '/resources/js_single/?f=PageBuilder/js/bootstrap-switch',
                '/resources/js_single/?f=PageBuilder/js/bootstrap-select',
                '/resources/js_single/?f=PageBuilder/js/bootstrap.min',
                '/resources/js_single/?f=PageBuilder/js/jquery.autocomplete.min',
                '/resources/js_single/?f=PageBuilder/js/jquery.ui.touch-punch.min',
                 '/resources/js_single/?f=PageBuilder/js/jquery-ui.min',
                '/resources/js_single/?f=PageBuilder/js/jquery-1.8.3.min'
            )
            ->css_url(
                '/resources/css_single/?f=PageBuilder/js/redactor/redactor',
                
                //                '/resources/css_single/?f=PageBuilder/css/font-awesome',
                
                '/resources/css_single/?f=PageBuilder/css/chosen',
                '/resources/css_single/?f=PageBuilder/css/spectrum',
                '/resources/css_single/?f=PageBuilder/css/style',
                '/resources/css_single/?f=PageBuilder/css/flat-ui',
                '/resources/css_single/?f=PageBuilder/bootstrap/css/bootstrap'
            );
        
        $response = new CustomResponse( $this->resources, 'website/landing-pages/builder' );
        $response->set( compact( 'account_page' ) );

        return $response;        
    }

    protected function elements(){
            if(!isset($_GET['f']))
                die();

            $page = $_GET['f'];
            
            $response = new CustomResponse( $this->resources, 'website/landing-pages/builder/'. basename($page) );
            $response->set_custom_header('website/landing-pages/builder/header');
            $response->set_custom_footer('website/landing-pages/builder/footer');
            return $response;
    }

    protected function get_content(){
        if ( !isset( $_GET['apid'] ) )
            return new RedirectResponse('/website/');
        
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );

        $response = new AjaxResponse( true );
        $response->add_response( 'content', unserialize($page->content) );
        return $response;

    }
    /**
     * Edit Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        // Make sure they can be here
        if ( !isset( $_GET['apid'] ) )
            return new RedirectResponse('/website/');

        // Set resources
        $this->resources
            ->css_url( Config::resource( 'jquery-ui' ) )
            ->css( 'website/edit', 'media-manager' )
            ->javascript_url(  Config::resource( 'jqueryui-js' ) )
            ->javascript( 'fileuploader', 'media-manager', 'website/edit' );

        // Initialize variables
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );
        $product_count = $page->count_products();

        $product_ids = $page->get_product_ids();

        if ( !empty( $product_ids ) ) {
            $product = new Product;
            $page->products = $product->get_by_ids( $product_ids );
        }

        $account_pagemeta = new AccountPagemeta();

        /***** VALIDATION *****/
        $v = new Validator( 'fEditPage' );

        // Custom validation
        switch ( $page->slug ) {
            case 'financing':
                $v->add_validation( 'tApplyNowLink', 'URL', _('The "Apply Now Link" field must contain a valid link') );
            break;

            case 'current-offer':
                $v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
                $v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email') );
            break;

            case 'contact-us':
                $v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
                $v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email') );
            break;

            default:break;
        }

        /***** HANDLE SUBMIT *****/

        $errs = false;

        // Make sure it's a valid request
        if ( $this->verified() ) {
            $errs = $v->validate();

            // Make sure another page doesn't have the same slug
            $test_page = new AccountPage();
            $test_page->get_by_slug( $this->user->account->id, $_POST['tPageSlug'] );

            if ( $test_page->id && $test_page->id != $page->id )
                $errs .= _('The page Link is already taken by another page. Please choose another link.');

            // if there are no errors
            if ( empty( $errs ) ) {
                // Home page can't update their slug
                $slug = ( 'home' == $page->slug ) ? 'home' : $_POST['tPageSlug'];
                $slug = trim( $slug );
                $title = ( _('Page Title...') == $_POST['tTitle'] ) ? '' : $_POST['tTitle'];
                $title = trim( $title );

                // Clear CloudFlare Cache
                $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

                if ( $cloudflare_zone_id ) {
                    library('cloudflare-api');
                    $cloudflare = new CloudFlareAPI( $this->user->account );
                    $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $page->slug . '/' );
                    $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $slug . '/' );
                }

                // Update the page
                $page->slug = $slug;
                $page->title = $title;
                $page->content = $_POST['taContent'];
                $page->meta_title = $_POST['tMetaTitle'];
                $page->meta_description = $_POST['tMetaDescription'];
                $page->meta_keywords = $_POST['tMetaKeywords'];
                $page->top = $_POST['rPosition'];
//                if ( isset( $_POST['taHeaderScript'] ) ) {
//                    $page->header_script = $_POST['taHeaderScript'];
//                }
                $page->save();

                // Update custom meta
                $pagemeta = array();
                switch ( $page->slug ) {
                    case 'current-offer':
                        $pagemeta = array(
                            'email' => $_POST['tEmail']
                            , 'display-coupon' => $_POST['cbDisplayCoupon']
                            , 'email-coupon' => ( isset( $_POST['cbEmailCoupon'] ) ) ? 'yes' : 'no'
                        );
                    break;

                    case 'financing':
                        $pagemeta = array( 'apply-now' => $_POST['tApplyNowLink'] );
                    break;

                    case 'products':
                        $pagemeta = array(
                            'top' => $_POST['sTop']
                            , 'page-title' => $page->title
                        );
                    break;

                    case 'contact-us':
                        $pagemeta = array(
                            'email' => $_POST['tEmail']
                        );
                    break;

                    default:break;
                }

                if ( $page->slug != 'home' )
                    $pagemeta['hide-sidebar'] = isset( $_POST['cbHideSidebar'] ) && $_POST['cbHideSidebar'] == 'yes';

                // Set pagemeta
                if ( !empty( $pagemeta ) )
                    $account_pagemeta->add_bulk_by_page( $page->id, $pagemeta );

                $page->delete_products();

                if ( isset( $_POST['products'] ) ) {
                    $product_add_limit = 100;

                    // Make sure they can only add the right amount
                    if ( count( $_POST['products'] ) > $product_add_limit )
                        $_POST['products'] = array_slice( $_POST['products'], 0, $product_add_limit );

                    $page->add_products( $_POST['products'] );
                }

                $this->notify( _('Your page has been successfully saved!') );
                $this->log( 'update-website-page', $this->user->contact_name . ' updated a website page on ' . $this->user->account->title, $page->id );
                

                return new RedirectResponse('/website/');
            }
        }

        /***** ATTACHMENTS & PAGEMETA *****/

        $resources = array();

        switch ( $page->slug ) {
            case 'contact-us':
                $this->resources
                    ->css('website/pages/contact-us')
                    ->javascript('website/pages/contact-us');

                $website_location = new WebsiteLocation();
                $locations = $website_location->get_by_website( $this->user->account->id );

                $pagemeta = $account_pagemeta->get_by_keys( $page->id, 'multiple-location-map', 'hide-all-maps', 'email', 'hide-contact-form' );

                foreach ( $pagemeta as $key => $value ) {
                    $key = str_replace( '-', '_', $key );
                    $$key = $value;
                }

                if ( !isset($email) || $email == '') {
                    $owner = new User();
                    $owner->get($this->user->account->user_id);
                    $email = $owner->email;
                }

                $cv = new Validator( 'fAddEditLocation' );
                $cv->add_validation( 'phone', 'phone', _('The "Phone" field must contain a valid phone number') );
                $cv->add_validation( 'fax', 'phone', _('The "Fax" field must contain a valid fax number') );
                $cv->add_validation( 'email', 'email', _('The "Email" field must contain a valid email') );
                $cv->add_validation( 'website', 'URL', _('The "Website" field must contain a valid link') );
                $cv->add_validation( 'zip', 'zip', _('The "Zip" field must contain a valid zip code') );
                $contact_validation = $cv->js_validation();

                $resources = compact( 'locations', 'multiple_location_map', 'hide_all_maps', 'email', 'hide_contact_form', 'contact_validation' );
            break;

            case 'current-offer':
                // Need to get an attachment
                $account_page_attachment = new AccountPageAttachment();

                $coupon = $account_page_attachment->get_by_key( $page->id, 'coupon' );
                $metadata = $account_pagemeta->get_by_keys( $page->id, 'email', 'display-coupon', 'email-coupon' );
                if(is_array($coupon)){
                        $coupon = array_pop($coupon);
                }

                $this->resources->javascript( 'website/pages/current-offer' );
                $resources = compact( 'coupon', 'metadata' );
            break;

            case 'financing':
                // Need to get an attachment
                $account_page_attachment = new AccountPageAttachment();

                $apply_now = $account_page_attachment->get_by_key( $page->id, 'apply-now' );
                $apply_now_link = $account_pagemeta->get_by_keys( $page->id, 'apply-now' );

                $this->resources->javascript('website/pages/financing');
                $resources = compact( 'apply_now', 'apply_now_link' );
            break;

            case 'products':
                $top = $account_pagemeta->get_by_keys( $page->id, 'top' );
                $resources = compact( 'top' );
            break;

            default:break;
        }

        if ( 'products' == $page->slug ) {
            $page_title = $account_pagemeta->get_by_keys( $page->id, 'page-title' );
        } else {
            $page_title = $page->title;
        }

        if ( $page->slug != 'home' ){
            $hide_sidebar = $account_pagemeta->get_by_keys( $page->id, 'hide-sidebar');
            $resources['hide_sidebar'] = $hide_sidebar;
        }

        /***** NORMAL PAGE FUNCTIONS *****/

        // Setup response
        $js_validation = $v->js_validation();
        
        return $this->get_template_response( 'edit' )
            ->kb( 37 )
            ->menu_item('website/pages/add')
            ->add_title( $page->title . ' | ' . _('Pages') )
            ->set( array_merge( compact( 'errs', 'files', 'js_validation', 'page', 'page_title', 'product_count' ), $resources ) );
    }

    protected function save(){
        if ( !isset( $_GET['apid'] ) )
            return new RedirectResponse('/website/');
        
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );

        $page->content = serialize($_POST);
        $page->save();

    }


}
