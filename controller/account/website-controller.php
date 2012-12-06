<?php
class WebsiteController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'website/';
        $this->title = 'Website';
    }

    /**
     * List Website Pages
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'website', 'pages' );
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
            ->select( 'website', 'edit' )
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
            ->select( 'website', 'add' )
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
            ->select( 'website', 'category-pages' )
            ->set( compact( 'categories' ) );
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
     * List Cateogires
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

        $response->check( isset( $_GET['fn'], $_GET['aid'] ), _('File failed to upload') );

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

        $response->check( isset( $_GET['fn'], $_GET['aid'], $_GET['apid'], $_GET['fn'] ), _('Not enough data to upload image') );

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
        $page->get( $_GET['apid'] );

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
        $account_file->get( $_GET['afid'], $this->user->account->domain );

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
}


