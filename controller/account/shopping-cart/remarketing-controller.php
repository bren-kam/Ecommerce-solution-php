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
        return $this->get_template_response( 'index' )
            ->kb( 120 )
            ->menu_item( 'shopping-cart/remarketing/list' );
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
        $dt->add_where( ' AND wc.`website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( 'wc.`website_cart_id`' => false ) );

        // Get items
        $website_carts = $website_cart->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_cart->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $abandoned_limit_minutes = $this->user->account->get_settings('remarketing-idle-seconds');
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

        $settings = $this->user->account->get_settings('remarketing-title', 'remarketing-intro-text', 'remarketing-idle-seconds');

        $form->add_field('text', 'Show popup after seconds', 'idle-seconds', $settings['remarketing-idle-seconds'] ? $settings['remarketing-idle-seconds'] : 60)
            ->add_validation('req', 'Required');

        $form->add_field('text', 'Title', 'title', $settings['remarketing-title']);
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

        if ( $form->posted() ) {
            $this->user->account->set_settings([
                'remarketing-title' => $_POST['title']
                , 'remarketing-intro-text' => $_POST['intro-text']
                , 'remarketing-idle-seconds' => $_POST['idle-seconds']
            ]);

            return new RedirectResponse('/shopping-cart/remarketing/settings/');
        }

        $form_html = $form->generate_form();

        $this->resources->javascript('media-manager')->css('media-manager');

        return $this->get_template_response('settings')
            ->menu_item('shopping-cart/remarketing/settings')
            ->add_title('Settings')
            ->set(compact('form_html'));
    }

}