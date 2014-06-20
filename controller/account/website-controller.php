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
        return $this->get_template_response( 'index' )
            ->kb( 36 )
            ->select( 'pages', 'view' );
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
            ->css('website/pages/page')
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'fileuploader', 'gsr-media-manager', 'website/pages/page' );

        // Initialize variables
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );
        $product_count = $page->count_products();

        $product_ids = $page->get_product_ids();

        if ( !empty( $product_ids ) ) {
            $product = new Product;
            $page->products = $product->get_by_ids( $product_ids );
        }

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

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
                $title = ( _('Page Title...') == $_POST['tTitle'] ) ? '' : $_POST['tTitle'];

                // Update the page
                $page->slug = $slug;
                $page->title = $title;
                $page->content = $_POST['taContent'];
                $page->meta_title = $_POST['tMetaTitle'];
                $page->meta_description = $_POST['tMetaDescription'];
                $page->meta_keywords = $_POST['tMetaKeywords'];
                $page->top = $_POST['rPosition'];
                $page->save();

                // Update custom meta
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

                // Set pagemeta
                if ( isset( $pagemeta ) )
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

                $pagemeta = $account_pagemeta->get_by_keys( $page->id, 'multiple-location-map', 'hide-all-maps', 'email' );

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

                $resources = compact( 'locations', 'multiple_location_map', 'hide_all_maps', 'email', 'contact_validation' );
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

        /***** NORMAL PAGE FUNCTIONS *****/

        // Setup response
        $js_validation = $v->js_validation();

        return $this->get_template_response( 'edit' )
            ->kb( 37 )
            ->select( 'pages', 'edit' )
            ->add_title( $page->title . ' | ' . _('Pages') )
            ->set( array_merge( compact( 'errs', 'files', 'js_validation', 'page', 'page_title', 'product_count' ), $resources ) );
    }

    /**
     * Add Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add() {
        // Make sure they have the right permissions
        if ( !$this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) )
            return new RedirectResponse('/website/');

        $form = new FormTable( 'fAddPage' );
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

            return new RedirectResponse('/website/');
        }

        $form = $form->generate_form();

        $this->resources->javascript('website/add');

        return $this->get_template_response('add')
            ->kb( 34 )
            ->select( 'pages', 'add' )
            ->add_title( _('Add Page') )
            ->set( compact( 'form') );
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
            ->select( 'pages', 'category-pages' )
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

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        if ( $this->verified() ) {
            if ( _('Category Title...') == $_POST['tTitle'] )
                $_POST['tTitle'] = '';

            $category->title = $_POST['tTitle'];
            $category->content = $_POST['taContent'];
            $category->meta_title = $_POST['tMetaTitle'];
            $category->meta_description = $_POST['tMetaDescription'];
            $category->meta_keywords = $_POST['tMetaKeywords'];
            $category->top = $_POST['rPosition'];
            $category->save();

            $this->notify( _('Your category has been successfully saved!') );

            return new RedirectResponse('/website/categories/');
        }

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'gsr-media-manager', 'website/pages/page' );

        return $this->get_template_response('edit-category')
            ->kb( 39 )
            ->select( 'pages', 'edit-category' )
            ->add_title( _('Edit Category') )
            ->set( compact( 'category', 'files' ) );
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
            ->css( 'website/website-sidebar' )
            ->javascript( 'fileuploader', 'gsr-media-manager', 'website/website-sidebar' );

        return $this->get_template_response( 'website-sidebar' )
            ->kb( 40 )
            ->select( 'sidebar' )
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
            ->css( 'website/banners' )
            ->javascript( 'fileuploader', 'website/banners' );

        return $this->get_template_response( 'banners' )
            ->kb( 41 )
            ->select( 'banners' )
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

            $this->notify( _('Your sale page has been successfully saved!') );

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

            $this->notify( _('Your room planner page has been successfully saved!') );

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
            }
        } else {
            $layout = json_decode( $layout );
        }

        $this->resources
            ->css( 'website/home-page-layout' )
            ->javascript( 'website/home-page-layout' );

        return $this->get_template_response( 'home-page-layout' )
            ->kb( 135 )
            ->select( 'settings', 'home-page-layout' )
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
            ->css( 'website/navigation' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'website/navigation' );

        if ( $this->verified() ) {
            $navigation = array();
            $tree = json_decode( $_POST['tree'], true );

            if ( $tree ) {
                foreach ( $tree as $tree_node ) {
                    $page = $_POST['navigation'][$tree_node['id']];
                    list( $url, $name ) = explode( '|', $page );
                    $navigation_node = compact( 'url', 'name' );

                    // children - sub items
                    // we only accept one child level, so we are ok with this
                    if ( isset( $tree_node['children'] ) ) {
                        $navigation_node['children'] = array();
                        foreach ( $tree_node['children'] as $child_node ) {
                            $sub_page = $_POST['navigation'][$child_node['id']];
                            list( $url, $name ) = explode( '|', $sub_page );
                            $navigation_node['children'][] = compact( 'url', 'name' );
                        }
                    }

                    $navigation[] = $navigation_node;
                }
            }

            $this->user->account->set_settings( array( 'navigation' => json_encode( $navigation ) ) );
            $this->notify('Your Navigation settings have been saved!');
        }

        $navigation = $this->user->account->get_settings('navigation');
        $navigation = ( empty( $navigation ) ) ? array() : json_decode( $navigation );

        return $this->get_template_response( 'navigation' )
            ->kb( 136 )
            ->select( 'settings', 'sidebar-navigation' )
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
            $footer_navigation = array();

            foreach ( $_POST['footer-navigation'] as $page ) {
                list( $url, $name ) = explode( '|', $page );
                $footer_navigation[] = compact( 'url', 'name' );
            }

            $this->user->account->set_settings( array( 'footer-navigation' => json_encode( $footer_navigation ) ) );
            $this->notify('Your Footer Navigation settings have been saved!');
        }

        $page = new AccountPage();
        $pages = $page->get_by_account( $this->user->account->id );

        $footer_navigation = $this->user->account->get_settings('footer-navigation');
        $footer_navigation = ( empty( $footer_navigation ) ) ? array() : json_decode( $footer_navigation );

        $this->resources
            ->css( 'website/footer-navigation' )
            ->javascript( 'website/footer-navigation' );

        return $this->get_template_response( 'footer-navigation' )
            ->kb( 138 )
            ->select( 'settings', 'footer-navigation' )
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
        $form = new FormTable( 'fSettings' );

        // Get settings
        $settings_array = array(
            'banner-width', 'banner-height', 'banner-speed', 'banner-background-color'
            , 'banner-effect', 'banner-hide-scroller', 'disable-banner-fade-out', 'images-alt'
            , 'logo-link'
        );
        if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && $this->user->account->is_new_template() ) {
            $settings_array = array_merge( $settings_array
                , array( 'sidebar-image-width', 'timezone' )
            );
        }
        if ( $this->user->account->is_new_template() ) {
            $settings_array = array_merge( $settings_array
                , array( 'sm-facebook-link', 'sm-twitter-link', 'sm-google-link', 'sm-pinterest-link', 'sm-linkedin-link', 'sm-youtube-link' )
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

        $form->add_field( 'text', _('Speed'), 'banner-speed', $settings['banner-speed'] )
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

        $form->add_field( 'checkbox', _('Disable Banner Fade-out'), 'disable-banner-fade-out', $settings['disable-banner-fade-out'] );

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
        }

        // Next section
        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Other') );

        if ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && $this->user->account->is_new_template() ) {
            $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
                ->options( data::timezones( false, false, true ) );
        }

        $form->add_field( 'text', _('Logo Link URL'), 'logo-link', $settings['logo-link'] )
            ->add_validation( 'url', _('The "Logo Link" must be a valid link') );

        $form->add_field( 'checkbox', _('Images - Alt Tags'), 'images-alt', $settings['images-alt'] );

        if ( $form->posted() ) {
            $new_settings = array();

            foreach ( $settings_array as $k ) {
                $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
            }
            
            $this->user->account->set_settings( $new_settings );

            $this->notify( _('Your settings have been successfully saved!') );

            // Refresh to get all the changes
            return new RedirectResponse('/website/settings/');
        }

        return $this->get_template_response( 'settings' )
            ->kb( 44 )
            ->add_title( _('Settings') )
            ->select( 'settings', 'page-settings' )
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

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

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

        // If they don't have any files, remove the message that is sitting there
        jQuery('#file-list p.no-files')->remove();

        $delete_file_nonce = nonce::create('delete_file');
        $date = new DateTime( $account_file->date_created );
        $confirm = _('Are you sure you want to delete this file?');

        // Add the new link and apply sparrow to it
        if ( in_array( $extension, image::$extensions ) ) {
            $html = '<div id="file-' . $account_file->id . '" class="file"><a href="#' . $account_file->file_path . '" id="aFile' . $account_file->id . '" class="file img" title="' . $file_name . '" rel="' . $date->format( 'F jS, Y') . '"><img src="' . $account_file->file_path . '" alt="' . $file_name . '" /></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $account_file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
        } else {
            $html = '<div id="file-' . $account_file->id . '" class="file"><a href="#' . $account_file->file_path . '" id="aFile' . $account_file->id . '" class="file" title="' . $file_name . '" rel="' . $date->format( 'F jS, Y') . '"><img src="/images/icons/extensions/' . $extension . '.png" alt="' . $file_name . '" /><span>' . $file_name . '</span></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $account_file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
        }

        jQuery('#file-list')
            ->append( $html )
            ->sparrow();

        // Adjust back to original name
        jQuery('#tFileName')
            ->val('')
            ->trigger('blur');

        jQuery('#upload-file-loader')->hide();
        jQuery('#aUploadFile')->show();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );
        $response->add_response( 'file', $account_file->file_path );

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

        switch ( $page->slug ) {
            case 'current-offer':
                jQuery('#dCouponContent')->html('<img src="' . $image_url . '" style="padding-bottom:20px" alt="' . _('Coupon') . '" /><br />');
            break;

            case 'financing':
                jQuery('#dApplyNowContent')->html('<img src="' . $image_url . '" style="padding-bottom:10px" alt="' . _('Apply Now') . '" /><br /><p>' . _('Place "[apply-now]" into the page content above to place the location of your image. When you view your website, this will be replaced with the image uploaded.') . '</p>' );
            break;
        }

        jQuery('#upload-image-loader')->hide();
        jQuery('#aUploadImage')->show();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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
        $uploader = new qqFileUploader( array( 'mp4' ), 26214400 );

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
     * Delete File
     *
     * @return AjaxResponse
     */
    protected function delete_file() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['afid'] ), _('Image failed to upload') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Instantiate classes
        $bucket = 'websites' . Config::key('aws-bucket-domain');
        $file = new File( $bucket );
        $account_file = new AccountFile();

        // Get the account file
        $account_file->get( $_GET['afid'], $this->user->account->domain, $this->user->account->id );

        $url_info = parse_url( $account_file->file_path );
        $key = substr( str_replace( $bucket . '/', '', $url_info['path'] ), 1 );

        // Delete from Amazon
        $file->delete_file( $key );

        // Remove that file
        jQuery('#file-' . $account_file->id )->remove();

        // Delete record
        $account_file->remove();

        // Get the files, see how many there are
        if ( 0 == count( $account_file->get_by_account( $this->user->account->id ) ) )
            jQuery('#file-list')->append( '<p class="no-files">' . _('You have not uploaded any files.') . '</p>'); // Add a message

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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

        $response->check( isset( $_POST['apid'], $_POST['k'], $_POST['v'] ), _('Image failed to upload') );

        if ( $response->has_error() )
            return $response;

        switch ( $_POST['k'] ) {
            case 'ham':
                $key = 'hide-all-maps';
            break;

            case 'mlm':
                $key = 'multiple-location-map';
            break;

            default:
                $response->check( false, _('An error occurred when trying to change your setting. Please refresh the page and try again') );
                return $response;
            break;
        }

        $account_pagemeta = new AccountPagemeta();
        $account_pagemeta->add_bulk_by_page( $_POST['apid'], array( $key => $_POST['v'] ) );

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

        // Empty it the link they didn't enter anything
        if ( 'Enter Link...' == $_POST['extra'] || 'http://' == $_POST['extra'] )
            $_POST['extra'] = '';

        $meta = ( isset( $_POST['meta'] ) ) ? $_POST['meta'] : '';

        // Do validation
        $v = new Validator( 'fUpdateExtra' );
        $v->add_validation( 'extra', 'URL' );

        $response->check( empty( $errs ) && ( empty( $_POST['extra'] ) || stristr( $_POST['extra'], 'http' ) ), _('Please make sure you enter in a valid link') );

        if ( $response->has_error() )
            return $response;

        // Update attachment
        $attachment->extra = $_POST['extra'];
        $attachment->meta = $meta;
        $attachment->save();

        // Show and hide success
        jQuery( '#' . $_POST['target'] )->show()->delay(5000)->hide();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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

        $enable_disable_url = url::add_query_arg( array(
            '_nonce' => nonce::create( 'update_attachment_status' )
            , 'apaid' => $attachment->id
            , 's' => ( '0' == $_GET['s'] ) ? '1' : '0'
        ), '/website/update-attachment-status/' );

        if ( '0' == $_GET['s'] ) {
            jQuery('#aEnableDisable' . $attachment->id)->replaceWith('<a href="' . $enable_disable_url . '" id="aEnableDisable' . $attachment->id . '" class="enable-disable disabled" title="' . _('Enable/Disable') . '" ajax="1"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>');

            // Disabled
                jQuery('#dAttachment_' . $attachment->id)
                ->addClass('disabled')
                ->insertAfter('#dElementBoxes .element-box:last:not(#dAttachment_' . $attachment->id . ')')
                ->updateElementOrder()
                ->updateDividers();
        } else {
            jQuery('#aEnableDisable' . $attachment->id)->replaceWith('<a href="' . $enable_disable_url . '" id="aEnableDisable' . $attachment->id . '" class="enable-disable" title="' . _('Enable/Disable') . '" ajax="1" confirm="' . _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.') . '"><img src="/images/trans.gif" width="26" height="28" alt="' . _('Enable/Disable') . '" /></a>');

            // Enabled
            jQuery('#dAttachment_' . $attachment->id)->removeClass('disabled');
        }

        jQuery('#aEnableDisable' . $attachment->id)->parent()->sparrow();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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

        // Show and hide success
        jQuery('#pTempEmailMessage')->show()->delay(5000)->hide();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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

        $response->check( isset( $_GET['si'], $_GET['t'] ), _('You do not have permission to remove this attachment') );

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

        if ( '1' == $_GET['si'] ) {
            $attachment->remove();

            jQuery('#' . $_GET['t'])->remove()->updateDividers();
        } else {
            $attachment->value = '';
            $attachment->save();

            // Figure out what it's getting replaced with
            switch ( $_GET['t'] ) {
                case 'dRoomPlannerContent':
                    $replacement = '<img src="/media/images/placeholders/240x100.png" width="200" height="100" alt="' . _('Placeholder') . '" />';
                break;

                case 'dVideoContent':
                    $replacement = '<img src="/media/images/placeholders/354x235.png" width="354" height="235" alt="' . _('Placeholder') . '" />';
                break;

                default:
                    $replacement = '<img src="/media/images/placeholders/240x300.png" width="240" height="300" alt="' . _('Placeholder') . '" />';
                break;
            }

            // Replace the current image and remove the remove link
            jQuery('#' . $_GET['t'])->html($replacement);
            jQuery('#aRemove' . $attachment->id)->remove();
        }

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

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

        $sequence = explode( '&dAttachment[]=', $_POST['s'] );
        $sequence[0] = substr( $sequence[0], 14 );

        $attachment = new AccountPageAttachment();
        $attachment->update_sequence( $this->user->account->id, $sequence );

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

        // Form the response HTML
        $product_box = '<div id="dProduct_' . $product->id . '" class="product">';
        $product_box .= '<h4>' . $product->name . '</h4>';
        $product_box .= '<p align="center"><img src="http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/small/' . current( $product->images ) . '" alt="' . $product->name . '" height="110" style="margin:10px" /></p>';
        $product_box .= '<p>' . _('Brand') . ': ' . $product->brand . '</p>';
        $product_box .= '<p class="product-actions" id="pProductAction' . $product->id . '"><a href="#" class="remove-product" title="' . _('Remove Product') . '">' . _('Remove') . '</a></p>';
        $product_box .= '<input type="hidden" name="products[]" class="hidden" value="' . $product->id . '" />';
        $product_box .= '</div>';

        jQuery('#dSelectedProducts')->append( $product_box );
        jQuery('#product-count')->text( number_format( $product_count + 1 ) );
        jQuery('a.close:visible')->click();

        $response->add_response( 'jquery', jQuery::getResponse() );

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
        } else {
            $location->website_id = $this->user->account->website_id;
            $location->create();
        }


        $delete_location = nonce::create( 'delete_location' );
        $confirm_delete = _('Are you sure you want to delete this location? This cannot be undone.');

        $location_html = '<div class="location" id="location-' . $location->id . '">';
        $location_html .= '<h2><span class="name">' . $location->name . '</span></h2>';
        $location_html .= '<div class="location-left">';
        $location_html .= '<span class="address">' . $location->address . '</span><br />';
        $location_html .= '<span class="city">' . $location->city . '</span>, <span class="state">' . $location->state . '</span> <span class="zip">' . $location->zip . '</span>';
        $location_html .= '</div>';
        $location_html .= '<div class="location-right">';
        $location_html .= '<span class="phone">' . $location->phone . '</span><br />';
        $location_html .= '<span class="fax">' . $location->fax . '</span><br />';
        $location_html .= '</div>';
        $location_html .= '<div class="float-right">';
        $location_html .= '<span class="email">' . $location->email . '</span><br />';
        $location_html .= '<span class="website">' . $location->website . '</span>';
        $location_html .= '</div>';
        $location_html .= '<br />';
        $location_html .= '<br clear="all" />';
        $location_html .= '<br />';
        $location_html .= '<strong>' . _('Store Hours') . ':</strong>';
        $location_html .= '<br />';
        $location_html .= '<span class="store-hours">' . $location->store_hours . '</span>';
        $location_html .= '<div class="actions">';
        $location_html .= '<a href="' . url::add_query_arg( array( '_nonce' => $delete_location, 'wlid' => $location->id ), '/website/delete-location/' ) . '" class="delete-location" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete') . '" /></a>';
        $location_html .= '<a href="#' . $location->id . '" class="edit-location" title="' . _('Edit') . '"><img src="/images/icons/edit.png" width="15" height="17" alt="' . _('Edit') . '" /></a>';
        $location_html .= '</div>';
        $location_html .= '</div>';

        // Are we replacing or appending
        if ( $website_location_id ) {
            jQuery('#location-' . $location->id)->replaceWith( $location_html );
            jQuery('#location-' . $location->id)->sparrow();
        } else {
            jQuery('#dContactUsList')->append( $location_html )->sparrow();
        }

        jQuery('a.close:visible')->click();

        $response->add_response( 'jquery', jQuery::getResponse() );

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
        $response->check( isset( $_POST['wlid'] ), _('Unable to get location. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        $location = new WebsiteLocation();
        $location->get( $_POST['wlid'], $this->user->account->id );

        jQuery('#name')->val( $location->name );
        jQuery('#address')->val( $location->address );
        jQuery('#city')->val( $location->city );
        jQuery('#state')->val( $location->state );
        jQuery('#zip')->val( $location->zip );
        jQuery('#phone')->val( $location->phone );
        jQuery('#fax')->val( $location->fax );
        jQuery('#email')->val( $location->email );
        jQuery('#website')->val( $location->website );
        jQuery('#store-hours')->val( str_replace( '<br />', '', $location->store_hours ) );
        jQuery('#store-image')->val( $location->store_image );
        if ( $location->store_image )
            jQuery('#store-image-preview .image')->attr( 'src', $location->store_image )->show();
        else
            jQuery('#store-image-preview .image')->hide();
        jQuery('#wlid')->val( $location->id );

        $response->add_response( 'jquery', jQuery::getResponse() );

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

        jQuery('#location-' . $location->id)->remove();

        $response->add_response( 'jquery', jQuery::getResponse() );

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

        $sequence = explode( '&location[]=', $_POST['s'] );
        $sequence[0] = substr( $sequence[0], 9 );

        $location = new WebsiteLocation();
        $location->update_sequence( $this->user->account->id, $sequence );

        return $response;
    }

    /**
     * Update Home Page Layout
     *
     * @return AjaxResponse
     */
    protected function save_layout() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['layout'] ), _('Unable to update Home Page Layout. Please contact your Online Specialist.') );

        // Return if there is an error
        if ( $response->has_error() )
            return $response;

        $layout_array = explode( '&layout[]=', urldecode( $_POST['layout'] ) );
        $layout_array[0] = substr( $layout_array[0], 9 );
        $layout = array();

        foreach ( $layout_array as $element ) {
            list( $name, $disabled ) = explode( '|', $element );
            $name = strtolower( $name );
            $layout[] = compact( 'name', 'disabled' );
        }

        $this->user->account->set_settings( array( 'layout' => json_encode( $layout ) ) );
        $this->notify('Your Layout settings have been saved!');

        return $response;
    }

    /**
     * Header
     *
     * @return TemplateResponse
     */
    public function header() {

        if ( $this->verified() ) {
            $header = htmlentities( $_POST['header'] );
            $this->user->account->set_settings( array( 'header' => $header ) );
            $this->notify('Your Header settings have been saved!');
        }

        $header = $this->user->account->get_settings('header');
        $header = html_entity_decode($header);

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $this->resources
            ->javascript('fileuploader', 'gsr-media-manager');

        return $this->get_template_response('header')
            ->kb( 139 )
            ->select( 'settings', 'header-html' )
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
            ->select( 'pages', 'brand-pages' );
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

            $this->notify( _('Your brand has been successfully saved!') );

            return new RedirectResponse('/website/brands/');
        }

        $this->resources
            ->css( 'website/pages/page' )
            ->javascript( 'fileuploader', 'gsr-media-manager', 'website/pages/page' );

        return $this->get_template_response('edit-brand')
            ->kb( 0 )
            ->select( 'pages', 'edit-brand' )
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
        $dt->order_by( 'name', 'wb.`date_updated`' );
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
    public function html_header() {

        if ( $this->verified() ) {
            $html_header = htmlentities( $_POST['html-header'] );
            $this->user->account->set_settings( array( 'html-header' => $html_header ) );
            $this->notify('Your HTML Header settings have been saved!');
        }

        $html_header = $this->user->account->get_settings('html-header');
        $html_header = html_entity_decode($html_header);

        return $this->get_template_response('html-header')
            ->kb( 0 )
            ->select( 'settings', 'html-header' )
            ->add_title( _('HTML Header') )
            ->set( compact( 'html_header' ) );

    }

    function custom_404() {

        if ( $this->verified() ) {
            $text_404 = format::strip_only( $_POST['text-404'], '<script>' );
            $this->user->account->set_settings( array( 'text-404' => $text_404 ) );
            $this->notify('Your 404 Page Text has been saved!');
        }

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $this->resources
            ->javascript('fileuploader', 'gsr-media-manager');

        $text_404 = $this->user->account->get_settings('text-404');

        return $this->get_template_response('custom-404')
            ->kb( 0 )
            ->select( 'settings', 'custom-404' )
            ->add_title( _('Custom 404 Page') )
            ->set( compact( 'text_404', 'files' ) );

    }
}