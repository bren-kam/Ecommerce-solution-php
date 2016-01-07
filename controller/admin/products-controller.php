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
            ->javascript('products/index')
            ->javascript_url( Config::resource('typeahead-js') )
            ->css('products/index');

        return $this->get_template_response( 'index' )
            ->kb( 11 )
            ->select( 'products', 'products/index' )
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

        $product = new Product();
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

            $product->get_specifications();
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

        $validation = new BootstrapValidator('fAddEditProduct');
        $validation->add_validation('sCategory', 'req', 'Category is required');
        $validation->add_validation('sBrand', 'req', 'Brand is required');
        $validation->add_validation('sIndustry', 'req', 'Industry is required');
        $validation->add_validation('tName', 'req', 'Name is required');
        $validation->add_validation('tSKU', 'req', 'SKU is required');
        $validation->add_validation('tPrice', 'float', 'Wholesale Price must be a valid price');
        $validation->add_validation('tPriceMin', 'float', 'MAP Price must be a valid price');
        $validation->add_validation('hPublishDate', 'req', 'Publish Date is required');

        if ( $this->verified() && $product->id ) {
            $errors = $validation->validate();
            if ( empty( $errors ) ) {
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

                        // Reassign different product to categories linked for image
                        $account_category->reassign_image( $account->id, $product->id );
                    }


                }

                $product->category_id = $_POST['sCategory'];
                $product->brand_id = $_POST['sBrand'];
                $product->industry_id = $_POST['sIndustry'];
                $product->industry = $industry->name;
                $product->name = $_POST['tName'];
                $product->slug = $_POST['tProductSlug'];
                $product->description = $_POST['taDescription'];
                $product->sku = $_POST['tSKU'];
                $product->weight = $_POST['tWeight'];
                $product->price = $_POST['tPrice'];
                $product->price_min = $_POST['tPriceMin'];
                $product->status = $_POST['sProductStatus'];
                $product->publish_date = $_POST['hPublishDate'];
                $product->publish_visibility = $_POST['sStatus'];
                $product->user_id_modified = $this->user->id;

                // Update the product
                $product->save();

                // Delete all the things
                $product->delete_images();
                $product->delete_specifications();
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

                // Add Specifications
                if ( isset( $_POST['product-specs'] ) ) {
                    $product_specs = array();

                    foreach( $_POST['product-specs'] as $ps ) {
                        list ( $spec_name, $spec_value ) = explode( '|', $ps );
                        $product_specs[] = array( format::convert_characters( $spec_name ), format::convert_characters( $spec_value ) );
                    }

                    $product->add_specifications( $product_specs );
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
            } else {
                $this->notify( $errors, false );
            }
        }

        $this->resources
            ->javascript( 'fileuploader', 'products/add-edit' )
            ->javascript_url( Config::resource( 'bootstrap-datepicker-js' ), Config::resource( 'jqueryui-js' ) )
            ->css('products/add-edit')
            ->css_url( Config::resource( 'bootstrap-datepicker-css' ) );

        return $this->get_template_response( 'add-edit' )
            ->kb( 12 )
            ->select( 'products', 'products/add-edit' )
            ->add_title( $title )
            ->set( compact( 'product_id', 'product', 'account', 'industries', 'brands', 'date', 'categories', 'attribute_items', 'tags', 'product_images', 'product_attribute_items', 'accounts', 'validation' ) );
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

        $response->add_response( 'product_id', $product->id );

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

        $response->add_response( 'attributes', $attribute_items );

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

        $response->add_response( 'image_url', $image_url );
        $response->add_response( 'image_name', $image_name );

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
                case Product::PUBLISH_VISIBILITY_PUBLIC:
                    $visibility = " AND p.`publish_visibility` = 'public'";
                break;

                case Product::PUBLISH_VISIBILITY_PRIVATE:
                    $visibility = " AND p.`publish_visibility` = 'private'";
                break;

                case Product::PUBLISH_VISIBILITY_DELETED:
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

            $dt->add_where(' AND p.`category_id` IN(' . implode( ',', $category_ids ) . ')');
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
            $created_by = ( 0 == $p->website_id ) ? $p->created_by : '<span class="label label-primary">' . $p->created_by . '</span>';

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

            // Reassign different product to categories linked for image
            foreach ( $accounts as $account ) {
                $account_category->reassign_image($account->id, $product->id);
            }

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
                case Product::PUBLISH_VISIBILITY_PUBLIC:
                    $where .= " AND p.`publish_visibility` = 'public'";
                break;

                case Product::PUBLISH_VISIBILITY_PRIVATE:
                    $where .= " AND p.`publish_visibility` = 'private'";
                break;

                case Product::PUBLISH_VISIBILITY_DELETED:
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
        switch ( $_GET['type'] ) {
            case 'products':
                $results = $product->autocomplete( $_GET['term'] , 'p.`name`', 'products', $where );
            break;

            case 'sku':
                $results = $product->autocomplete( $_GET['term'], 'p.`sku`', 'sku', $where );
            break;

            case 'brands':
                $results = $product->autocomplete( $_GET['term'], 'b.`name`', 'brands', $where );
            break;
        }

        $ajax_response->add_response( 'objects', $results );

        return $ajax_response;
    }


    /**
     * Import
     *
     * @return TemplateResponse
     */
    protected function import() {
        $this->resources
            ->css( 'products/import' )
            ->javascript( 'fileuploader', 'products/import' );

        $brand = new Brand();
        $brands = $brand->get_all();

        return $this->get_template_response( 'import' )
            ->kb( 0 )
            ->select( 'products', 'products/import' )
            ->add_title( _('Import') )
            ->set( compact( 'brands' ) );
    }

    /**
     * Import Products
     *
     * @return AjaxResponse
     */
    protected function prepare_import() {
        set_time_limit( 30 * 60 );
        ini_set( 'memory_limit', '512M' );

        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error, return
        if ( $response->has_error() )
            return $response;

        // All imported products are of the following brand
        $brand_id = (int) $_GET['brand_id'];

        // Get file uploader
        library('file-uploader');

        // Upload file
        $uploader = new qqFileUploader( array( 'csv', 'xls' ), 26214400 );
        $result = $uploader->handleUpload( 'gsrs_' );

        // Setup variables
        $file_extension = strtolower( f::extension( $_GET['qqfile'] ) );

        // get data regarding file extension
        switch ( $file_extension ) {
            case 'xls':
                // Load excel reader
                library('Excel_Reader/Excel_Reader');
                $er = new Excel_Reader();
                // Set the basics and then read in the rows
                $er->setOutputEncoding('ASCII');
                $er->read( $result['file_path'] );

                $rows = $er->sheets[0]['cells'];
                
                break;

            case 'csv':
                // Make sure it's opened properly
                $response->check( $handler = fopen( $result['file_path'], 'r' ), _('An error occurred while trying to read your file.') );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;

                // Loop through the rows
                while ( $row = fgetcsv( $handler ) ) {
                    $rows[] = $row;
                }

                fclose( $handler );

                break;

            default:
                // Display an error
                $response->check( false, _('Only CSV and Excel file types are accepted. File type: ') . $file_extension );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;
                break;
        }

        $response->check( is_array( $rows ), _('There were no emails to import') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        foreach ($rows as &$r)
            foreach ($r as &$c)
                $c = trim($c);

        $headers = array_shift( $rows );

        // Industries
        $industry = new Industry();
        $industries = $industry->get_all();

        // Categories
        $category = new Category();
        $categories = $category->get_all();
        $categories_by_name = array();
        foreach ( $categories as $category ) {
            if ( $category->has_children() )
                continue;

            $category_string = $category->name;
            $parents = $category->get_all_parents( $category->id );

            foreach ( $parents as $parent_category ) {
                $category_string = $parent_category->name . ' > ' . $category_string;
            }

            $categories_by_name[$category_string] = $category->id;
        }
        ksort( $categories_by_name );

        // Our products to import
        $products = array();
        // Products that won't be imported
        $skipped_products = array();
        // # of Products that will update
        $to_update = 0;

        foreach ( $rows as &$values ) {
            if ( count($headers) == count($values) ) {
                $r = array_combine( $headers, $values );
            } else {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Incomplete row. ";
                $skipped_products[] = $r;
                continue;
            }

            // basic input validation
            $required_keys = array( 'sku', 'name', 'description', 'industry', 'category', 'image' );
            $valid = true;
            foreach ($required_keys as $k) {
                if ( empty( $r[$k] ) ) {
                    $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Required field '$k'. ";
                    $valid = false;
                }
            }

            $r['status'] = strtolower( $r['status'] );
            if ( empty( $r['status'] ) )
                $r['status'] = 'in-stock';
            $available_status = array( 'in-stock', 'out-of-stock', 'discontinued' );
            if ( !in_array( $r['status'], $available_status ) ) {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Invalid status '{$r['status']}'. ";
                $valid = false;
            }

            $category_id = null;
            if ( isset( $categories_by_name[$r['category']] ) ) {
                $category_id = $categories_by_name[$r['category']];
            }

            if ( !$category_id ) {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Category not found '{$r['category']}'. ";
                $valid = false;
            }

            $industry_id = null;
            foreach ( $industries as $i ) {
                if ( strcasecmp($i->name, $r['industry']) === 0) {
                    $industry_id = $i->industry_id;
                    break;
                }
            }

            if ( !$industry_id ) {
                $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Industry not found '{$r['industry']}'. ";
                $valid = false;
            }

            if ( !$valid ) {
                $skipped_products[] = $r;
                continue;
            }

            // see if the product exists
            $matching_product = new Product();
            $matching_product->get_by_sku_by_brand( $r['sku'], $brand_id );
            // we will only load images for new products
//            if ( !$matching_product->id ) {

                $image_list = explode(',', $r['image']);
                foreach( $image_list as $k => $image ) {
                    $image = trim($image);

                    if ( !regexp::match( $image, 'url' ) ) {
                        $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Bad image URL. ";
                        $valid = false;
                    }

                    // we ensure the url is decoded before encode
                    $image = rawurldecode( $image );
                    // encode url
                    $url_parts = parse_url( $image );
                    $path_parts = array_slice( explode( '/', $url_parts['path'] ), 1 );
                    foreach ( $path_parts as &$part)
                        $part = rawurlencode( $part );
                    $url_parts['path'] = implode( '/', $path_parts );
                    $image = "{$url_parts['scheme']}://{$url_parts['host']}/{$url_parts['path']}?{$url_parts['query']}";

                    // check if remote file exists
                    $file_exists = curl::check_file( $image );
                    if ( !$file_exists ) {
                        $r['reason'] = (isset( $r['reason'] ) ? $r['reason'] : '') . "Image not found.";
                        $skipped_products[] = $r;
                        continue 2;
                    }
                }
            //} else {
            if ( $matching_product->id )
                $to_update++;
            //}

            $product = array_slice($r, 0, 9);
            
            $product['price_map'] = (float) preg_replace( '/[^0-9.]/', '', $r['price_map']);
            $product['price_map'] = $product['price_map'] ? $product['price_map'] : 0;
            $product['price_wholesale'] = (float) preg_replace( '/[^0-9.]/', '', $r['price_wholesale']);
            $product['price_wholesale'] = $product['price_wholesale'] ? $product['price_wholesale'] : 0;
            $product['category_id'] = $category_id;
            $product['industry_id'] = $industry_id;
            $product['brand_id'] = $brand_id;
            $product['image'] = $r['image'];
            $product['inventory'] = $r['inventory'];
            $product['alternate_price'] = $r['alternate_price'];
            $product['retail_price'] = $r['retail_price'];
            $product['wholesale_price'] = $r['wholesale_price'];
            $product['sale_price'] = $r['sale_price'];
            $product['product_specifications'] = array();

            // Set product specifications
            $product_specifications = array_slice($r, 9);
            foreach ( $product_specifications as $spec_name => $spec_value ) {
                $product['product_specifications'][] = array( ucwords($spec_name), $spec_value );
            }

            // Append product
            $products[] = $product;
        }
        
        $product = new Product();
        $product_import = new ProductImport();
        $product_import->delete_all();

        foreach ( $products as $pi ) {
            $product_import = new ProductImport();
            $product_import->category_id = $pi['category_id'];
            $product_import->brand_id = $pi['brand_id'];
            $product_import->industry_id = $pi['industry_id'];
            $product_import->website_id = 0;
            $product_import->name = ucwords( $pi['name'] );
            $product_import->slug = format::slug( $pi['name'] );
            $product_import->description = $pi['description'];
            $product_import->status = $pi['status'];
            $product_import->sku = $pi['sku'];
            $product_import->price = $pi['price_wholesale'];
            $product_import->price_min = $pi['price_map'];
            $product_import->product_specifications = json_encode( $pi['product_specifications'] );
            $product_import->image = $pi['image'];
            $product_import->inventory = $pi['inventory'];
            $product_import->create();
        }


        // Add the response
        $response->add_response( 'count', count($rows) );
        $response->add_response( 'count_skipped', count($skipped_products) );
        $response->add_response( 'count_to_import', count($products) );
        $response->add_response( 'count_to_update', $to_update );
        $response->add_response( 'skipped_rows', $skipped_products );

        return $response;
    }

    /**
     * Confirm Import
     *
     * @return RedirectResponse
     */
    protected function confirm_import() {
        set_time_limit( 30 * 60 );
        ini_set( 'memory_limit', '512M' );
        
        if ( !$this->verified() ) {
            return new RedirectResponse( '/products/' );
        }

        $product_import = new ProductImport();
        $products = $product_import->get_all();

        foreach ( $products as $p ) {

            $product = new Product();
            $product->get_by_sku_by_brand( $p->sku, $p->brand_id );
            $product->category_id = $p->category_id;
            $product->brand_id = $p->brand_id;
            $product->industry_id = $p->industry_id;
            $product->website_id = 0;
            $product->name = $p->name;
            $product->slug = $p->slug;
            $product->status = $p->status;
            $product->description = $p->description;
            $product->sku = $p->sku;
            $product->price = $p->price;
            $product->price_min = $p->price_min;
            $product->user_id_modified = $this->user->id;
            $product->weight = 0;
            
            // a new product?
            if ( $product->id == null ) {
                $product->user_id_created = $this->user->id;

                $product->create();

                $product->publish_visibility = 'public';
                $product->publish_date = date( 'Y-m-d H:i:s' );
            } else {
                // Override Images
                $product->delete_images();
            }

            $industry = format::slug( $p->industry_name );
            $product->industry = $industry;

            $image_list = explode(',', $p->image);
            $product_images = [];
            foreach( $image_list as $k => $image ) {
                $image = trim($image);
                $slug = f::strip_extension( f::name( $image ) );
                $image_name = $product->upload_image( $image, $slug, $industry );
                $product_images[] = $image_name;
            }
            $product->add_images( $product_images );

            $product->save();

            $product_specifications = json_decode( $p->product_specifications, true );
            $product->delete_specifications(); // should I ?
            if ( $product_specifications ) {
                $product->add_specifications($product_specifications);
            }

            // Now need to add to the site via an account product
            $account_product = new AccountProduct();
            $account_product->get( $product->id, $this->user->account->id );

            $account_product->alternate_price = $p->alternate_price;
            $account_product->price = $p->price;
            $account_product->sale_price = $p->sale_price;
            $account_product->wholesale_price = $p->price_wholesale;
            $account_product->inventory = $p->inventory;
            $account_product->active = AccountProduct::ACTIVE;

            if ( $account_product->product_id ) {
                $account_product->save();
            } else {
                $account_product->website_id = $this->user->account->id;
                $account_product->product_id = $product->id;
                $account_product->create();
            }
        }

        $product_import->delete_all();

        $this->notify( _( 'Your products has been imported successfully!' ) );
        return new RedirectResponse( '/products/import/' );
    }

}