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
        $since->sub(new DateInterval('P1Y'));
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
            'remarketing-enabled'
            , 'remarketing-popup-image'
            , 'remarketing-title'
            , 'remarketing-intro-text'
            , 'remarketing-submit-color'
            , 'remarketing-idle-seconds'
            , 'remarketing-notification-email'
            , 'remarketing-coupon'
            , 'remarketing-autoresponder'            
            , 'remarketing-email1-enabled'
            , 'remarketing-email1-delay'
            , 'remarketing-email1-header'
            , 'remarketing-email1-title'
            , 'remarketing-email1-body'
            , 'remarketing-email2-enabled'
            , 'remarketing-email2-delay'
            , 'remarketing-email2-header'
            , 'remarketing-email2-title'
            , 'remarketing-email2-body'
            , 'remarketing-email3-enabled'
            , 'remarketing-email3-delay'
            , 'remarketing-email3-header'
            , 'remarketing-email3-title'
            , 'remarketing-email3-body'
        );

        // Some defaults
        $set_defaults = [];
        if ( !$settings['remarketing-idle-seconds'] )       $set_defaults['remarketing-idle-seconds'] = 60;
        if ( !$settings['remarketing-email1-delay'] )       $set_defaults['remarketing-email1-delay'] = 3600;
        if ( !$settings['remarketing-email2-delay'] )       $set_defaults['remarketing-email2-delay'] = 3600*24;
        if ( !$settings['remarketing-email3-delay'] )       $set_defaults['remarketing-email3-delay'] = 3600*72;
        if( $set_defaults ){
            $this->user->account->set_settings($set_defaults);
        }

        // Process POST/Submit
        if ( $this->verified() ) {
            $this->user->account->set_settings([
                'remarketing-popup-image' => $_POST['popup-image']
                , 'remarketing-title' => $_POST['title']
                , 'remarketing-intro-text' => $_POST['intro-text']
                , 'remarketing-submit-color' => $_POST['submit-color']
                , 'remarketing-idle-seconds' => $_POST['idle-seconds']
                , 'remarketing-notification-email' => $_POST['notification-email']
                , 'remarketing-coupon' => $_POST['coupon-path']
                , 'remarketing-autoresponder' => $_POST['autoresponder']                
                , 'remarketing-email1-enabled' => isset($_POST['email1-enabled'])
                , 'remarketing-email1-delay' => $_POST['email1-delay']
                , 'remarketing-email1-header' => $_POST['email1-header']
                , 'remarketing-email1-title' => $_POST['email1-title']
                , 'remarketing-email1-body' => $_POST['email1-body']
                , 'remarketing-email2-enabled' => isset($_POST['email2-enabled'])
                , 'remarketing-email2-delay' => $_POST['email2-delay']
                , 'remarketing-email2-header' => $_POST['email2-header']
                , 'remarketing-email2-title' => $_POST['email2-title']
                , 'remarketing-email2-body' => $_POST['email2-body']
                , 'remarketing-email3-enabled' => isset($_POST['email3-enabled'])
                , 'remarketing-email3-delay' => $_POST['email3-delay']
                , 'remarketing-email3-header' => $_POST['email3-header']
                , 'remarketing-email3-title' => $_POST['email3-title']
                , 'remarketing-email3-body' => $_POST['email3-body']
            ]);

            $this->notify('Remarketing settings updated');
            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $this->resources->javascript('autosize.min', 'fileuploader', 'media-manager', 'colpick', 'shopping-cart/remarketing/settings')
            ->css('media-manager', 'colpick', 'shopping-cart/remarketing/settings');

        return $this->get_template_response('settings')
            ->menu_item('shopping-cart/remarketing/settings')
            ->add_title('Settings')
            ->set(compact('form_html', 'settings'));
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

    public function popup(){

        $form = new BootstrapForm('remarketing-settings');

        $settings = $this->user->account->get_settings(
            'remarketing-enabled'
            , 'remarketing-popup-image'
            , 'remarketing-title'
            , 'remarketing-intro-text'
            , 'remarketing-submit-color'
            , 'remarketing-idle-seconds'
            , 'remarketing-notification-email'
            , 'remarketing-coupon'
            , 'remarketing-autoresponder'            
        );

        // Some defaults
        $set_defaults = [];
        if( $set_defaults ){
            $this->user->account->set_settings($set_defaults);
        }

        // Process POST/Submit
        if ( $this->verified() ) {
            $this->user->account->set_settings([
                'remarketing-popup-image' => $_POST['popup-image']
                , 'remarketing-title' => $_POST['title']
                , 'remarketing-intro-text' => $_POST['intro-text']
                , 'remarketing-submit-color' => $_POST['submit-color']
                , 'remarketing-idle-seconds' => $_POST['idle-seconds']
                , 'remarketing-notification-email' => $_POST['notification-email']
                , 'remarketing-coupon' => $_POST['coupon-path']
                , 'remarketing-autoresponder' => $_POST['autoresponder']                
            ]);

            $this->notify('Remarketing settings updated');
            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $this->resources->javascript('autosize.min', 'fileuploader', 'media-manager', 'colpick', 'shopping-cart/remarketing/settings')
            ->css('media-manager', 'colpick', 'shopping-cart/remarketing/settings');

        return $this->get_template_response('popup')
            ->menu_item('shopping-cart/remarketing/popup')
            ->add_title('Email popup box & coupon')
            ->set(compact('form_html', 'settings'));
    }

    public function emails(){
       $form = new BootstrapForm('remarketing-settings');

        $settings = $this->user->account->get_settings(
            'remarketing-enabled'
            , 'remarketing-email1-enabled'
            , 'remarketing-email1-delay'
            , 'remarketing-email1-header'
            , 'remarketing-email1-title'
            , 'remarketing-email1-body'
            , 'remarketing-email2-enabled'
            , 'remarketing-email2-delay'
            , 'remarketing-email2-header'
            , 'remarketing-email2-title'
            , 'remarketing-email2-body'
            , 'remarketing-email3-enabled'
            , 'remarketing-email3-delay'
            , 'remarketing-email3-header'
            , 'remarketing-email3-title'
            , 'remarketing-email3-body'
        );

        // Some defaults
        $set_defaults = [];
        if ( !$settings['remarketing-idle-seconds'] )       $set_defaults['remarketing-idle-seconds'] = 60;
        if ( !$settings['remarketing-email1-delay'] )       $set_defaults['remarketing-email1-delay'] = 3600;
        if ( !$settings['remarketing-email2-delay'] )       $set_defaults['remarketing-email2-delay'] = 3600*24;
        if ( !$settings['remarketing-email3-delay'] )       $set_defaults['remarketing-email3-delay'] = 3600*72;
        if( $set_defaults ){
            $this->user->account->set_settings($set_defaults);
        }

        // Process POST/Submit
        if ( $this->verified() ) {
            $this->user->account->set_settings([
                  'remarketing-email1-enabled' => isset($_POST['email1-enabled'])
                , 'remarketing-email1-delay' => $_POST['email1-delay']
                , 'remarketing-email1-header' => $_POST['email1-header']
                , 'remarketing-email1-title' => $_POST['email1-title']
                , 'remarketing-email1-body' => $_POST['email1-body']
                , 'remarketing-email2-enabled' => isset($_POST['email2-enabled'])
                , 'remarketing-email2-delay' => $_POST['email2-delay']
                , 'remarketing-email2-header' => $_POST['email2-header']
                , 'remarketing-email2-title' => $_POST['email2-title']
                , 'remarketing-email2-body' => $_POST['email2-body']
                , 'remarketing-email3-enabled' => isset($_POST['email3-enabled'])
                , 'remarketing-email3-delay' => $_POST['email3-delay']
                , 'remarketing-email3-header' => $_POST['email3-header']
                , 'remarketing-email3-title' => $_POST['email3-title']
                , 'remarketing-email3-body' => $_POST['email3-body']
            ]);

            $this->notify('Remarketing settings updated');
            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $this->resources->javascript('autosize.min', 'fileuploader', 'media-manager', 'colpick', 'shopping-cart/remarketing/settings')
            ->css('media-manager', 'colpick', 'shopping-cart/remarketing/settings');

        return $this->get_template_response('emails')
            ->menu_item('shopping-cart/remarketing/emails')
            ->add_title('Abandoned Cart Emails')
            ->set(compact('form_html', 'settings'));

    }
    
    /**
     * Enable
     * @return RedirectResponse
     */
    public function enable() {
        if ( $this->verified() ) {
            $this->user->account->set_settings([
                'remarketing-enabled' => 1
            ]);
        }
        return new RedirectResponse('/shopping-cart/remarketing/settings/');
    }

    /**
     * Disable
     * @return RedirectResponse
     */
    public function disable() {
        if ( $this->verified() ) {
            $this->user->account->set_settings([
                'remarketing-enabled' => 0
            ]);
        }
        return new RedirectResponse('/shopping-cart/remarketing/settings/');
    }

}