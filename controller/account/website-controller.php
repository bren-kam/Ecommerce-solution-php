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

        // Initialize variables
        $page = new AccountPage();
        $page->get( $_GET['apid'], $this->user->account->id );

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $account_pagemeta = new AccountPagemeta();

        /***** VALIDATION *****/
        $v = new Validator( 'fEditPage' );

        // Products can be blank
        if ( 'products' != $page->slug )
            $v->add_validation( 'taContent', 'req', _('Page Content is required.') );

        // Custom validation
        switch ( $page->slug ) {
            case 'financing':
                $v->add_validation( 'tApplyNowLink', 'URL', _('The "Apply Now Link" field must contain a valid link') );
            break;

            case 'current-offer':
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

            // if there are no errors
            if ( empty( $errs ) ) {
                // Home page can't update their slug
                $slug = ( 'home' == $page->slug ) ? 'home' : $_POST['tPageSlug'];
                $title = ( _('Page Title...') == $_POST['tTitle'] ) ? '' : $_POST['tTitle'];
                $mobile = (int) 'on' == $_POST['cbIsMobile'];

                // Update the page
                $page->slug = $slug;
                $page->title = $title;
                $page->content = $_POST['taContent'];
                $page->meta_title = $_POST['tMetaTitle'];
                $page->meta_description = $_POST['tMetaDescription'];
                $page->meta_keywords = $_POST['tMetaKeywords'];
                $page->mobile = $mobile;
                $page->save();

                // Update custom meta
                switch ( $page->slug ) {
                    case 'contact-us':
                        $pagemeta = array( 'addresses' => htmlspecialchars( $_POST['hAddresses'] ) );
                    break;

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

                    default:break;
                }

                // Set pagemeta
                if ( isset( $pagemeta ) )
                    $account_pagemeta->add_bulk_by_page( $page->id, $pagemeta );

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

                $pagemeta = $account_pagemeta->get_by_keys( $page->id, 'addresses', 'multiple-location-map', 'hide-all-maps' );

                foreach ( $pagemeta as $key => $value ) {
                    $key = str_replace( '-', '_', $key );
                    $$key = $value;
                }

                $resources = compact( 'contacts', 'multiple_location_map', 'hide_all_maps' );
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

        $this->resources
            ->css('website/pages/page')
            ->javascript( 'fileuploader', 'website/pages/page' );

        $response = $this->get_template_response( 'edit' )
            ->select( 'pages', 'edit' )
            ->add_title( $page->title . ' | ' . _('Pages') )
            ->set( array_merge( compact( 'errs', 'files', 'js_validation', 'page', 'page_title' ), $resources ) );

        return $response;
    }

    /**
     * Add Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function add() {
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

        $response = $this->get_template_response('add')
            ->select( 'pages', 'add' )
            ->add_title( _('Add Page') )
            ->set( compact( 'form') );

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
            if ( !$category->has_parent() || !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }

        $this->resources
            ->css('website/categories')
            ->javascript('website/categories');

        return $this->get_template_response( 'categories' )
            ->select( 'pages', 'category-pages' )
            ->set( compact( 'categories' ) );
    }

    /**
     * Add Page
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function edit_category() {
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
            ->javascript( 'fileuploader', 'website/pages/page' );

        $response = $this->get_template_response('edit-category')
            ->select( 'pages', 'edit-category' )
            ->add_title( _('Edit Category') )
            ->set( compact( 'category', 'files' ) );

        return $response;
    }

    /**
     * Sidebar
     *
     * @return TemplateResponse
     */
    public function sidebar() {
        // Initialize classes
        $account_file = new AccountFile();
        $attachment = new AccountPageAttachment();
        $page = new AccountPage();

        // Get variables
        $files = $account_file->get_by_account( $this->user->account->id );
        $page->get_by_slug( $this->user->account->id, 'sidebar' );
        $attachments_array = $attachment->get_by_account_page_ids( array( $page->id ) );
        $settings = $this->user->account->get_settings( 'sidebar-image-width', 'images-alt' );

        // Do stuff with variables
        $dimensions = ( empty( $settings['sidebar-image-width'] ) ) ? '' : _('Width') . ': ' . $settings['sidebar-image-width'];
        $images_alt = '1' == $settings['images-alt'];

        $attachments = array();

        /**
         * @var AccountPageAttachment $a
         */
        foreach( $attachments_array as $a ) {
            $attachments[$a->key] = $a;
        }

        $this->resources
            ->css( 'website/website-sidebar' )
            ->javascript( 'fileuploader', 'website/pages/page', 'website/website-sidebar' );

        $response = $this->get_template_response( 'website-sidebar' )
            ->select( 'sidebar' )
            ->add_title( _('Sidebar') )
            ->set( compact( 'dimensions', 'files', 'attachments', 'page', 'images_alt' ) );

        return $response;
    }

    /**
     * Banners
     *
     * @return TemplateResponse
     */
    public function banners() {
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

        // Determinve variables
        $dimensions = $settings['banner-width'] . 'x' . $settings['banner-height'];
        $images_alt = '1' == $settings['images-alt'];

        $this->resources
            ->css( 'website/banners' )
            ->javascript( 'fileuploader', 'website/banners' );

        $response = $this->get_template_response( 'banners' )
            ->select( 'banners' )
            ->add_title( _('Banners') )
            ->set( compact( 'attachments', 'dimensions', 'images_alt', 'page', 'settings' ) );

        return $response;
    }

    /**
     * Sale
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function sale() {
        // Instantiate classes
        $form = new FormTable( 'fSale' );

        // Get settings
        $settings = $this->user->account->get_settings( 'page_sale-slug', 'page_sale-title' );

        $form->add_field( 'text', _('Page Title'), 'tPageTitle', $settings['page_sale-title'] )
            ->attribute( 'maxlength', '50' );

        $form->add_field( 'text', _('Page Slug'), 'tPageSlug', $settings['page_sale-slug'] )
            ->attribute( 'maxlength', '50' );

        if ( $form->posted() ) {
            $this->user->account->set_settings( array(
                    'page_sale-title' => $_POST['tPageTitle']
                    , 'page_sale-slug' => format::slug( $_POST['tPageSlug'] )
                )
            );

            $this->notify( _('Your sale page has been successfully saved!') );

            // Refresh to get all the changes
            return new RedirectResponse('/website/sale/');
        }

        $response = $this->get_template_response( 'sale' )
            ->add_title( _('Sale') )
            ->select( 'sale' )
            ->set( array( 'form' => $form->generate_form() ) );

        return $response;
    }

    /**
     * Room Planner
     */
    public function room_planner() {
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

        $response = $this->get_template_response( 'room-planner' )
            ->add_title( _('Room Planner') )
            ->select( 'room-planner' )
            ->set( array( 'form' => $form->generate_form() ) );

        return $response;
    }

    /**
     * Settings page
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function settings() {
        // Instantiate classes
        $form = new FormTable( 'fSettings' );

        // Get settings
        $settings_array = array( 'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-effect', 'banner-hide-scroller', 'sidebar-image-width', 'timezone', 'images-alt' );
        $settings = $this->user->account->get_settings( $settings_array );

        // Create form
        $form->add_field( 'title', _('Banners') );

        $form->add_field( 'text', _('Width'), 'banner-width', $settings['banner-width'] )
            ->attribute( 'maxlength', '4' )
            ->add_validation( 'req', _('The "Banners - Width" field is required') )
            ->add_validation( 'num', _('The "Banners - Width" field may only contain a number') );

        $form->add_field( 'text', _('Height'), 'banner-height', $settings['banner-height'] )
            ->attribute( 'maxlength', '3' )
            ->add_validation( 'req', _('The "Banners - Height" field is required') )
            ->add_validation( 'num', _('The "Banners - Height" field may only contain a number') );

        $form->add_field( 'text', _('Speed'), 'banner-speed', $settings['banner-speed'] )
            ->attribute( 'maxlength', '2' )
            ->add_validation( 'req', _('The "Banners - Speed" field is required') )
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

        // Next section
        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Sidebar Images') );

        $form->add_field( 'text', _('Width'), 'sidebar-image-width', $settings['sidebar-image-width'] )
            ->attribute( 'maxlength', '4' )
            ->add_validation( 'num', _('The "Sidebar Image - Width" field may only contain a number') );

        // Next section
        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Other') );

        $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
            ->options( data::timezones( false, false, true ) );

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

        $response = $this->get_template_response( 'settings' )
            ->add_title( _('Settings') )
            ->select( 'settings' )
            ->set( array( 'form' => $form->generate_form() ) );

        return $response;
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
        $dt->order_by( '`title`', '`status`', '`date_updated`' );
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

            $date_update = new DateTime( $page->date_updated );

            $data[] = array(
                $title . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . '/' . $page->slug . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'apid', $page->id, '/website/edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions .
                    '</div>'
                , ( $page->status ) ? _('Visible') : _('Not Visible')
                , $date_update->format('F jS, Y')
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
                    '<a href="http://' . $this->user->account->domain . '/' . $category->get_url( $account_category->category_id ) . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
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
    public function delete_page() {
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
    public function upload_file() {
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
        $uploader = new qqFileUploader( array( 'pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v;*mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml' ), 6144000 );

        // Change the name
        $file_name =  format::slug( f::strip_extension( $_GET['fn'] ) ) . '.' . f::extension( $_GET['qqfile'] );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $file_url = $file->upload_file( $result['file_path'], $file_name, $this->user->account->id . '/mm/' );

        // Create the account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = $file_url;
        $account_file->create();

        // If they don't have any files, remove the message that is sitting there
        jQuery('#ulUploadFile li.no-files')->remove();

        // Add the new link and apply sparrow to it
        jQuery('#ulUploadFile')
            ->append( '<li id="li' . $account_file->id . '"><a href="' . $account_file->file_path . '" id="aFile' . $account_file->id . '" title="' . $file_name . '" class="file">' . $file_name . '</a><a href="' . url::add_query_arg( array( '_nonce' => nonce::create('delete_file'), 'afid' => $account_file->id ), '/website/delete-file/' ) . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . _('Are you sure you want to delete this file?') . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a>' )
            ->sparrow();

        // Adjust back to original name
        jQuery('#tFileName')
            ->val('')
            ->trigger('blur');

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload Image
     *
     * @return AjaxResponse
     */
    public function upload_image() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['fn'], $_GET['apid'], $_GET['fn'] ), _('Not enough data to upload image') );

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

        // Form image url
        $image_url = 'http://websites.retailcatalog.us/' . $image_dir . $image_name;

        // Create/update the account attachment
        $attachment = $attachment->get_by_key( $page->id, $key );

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

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload Sidebar Image
     *
     * @return AjaxResponse
     */
    public function upload_sidebar_image() {
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

        // Form image url
        $image_url = 'http://websites.retailcatalog.us/' . $image_dir . $image_name;

        // Create the account attachment
        $attachment->website_page_id = $page->id;
        $attachment->key = 'sidebar-image';
        $attachment->value = $image_url;
        $attachment->create();

        $element_box = '<div class="element_box" id="dAttachment_' . $attachment->id . '">';
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
        $element_box .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $attachment->id . '" tmpval="' . _('Enter Link...') . '" value="http://" /></p>';
        $element_box .= '<p id="pTempSidebarImage' . $attachment->id . '" class="success hidden">' . _('Your Sidebar Image link has been successfully updated.') . '</p><br />';
        $element_box .= '<p align="center"><input type="submit" class="button" value="' . _('Save') . '" /></p>';
        $element_box .= '</div>';
        $element_box .= '<input type="hidden" name="hWebsiteAttachmentID" value="' . $attachment->id . '" />';
        $element_box .= '<input type="hidden" name="target" value="pTempSidebarImage' . $attachment->id . '" />';
        $element_box .= nonce::field( 'update_attachment_extra', '_nonce', false );
        $element_box .= '</form></div></div>';
        
        jQuery('#dElementBoxes')
            ->append( $element_box )
            ->updateElementOrder()
            ->updateDividers()
            ->sparrow();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload Banner
     *
     * @return AjaxResponse
     */
    public function upload_banner() {
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

        $max_width = ( empty ( $settings['banner-width'] ) ) ? 1000 : $settings['banner-width'];
        $max_height = ( empty ( $settings['banner-height'] ) ) ? 1000 : $settings['banner-height'];

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
        $banner .= '<p><input type="text" class="tb" name="extra" id="tSidebarImage' . $attachment->id . '" tmpval="' . _('Enter Link...') . '" value="http://" /></p>';
        $banner .= '<input type="submit" class="button" value="' . _('Save') . '" />';
        $banner .= '<input type="hidden" name="hAccountPageAttachmentId" value="' . $attachment->id . '" />';
        $banner .= '<input type="hidden" name="target" value="pTempSuccess' . $attachment->id . '" />';
        $banner .= nonce::field( 'update_attachment_extra', '_nonce', false );
        $banner .= '</form>';
        $banner .= '<a href="' . $remove_attachment_url . '" class="remove" title="' . _('Remove Banner') . '" ajax="1" confirm="' . _('Are you sure you want to remove this banner?') . '">' . _('Remove') . '</a></p>';
        $banner .= '<br clear="all" /></div>';
        
        jQuery('#dElementBoxes')
            ->append( $banner )
            ->updateElementOrder()
            ->updateDividers()
            ->sparrow();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Delete File
     *
     * @return AjaxResponse
     */
    public function delete_file() {
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

        // Remove that li
        jQuery('#li' . $account_file->id )->remove();

        // Delete record
        $account_file->remove();

        // Get the files, see how many there are
        if ( 0 == count( $account_file->get_by_account( $this->user->account->id ) ) )
            jQuery('#ulUploadFile')->append( '<li class="no-files">' . _('You have not uploaded any files.') . '</li>'); // Add a message

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Set Pagemeta
     *
     * @return AjaxResponse
     */
    public function set_pagemeta() {
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
    public function remove_sale_items() {
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
    public function update_attachment_extra() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

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
    public function update_attachment_status() {
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
    public function update_sidebar_email() {
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
    public function remove_attachment() {
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

            // Delete from Amazon S3 (Not checking because it may have been removed other ways )
            $file->delete_file( str_replace( 'http://websites.retailcatalog.us/', '', $account_file->file_path ) );
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
    public function update_attachment_sequence() {
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
}