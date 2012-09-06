<?php
class ProductsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/';
        $this->section = 'products';
    }

    /**
     * List Products
     *
     * @return TemplateResponse
     */
    protected function index() {
        unset( $_SESSION['products'] );

        // Get categories
        $category = new Category;
        $categories = $category->sort_by_hierarchy();

        // Get product users
        $product_users = $this->user->get_product_users();

        $template_response = $this->get_template_response( 'index' )
            ->select( 'products', 'view' )
            ->set( compact( 'categories', 'product_users' ) );

        $this->resources->javascript('products/list');
        $this->resources->css_url( Config::resource('jquery-ui') );

        return $template_response;
    }

    /**
     * Add/Edit a Product
     *
     * @return TemplateResponse
     */
    protected function add_edit() {
        // Determine if we're adding or editing the product
        $product_id = ( isset( $_GET['pid'] ) ) ? (int) $_GET['pid'] : false;

        $product = new Product;
        $industry = new Industry();
        $brand = new Brand();
        $category = new Category();
        $attribute_item = new AttributeItem();
        $tag = new Tag();

        // Get variables

        if ( $product_id ) {
            // If we're editing a product
            $product->get( $product_id );

            $product_images = $product->get_images();
            $product_attribute_items = $attribute_item->get_by_product( $product_id );

            $tags = $tag->get_value_by_type( 'product', $product_id );
            $date = new DateTime( $product->publish_date );

            $title = _('Edit');
        } else {
            $product_attribute_items = $tags = array();

            $date = new DateTime();

            $title = _('Add');
        }

        $industries_array = $industry->get_all();
        $brands = $brand->get_all();
        $categories = $category->sort_by_hierarchy();
        $attribute_items_array = $attribute_item->get_all();


        // Add on an associative aspect
        $attribute_items = $industries = array();

        foreach ( $attribute_items_array as $aia ) {
            $attribute_items[$aia->title][] = $aia;
        }

        foreach ( $industries_array as $industry ) {
            $industries[$industry->id] = $industry;
        }

        $template_response = $this->get_template_response( 'add-edit' )
            ->select( 'products', 'add' )
            ->add_title( $title )
            ->set( compact( 'product_id', 'product', 'industries', 'brands', 'date', 'categories', 'attribute_items', 'tags', 'product_images', 'product_attribute_items' ) );

        $this->resources
            ->javascript( 'fileuploader', 'products/add-edit' )
            ->css('products/add-edit')
            ->css_url( Config::resource('jquery-ui') );

        return $template_response;
    }

    /***** REDIRECTS *****/

    /**
     * Clone
     *
     * @return RedirectResponse
     */
    public function clone_product() {
        $product_id = (int) $_GET['pid'];

        if ( empty( $product_id ) )
            return new RedirectResponse('/products/');

        $product = new Product;
        $product->clone_product( $product_id, $this->user->id );

        // Redirect to the new cloned product
        return new RedirectResponse( url::add_query_arg( 'pid', $product->id, '/products/add-edit/' ) );
    }

    /***** AJAX *****/

    /**
     * Create a product
     *
     * @return AjaxResponse
     */
    public function create() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get the user
        $product = new Product();
        $product->create( 0, $this->user->id );

        // Change Form
        jQuery('#fAddEditProduct')->attr( 'action', url::add_query_arg( 'pid', $product->id, '' ) );
        jQuery('#hProductId')->val( $product->id );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get Models
        $product = new Product;

        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'a.`name`', 'e.`contact_name`', 'f.`contact_name`', 'd.`name`', 'a.`sku`', 'a.`status`' );

        if ( isset( $_SESSION['products']['visibility'] ) && !empty( $_SESSION['products']['visibility'] ) ) {
            switch ( $_SESSION['products']['visibility'] ) {
                default:
                case 'public':
                    $visibility = " AND `publish_visibility` = 'public'";
                break;

                case 'private':
                    $visibility = " AND `publish_visibility` = 'private'";
                break;

                case 'deleted':
                    $visibility = " AND `publish_visibility` = 'deleted'";
                break;

            }
        }  else {
            $visibility = " AND `publish_visibility` <> 'deleted'";
        }

        // Add the visibility check
        $dt->add_where( $visibility );

        if ( isset( $_SESSION['products']['user-option'] ) && isset( $_SESSION['products']['user'] ) ) {
            switch ( $_SESSION['products']['user-option'] ) {
                default:
                case 'created':
                    $product_status = ' AND `user_id_created` = ' . (int) $_SESSION['products']['user'];
                break;

                case 'modified':
                    $product_status = ' AND `user_id_modified` = ' . (int) $_SESSION['products']['user'];
                break;
            }

            // Add product status check
            $dt->add_where( $product_status );
        }

        // Add search
        if ( isset( $_SESSION['products']['search'] ) ) {
            $_GET['sSearch'] = $_SESSION['products']['search'];

            if ( isset( $_SESSION['products']['type'] ) ) {
                switch ( $_SESSION['products']['type'] ) {
                    case 'products':
                        $type = 'a.`name`';
                    break;

                    default:
                    case 'sku':
                        $type = 'a.`sku`';
                    break;

                    case 'brands':
                        $type = 'd.`name`';
                    break;
                }
            } else {
                $type = 'a.`sku`';
            }

            $dt->search( array( $type => false ) );
        }

        // Add categories
        if ( isset( $_SESSION['products']['cid'] ) ) {
            $category = new Category();
            $categories = $category->get_all_children( $_SESSION['products']['cid'] );
            $category_ids[] = (int) $_SESSION['products']['cid'];

            // Make sure they are all integers
            foreach ( $categories as $category ) {
                $category_ids[] = (int) $category->id;
            }

            $dt->add_where(' AND b.`category_id` IN(' . implode( ',', $category_ids ) . ')');
        }

        // Get accounts
        $products = $product->list_all( $dt->get_variables() );
        $dt->set_row_count( $product->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this product? This cannot be undone.');
        $delete_product_nonce = nonce::create( 'delete' );

        if ( is_array( $products ) )
        foreach ( $products as $p ) {
            $data[] = array(
                $p->name .
                    '<div>' .
                        '<a href="/products/add-edit/?pid=' . $p->id . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                        '<a href="' . url::add_query_arg( array( 'pid' => $p->id, '_nonce' => $delete_product_nonce ), '/products/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a> | ' .
                        '<a href="/products/clone-product/?pid=' . $p->id . '" title="' . _('Clone') . '" target="_blank">' . _('Clone') . '</a>' .
                    '</div>'
                , $p->created_by
                , $p->updated_by
                , $p->brand
                , $p->sku
                , ucwords( $p->status )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }



    /**
     * Delete a user
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['pid'] ) )
            return $response;

        // Get the user
        $product = new Product();
        $product->get( $_GET['pid'] );

        // Deactivate product
        if ( $product->id && 'deleted' != $product->publish_visibility ) {
            $product->publish_visibility = 'deleted';
            $product->update();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

    /**
     * AutoComplete
     *
     * @return AjaxResponse
     */
    public function autocomplete() {
        $ajax_response = new AjaxResponse( $this->verified() );

        // Get Product setup
        $product = new Product();

        // Setup ararys
        $results = array();
        $where = '';

        /* Filtering  */

        // Visibility
        if ( isset( $_SESSION['products']['visibility'] ) ) {
            switch ( $_SESSION['products']['visibility'] ) {
                case 'public':
                    $where .= " AND p.`publish_visibility` = 'public'";
                break;

                case 'private':
                    $where .= " AND p.`publish_visibility` = 'private'";
                break;

                case 'deleted':
                    $where .= " AND p.`publish_visibility` = 'deleted'";
                break;
            }
        } else {
            $where .= " AND p.`publish_visibility` <> 'deleted'";
        }

        // Categories
        if ( isset( $_SESSION['products']['cid'] ) ) {
            $category = new Category();
            $categories = $category->get_all_children( $_SESSION['products']['cid'] );
            $category_ids[] = (int) $_SESSION['products']['cid'];

            // Make sure they are all integers
            foreach ( $categories as $category ) {
                $category_ids[] = (int) $category->id;
            }

            $where .= ' AND c.`category_id` IN(' . implode( ',', $category_ids ) . ')';
        }

        if ( isset( $_SESSION['products']['user-option'] ) && isset( $_SESSION['products']['user'] ) ) {
            switch ( $_SESSION['products']['user-option'] ) {
                case 'created':
                    $where .= ' AND p.`user_id_created` = ' . (int) $_SESSION['products']['user'];
                break;

                case 'modified':
                    $where .= ' AND p.`user_id_modified` = ' . (int) $_SESSION['products']['user'];
                break;
            }
        }


        // Get the right suggestions for the right type
        switch ( $_POST['type'] ) {
            case 'products':
                $results = $product->autocomplete( $_POST['term'] , 'p.`name`', 'products', $where );
            break;

            case 'sku':
                $results = $product->autocomplete( $_POST['term'], 'p.`sku`', 'sku', $where );
            break;

            case 'brands':
                $results = $product->autocomplete( $_POST['term'], 'b.`name`', 'brands', $where );
            break;
        }

        $ajax_response->add_response( 'objects', $results );

        return $ajax_response;
    }
}