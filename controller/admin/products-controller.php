<?php
class
ProductsController extends BaseController {
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

        $this->resources
            ->css_url( Config::resource('jquery-ui') )
            ->javascript('products/list');

        return $this->get_template_response( 'index' )
            ->kb( 11 )
            ->select( 'sub-products', 'view' )
            ->set( compact( 'categories', 'product_users' ) );
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
        $account = new Account();

        // Get variables

        if ( $product_id ) {
            // If we're editing a product
            $product->get( $product_id );

            $product_images = $product->get_images();
            $product_attribute_items = $attribute_item->get_by_product( $product_id );

            $tags = $tag->get_value_by_type( 'product', $product_id );
            $date = new DateTime( $product->publish_date );

            $title = _('Edit');

            // Get the industry as it may be needed
            if ( $this->verified() )
                $industry->get( $product->industry_id );

            $accounts = $account->get_by_product( $product->id );
        } else {
            $product_attribute_items = $tags = $product_images = $accounts = array();

            $date = new DateTime();

            $title = _('Add');
        }

        if ( $product->website_id > 0 )
            $account->get( $product->website_id );

        $industries_array = $industry->get_all();
        $brands = $brand->get_all();
        $categories = $category->sort_by_hierarchy();

        // Add on an associative aspect
        $industries = array();

        foreach ( $industries_array as $industry ) {
            $industries[$industry->id] = $industry;
        }

        if ( $this->verified() && $product->id ) {
            // Need to delete it from all websites
            if ( 'deleted' == $_POST['sStatus'] ) {
                // We need to remove it from all user websites
                $account_product = new AccountProduct();
                $account_category = new AccountCategory();

                /**
                 * Get variables
                 * @var Account $account
                 */
                $accounts = $account->get_by_product( $product->id );

                // Delete product from all accounts
                $account_product->delete_by_product( $product->id );

                // Recategorize them
                foreach ( $accounts as $account ) {
                    $account_category->reorganize_categories( $account->id, $category );
                }
            }

            $product->category_id = $_POST['sCategory'];
            $product->brand_id = $_POST['sBrand'];
            $product->industry_id = $_POST['sIndustry'];
            $product->name = $_POST['tName'];
            $product->slug = $_POST['tProductSlug'];
            $product->description = $_POST['taDescription'];
            $product->sku = $_POST['tSKU'];
            $product->weight = $_POST['tWeight'];
            $product->status = $_POST['sProductStatus'];
            $product->publish_date = $_POST['hPublishDate'];
            $product->publish_visibility = $_POST['sStatus'];
            $product->user_id_modified = $this->user->id;

            $product_specs = array();
            $sequence = 0;

            if ( isset( $_POST['product-specs'] ) )
            foreach( $_POST['product-specs'] as $ps ) {
                list ( $spec_name, $spec_value ) = explode( '|', $ps );
                $product_specs[] = array( format::convert_characters( $spec_name ), format::convert_characters( $spec_value ), $sequence );

                $sequence++;
            }

            $product->product_specifications = serialize( $product_specs );

            // Update the product
            $product->save();

            // Delete all the things
            $product->delete_images();
            $tag->delete_by_type( 'product', $product->id );
            $attribute_item->delete_relations( $product->id );

            if ( isset( $_POST['tags'] ) )
                $tag->add_bulk( 'product', $product->id, $_POST['tags'] );

            if ( isset( $_POST['attributes'] ) )
                $attribute_item->add_relations( $product->id, $_POST['attributes'] );

            if ( isset( $_POST['images'] ) ) {
                $product->add_images( $_POST['images'] );

                // What images do we need to remove
                $remove_images = array_diff( $product_images, $_POST['images'] );
            } else {
                $remove_images = $product_images;
            }

            // Need to remove images
            if ( count( $remove_images ) > 0 ) {
                $file = new File();
                $path_base = 'products/' . $product->id . '/';

                foreach ( $remove_images as $ri ) {
                    $file->delete_image( $path_base . $ri, $industry->name );
                    $file->delete_image( $path_base . 'thumbnail/' . $ri, $industry->name );
                    $file->delete_image( $path_base . 'small/' . $ri, $industry->name );
                    $file->delete_image( $path_base . 'large/' . $ri, $industry->name );
                }
            }

            // Now go back to products list with a notification
            $this->notify( _('Your product was successfully created or updated!') );

            // Return to products list
            return new RedirectResponse('/products/');
        }

        $this->resources
            ->javascript( 'fileuploader', 'products/add-edit' )
            ->css('products/add-edit')
            ->css_url( Config::resource('jquery-ui') );

        return $this->get_template_response( 'add-edit' )
            ->kb( 12 )
            ->select( 'sub-products', 'add' )
            ->add_title( $title )
            ->set( compact( 'product_id', 'product', 'account', 'industries', 'brands', 'date', 'categories', 'attribute_items', 'tags', 'product_images', 'product_attribute_items', 'accounts' ) );
    }

    /***** REDIRECTS *****/

    /**
     * Clone
     *
     * @return RedirectResponse
     */
    protected function clone_product() {
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
    protected function create() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $product = new Product();
        $product->website_id = 0;
        $product->user_id_created = $this->user->id;
        $product->create();

        // Change Form
        jQuery('#fAddEditProduct')->attr( 'action', url::add_query_arg( 'pid', $product->id, '' ) );
        jQuery('#hProductId')->val( $product->id );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
    /**
     * Get Attribute Items
     *
     * @return AjaxResponse
     */
    protected function get_attribute_items() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() || !isset( $_POST['cid'] ) );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $attribute_item = new AttributeItem();

        $attribute_items_array = $attribute_item->get_by_category( $_POST['cid'] );
        $attribute_items = array();

        foreach ( $attribute_items_array as $aia ) {
            $attribute_items[$aia->title][] = $aia;
        }

        $html = '';

        $attributes = array_keys( $attribute_items );

        foreach ( $attributes as $attribute ) {
            $html .= '<optgroup label="' . $attribute . '">';

            foreach ( $attribute_items[$attribute] as $attribute_item ) {
                $html .= '<option value="' . $attribute_item->id . '">' . $attribute_item->name . '</option>';
            }

            $html .= '</optgroup>';
        }

        // Change Form
        jQuery('#sAttributes')
            ->html( $html )
            ->disableAttributes();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload Image
     *
     * @return AjaxResponse
     */
    protected function upload_image() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );
        $response->check( isset( $_GET['pid'], $_GET['iid'] ), _('Image failed to upload') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $product = new Product();
        $industry = new Industry();
        $file = new File();
        $uploader = new qqFileUploader( array('gif', 'jpg', 'jpeg', 'png'), 6144000 );

        // Change the name
        $new_image_name =  format::slug( f::strip_extension( $_GET['qqfile'] ) );

        // Get variables
        $product->get( $_GET['pid'] );

        // Make sure it's done right
        $response->check( $product->id, _('Please add the "Product Name" before uploading images.') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $industry->get( $_GET['iid'] );
        $industry_name = str_replace( " ", "", $industry->name );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the different versions we need
        $file->upload_image( $result['file_path'], $new_image_name, 350, 350, $industry_name, 'products/' . $product->id . '/', false, true );
        $file->upload_image( $result['file_path'], $new_image_name, 64, 64, $industry_name, 'products/' . $product->id . '/thumbnail/', false, true );
        $file->upload_image( $result['file_path'], $new_image_name, 200, 200, $industry_name, 'products/' . $product->id . '/small/', false, true );
        $image_name = $file->upload_image( $result['file_path'], $new_image_name, 1000, 1000, $industry_name, 'products/' . $product->id . '/large/' );

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Get image url
        $image_url = "http://$industry_name.retailcatalog.us/products/$product->id/small/$image_name";

        // Clone image template
        jQuery('#image-template')->clone()
            ->removeAttr('id')
            ->find('a:first')
                ->attr( 'href', str_replace( '/small/', '/large/', $image_url ) )
                ->find('img:first')
                    ->attr( 'src', $image_url )
                    ->parents('.image:first')
            ->find('input:first')
                ->val($image_name)
                ->parent()
            ->appendTo('#images-list');

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
        $dt->order_by( 'p.`name`', 'u.`contact_name`', 'u2.`contact_name`', 'b.`name`', 'p.`sku`', 'c.`name`' );

        if ( isset( $_SESSION['products']['visibility'] ) && !empty( $_SESSION['products']['visibility'] ) ) {
            switch ( $_SESSION['products']['visibility'] ) {
                default:
                case 'public':
                    $visibility = " AND p.`publish_visibility` = 'public'";
                break;

                case 'private':
                    $visibility = " AND p.`publish_visibility` = 'private'";
                break;

                case 'deleted':
                    $visibility = " AND p.`publish_visibility` = 'deleted'";
                break;

            }
        }  else {
            $visibility = " AND p.`publish_visibility` <> 'deleted'";
        }

        // Add the visibility check
        $dt->add_where( $visibility );

        if ( isset( $_SESSION['products']['user-option'] ) && isset( $_SESSION['products']['user'] ) ) {
            switch ( $_SESSION['products']['user-option'] ) {
                default:
                case 'created':
                    $product_status = ' AND p.`user_id_created` = ' . (int) $_SESSION['products']['user'];
                break;

                case 'modified':
                    $product_status = ' AND p.`user_id_modified` = ' . (int) $_SESSION['products']['user'];
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
                        $type = 'p.`name`';
                    break;

                    default:
                    case 'sku':
                        $type = 'p.`sku`';
                    break;

                    case 'brands':
                        $type = 'b.`name`';
                    break;
                }
            } else {
                $type = 'p.`sku`';
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

            $dt->add_where(' AND pc.`category_id` IN(' . implode( ',', $category_ids ) . ')');
        }

        // Get accounts
        $products = $product->list_all( $dt->get_variables() );
        $dt->set_row_count( $product->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this product? This cannot be undone.');
        $delete_product_nonce = nonce::create( 'delete' );

        /**
         * @var Product $p
         */
        if ( is_array( $products ) )
        foreach ( $products as $p ) {
            $created_by = ( 0 == $p->website_id ) ? $p->created_by : '<span class="highlight">' . $p->created_by . '</span>';

            $data[] = array(
                $p->name .
                    '<div>' .
                        '<a href="/products/add-edit/?pid=' . $p->id . '" title="' . _('Edit') . '">' . _('Edit') . '</a> | ' .
                        '<a href="' . url::add_query_arg( array( 'pid' => $p->id, '_nonce' => $delete_product_nonce ), '/products/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a> | ' .
                        '<a href="/products/clone-product/?pid=' . $p->id . '" title="' . _('Clone') . '" target="_blank">' . _('Clone') . '</a>' .
                    '</div>'
                , $created_by
                , $p->updated_by
                , $p->brand
                , $p->sku
                , ucwords( $p->category )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }



    /**
     * Delete a product
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
            // We need to remove it from all user websites
            $account = new Account();
            $account_product = new AccountProduct();
            $account_category = new AccountCategory();
            $category = new Category();

            // Get variables
            $accounts = $account->get_by_product( $product->id );

            // Delete product from all accounts
            $account_product->delete_by_product( $product->id );

            // Recategorize them
            foreach ( $accounts as $account ) {
                $account_category->reorganize_categories( $account->id, $category );
            }

            // Delete the product
            $product->publish_visibility = 'deleted';
            $product->save();

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
    protected function autocomplete() {
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