<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 24/04/15
 * Time: 15:10
 */

class RemarketingController extends BaseController {

    /**
     * __construct
     */
    public function __construct() {
        parent::__construct();
        $this->title = 'Remarketing';
        $this->view_base = 'shopping-cart/remarketing/';
    }

    /**
     * List Carts
     *
     * @return TemplateResponse
     */
    protected function index() {

        $website_cart = new WebsiteCart();

        $since = new DateTime();
        $since->sub(new DateInterval('P1M'));
        $overview = $website_cart->get_remarketing_report( $this->user->account->id, $since );

        $this->resources->css('shopping-cart/remarketing/index');

        return $this->get_template_response( 'index' )
            ->kb( 120 )
            ->menu_item( 'shopping-cart/remarketing/list' )
            ->set( compact('overview', 'since') );
    }

    /**
     * List Shopping Cart
     *
     * @return DataTableResponse
     */
    protected function list_carts() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $website_cart = new WebsiteCart();

        // Set Order by
        $dt->order_by( 'wc.`timestamp`' );
        $dt->add_where( ' AND wc.`email` IS NOT NULL AND wc.`website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( 'wc.`website_cart_id`' => false ) );

        // Get items
        $website_carts = $website_cart->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_cart->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $abandoned_limit_minutes = $this->user->account->get_settings('remarketing-idle-seconds');
        if ( empty( $abandoned_limit_minutes ) )
            $abandoned_limit_minutes = 30;
        $abandoned_limit = new DateTime();
        $abandoned_limit->sub(new DateInterval("P{$abandoned_limit_minutes}M"));

        /**
         * @var WebsiteCart $cart
         */
        if ( is_array( $website_carts ) )
            foreach ( $website_carts as $cart ) {

                $cart_updated = new DateTime($cart->timestamp);

                if ( $cart->website_order_id ) {
                    $status = 'Converted';
                } else if ( $cart_updated > $abandoned_limit ) {
                    $status = 'Abandoned';
                } else if ( $cart_updated <= $abandoned_limit ) {
                    $status = 'New';
                }

                $cart_created = new DateTime( $cart->date_created );

                $data[] = array(
                    '<a href="' . url::add_query_arg( 'wcid', $cart->id, '/shopping-cart/remarketing/view/' ) . '" title="' . _('View') . '">' . $cart->id . '</a>'
                , $cart->name
                , $cart->products
                , '$' . number_format( $cart->total_price, 2 )
                , $status
                , $cart_created->format('F jS, Y h:ia')
                , $cart_updated->format('F jS, Y h:ia')
                );
            }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }


    /**
     * View a Cart
     *
     * @return TemplateResponse
     */
    protected function view() {
        if ( !isset( $_GET['wcid'] ) )
            return new RedirectResponse('/shopping-cart/remarketing/');

        $cart = new WebsiteCart();
        $cart->get_complete( $_GET['wcid'], $this->user->account->id );

        if ( !$cart->id )
            return new RedirectResponse('/shopping-cart/remarketing/');

        $this->resources
            ->css('shopping-cart/remarketing/view')
            ->javascript('shopping-cart/remarketing/view');

        return $this->get_template_response( 'view' )
            ->kb( 121 )
            ->add_title( _('View Cart') )
            ->menu_item( 'shopping-cart/remarketing/list' )
            ->set( compact( 'cart' ) );
    }

    /**
     * Settings
     * @return RedirectResponse|TemplateResponse
     */
    public function settings() {

        $form = new BootstrapForm('remarketing-settings');

        $settings = $this->user->account->get_settings(
            'remarketing-title'
            , 'remarketing-intro-text'
            , 'remarketing-idle-seconds'
            , 'remarketing-notification-email'
            , 'remarketing-coupon'
        );

        $form->add_field('text', 'Show popup after seconds', 'idle-seconds', $settings['remarketing-idle-seconds'] ? $settings['remarketing-idle-seconds'] : 60)
            ->add_validation('req', 'Required');

        $form->add_field('text', 'Title', 'title', $settings['remarketing-title']);

        $settings['remarketing-intro-text'] = html_entity_decode($settings['remarketing-intro-text']);
        $form->add_field('textarea', 'Intro Text', 'intro-text', $settings['remarketing-intro-text'])
            ->attribute('rte', 1);

        $upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
        $search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
        $delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
        $form->add_field('anchor', 'Add Image', 'Add Image')
            ->attribute('href', 'javascript:;')
            ->attribute('class', 'btn btn-default btn-xs')
            ->attribute('title', 'Open Media Manager')
            ->attribute('data-media-manager', '1')
            ->attribute('data-upload-url', $upload_url)
            ->attribute('data-search-url', $search_url)
            ->attribute('data-delete-url', $delete_url);

        $form->add_field('text', 'Notification Email', 'notification-email', $settings['remarketing-notification-email']);

        $form->add_field('image', 'Coupon', 'coupon' )
            ->attribute('src', $settings['remarketing-coupon'] ? $settings['remarketing-coupon'] : '//placehold.it/200x150&text=No+Coupon+Yet');

        $form->add_field('hidden', 'coupon-path', 'coupon-path', $settings['remarketing-coupon']);

        $form->add_field('anchor', 'Upload Coupon', 'upload')
            ->attribute('href', 'javascript:;')
            ->attribute('class', 'btn btn-default btn-xs')
            ->attribute('title', 'Upload Coupon');

        $form->add_field('block', 'uploader', 'uploader', '');

        if ( $form->posted() ) {

            // Make URLs work on SSL and non-SSL
            $intro = preg_replace( '/src="http(s?):\/\//i', '/src="//', $_POST['intro-text'] );
            // Make S3 Images work on SSL and non-SSL
            $intro = preg_replace( '/src="http:\/\/(.*?)\.retailcatalog\.us\/(.*?)"/i', 'src="//s3.amazonaws.com/$1.retailcatalog.us/$2"', $intro );
            // Encode Entities
            $intro = htmlentities( $intro );

            $this->user->account->set_settings([
                'remarketing-title' => $_POST['title']
                , 'remarketing-intro-text' => $intro
                , 'remarketing-idle-seconds' => $_POST['idle-seconds']
                , 'remarketing-notification-email' => $_POST['notification-email']
                , 'remarketing-coupon' => $_POST['coupon-path']
            ]);

            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $form_html = $form->generate_form();

        $this->resources->javascript('fileuploader', 'media-manager', 'shopping-cart/remarketing/settings')
            ->css('media-manager');

        return $this->get_template_response('settings')
            ->menu_item('shopping-cart/remarketing/settings')
            ->add_title('Settings')
            ->set(compact('form_html'));
    }

    public function upload_coupon() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $uploader = new qqFileUploader( array( 'gif', 'jpg', 'jpeg', 'png' ), 6144000 );

        $name = $key = 'remarketing-coupon';
        $width = 405;
        $height = 450;

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

        $response->add_response( 'url', $image_url );

        return $response;

    }

}