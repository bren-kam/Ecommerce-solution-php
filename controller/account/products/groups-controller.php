<?php
class GroupsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'products/groups/';
        $this->section = 'Groups';
    }

    /**
     * List Product Groups
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'product-groups', 'view' );
    }

    /**
     * Add Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Determine if we're adding or editing the user
        $website_product_group_id = ( isset( $_GET['wpgid'] ) ) ? (int) $_GET['wpgid'] : false;

        $group = new WebsiteProductGroup();
        $products = array();

        if ( $website_product_group_id ) {
            $group->get( $website_product_group_id, $this->user->account->id );

            $product = new Product();
            $products = $product->get_by_ids( $group->get_product_relation_ids() );
        }

        $v = new Validator( 'fAddEditGroup' );
        $v->add_validation( 'tName', 'req', _('The "Name" field is required') );
        $v->add_validation( 'tName', '!val=' . _('Product Group Name...'), _('The "Name" field is required') );

        $js_validation = $v->js_validation();
        $errs = '';

        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) ) {
                $group->website_id = $this->user->account->id;
                $group->name = $_POST['tName'];

                if ( $group->id ) {
                    $group->save();
                } else {
                    $group->create();
                }

                $group->remove_relations();

                if ( isset( $_POST['products'] ) )
                    $group->add_relations( $_POST['products'] );

                $this->notify( _('Your product group has been added/updated successfully!' ) );
                return new RedirectResponse('/products/groups/');
            }
        }

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->css( 'products/groups/add-edit')
            ->javascript( 'jquery.datatables', 'products/groups/add-edit' );

        $title = ( $group->id ) ? _('Edit') : _('Add');

        return $this->get_template_response( 'add-edit' )
            ->select( 'product-groups', 'add' )
            ->add_title( $title . ' ' . _('Product Group') )
            ->set( compact( 'errs', 'js_validation', 'group', 'products' ) );
    }


    /***** AJAX *****/

    /**
     * List
     *
     * @return DataTableResponse
     */
    protected function list_groups() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set variables
        $dt->order_by( '`name`' );
        $dt->add_where( " AND `website_id` = " . $this->user->account->id );
        $dt->search( array( '`name`' => true ) );

        // Get Groups
        $website_product_group = new WebsiteProductGroup();
        $groups = $website_product_group->list_all( $dt->get_variables() );
        $dt->set_row_count( $website_product_group->count_all( $dt->get_count_variables() ) );

        // Setup variables
        $confirm = _('Are you sure you want to delete this product group? This cannot be undone.');
        $delete_nonce = nonce::create( 'delete' );
        $data = array();

        // Create output
        if ( is_array( $groups ) )
        foreach ( $groups as $group ) {
            $actions = '<a href="' . url::add_query_arg( 'wpgid', $group->id, '/products/groups/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>';
           	$actions .= ' | <a href="' . url::add_query_arg( array( 'wpgid' => $group->id, '_nonce' => $delete_nonce ), '/products/groups/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
           	$actions .= ' | <a href="' . url::add_query_arg( 'wpgid', $group->id, '/products/groups/show-products/#dShowGroups' . $group->id ) . '" title="' . _("Product Group's Products") . '" rel="dialog">' . _('Show Products') . '</a>';

            $data[] = array(
                $group->name . '<div class="actions">' . $actions . '</div>'
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
    public function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['wpgid'] ), _('Failed to delete Product Group') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Remove Product Group
        $website_product_group = new WebsiteProductGroup();
        $website_product_group->get( $_GET['wpgid'], $this->user->account->id );

        // Then delete
        $website_product_group->remove_relations();
        $website_product_group->remove();

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        // Add jquery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Show Products
     *
     * @return CustomResponse
     */
    protected function show_products() {
        // Instantiate Object
        $website_product_group = new WebsiteProductGroup();
        $product = new Product();

        // Get Product Groups
        $website_product_group->get( $_GET['wpgid'], $this->user->account->id );
        $products = $product->get_by_ids( $website_product_group->get_product_relation_ids() );

        $response = new CustomResponse( $this->resources, 'products/groups/show-products' );
        $response->set( compact( 'products' ) );

        return $response;
    }

    /**
     * List
     *
     * @return DataTableResponse
     */
    protected function list_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_product = new AccountProduct();

        // Set variables
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

        // Get Products
        $products = $account_product->list_products( $dt->get_variables() );
        $dt->set_row_count( $account_product->count_products( $dt->get_count_variables() ) );

        // Setup variables
        $get_product_nonce = nonce::create( 'get_product' );
        $add_product_nonce = nonce::create( 'add_product' );
        $data = array();

        // Create output
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
            $dialog = '<a href="' . url::add_query_arg( array( '_nonce' => $get_product_nonce, 'pid' => $product->id ), '/products/groups/get-product/#dProductDialog' . $product->id ) . '" title="' . _('View') . '" rel="dialog">';
           	$actions = '<a href="' . url::add_query_arg( array( '_nonce' => $add_product_nonce, 'pid' => $product->id ), '/products/groups/add-product/' ) . '" title="' . _('Add') . '" ajax="1">' . _('Add Product') . '</a>';

            $data[] = array(
                $dialog . format::limit_chars( $product->name,  50, '...' ) . '</a><br /><div class="actions">' . $actions . '</div>',
                $product->brand,
                $product->sku,
                $product->status
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Add Product
     *
     * @return AjaxResponse
     */
    protected function add_product() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['pid'] ), _('Failed to add product to group') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $account_product = new AccountProduct();
        $product = new Product();

        $account_product->get( $_GET['pid'], $this->user->account->id );
        $product->get( $account_product->product_id );
        $image = current( $product->get_images() );

        // Form the response HTML
        $product_box = '<div id="dProduct_' . $product->id . '" class="product">';
        $product_box .= '<h4>' . format::limit_chars( $product->name, 37 ) . '</h4>';
        $product_box .= '<p align="center"><img src="http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/small/' . $image . '" alt="' . $product->name . '" height="110" style="margin:10px" /></p>';
        $product_box .= '<p>' . _('Brand') . ': ' . $product->brand . '</p>';
        $product_box .= '<p class="product-actions" id="pProductAction' . $product->id . '"><a href="#" class="remove-product" title="' . _('Remove') . '">' . _('Remove') . '</a></p>';
        $product_box .= '<input type="hidden" name="products[]" class="hidden-product" id="hProduct' . $product->id . '" value="' . $product->id . '" />';
        $product_box .= '</div>';

        jQuery('#dSelectedProducts')->append( $product_box );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Get Product
     *
     * @return CustomResponse
     */
    protected function get_product() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['pid'] ), _('Failed to add product to group') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $account_product = new AccountProduct();
        $product = new Product();

        $account_product->get( $_GET['pid'], $this->user->account->id );
        $product->get( $account_product->product_id );
        $image = current( $product->get_images() );

        $response = new CustomResponse( $this->resources, 'products/groups/get-product' );
        $response->set( compact( 'product', 'image' ) );

        return $response;
    }
}

