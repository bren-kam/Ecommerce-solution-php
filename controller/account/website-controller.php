<?php
class WebsiteController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'website/';
        $this->section = 'website';
    }

    /**
     * List Website Pages
     *
     * @return TemplateResponse
     */
    protected function index() {
        $this->resources->css('website/index');
        return $this->get_template_response( 'index' )
            ->kb( 36 )
            ->menu_item('website/pages/list');
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
            ->javascript_url( Config::resource( 'typeahead-js' ), Config::resource( 'jqueryui-js' ) )
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

        if ( $form->posted() ) {
            $page = new AccountPage();

            $page->website_id = $this->user->account->id;
            $page->title = $_POST['tTitle'];
            $page->slug = $_POST['tSlug'];
            $page->create();

            $this->notify( _('Your page has been successfully added!') );
            $this->log( 'create-website-page', $this->user->contact_name . ' has created a website page on ' . $this->user->account->title, $page->id );

            return new RedirectResponse('/website/');
        }

        $form = $form->generate_form();

        $this->resources->javascript('website/add');

        $response = new CustomResponse( $this->resources, 'website/add' );
        $response->set( compact( 'form' ) );
        return $response;
    }

    /**
     * List Website Categories
     *
     * @return TemplateResponse
     */
    protected function categories() {
        // Reset any defaults
        unset( $_SESSION['categories'] );

        $category = new Category();
        $account_category = new AccountCategory();

        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        /**
         * @var Category $category
         */
        foreach ( $categories_array as $category ) {
            if ( !$category->has_children() || !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }

        $this->resources
            ->css('website/categories')
            ->javascript('website/categories');

        return $this->get_template_response( 'categories' )
            ->kb( 38 )
            ->menu_item('website/pages/categories')
            ->set( compact( 'categories' ) );
    }

    /**
     * Add Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit_category() {
        // Make sure they can be here
        if ( !isset( $_GET['cid'] ) )
            return new RedirectResponse('/website/categories/');

        $category = new AccountCategory();
        $category->get( $this->user->account->id, $_GET['cid'] );

        if ( $this->verified() ) {
            $category->title = $_POST['tTitle'];
            $category->content = $_POST['taContent'];
            $category->meta_title = $_POST['tMetaTitle'];
            $category->meta_description = $_POST['tMetaDescription'];
            $category->meta_keywords = $_POST['tMetaKeywords'];
            $category->top = $_POST['rPosition'];
//            if ( isset( $_POST['taHeaderScript'] ) ) {
//                $category->header_script = $_POST['taHeaderScript'];
//            }
            $category->save();

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/category/' . $category->slug . '/' );
            }

            // Notification
            $this->notify( _('Your category has been successfully saved!') );
            $this->log( 'update-website-category', $this->user->contact_name . ' updated a website category on ' . $this->user->account->title, $category->id );

            return new RedirectResponse('/website/categories/');
        }

        $this->resources
            ->css( 'website/pages/page', 'media-manager' )
            ->javascript( 'fileuploader', 'media-manager' );

        return $this->get_template_response('edit-category')
            ->kb( 39 )
            ->select( 'website', 'website/categories' )
            ->menu_item('website/pages/categories')
            ->set( compact( 'category' ) );
    }

    /**
     * Sidebar
     *
     * @return TemplateResponse
     */
    protected function sidebar() {
        // Initialize classes
        $account_file = new AccountFile();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        // Get variables
        $files = $account_file->get_by_account( $this->user->account->id );
        $page->get_by_slug( $this->user->account->id, 'sidebar' );
        $attachments = $attachment->get_by_account_page_ids( array( $page->id ) );
        $settings = $this->user->account->get_settings( 'sidebar-image-width', 'images-alt' );

        // Do stuff with variables
        $dimensions = ( empty( $settings['sidebar-image-width'] ) ) ? '' : _('Width') . ': ' . $settings['sidebar-image-width'];
        $images_alt = '1' == $settings['images-alt'];

        $this->resources
            ->css( 'website/website-sidebar', 'media-manager' )
            ->css_url( Config::resource( 'videojs-css' ), Config::resource( 'bootstrap-datepicker-css' ) )
            ->javascript_url( Config::resource('bootstrap-datepicker-js'), Config::resource( 'jqueryui-js' ), Config::resource( 'videojs-js' ) )
            ->javascript( 'fileuploader', 'media-manager', 'website/website-sidebar' );

        return $this->get_template_response( 'website-sidebar' )
            ->kb( 40 )
            ->menu_item('website/sidebar')
            ->add_title( _('Sidebar') )
            ->set( compact( 'dimensions', 'files', 'attachments', 'page', 'images_alt' ) );
    }

    /**
     * Banners
     *
     * @return TemplateResponse
     */
    protected function banners() {
        // Initialize classes
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        // Get variables
        $page->get_by_slug( $this->user->account->id, 'home' );
        $attachments = $attachment->get_by_account_page_ids( array( $page->id ) );

        $settings = $this->user->account->get_settings( 'banner-width', 'banner-height', 'images-alt' );
        $new_settings = array();

        // Set dimensions if they are empty
        foreach ( $settings as $k => &$v ) {
            if ( !empty( $v ) )
                continue;

            $v = ( 'banner-width' == $k ) ? 680 : 300;

            $new_settings[$k] = $v;
        }

        // Update settings
        if ( !empty( $new_settings ) )
            $this->user->account->set_settings( $new_settings );

        // Determine variables
        $dimensions = $settings['banner-width'] . 'x' . $settings['banner-height'];
        $images_alt = '1' == $settings['images-alt'];
        $slideshow_fixed_width = $this->user->account->get_settings('slideshow-fixed-width');

        $this->resources
            ->css( 'website/banners', 'media-manager' )
            ->css_url( Config::resource('bootstrap-datepicker-css') )
            ->javascript_url( Config::resource('bootstrap-datepicker-js'), Config::resource( 'jqueryui-js' ) )
            ->javascript( 'fileuploader', 'media-manager', 'website/banners' );

        return $this->get_template_response( 'banners' )
            ->kb( 41 )
            ->menu_item('website/banners')
            ->add_title( _('Banners') )
            ->set( compact( 'attachments', 'dimensions', 'images_alt', 'page', 'slideshow_fixed_width' ) );
    }

    /**
     * Sale
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function sale() {
        // Instantiate classes
        $form = new FormTable( 'fSale' );

        // Get settings
        $settings = $this->user->account->get_settings( 'page_sale-slug', 'page_sale-title', 'page_sale-description' );

        $form->add_field( 'text', _('Title'), 'tPageTitle', $settings['page_sale-title'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Slug'), 'tPageSlug', $settings['page_sale-slug'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Meta Description'), 'tPageDescription', $settings['page_sale-description'] )
            ->attribute( 'maxlength', '250' );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                    'page_sale-title' => $_POST['tPageTitle']
                    , 'page_sale-slug' => format::slug( $_POST['tPageSlug'] )
                    , 'page_sale-description' => $_POST['tPageDescription']
                )
            );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $settings['page_sale-slug'] . '/' );
            }

            // Notification
            $this->notify( _('Your sale page has been successfully saved!') );
            $this->log( 'update-sale-page', $this->user->contact_name . ' updated the sale page on ' . $this->user->account->title, $settings );

            // Refresh to get all the changes
            return new RedirectResponse('/website/sale/');
        }

        $this->resources->javascript( 'website/sale' );

        return $this->get_template_response( 'sale' )
            ->kb( 42 )
            ->add_title( _('Sale') )
            ->select( 'sale' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /**
     * Room Planner
     */
    protected function room_planner() {
        // Instantiate classes
        $form = new FormTable( 'fRoomPlanner' );

        // Get settings
        $settings = $this->user->account->get_settings( 'page_room-planner-slug', 'page_room-planner-title' );

        $form->add_field( 'text', _('Page Title'), 'tPageTitle', $settings['page_room-planner-title'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Page Slug'), 'tPageSlug', $settings['page_room-planner-slug'] )
            ->attribute( 'maxlength', '50' );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                    'page_room-planner-title' => $_POST['tPageTitle']
                    , 'page_room-planner-slug' => format::slug( $_POST['tPageSlug'] )
                )
            );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $settings['page_room-planner-slug'] . '/' );
            }

            // Notification
            $this->notify( _('Your room planner page has been successfully saved!') );
            $this->log( 'update-room-planner', $this->user->contact_name . ' updated the room planner on ' . $this->user->account->title, $settings );

            // Refresh to get all the changes
            return new RedirectResponse('/website/room-planner/');
        }

        return $this->get_template_response( 'room-planner' )
            ->kb( 43 )
            ->add_title( _('Room Planner') )
            ->select( 'room-planner' )
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /**
     * Layout
     *
     * @return TemplateResponse
     */
    protected function home_page_layout() {
        // Handle Post
        if ( $this->verified() ) {
            $layout = array();

            foreach ( $_POST['layout'] as $element ) {
                list( $name, $disabled ) = explode( '|', $element );
                $name = strtolower( $name );
                $layout[] = compact( 'name', 'disabled' );
            }

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
            }

            $this->user->account->set_settings( array( 'layout' => json_encode( $layout ) ) );
            $this->log( 'update-home-page-layout', $this->user->contact_name . ' updated home page layout on ' . $this->user->account->title, $layout );
        }

        $layout = $this->user->account->get_settings('layout');
        if ( empty( $layout ) ) {
            $layout = array(
                (object) array( 'name' => 'slideshow', 'disabled' => 0 )
                , (object) array( 'name' => 'categories', 'disabled' => 0 )
                , (object) array( 'name' => 'content', 'disabled' => 0 )
                , (object) array( 'name' => 'sidebar', 'disabled' => 0 )
            );
            if ( $this->user->account->is_new_template() ) {
                $layout[] = (object) array( 'name' => 'popular-items', 'disabled' => 0 );
                $layout[] = (object) array( 'name' => 'best-seller-items', 'disabled' => 0 );
                $layout[] = (object) array( 'name' => 'recently-viewed-items', 'disabled' => 0 );
            }
        } else if ( is_string( $layout ) ) {
            $layout = json_decode( $layout );
        }

        $this->resources
            ->css( 'website/home-page-layout' )
            ->javascript_url( Config::resource( 'jqueryui-js' ) )
            ->javascript( 'website/home-page-layout' );

        return $this->get_template_response( 'home-page-layout' )
            ->kb( 135 )
            ->menu_item('website/settings/home-page-layout')
            ->add_title( _('Home Page Layout') )
            ->set( compact( 'layout' ) );
    }

    /**
     * Navigation
     *
     * @return TemplateResponse
     */
    protected function navigation() {
        $page = new AccountPage();
        $pages = $page->get_by_account( $this->user->account->id );

        $this->resources
            ->css( 'jquery.nestable', 'website/navigation' )
            ->javascript( 'jquery.nestable', 'website/navigation' );

        if ( $this->verified() ) {
            $tree = json_decode( $_POST['tree'], true );

            $get_navigation = function($tree, $data) use (&$get_navigation){
                $navigation = [];
                foreach ( $tree as $tree_node ) {
                    $page = $data[$tree_node['id']];
                    list( $url, $name ) = explode( '|', $page );
                    $name = htmlentities( $name );
                    $navigation_node = compact( 'url', 'name' );

                    if ( isset( $tree_node['children'] ) ) {
                        $navigation_node['children'] = $get_navigation($tree_node['children'], $data);
                    }

                    $navigation[] = $navigation_node;
                }
                return $navigation;
            };

            if ( $tree ) {
                $navigation = $get_navigation($tree, $_POST['navigation']);
            }

            $this->user->account->set_settings( array( 'navigation' => json_encode( $navigation ) ) );
            $this->log( 'update-navigation', $this->user->contact_name . ' updated the navigation on ' . $this->user->account->title, $navigation );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify('Your Navigation settings have been saved!');
        }

        $navigation = $this->user->account->get_settings('navigation');
        $navigation = ( empty( $navigation ) ) ? array() : json_decode( $navigation );

        return $this->get_template_response( 'navigation' )
            ->kb( 136 )
            ->menu_item('website/navigation-menus/header-navigation')
            ->add_title( _('Navigation') )
            ->set( compact( 'pages', 'navigation' ) );
    }

    /**
     * Footer Navigation
     *
     * @return TemplateResponse
     */
    protected function footer_navigation() {

        if ( $this->verified() ) {


            $this->user->account->set_settings( array( 'footer-navigation' => json_encode( $footer_navigation ) ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }
            

            $tree = json_decode( $_POST['tree'], true );

            $get_navigation = function($tree, $data) use (&$get_navigation){
                $navigation = [];
                foreach ( $tree as $tree_node ) {
                    $page = $data[$tree_node['id']];
                    list( $url, $name ) = explode( '|', $page );
                    $name = htmlentities( $name );
                    $navigation_node = compact( 'url', 'name' );

                    if ( isset( $tree_node['children'] ) ) {
                        $navigation_node['children'] = $get_navigation($tree_node['children'], $data);
                    }

                    $navigation[] = $navigation_node;
                }
                return $navigation;
            };

            if ( $tree ) {
                $footer_navigation = $get_navigation($tree, $_POST['footer-navigation']);
            }


            $this->user->account->set_settings( array( 'footer-navigation' => json_encode( $footer_navigation ) ) );
            // Notification
            $this->notify('Your Footer Navigation settings have been saved!');
            $this->log( 'update-footer-navigation', $this->user->contact_name . ' updated the footer navigation on ' . $this->user->account->title, $footer_navigation );
        }

        $page = new AccountPage();
        $pages = $page->get_by_account( $this->user->account->id );

        $footer_navigation = $this->user->account->get_settings('footer-navigation');

        $footer_navigation = ( empty( $footer_navigation ) ) ? array() : json_decode( $footer_navigation );

        $this->resources
            ->css( 'jquery.nestable', 'website/footer-navigation' )
            ->javascript( 'jquery.nestable', 'website/footer-navigation' );

        return $this->get_template_response( 'footer-navigation' )
            ->kb( 138 )
            ->menu_item('website/navigation-menus/footer-navigation')
            ->add_title( _('Footer Navigation') )
            ->set( compact( 'pages', 'footer_navigation' ) );
    }

    /**
     * Settings page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function settings() {
        // Instantiate classes
        $form = new BootstrapForm( 'fSettings' );

        // Get settings
        $settings_array = array(
            'banner-width', 'banner-height', 'banner-speed', 'banner-background-color'
            , 'banner-effect', 'banner-hide-scroller', 'disable-banner-fade-out', 'banner-links-new-window', 'banner-hide-navigation-arrows'
            , 'images-alt'
            , 'logo-link'
            , 'page_sale-slug', 'page_sale-title', 'page_sale-description'
            ,'slideshow-fixed-width'
            , 'slideshow-categories'
            , 'dropdown-hover'            
            , 'sidebar-left'
            , 'ssl-enabled-pages'            
        );

        if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && $this->user->account->is_new_template() ) {
            $settings_array = array_merge( $settings_array
                , array( 'sidebar-image-width' )
            );
        }
        if ( $this->user->account->is_new_template() ) {
            $settings_array = array_merge( $settings_array
                , array( 'sm-facebook-link', 'sm-twitter-link', 'sm-google-link', 'sm-pinterest-link', 'sm-linkedin-link', 'sm-youtube-link', 'sm-instagram-link', 'sm-foursquare-link', 'sm-yelp-link', 'price-decimals' )
            );
        }

        $settings = $this->user->account->get_settings( $settings_array );

        // Create form
        $form->add_field( 'title', _('Banners') );

        $form->add_field( 'text', _('Width'), 'banner-width', $settings['banner-width'] )
            ->attribute( 'maxlength', '4' )
            ->add_validation( 'req', _('The "Banners - Width" field is required') )
            ->add_validation( 'num', _('The "Banners - Width" field may only contain a number') );

        $form->add_field( 'text', _('Height'), 'banner-height', $settings['banner-height'] )
            ->attribute( 'maxlength', '4' )
            ->add_validation( 'req', _('The "Banners - Height" field is required') )
            ->add_validation( 'num', _('The "Banners - Height" field may only contain a number') );

        $form->add_field( 'text', _('Delay (in seconds)'), 'banner-speed', $settings['banner-speed'] )
            ->attribute( 'maxlength', '2' )
            ->add_validation( 'num', _('The "Banners - Speed" field may only contain a number') );


        
        $effects = array(
            'random' => _('Random')
            , 'fade' => _('Fade')
            , 'fold' => _('Fold')
            , 'sliceDownRight' => _('Slice Down-Right')
            , 'sliceDownLeft' => _('Slice Down-Left')
            , 'sliceUpRight' => _('Slice Up-Right')
            , 'sliceUpLeft' => _('Slice Up-Left')
            , 'sliceUpDown' => _('Slice Up-Down')
            , 'sliceUpDownLeft' => _('Slice Up-Down-Left')
            , 'boxRandom' => _('Box Random')
            , 'boxRain' => _('Box Rain')
            , 'boxRainReverse' => _('Box Rain-Reverse')
            , 'boxRainGrow' => _('Box Rain-Grow')
            , 'boxRainGrowReverse' => _('Box Rain-Grow-Reverse')
        );

        $form->add_field( 'select', _('Effect'), 'banner-effect', $settings['banner-effect'] )
            ->options( $effects );

        $form->add_field( 'text', _('Background Color'), 'banner-background-color', $settings['banner-background-color'] )
            ->attribute( 'maxlength', '6' );

        $form->add_field( 'checkbox', _('Hide Scroller'), 'banner-hide-scroller', $settings['banner-hide-scroller'] );

        $form->add_field( 'checkbox', _('Hide Navigation Arrows'), 'banner-hide-navigation-arrows', $settings['banner-hide-navigation-arrows'] );

        $form->add_field( 'checkbox', _('Disable Banner Fade-out'), 'disable-banner-fade-out', $settings['disable-banner-fade-out'] );

        $form->add_field( 'checkbox', _('Open Banner & Sidebar Links in a New Window'), 'banner-links-new-window', $settings['banner-links-new-window'] );

        // Next section
        if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && $this->user->account->is_new_template() ) {
            $form->add_field( 'blank', '' );
            $form->add_field( 'title', _('Sidebar Images') );

            $form->add_field( 'text', _('Width'), 'sidebar-image-width', $settings['sidebar-image-width'] )
                ->attribute( 'maxlength', '4' )
                ->add_validation( 'num', _('The "Sidebar Image - Width" field may only contain a number') );
        }

        // Next section
        if ( $this->user->account->is_new_template() ) {
            $form->add_field( 'blank', '' );
            $form->add_field( 'title', _('Social Media') );

            $form->add_field( 'text', _('Facebook Link'), 'sm-facebook-link', $settings['sm-facebook-link'] )
                ->add_validation( 'url', _('The "Facebook Link" must be a valid link') );

            $form->add_field( 'text', _('Twitter Link'), 'sm-twitter-link', $settings['sm-twitter-link'] )
                ->add_validation( 'url', _('The "Twitter Link" must be a valid link') );

            $form->add_field( 'text', _('Google Link'), 'sm-google-link', $settings['sm-google-link'] )
                ->add_validation( 'url', _('The "Google Link" must be a valid link') );

            $form->add_field( 'text', _('Pinterest Link'), 'sm-pinterest-link', $settings['sm-pinterest-link'] )
                ->add_validation( 'url', _('The "Pinterest Link" must be a valid link') );

            $form->add_field( 'text', _('LinkedIn Link'), 'sm-linkedin-link', $settings['sm-linkedin-link'] )
                ->add_validation( 'url', _('The "LinkedIn Link" must be a valid link') );

            $form->add_field( 'text', _('YouTube Link'), 'sm-youtube-link', $settings['sm-youtube-link'] )
                ->add_validation( 'url', _('The "YouTube Link" must be a valid link') );

            $form->add_field( 'text', _('Instagram Link'), 'sm-instagram-link', $settings['sm-instagram-link'] )
                ->add_validation( 'url', _('The "Instagram Link" must be a valid link') );

            $form->add_field( 'text', _('Foursquare Link'), 'sm-foursquare-link', $settings['sm-foursquare-link'] )
                ->add_validation( 'url', _('The "FourSquare Link" must be a valid link') );

            $form->add_field( 'text', _('Yelp Link'), 'sm-yelp-link', $settings['sm-yelp-link'] )
                ->add_validation( 'url', _('The "Yelp Link" must be a valid link') );
        }

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Sale Page') );

        $form->add_field( 'text', _('Title'), 'page_sale-title', $settings['page_sale-title'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Slug'), 'page_sale-slug', $settings['page_sale-slug'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Meta Description'), 'page_sale-description', $settings['page_sale-description'] )
            ->attribute( 'maxlength', '250' );
        

        // Next section
        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Other') );

        //if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && $this->user->account->is_new_template() ) {
        //    $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
        //        ->options( data::timezones( false, false, true ) );
        //}

        $form->add_field( 'text', _('SSL Enabled Pages'), 'ssl-enabled-pages', $settings['ssl-enabled-pages'] );


        
        $form->add_field( 'text', _('Logo Link URL'), 'logo-link', $settings['logo-link'] )
            ->add_validation( 'url', _('The "Logo Link" must be a valid link') );

        $form->add_field( 'checkbox', _('Images - Alt Tags'), 'images-alt', $settings['images-alt'] );

        $form->add_field( 'text', 'Product Price Max. Decimals', 'price-decimals', $settings['price-decimals'] );

        $form->add_field( 'checkbox', _('Fixed-width Slideshow'), 'slideshow-fixed-width', $settings['slideshow-fixed-width'] );
        $form->add_field( 'checkbox', _('Slideshow w/ Categories'), 'slideshow-categories', $settings['slideshow-categories'] );
        $form->add_field( 'checkbox', _('Left-hand-side Sidebar'), 'sidebar-left', $settings['sidebar-left'] );
        $form->add_field( 'checkbox', _('Dropdown Hover'), 'dropdown-hover', $settings['dropdown-hover'] );

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }
            
            $this->user->account->set_settings( $new_settings );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('Your settings have been successfully saved!') );
            $this->log( 'update-website-settings', $this->user->contact_name . ' updated the website settings on ' . $this->user->account->title );

            // Refresh to get all the changes
            return new RedirectResponse('/website/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->kb( 44 )
            ->add_title( _('Settings') )
            ->menu_item('website/settings/settings')
            ->set( array( 'form' => $form->generate_form() ) );
    }

    /***** AJAX *****/

    /**
     * List Pages
     *
     * @return DataTableResponse
     */
    protected function list_pages() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_page = new AccountPage();

        // Set Order by
        $dt->order_by( '`title`', '`date_updated`' );
        $dt->search( array( '`title`' => false ) );
        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );

        // Get account pages
        $account_pages = $account_page->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_page->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $can_delete = $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST );

        if ( $can_delete ) {
            $confirm = _('Are you sure you want to delete this page? This cannot be undone.');
            $delete_page_nonce = nonce::create( 'delete_page' );
        }

        $dont_show = array( 'sidebar', 'furniture', 'brands' );
        $standard_pages = array( 'home', 'financing', 'current-offer', 'contact-us', 'about-us', 'products' );

        /**
         * @var AccountPage $page
         * @var string $confirm
         * @var string $delete_page_nonce
         */
        if ( is_array( $account_pages ) )
        foreach ( $account_pages as $page ) {
            // We don't want to show all the pages
            if ( in_array( $page->slug, $dont_show ) )
                continue;

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
                    '<a href="' . url::add_query_arg( 'apid', $page->id, '/website/edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions .
                    '</div>'
                , $updated->format('F jS, Y h:ia')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * List Categories
     *
     * @return DataTableResponse
     */
    protected function list_categories() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_category = new AccountCategory();
        $category = new Category();

        $parent_category_id = ( isset( $_SESSION['categories']['pcid'] ) ) ? $_SESSION['categories']['pcid'] : 0;

        // Set Order by
        $dt->order_by( 'title', 'wc.`date_updated`' );
        $dt->add_where( ' AND wc.`website_id` = ' . (int) $this->user->account->id . ' AND c.`parent_category_id` = ' . (int) $parent_category_id );
        $dt->search( array( 'title' => false ) );

        // Get account pages
        $account_categories = $account_category->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_category->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var AccountCategory $account_category
         */
        if ( is_array( $account_categories ) )
        foreach ( $account_categories as $account_category ) {
            $date_update = new DateTime( $account_category->date_updated );

            $data[] = array(
                $account_category->title . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . ( $this->user->account->is_new_template() ? '/category' : '' ) . $category->get_url( $account_category->category_id ) . '" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'cid', $account_category->category_id, '/website/edit-category/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' .
                    '</div>'
                , $date_update->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete page
     *
     * @return AjaxResponse
     */
    protected function delete_page() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['apid'] ), _('You cannot delete this page') );
        $response->check( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ), _('You do not have permission to delete this page') );

        if ( $response->has_error() )
            return $response;

        // Remove the page
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );
        $page->remove();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );
        $this->log( 'delete-website-page', $this->user->contact_name . ' deleted a website page on ' . $this->user->account->title );
        
        // Add the response
        $response->add_response( 'refresh', 1 );

        return $response;
    }

    /**
     * Upload File
     *
     * @return AjaxResponse
     */
    protected function upload_file() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['fn'] ), _('File failed to upload') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $account_file = new AccountFile();
        $uploader = new qqFileUploader( array( 'pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml' ), 6144000 );

        // Change the name
        $extension = strtolower( f::extension( $_GET['qqfile'] ) );
        $file_name =  format::slug( f::strip_extension( $_GET['fn'] ) ) . '.' . $extension;


        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $file->upload_file( $result['file_path'], $file_name, $this->user->account->id . '/mm/' );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create the account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = 'http://websites.retailcatalog.us/' . $this->user->account->id . '/mm/' . $file_name;
        $account_file->create();

        $this->log( 'upload-media-manager-file', $this->user->contact_name . ' uploaded a file to the media manager on ' . $this->user->account->title, $file_name );

        $response->add_response( 'id', $account_file->website_file_id );
        $response->add_response( 'name', f::name( $account_file->file_path ) );
        $response->add_response( 'url', $account_file->file_path );

        return $response;
    }

    /**
     * Upload Image
     *
     * @return AjaxResponse
     */
    protected function upload_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['fn'], $_GET['apid'] ), _('Not enough data to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();
        $uploader = new qqFileUploader( array( 'gif', 'jpg', 'jpeg', 'png' ), 6144000 );

        // Get some stuff
        $page->get( $_GET['apid'], $this->user->account->id );

        switch ( $_GET['fn'] ) {
            case 'coupon':
                $name = $key = 'coupon';
                $width = 405;
                $height = 450;
            break;

            case 'financing':
                $name = 'btn.apply-now';
                $key = 'apply-now';
                $width = 200;
                $height = 70;
            break;

            default:
                $response->check( false, _('Wrong data found to upload image') );
                return $response;
            break;
        }

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $image_dir = $this->user->account->id . "/$key/";
        $image_name = $file->upload_image( $result['file_path'], $name, $width, $height, 'websites', $image_dir );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Form image url
        $image_url = 'http://websites.retailcatalog.us/' . $image_dir . $image_name;

        // Create/update the account attachment
        $attachment = $attachment->get_by_key( $page->id, $key );

        if ( !$attachment instanceof AccountPageAttachment )
        $attachment = new AccountPageAttachment();

        // Set variables
        $attachment->website_page_id = $page->id;
        $attachment->key = $key;
        $attachment->value = $image_url;

        if ( $attachment->id ) {
            $attachment->save();
        } else {
            $attachment->create();
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $page->slug . '/' );
        }

        $this->log( 'upload-image', $this->user->contact_name . ' uploaded an image to ' . $this->user->account->title, $image_url );

        $response->add_response( 'url', $image_url );

        return $response;
    }

    /**
     * Upload Sidebar Image
     *
     * @return AjaxResponse
     */
    protected function upload_sidebar_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['apid'] ), _('Not enough data to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();
        $uploader = new qqFileUploader( array( 'gif', 'jpg', 'jpeg', 'png' ), 6144000 );

        // Get some stuff
        $sidebar_image_width = $this->user->account->get_settings( 'sidebar-image-width' );
        $max_width = ( empty ( $settings['sidebar-image-width'] ) ) ? 1000 : $settings['sidebar-image-width'];

        $image_name =  format::slug( f::strip_extension( $_GET['qqfile'] ) ) . '.' . f::extension( $_GET['qqfile'] );
        $page->get( $_GET['apid'], $this->user->account->id );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $image_dir = $this->user->account->id . "/sidebar/";
        $image_name = $file->upload_image( $result['file_path'], $image_name, $max_width, 1000, 'websites', $image_dir );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Form image url
        $image_url = 'http://websites.retailcatalog.us/' . $image_dir . $image_name;

        // Create the account attachment
        $attachment->website_page_id = $page->id;
        $attachment->key = 'sidebar-image';
        $attachment->value = $image_url;
        $attachment->create();

        $this->log( 'upload-sidebar-image', $this->user->contact_name . ' uploaded a sidebar image to ' . $this->user->account->title, $image_url );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }
        
        $element_box = '<div class="element-box" id="dAttachment_' . $attachment->id . '">';
        $element_box .= '<h2>' . _('Sidebar Image') . '</h2>';
        
        // Add Sidebar Image
        if ( !empty( $sidebar_image_width ) )
            $element_box .= '<p><small>' . _('Width') . " $sidebar_image_width</small></p>";

        $enable_disable_url = url::add_query_arg( array(
            '_nonce' => nonce::create( 'update_status' )
            , 'apaid' => $attachment->id
            , 's' => '0'
        ), '/website/update-status/' );

        $remove_attachment_url = url::add_query_arg( array(
            '_nonce' => nonce::create('remove_attachment')
            , 'apaid' => $attachment->id
            , 't' => 'dAttachment_' . $attachment->id
            , 'si' => '1'
        ), '/website/remove-attachment/' );

        $element_box .= '<a href="' . $enable_disable_url . '" id="aEnableDisable' . $attachment->id . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.') . '"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>';
        $element_box .= '<div id="dSidebarImage' . $attachment->id . '"><br />';
        $element_box .= '<form action="/website/update-attachment-extra/" method="post" ajax="1">';
        $element_box .= '<div align="center">';
        $element_box .= '<p><img src="' . $image_url . '" alt="' . _('Sidebar Image') . '" /></p>';
        $element_box .= '<p><a href="' . $remove_attachment_url . '" id="aRemove' . $attachment->id . '" title="' . _('Remove Image') . '" ajax="1" confirm="' . _('Are you sure you want to remove this sidebar element?') . '">' . _('Remove') . '</a></p>';
        $element_box .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $attachment->id . '" placeholder="' . _('Enter Link...') . '" value="http://" /></p>';
        $element_box .= '<p id="pTempSidebarImage' . $attachment->id . '" class="success hidden">' . _('Your Sidebar Image link has been successfully updated.') . '</p><br />';
        $element_box .= '<p align="center"><input type="submit" class="button" value="' . _('Save') . '" /></p>';
        $element_box .= '</div>';
        $element_box .= '<input type="hidden" name="hAccountPageAttachmentId" value="' . $attachment->id . '" />';
        $element_box .= '<input type="hidden" name="target" value="pTempSidebarImage' . $attachment->id . '" />';
        $element_box .= nonce::field( 'update_attachment_extra', '_nonce', false );
        $element_box .= '</form></div></div>';
        
        jQuery('#dElementBoxes')
            ->prepend( $element_box )
            ->updateElementOrder()
            ->updateDividers()
            ->sparrow();

        jQuery('#upload-sidebar-image-loader')->hide();
        jQuery('#aUploadSidebarImage')->show();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }


    /**
     * Create Sidebar Image
     *
     * @return AjaxResponse
     */
    protected function create_sidebar_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['apid'], $_POST['fn'] ), _('Not enough data to create image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $file = new File();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        $file_path = tempnam( sys_get_temp_dir(), rand(1, 9999) );
        @copy( $_POST['fn'], $file_path );

        // Get some stuff
        $sidebar_image_width = $this->user->account->get_settings( 'sidebar-image-width' );
        $max_width = ( empty ( $settings['sidebar-image-width'] ) ) ? 1000 : $settings['sidebar-image-width'];

        $image_name =  format::slug( f::strip_extension( $file_path ) ) . '.' . f::extension( $file_path );
        $page->get( $_POST['apid'], $this->user->account->id );

        // Create the different versions we need
        $image_dir = $this->user->account->id . "/sidebar/";
        $image_name = $file->upload_image( $file_path, $image_name, $max_width, 1000, 'websites', $image_dir );

        @unlink( $file_path );

        // Form image url
        $image_url = 'http://websites.retailcatalog.us/' . $image_dir . $image_name;

        // Create the account attachment
        $attachment->website_page_id = $page->id;
        $attachment->key = 'sidebar-image';
        $attachment->value = $image_url;
        $attachment->create();

        $this->log( 'create-sidebar-image', $this->user->contact_name . ' created a sidebar image on ' . $this->user->account->title, $image_url );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        $response->add_response( 'id', $attachment->id );
        $response->add_response( 'url', $image_url );

        return $response;
    }


    /**
     * Upload Sidebar Image
     *
     * @return AjaxResponse
     */
    protected function upload_sidebar_video() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['apid'] ), _('Not enough data to upload video') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();
        $account_file = new AccountFile();
        $uploader = new qqFileUploader( array( 'mp4', 'wmv', '3gp', 'mpg', 'mpeg', 'avi' ), 26214400 );

        // Set video
        $video_name =  'video.' . f::extension( $_GET['qqfile'] );
        $page->get( $_GET['apid'], $this->user->account->id );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload video') );
        $response->check( $page->id, _('Failed to upload video') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $video_dir = $this->user->account->id . "/sidebar/";
        $video_url = $file->upload_file( $result['file_path'], $video_name, $video_dir );
        $video_url = str_replace( 's3.amazonaws.com/', '', $video_url );

        // AMAZON VIDEO TRANSCODER
        library('ElasticTranscoder');
        $input_key = "{$video_dir}{$video_name}";
        $output_key = "{$video_dir}transcoded.{$video_name}";
        $region = 'us-west-2';
        $pipeline_id = '1431531483367-87tajb';
        $transcoder = new AWS_ET(Config::key('aws-access-key'), Config::key('aws-secret-key'), $region);

        $input = ['Key' => $input_key];
        $output = ['Key' => $output_key, 'PresetId' => '1351620000001-000050'];


        // Delete any file that may exist
        $file->delete_file($output_key);

        // Transcode
        $result = $transcoder->createJob($input, [$output], $pipeline_id);

        if ( $result ) {
            $video_url = str_replace($input_key, $output_key, $video_url);
            $this->notify('Your video is being uploaded and will take a few minutes to be optimized and publicly available.  Please check back in 5 minutes.');
        }

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = $video_url;
        $account_file->create();

        // Create the account attachment
        $attachment = $attachment->get_by_key( $page->id, 'video' );
        $attachment->value = $video_url;
        $attachment->save();

        $this->log( 'upload-video', $this->user->contact_name . ' uploaded a video to ' . $this->user->account->title, $video_url );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Add the response
        $response->add_response( 'refresh', 1 );

        return $response;
    }

    /**
     * Upload Banner
     *
     * @return AjaxResponse
     */
    protected function upload_banner() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['apid'] ), _('Not enough data to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();
        $uploader = new qqFileUploader( array( 'gif', 'jpg', 'jpeg', 'png' ), 6144000 );

        // Get some stuff
        $page->get( $_GET['apid'], $this->user->account->id );
        $settings = $this->user->account->get_settings( 'banner-width', 'banner-height' );

        $max_width = ( empty ( $settings['banner-width'] ) ) ? 1500 : $settings['banner-width'];
        $max_height = ( empty ( $settings['banner-height'] ) ) ? 1500 : $settings['banner-height'];

        $banner_name =  format::slug( f::strip_extension( $_GET['qqfile'] ) ) . '.' . f::extension( $_GET['qqfile'] );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $banner_dir = $this->user->account->id . "/banners/";
        $banner_name = $file->upload_image( $result['file_path'], $banner_name, $max_width, $max_height, 'websites', $banner_dir );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Form image url
        $banner_url = 'http://websites.retailcatalog.us/' . $banner_dir . $banner_name;

        // Set variables
        $attachment->website_page_id = $page->id;
        $attachment->key = 'banner';
        $attachment->value = $banner_url;
        $attachment->create();

        $this->log( 'upload-banner', $this->user->contact_name . ' uploaded a banner to ' . $this->user->account->title, $banner_url );

         // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
        }

        // Create new box

        $enable_disable_url = url::add_query_arg( array(
            '_nonce' => nonce::create( 'update_status' )
            , 'apaid' => $attachment->id
            , 's' => '0'
        ), '/website/update-status/' );

        $remove_attachment_url = url::add_query_arg( array(
            '_nonce' => nonce::create('remove_attachment')
            , 'apaid' => $attachment->id
            , 't' => 'dAttachment_' . $attachment->id
            , 'si' => '1'
        ), '/website/remove-attachment/' );

        $banner = '<div class="element-box" id="dAttachment_' . $attachment->id . '" style="width:' . $settings['banner-width'] . 'px">';
        $banner .= '<h2>' . _('Banner') . '</h2>';
        $banner .= '<p><small>' . $settings['banner-width'] . 'x' . $settings['banner-height'] . '</small></p>';
        $banner .= '<a href="' . $enable_disable_url . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this banner?') . '"><img src="/images/trans.gif" width="76" height="25" alt="' . _('Enable/Disable') . '" /></a>';
        $banner .= '<div id="dBanner' . $attachment->id . '" class="text-center">';
        $banner .= '<img src="' . $banner_url . '" alt="' . _('Banner Image') . '" />';
        $banner .= '</div><br />';
        $banner .= '<form action="/website/update-attachment-extra/" method="post" ajax="1">';
        $banner .= '<p id="pTempSuccess' . $attachment->id . '" class="success hidden">' . _('Your banner link has been successfully updated.') . '</p>';
        $banner .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $attachment->id . '" placeholder="' . _('Enter Link...') . '" value="http://" /></p>';
        $banner .= '<input type="submit" class="button" value="' . _('Save') . '" />';
        $banner .= '<input type="hidden" name="hAccountPageAttachmentId" value="' . $attachment->id . '" />';
        $banner .= '<input type="hidden" name="target" value="pTempSuccess' . $attachment->id . '" />';
        $banner .= nonce::field( 'update_attachment_extra', '_nonce', false );
        $banner .= '</form>';
        $banner .= '<a href="' . $remove_attachment_url . '" class="remove" title="' . _('Remove Banner') . '" ajax="1" confirm="' . _('Are you sure you want to remove this banner?') . '">' . _('Remove') . '</a></p>';
        $banner .= '<br clear="all" /></div>';
        
        jQuery('#dElementBoxes')
            ->prepend( $banner )
            ->updateElementOrder()
            ->updateDividers()
            ->sparrow();

        jQuery('#upload-banner-loader')->hide();
        jQuery('#aUploadBanner')->show();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Create Banner
     *
     * @return AjaxResponse
     */
    protected function create_banner() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['apid'] ), _('Not enough data to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate classes
        $file = new File();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        // Get some stuff
        $page->get( $_POST['apid'], $this->user->account->id );
        $settings = $this->user->account->get_settings( 'banner-width', 'banner-height' );

        $max_width = ( empty ( $settings['banner-width'] ) ) ? 1500 : $settings['banner-width'];
        $max_height = ( empty ( $settings['banner-height'] ) ) ? 1500 : $settings['banner-height'];

        $file_path = tempnam( sys_get_temp_dir(), rand(1, 9999) );
        @copy( $_POST['fn'], $file_path );

        $banner_name =  format::slug( f::strip_extension( $_POST['fn'] ) ) . '.' . f::extension( $_POST['fn'] );

        // Create the different versions we need
        $banner_dir = $this->user->account->id . "/banners/";
        $banner_name = $file->upload_image( $file_path, $banner_name, $max_width, $max_height, 'websites', $banner_dir );

        // Delete file
        @unlink( $file_path );

        // Form image url
        $banner_url = 'http://websites.retailcatalog.us/' . $banner_dir . $banner_name;

        // Set variables
        $attachment->website_page_id = $page->id;
        $attachment->key = 'banner';
        $attachment->value = $banner_url;
        $attachment->create();

        $this->log( 'create-banner', $this->user->contact_name . ' created a banner on ' . $this->user->account->title, $banner_url );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' );
        }

        // Add the response
        $response->add_response( 'id', $attachment->id );
        $response->add_response( 'url', $banner_url );

        return $response;
    }

    /**
     * Delete File
     *
     * @return AjaxResponse
     */
    protected function delete_file() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['id'] ), _('Failed to delete file') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate classes
        $bucket = 'websites' . Config::key('aws-bucket-domain');
        $file = new File( $bucket );
        $account_file = new AccountFile();

        // Get the account file
        $account_file->get( $_GET['id'], $this->user->account->domain, $this->user->account->id );

        $file_path = $account_file->file_path;
        if ( $file_path ) {
            $website_attachment = new AccountPageAttachment();
            $website_attachment->remove_by_value( $this->user->account->id, $file_path);
        }

        $response->check( $account_file->id, "File {$_GET['id']} not found");
        if ( $response->has_error() )
            return $response;

        $url_info = parse_url( $account_file->file_path );
        $key = substr( str_replace( $bucket . '/', '', $url_info['path'] ), 1 );

        // Delete from Amazon
        $file->delete_file( $key );

        // Delete record
        $account_file->remove();

        $this->log( 'delete-file', $this->user->contact_name . ' deleted a file on ' . $this->user->account->title, $account_file->id );

        return $response;
    }

    /**
     * Set Pagemeta
     *
     * @return AjaxResponse
     */
    protected function set_pagemeta() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['apid'], $_POST['k'], $_POST['v'] ), _('Bad Parameters') );

        if ( $response->has_error() )
            return $response;

        switch ( $_POST['k'] ) {
            case 'ham':
                $key = 'hide-all-maps';
            break;

            case 'mlm':
                $key = 'multiple-location-map';
            break;

            case 'hcf':
                $key = 'hide-contact-form';
                break;

            default:
                $response->check( false, _('An error occurred when trying to change your setting. Please refresh the page and try again') );
                return $response;
            break;
        }

        $account_pagemeta = new AccountPagemeta();
        $account_pagemeta->add_bulk_by_page( $_POST['apid'], array( $key => $_POST['v'] ) );

        $this->log( 'set-pagemeta', $this->user->contact_name . ' set pagemeta on ' . $this->user->account->title, $_POST );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            $page = new AccountPage();
            $page->get( $_POST['apid'], $this->user->account );

            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $page->slug . '/' );
        }


        return $response;
    }

    /**
     * Remove Sale Items
     *
     * @return AjaxResponse
     */
    protected function remove_sale_items() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $account_product = new AccountProduct();
        $account_product->remove_sale_items( $this->user->account->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/' . $this->user->account->get_settings('page_sale-slug') . '/' );
        }

        $this->log( 'remove-sales-items', $this->user->contact_name . ' remove sales items on ' . $this->user->account->title );

        // Let them know we did so successfully
        $response->check( false, _('All sale items were removed!') );

        return $response;
    }

    /**
     * Update Attachment Extra
     *
     * @return AjaxResponse
     */
    protected function update_attachment_extra() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['hAccountPageAttachmentId'] ), _('Oops! Something went wrong. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        $attachment = new AccountPageAttachment();
        $attachment->get( $_POST['hAccountPageAttachmentId'], $this->user->account->id );

        $meta = ( isset( $_POST['meta'] ) ) ? $_POST['meta'] : '';

        // Do validation
        $extra = isset( $_POST['extra']) ? $_POST['extra'] : '';

        // "extra" can be an array, in that case we store it as JSON
        if ( is_array( $extra ) ) {
            // date parsing
            if ( isset( $extra['date-start'] ) ) {
                $date_start = new DateTime( $extra['date-start'] );
                $extra['date-start'] = $date_start->format('Y-m-d');
            }

            if ( isset( $extra['date-end'] ) ) {
                $date_end = new DateTime( $extra['date-end'] );
                $extra['date-end'] = $date_end->format('Y-m-d');
            }

            // make it json
            $extra = json_encode($extra);

        }

        // Update attachment
        $attachment->extra = $extra;
        $attachment->meta = $meta;
        $attachment->save();

        $this->log( 'update-attachment-extra', $this->user->contact_name . ' updated attachment extra on ' . $this->user->account->title, $attachment->id );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge($cloudflare_zone_id);
        }

        // Update GeoMarketing Locations
        if ( $this->user->account->geo_marketing ) {
            $location = new WebsiteYextLocation();
            $locations = $location->get_all( $this->user->account->id );
            foreach ( $locations as $location ) {
                $location->do_upload_photos( $location );
            }

        }

        $response->notify( 'Sidebar information updated.' );
        return $response;
    }

    /**
     * Update Attachment status
     *
     * @return AjaxResponse
     */
    protected function update_attachment_status() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['apaid'], $_GET['s'] ), _('You do not have permission to update this item') );

        if ( $response->has_error() )
            return $response;

        $attachment = new AccountPageAttachment();
        $attachment->get( $_GET['apaid'], $this->user->account->id );

        $attachment->status = $_GET['s'];
        $attachment->save();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge($cloudflare_zone_id);
        }

        // Update GeoMarketing Locations
        if ( $this->user->account->geo_marketing ) {
            $location = new WebsiteYextLocation();
            $locations = $location->get_all( $this->user->account->id );
            foreach ( $locations as $location ) {
                $location->do_upload_photos( $location );
            }

        }

        $this->log( 'update-attachment-status', $this->user->contact_name . ' updated attachment status on ' . $this->user->account->title, $attachment->id );

        $response->add_response( 'id', $attachment->id );

        return $response;
    }

    /**
     * Update Sidebar Email
     *
     * @return AjaxResponse
     */
    protected function update_sidebar_email() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['hAccountPageAttachmentId'], $_POST['taEmail'] ), _('You do not have permission to update this item') );

        if ( $response->has_error() )
            return $response;

        $attachment = new AccountPageAttachment();
        $attachment->get( $_POST['hAccountPageAttachmentId'], $this->user->account->id );

        $attachment->value = $_POST['taEmail'];
        $attachment->save();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge($cloudflare_zone_id);
        }

        $this->log( 'update-sidebar-email', $this->user->contact_name . ' updated sidebar email on ' . $this->user->account->title, $attachment->id );

        // Notification
        $response->notify( 'Sidebar email updated.' );

        return $response;
    }

    /**
     * Remove Attachment
     *
     * @return AjaxResponse
     */
    protected function remove_attachment() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $account_file = new AccountFile();
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $attachment = new AccountPageAttachment();
        $attachment->get( $_GET['apaid'], $this->user->account->id );

        if ( stristr( $attachment->value, 'http://' ) ) {
            $account_file->get_by_file_path( $attachment->value, $this->user->account->domain, $this->user->account->id );

            if ( $account_file->id )
                $account_file->remove();

            // Delete from Amazon S3 (Not checking because it may have been removed other ways )
            $file->delete_file( str_replace( 'http://websites.retailcatalog.us/', '', $attachment->value ) );
        }

        $attachment->remove();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge($cloudflare_zone_id);
        }

        $this->log( 'remove-attachment', $this->user->contact_name . ' removed an attachment on ' . $this->user->account->title, $attachment->id );

        return $response;
    }

    /**
     * Update Attachment Sequence
     *
     * @return AjaxResponse
     */
    protected function update_attachment_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('You do not have permission to change the sequence of this attachment') );

        if ( $response->has_error() )
            return $response;

        $sequence = explode( '|', $_POST['s'] );

        $attachment = new AccountPageAttachment();
        $attachment->update_sequence( $this->user->account->id, $sequence );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge($cloudflare_zone_id);
        }

        $this->log( 'update-attachment-sequence', $this->user->contact_name . ' updated attachment sequence on ' . $this->user->account->title );


        return $response;
    }

    /**
     * List Products
     *
     * @return DataTableResponse
     */
    protected function list_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        if ( empty( $_GET['s'] ) ) {
            $dt->set_data( array() );
            return $dt;
        }

        $account_product = new AccountProduct();

        // Set Order by
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'p.`status`', 'p.`name`' );
        $dt->add_where( ' AND ( p.`website_id` = 0 || p.`website_id` = ' . (int) $this->user->account->id . ' )' );
        $dt->add_where( ' AND wp.`website_id` = ' . (int) $this->user->account->id );
        $dt->add_where( " AND p.`publish_visibility` = 'public' AND p.`publish_date` <> '0000-00-00 00:00:00'" );

        $skip = true;

        switch ( $_GET['sType'] ) {
            case 'sku':
                if ( _('Enter SKU...') != $_GET['s'] ) {
                    $dt->add_where( " AND p.`sku` LIKE " . $account_product->quote( $_GET['s'] . '%' ) );
                    $skip = false;
                }
            break;

            case 'product':
                if ( _('Enter Product Name...') != $_GET['s'] ) {
                    $dt->add_where( " AND p.`name` LIKE " . $account_product->quote( $_GET['s'] . '%' ) );
                    $skip = false;
                }
            break;

            case 'brand':
                if ( _('Enter Brand...') != $_GET['s'] ) {
                    $dt->add_where( " AND b.`name` LIKE " . $account_product->quote( $_GET['s'] . '%' ) );
                    $skip = false;
                }
            break;
        }

        if ( $skip ) {
            $dt->set_data( array() );
            return $dt;
        }

        // Get items
        $products = $account_product->list_products( $dt->get_variables() );
        $dt->set_row_count( $account_product->count_products( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $add_product_nonce = nonce::create( 'add_product' );

        /**
         * @var Product $product
         */
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
            $dialog = '<a href="' . url::add_query_arg( 'pid', $product->id, '/website/get-product/' ) . '#dProductDialog' . $product->id . '" title="' . _('View') . '" rel="dialog">';
            $actions = '<a href="' . url::add_query_arg( array( '_nonce' => $add_product_nonce, 'pid' => $product->id ), '/website/add-product/' ) . '" class="add-product" title="' . _('Add Product') . '">' . _('Add Product') . '</a>';

            $data[] = array(
                $dialog . format::limit_chars( $product->name,  50, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>'
                , $product->brand
                , $product->sku
                , $product->status
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
    
    /**
     * Get Product
     *
     * @return CustomResponse
     */
    protected function get_product() {
        // Instantiate Object
        $product = new Product();
        $category = new Category();

        // Get Product
        $product->get( $_GET['pid'] );
        $product->images = $product->get_images();

        $category->get( $product->category_id );

        $response = new CustomResponse( $this->resources, 'website/pages/get-product' );
        $response->set( compact( 'product', 'category' ) );

        return $response;
    }
    
    /**
     * Add Product
     *
     * @return AjaxResponse
     */
    protected function add_product() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $product_count = (int) $_POST['product-count'];

        // Make sure we have everything right
        $response->check( isset( $_GET['pid'] ), _('Unable to add product. Please try again.') );
        $response->check( 100 > $product_count, _('You have reached your product limit for this page.') );

        if ( $response->has_error() )
            return $response;

        // Instantiate Object
        $account_product = new AccountProduct();
        $product = new Product();
        $category = new Category();

        // Get Product
        $account_product->get( $_GET['pid'], $this->user->account->id );
        $product->get( $account_product->product_id );
        $product->images = $product->get_images();

        $category->get( $product->category_id );

        $product->image_url = $product->get_image_url( current( $product->images ), 'small', $product->industry, $product->id );
        $response->add_response( 'product', $product );

        // Form the response HTML
        $product_box = '<div id="dProduct_' . $product->id . '" class="product">';
        $product_box .= '<h4>' . $product->name . '</h4>';
        $product_box .= '<p align="center"><img src="http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/small/' . current( $product->images ) . '" alt="' . $product->name . '" height="110" style="margin:10px" /></p>';
        $product_box .= '<p>' . _('Brand') . ': ' . $product->brand . '</p>';
        $product_box .= '<p class="product-actions" id="pProductAction' . $product->id . '"><a href="#" class="remove-product" title="' . _('Remove Product') . '">' . _('Remove') . '</a></p>';
        $product_box .= '<input type="hidden" name="products[]" class="hidden" value="' . $product->id . '" />';
        $product_box .= '</div>';

        return $response;
    }

    /**
     * Add/Edit Location
     *
     * @return AjaxResponse
     */
    protected function add_edit_location() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        // See if we're creating or not
        $website_location_id = (int) $_POST['wlid'];

        // Instantiate Object
        $location = new WebsiteLocation();

        if ( $website_location_id )
            $location->get( $website_location_id, $this->user->account->id );

        $location->name = $_POST['name'];
        $location->address = $_POST['address'];
        $location->city = $_POST['city'];
        $location->state = $_POST['state'];
        $location->zip = $_POST['zip'];
        $location->phone = $_POST['phone'];
        $location->fax = $_POST['fax'];
        $location->email = $_POST['email'];
        $location->website = $_POST['website'];
        $location->store_hours = nl2br( $_POST['store-hours'] );
        $location->store_image = $_POST['store-image'];

        // Get latitude and longitude
        library('google-maps-api');
        $gmaps = new GoogleMapsAPI( $this->user->account );
        $geo_location = $gmaps->geocode( $location->address . ', ' . $location->city . ', ' . $location->state . ' ' . $location->zip );

        if ( $gmaps->success() ) {
            $location->lat = $geo_location->lat;
            $location->lng = $geo_location->lng;
        }

        // Create or save
        if ( $location->id ) {
            $location->save();

            $this->log( 'update-website-location', $this->user->contact_name . ' updated a website location on ' . $this->user->account->title, $location->id );
        } else {
            $location->website_id = $this->user->account->website_id;
            $location->create();

            $this->log( 'create-website-location', $this->user->contact_name . ' created a website location on ' . $this->user->account->title, $location->id );
        }

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/contact-us/' );
        }

        $response->add_response( 'location', $location );

        return $response;
    }

    /**
     * Get location
     *
     * @return AjaxResponse
     */
    public function get_location() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['wlid'] ), _('Unable to get location. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        $location = new WebsiteLocation();
        $location->get( $_GET['wlid'], $this->user->account->id );

        $response->add_response( 'location', $location );

        return $response;
    }

    /**
     * Delete location
     *
     * @return AjaxResponse
     */
    public function delete_location() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['wlid'] ), _('Unable to delete location. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        $location = new WebsiteLocation();
        $location->get( $_GET['wlid'], $this->user->account->id );
        $location->remove();

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/contact-us/' );
        }

        $this->log( 'delete-website-location', $this->user->contact_name . ' deleted a website location on ' . $this->user->account->title, $location->id );

        return $response;
    }

    /**
     * Update Location Sequence
     *
     * @return AjaxResponse
     */
    protected function update_location_sequence() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['s'] ), _('Unable to update location sequence. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $sequence = explode( '|', $_POST['s'] );

        $location = new WebsiteLocation();
        $location->update_sequence( $this->user->account->id, $sequence );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI($this->user->account);
            $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/contact-us/' );
        }

        $this->log( 'update-website-location-sequence', $this->user->contact_name . ' updated website location sequence on ' . $this->user->account->title );

        return $response;
    }

    /**
     * Header
     *
     * @return TemplateResponse
     */
    public function header() {
        if ( $this->verified() ) {
            $header = $_POST['header'];

            // Make URLs work on SSL and non-SSL
            $header = preg_replace( '/src="http(s?):\/\//i', '/src="//', $header );
            // Make S3 Images work on SSL and non-SSL
            $header = preg_replace( '/src="http:\/\/(.*?)\.retailcatalog\.us\/(.*?)"/i', 'src="//s3.amazonaws.com/$1.retailcatalog.us/$2"', $header );
            // Encode Entities
            $header = htmlentities( $header );

            $this->user->account->set_settings( array( 'header' => $header ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify('Your Header settings have been saved!');
            $this->log( 'header-settings', $this->user->contact_name . ' updated header settings on ' . $this->user->account->title );
        }

        $header = $this->user->account->get_settings('header');
        $header = html_entity_decode($header);

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $this->resources
            ->css('media-manager')
            ->javascript('fileuploader', 'media-manager');

        return $this->get_template_response('header')
            ->kb( 139 )
            ->menu_item('website/settings/website-header')
            ->add_title( _('Header') )
            ->set( compact( 'header', 'files' ) );

    }

    /**
     * List Website Brands
     *
     * @return TemplateResponse
     */
    protected function brands() {
        // Reset any defaults
        unset( $_SESSION['brands'] );

        $this->resources
            ->css('website/categories')
            ->javascript('website/brands');

        return $this->get_template_response( 'brands' )
            ->kb( 141 )
            ->menu_item('website/pages/brands');
    }

    /**
     * Edit Brand
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit_brand() {
        // Make sure they can be here
        if ( !isset( $_GET['bid'] ) )
            return new RedirectResponse('/website/brands/');

        $brand = new AccountBrand();
        $brand->get( $this->user->account->id, $_GET['bid'] );

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        if ( $this->verified() ) {
            $brand->name = $_POST['tName'];
            $brand->content = $_POST['taContent'];
            $brand->meta_title = $_POST['tMetaTitle'];
            $brand->meta_description = $_POST['tMetaDescription'];
            $brand->meta_keywords = $_POST['tMetaKeywords'];
            $brand->top = $_POST['rPosition'];

            if ( $brand->website_id ) {
                $brand->save();
            } else {
                $brand->brand_id = $_GET['bid'];
                $brand->website_id = $this->user->account->id;
                $brand->create();
            }

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI($this->user->account);
                $cloudflare->purge_url( $cloudflare_zone_id, 'http://' . $this->user->account->domain . '/brand/' . $brand->slug . '/' );
            }

            $this->notify( _('Your brand has been successfully saved!') );
            $this->log( 'update-brand', $this->user->contact_name . ' updated a brand on ' . $this->user->account->title, $brand->id );

            return new RedirectResponse('/website/brands/');
        }

        $this->resources
            ->css( 'website/pages/page', 'media-manager' )
            ->javascript( 'fileuploader', 'media-manager' );

        return $this->get_template_response('edit-brand')
            ->kb( 0 )
            ->menu_item('website/pages/brands')
            ->add_title( _('Edit Brand') )
            ->set( compact( 'brand', 'files' ) );
    }

    /**
     * List Brands
     *
     * @return DataTableResponse
     */
    protected function list_brands() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_brand = new AccountBrand();

        // Set Order by
        $dt->order_by( '`name`', 'wb.`date_updated`' );
        $dt->add_where( ' AND ( wb.`website_id` IS NULL OR wb.`website_id` = ' . (int) $this->user->account->id . ') ' );
        $dt->add_where( ' AND ( wp.`website_id` = ' . (int) $this->user->account->id . ') ');
        $dt->search( array( 'b.`name`' => false ) );

        // Get account pages
        $account_brands = $account_brand->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_brand->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var AccountCategory $account_brand
         */
        if ( is_array( $account_brands ) )
            foreach ( $account_brands as $account_brand ) {
                $date_update = new DateTime( $account_brand->date_updated );

                $data[] = array(
                    $account_brand->name . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . '/brand/' . $account_brand->slug . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'bid', $account_brand->brand_id, '/website/edit-brand/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' .
                    '</div>'
                , $date_update->format('F jS, Y')
                );
            }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * HTML Header
     *
     * @return TemplateResponse
     */
    public function html_head() {

        if ( $this->verified() ) {
            $html_header = htmlentities( $_POST['html-header'] );
            $this->user->account->set_settings( array( 'html-header' => $html_header ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            $this->notify('Your HTML Header settings have been saved!');
            $this->log( 'update-html-head', $this->user->contact_name . ' updated HTML head on ' . $this->user->account->title );
        }

        $html_header = $this->user->account->get_settings('html-header');
        $html_header = html_entity_decode($html_header);

        return $this->get_template_response('html-head')
            ->kb( 0 )
            ->menu_item('website/settings/html-head')
            ->add_title( _('HTML Head') )
            ->set( compact( 'html_header' ) );

    }

    /**
     * Custom 404
     * @return TemplateResponse
     */
    public function custom_404() {

        if ( $this->verified() ) {
            $text_404 = format::strip_only( $_POST['text-404'], '<script>' );
            $this->user->account->set_settings( array( 'text-404' => $text_404 ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            $this->notify('Your 404 Page Text has been saved!');
            $this->log( 'update-custom-404', $this->user->contact_name . ' updated the custom 404 page on ' . $this->user->account->title );
        }

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $this->resources
            ->css('media-manager')
            ->javascript('fileuploader', 'media-manager');

        $text_404 = $this->user->account->get_settings('text-404');

        return $this->get_template_response('custom-404')
            ->kb( 0 )
            ->menu_item('website/settings/custom-404')
            ->add_title( _('Custom 404 Page') )
            ->set( compact( 'text_404', 'files' ) );

    }

    /**
     * Get Files
     * @return AjaxResponse
     */
    public function get_files() {
        $response = new AjaxResponse( $this->verified() );

        if ( $response->has_error() )
            return $response;

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $page = isset( $_GET['page'] ) ? (int) $_GET['page'] : 0;
        $per_page = isset( $_GET['pp'] ) ? (int) $_GET['pp'] : 18;
        $files = array_slice( $files, $page * $per_page, $per_page );

        foreach ( $files as $file ) {
            $file->name = f::name( $file->file_path );
            $file->url = $file->file_path;
            $file->date = $file->date_created;
            $file->id = $file->website_file_id;
        }

        $response->add_response( 'files', $files );
        return $response;
    }

    /**
     * Footer
     *
     * @return TemplateResponse
     */
    public function footer() {
        if ( $this->verified() ) {
            $footer = $_POST['footer'];

            // Make URLs work on SSL and non-SSL
            $footer = preg_replace( '/http(s?):\/\//i', '//', $footer );

            // Make S3 Images work on SSL and non-SSL
            $footer = preg_replace( '/\/\/(.*?)\.retailcatalog\.us\/(.*?)"/i', '//s3.amazonaws.com/$1.retailcatalog.us/$2"', $footer );

            // Encode Entities
            $footer = htmlentities( $footer );

            $this->user->account->set_settings( array( 'footer' => $footer ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify('Your Footer settings have been saved!');
            $this->log( 'update-footer', $this->user->contact_name . ' updated footer settings on ' . $this->user->account->title );
        }

        $footer = $this->user->account->get_settings('footer');
        $footer = html_entity_decode($footer);

        $this->resources
            ->css('media-manager')
            ->javascript('fileuploader', 'media-manager');

        return $this->get_template_response('footer')
            ->kb( 145 )
            ->menu_item( 'website/settings/website-footer' )
            ->add_title( _('Footer') )
            ->set( compact( 'footer', 'files' ) );

    }

    /**
     * Top Site Navigation
     *
     * @return TemplateResponse
     */
    protected function top_site_navigation() {
        if ( $this->verified() ) {
            $top_site_navigation = array();

            if ( !empty( $_POST['top-site-navigation'] ) ) {
                foreach ( $_POST['top-site-navigation'] as $page ) {
                    list( $url, $name ) = explode( '|', $page );
                    $name = htmlentities( $name );
                    $top_site_navigation[] = compact( 'url', 'name' );
                }
            }

            $this->user->account->set_settings( array( 'top-site-navigation' => json_encode( $top_site_navigation ) ) );

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify('Your Top Site Navigation settings have been saved!');
            $this->log( 'update-top-site-navigation', $this->user->contact_name . ' updated top site navigation on ' . $this->user->account->title );
        }

        $page = new AccountPage();
        $pages = $page->get_by_account( $this->user->account->id );

        $top_site_navigation = $this->user->account->get_settings('top-site-navigation');

        if ( empty( $top_site_navigation ) ) {
            $top_site_navigation = array(
                (object)array('name' => 'Products', 'url' => 'products')
            );
        } else {
            $top_site_navigation = json_decode($top_site_navigation);
        }

        $this->resources
            ->css( 'jquery.nestable', 'website/top-site-navigation' )
            ->javascript( 'jquery.nestable', 'website/top-site-navigation' );

        return $this->get_template_response( 'top-site-navigation' )
            ->kb( 146 )
            ->menu_item('website/navigation-menus/top-site-navigation')
            ->add_title( _('Header Bar Links') )
            ->set( compact( 'pages', 'top_site_navigation' ) );
    }

    /**
     * Add/Edit A Company
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function stylesheet() {
        // Get Account
        $account = new Account();
        $account->get( $this->user->account->id );

        if ( $this->user->account->id != Account::TEMPLATE_UNLOCKED ) {
            $unlocked = new Account();
            $unlocked->get( Account::TEMPLATE_UNLOCKED );
            $unlocked_less = $unlocked->get_settings('less');
        } else {
            $unlocked_less = false;
        }

        $less = $account->get_settings('less');

        $this->resources
            ->css('website/css')
            ->javascript('website/css')
            ->javascript_url( Config::resource('ace-js') );

        return $this->get_template_response( 'css' )
            ->kb( 10 )
            ->menu_item('website/settings/css')
            ->set( compact( 'less', 'account', 'unlocked_less' ) )
            ->add_title( _('LESS CSS') );
    }

    /**
     * Save LESS
     *
     * @return AjaxResponse
     */
    protected function save_less() {
        set_time_limit(3600);

        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());

        // We need backslashes
        $_POST['less'] = addcslashes( $_POST['less'], '\\');

        // Get account
        if ( $this->user->account->id == Account::TEMPLATE_UNLOCKED ) {
            $less_css = $_POST['less'];
        } else {
            $unlocked = new Account();
            $unlocked->get( Account::TEMPLATE_UNLOCKED );
            $unlocked_less = $unlocked->get_settings('less');
            $less_css = $unlocked_less . $_POST['less'];
        }

        $account = new Account();
        $account->get($this->user->account->id);

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        library('lessc.inc');
        $less = new lessc;
        $less->setFormatter("compressed");

        try {
            $css = $less->compile( $less_css );
            echo $css;
        } catch (exception $e) {
            $response->notify( 'Error: ' . $e->getMessage(), false );
            return $response;
        }

        $account->set_settings( array( 'less' => $_POST['less'], 'css' => $css ) );

        $response->notify( 'LESS/CSS has been successfully updated!' );

        // Update all other LESS sites
        if ( $this->user->account->id == Account::TEMPLATE_UNLOCKED ) {
            $less_accounts = $account->get_less_sites();

            /**
             * @var Account $less_account
             * @var string $unlocked_less
             */
            if ( !empty( $less_accounts ) )
                foreach ( $less_accounts as $less_account ) {
                    if ( $less_account->id == Account::TEMPLATE_UNLOCKED )
                        continue;

                    $less = new lessc;
                    $less->setFormatter("compressed");
                    $site_less = $less_account->get_settings('less');

                    $less_account->set_settings( array(
                        'css' => $less->compile( $less_css . $site_less )
                    ));

                    unset( $less );
                    unset( $site_less );
                    unset( $less_account );
                    gc_collect_cycles();
                }
        }

        return $response;
    }

    /**
     * Add/Edit Favicon
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function favicon() {
        $favicon = $this->user->account->get_settings("favicon");
        $this->resources->javascript('fileuploader', 'website/favicon')
            ->css( 'website/favicon' );

        return $this->get_template_response('favicon')
            ->menu_item('website/settings/favicon')
            ->set(compact('favicon', 'account'))
            ->add_title(_('Favicon'));
    }

    /**
     * Upload Favicon
     *
     * @return AjaxResponse
     */
    protected function upload_favicon() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse($this->verified());

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File( 'websites' . Config::key('aws-bucket-domain') );
        $account_file = new AccountFile();

        // Create uploader
        $uploader = new qqFileUploader( array( 'ico' ), 6144000 );

        // Upload file
        $result = $uploader->handleUpload('gsr_');

        $response->check( $result['success'], _('Failed to upload favicon') );

        // If there is an error or now user id, return
        if ( $response->has_error() ) {
            $response->add_response( "error", $result["error"] );
            return $response;
        }

        //Create favicon name
        $favicon_name =  'favicon.ico';

        // Create the different versions we need
        $favicon_dir = $this->user->account->id . '/favicon/';

        // Normal and large
        $file_url =  $file->upload_file( $result['file_path'], $favicon_name, $favicon_dir );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = $file_url;
        $account_file->create();

        $this->user->account->set_settings( array( 'favicon' => $file_url ) );

        $response->add_response( 'refresh', true );

        return $response;
    }

}