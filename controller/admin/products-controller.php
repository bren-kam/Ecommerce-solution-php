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

        $template_response = $this->get_template_response( 'index' )
            ->select( 'products', 'view' );

        $this->resources->javascript( 'jquery.autocomplete', 'products/list' );
        $this->resources->css_url('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css' );

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

        if ( isset( $_SESSION['products']['product-status'] ) && isset( $_SESSION['products']['user'] ) ) {
            switch ( $_SESSION['products']['product-status'] ) {
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
            $_GET['sSearch'] = $_SESSION['accounts']['search'];

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

        // Add search
        if ( isset( $_SESSION['accounts']['search'] ) ) {
            $_GET['sSearch'] = $_SESSION['accounts']['search'];
            $dt->search( array( 'a.`title`' => false, 'a.`domain`' => false, 'b.`contact_name`' => false, 'c.`contact_name`' => false ) );
        }

        // Add categories
        if ( isset( $_SESSION['products']['cid'] ) ) {
            $category = new Category();
            $categories = $category->get_sub_category_ids( $_SESSION['products']['cid'] );
            $categories[] = $_SESSION['products']['cid'];

            // Make sure they are all integers
            foreach ( $categories as &$cat ) {
                $cat = (int) $cat;
            }

            $dt->add_where(' AND b.`category_id` IN(' . implode( ',', $categories ) . ')');
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

        // Get the right suggestions for the right type
        switch ( $_POST['type'] ) {
            case 'domain':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_POST['term'], 'domain', $this->user, $status );
            break;

            case 'store_name':
                $results = $u->autocomplete( $_POST['term'] , 'store_name' );

                if ( is_array( $results ) )
                foreach ( $results as &$result ) {
                    $result['store_name'] = $result['store_name'];
                }
            break;

            case 'title':
                $account = new Account();

                $status = ( isset( $_SESSION['accounts']['state'] ) ) ? $_SESSION['accounts']['state'] : NULL;

                $results = $account->autocomplete( $_POST['term'], 'title', $this->user, $status );
            break;
        }

        $ajax_response->add_response( 'objects', $results );

        return $ajax_response;
    }
}