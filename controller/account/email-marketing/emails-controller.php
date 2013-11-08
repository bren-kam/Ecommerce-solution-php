<?php
class EmailsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/emails/';
        $this->section = 'email-marketing';
        $this->title = _('Emails') . ' | ' . _('Email Marketing');
    }

    /**
     * List Email Messages
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        return $this->get_template_response( 'index' )
            ->kb( 74 )
            ->add_title( _('Emails') )
            ->select( 'emails', 'view' );
    }

    /**
     * Send Email Message
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function send() {
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        // Initialize variable
        $email_message_id = ( isset( $_GET['emid'] ) ) ? $_GET['emid'] : '';

        // Get email lists
        $email_list = new EmailList();
        $email_lists = $email_list->get_count_by_account( $this->user->account->id );

        // Get email message
        $message = new EmailMessage();

        if ( $email_message_id ) {
            // Get Message
            $message->get( $email_message_id, $this->user->account->id );

            // Get email lists
            $email_lists_array = $email_list->get_by_message( $message->id, $this->user->account->id );

            foreach ( $email_lists_array as $el ) {
                $message->email_lists[$el->id] = $el;
            }

            // Get meta
            $message->get_smart_meta();
        }

        // Get settings
        $settings = $this->user->account->get_settings( 'from_name', 'from_email', 'timezone' );
        $timezone = $settings['timezone'];
        $server_timezone = Config::setting('server-timezone');

        // Make sure they don't have any blank settings
        if ( array_search( '', $settings ) ) {
            $this->notify( _('One or more of your email settings has not been set. Please update them and then try again.'), false );
            return new RedirectResponse('/email-marketing/settings/');
        }

        $this->resources
            ->css( 'email-marketing/emails/send', 'jquery.timepicker' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'fileuploader', 'gsr-media-manager', 'jquery.blockUI', 'jquery.timepicker', 'email-marketing/emails/send' );

        // Get fiels for media manager
        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $email_template = new EmailTemplate();
        $templates = $email_template->get_by_account( $this->user->account->id );

        return $this->get_template_response( 'send' )
            ->kb( 73 )
            ->add_title( _('Send') . ' | ' . _('Emails') )
            ->select( 'emails' )
            ->set( compact( 'email_lists', 'message', 'settings', 'timezone', 'server_timezone', 'templates', 'files' ) );
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

        $email_message = new EmailMessage();

        // Set Order by
        $dt->order_by( '`subject`', '`status`', 'date_sent' );
        $dt->add_where( ' AND `website_id` = ' . (int) $this->user->account->id );
        $dt->search( array( '`subject`' => false ) );

        // Get items
        $messages = $email_message->list_all( $dt->get_variables() );
        $dt->set_row_count( $email_message->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm = _('Are you sure you want to delete this email? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $statuses = array( 'Draft', 'Scheduled', 'Sent' );
        $timezone = $this->user->account->get_settings( 'timezone' );
        $server_timezone = Config::setting('server-timezone');

        /**
         * @var EmailMessage $message
         */
        if ( is_array( $messages ) )
        foreach ( $messages as $message ) {
            $message->date_sent = dt::adjust_timezone( $message->date_sent, $server_timezone, $timezone );
            $date = new DateTime( $message->date_sent );

            if ( $message->status != EmailMessage::STATUS_SENT ) {
                $actions = '<a href="' . url::add_query_arg( 'emid', $message->id, '/email-marketing/emails/send/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ';
                $actions .= '<a href="' . url::add_query_arg( array( 'emid' => $message->id, '_nonce' => $delete_nonce ), '/email-marketing/emails/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
            } else {
                $actions = '<a href="' . url::add_query_arg( 'accid', $message->ac_campaign_id, '/analytics/email/' ) . '" title="' . _('Analytics') . '">' . _('Analytics') . '</a>';
            }

            $data[] = array(
                format::limit_chars( $message->subject, 50, '...' ) . '<br /><div class="actions">' . $actions . '</div>',
                $statuses[$message->status],
                $date->format( 'F jS, Y g:ia' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['emid'] ), _('You cannot delete this email message') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email_message = new EmailMessage();
        $email_message->get( $_GET['emid'], $this->user->account->id );
        $email_message->remove_all( $this->user->account );

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Save
     *
     * @return AjaxResponse
     */
    protected function save() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['hEmailMessageID'] ), _('An error occurred while trying to save this email. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        // Create/Update message
        $email_message = new EmailMessage();

        if ( 0 != $_POST['hEmailMessageID'] )
            $email_message->get( $_POST['hEmailMessageID'], $this->user->account->id );

        $email_message->email_template_id = ( empty( $_POST['hEmailTemplateID'] ) ) ? 0 : $_POST['hEmailTemplateID'];
        $email_message->subject = $_POST['tSubject'];
        $email_message->message = $_POST['taContent'];
        $email_message->type = ( empty( $_POST['hEmailType'] ) ) ? 'none' : $_POST['hEmailType'];

        $date_sent = $_POST['tDate'];

        // Turn it into machine-readable time
        if ( !empty( $_POST['tTime'] ) ) {
        	list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );

        	if ( 'pm' == strtolower( $am_pm ) ) {
        		list( $hour, $minute ) = explode( ':', $time );

        		$date_sent .= ( 12 == $hour ) ? ' ' . $time . ':00' : ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
        	} else {
        		$date_sent .= ' ' . $time . ':00';
        	}
        }

        // Adjust for time zone
        $email_message->date_sent = dt::adjust_timezone( $date_sent, $this->user->account->get_settings( 'timezone' ), Config::setting('server-timezone') );

        if ( $email_message->id ) {
            $email_message->save();
            $email_message->remove_associations();
            $email_message->remove_meta();
        } else {
            $email_message->website_id = $this->user->account->id;
            $email_message->create();
        }

        // Get email lists
        if ( is_array( $_POST['email_lists'] ) )
            $email_message->add_associations( $_POST['email_lists'] );

        // Extra data
        if ( 'product' == $email_message->type ) {
            $message_meta = array();
            $i = 0;

            if ( isset( $_POST['products'] ) )
            foreach ( $_POST['products'] as $product_data ) {
                list( $product_id, $product_price ) = explode( '|', $product_data );
                $message_meta[] = array( 'product', serialize( array( 'product_id' => $product_id, 'price' => $product_price, 'order' => $i ) ) );
                $i++;
            }

            $email_message->add_meta( $message_meta );
        }

        $response->add_response( 'email_message_id', $email_message->id );

        // Get email lists
        $email_list = new EmailList();
        $email_lists = $email_list->get_by_message( $email_message->id, $this->user->account->id );
        $email_list_ids = array();

        foreach ( $email_lists as $el ) {
            $email_list_ids[] = $el->id;
        }
        return $response;
    }

    /**
     * Test
     *
     * @return AjaxResponse
     */
    protected function test() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['emid'], $_POST['email'] ), _('An error occurred while trying to test this message. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        // Test
        $email_message = new EmailMessage();

        // Get message
        $email_message->get( $_POST['emid'], $this->user->account->id );

        // Test message
        try {
            $email_message->test( $_POST['email'], $this->user->account );
        } catch ( ModelException $e ) {
            $response->check( false, $e->getMessage() );
        }

        return $response;
    }

    /**
     * Schedule
     *
     * @return AjaxResponse
     */
    protected function schedule() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_POST['emid'] ), _('An error occurred while trying to schedule this message. Please refresh the page and try again.') );

        if ( $response->has_error() )
            return $response;

        // Schedule
        $email_list = new EmailList();
        $email_message = new EmailMessage();

        // Get message
        $email_message->get( $_POST['emid'], $this->user->account->id );

        // Get email lists
        $email_lists = $email_list->get_by_message( $email_message->id, $this->user->account->id );

        // Test message
        try {
            $email_message->schedule( $this->user->account, $email_lists );
        } catch ( ModelException $e ) {
            $response->check( false, $e->getMessage() );
        }

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
            $dialog = '<a href="' . url::add_query_arg( 'pid', $product->id, '/email-marketing/emails/get-product/' ) . '#dProductDialog' . $product->id . '" title="' . _('View') . '" rel="dialog">';
           	$actions = '<a href="' . url::add_query_arg( array( '_nonce' => $add_product_nonce, 'pid' => $product->id ), '/email-marketing/emails/add-product/' ) . '" title="' . _('Add Product') . '" ajax="1">' . _('Add Product') . '</a>';

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

        $response = new CustomResponse( $this->resources, 'email-marketing/emails/get-product' );
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

        // Make sure we have everything right
        $response->check( isset( $_GET['pid'] ), _('Unable to add product. Please try again.') );

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
        $product_box .= '<h4>' . format::limit_chars( $product->name, 37 ) . '</h4>';
        $product_box .= '<p align="center"><img src="http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/small/' . current( $product->images ) . '" alt="' . $product->name . '" height="110" style="margin:10px" /></p>';
        $product_box .= '<p>' . _('Brand') . ': ' . $product->brand . '<br /><label for="tProductPrice' . $product->id . '">' . _('Price') . ':</label> <input type="text" name="tProductPrice' . $product->id . '" class="tb product-price" id="tProductPrice' . $product->id . '" value="' . $account_product->price . '" maxlength="10" /></p>';
        $product_box .= '<p class="product-actions" id="pProductAction' . $product->id . '"><a href="#" class="remove-product" title="' . _('Remove Product') . '">' . _('Remove') . '</a></p>';
        $product_box .= '<input type="hidden" name="products[]" class="hidden-product" id="hProduct' . $product->id . '" value="' . $product->id . '|' . $account_product->price . '" />';
        $product_box .= '</div>';

        jQuery('#dSelectedProducts')->append( $product_box );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}