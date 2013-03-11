<?php
class CustomProductsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct( );

        // Tell what is the base for all login
        $this->view_base = 'products/custom-products/';
        $this->section = _('Custom Products');
    }

    /**
     * List Reaches page
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        // Make sure they can be here
        if ( '2' == $this->user->account->get_settings('limited-products') ) // Change to 1
            return new RedirectResponse('/products/');

        $this->resources
            ->css( 'products/custom-products/index' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'jquery.datatables', 'products/custom-products/index' );

        return $this->get_template_response( 'index' )
            ->select( 'custom-products', 'view' );
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

            if ( $product->website_id != $this->user->account->id )
                return new RedirectResponse('/products/custom-products/');

            $product_images = $product->get_images();
            $product_attribute_items = $attribute_item->get_by_product( $product_id );

            $tags = $tag->get_value_by_type( 'product', $product_id );
            $date = new DateTime( $product->publish_date );

            $title = _('Edit');

            // Get the industry as it may be needed
            if ( $this->verified() )
                $industry->get( $product->industry_id );

            $account = new Account();
            $accounts = $account->get_by_product( $product->id );
        } else {
            $product_attribute_items = $tags = $product_images = $accounts = array();

            $date = new DateTime();

            $title = _('Add');
        }

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
            $product->delete_categories();
            $product->delete_images();
            $tag->delete_by_type( 'product', $product->id );
            $attribute_item->delete_relations( $product->id );

            $product->add_category( $_POST['sCategory'] );

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
            return new RedirectResponse('/products/custom-products/');
        }

        $this->resources
            ->javascript( 'fileuploader', 'products/custom-products/add-edit' )
            ->css('products/custom-products/add-edit')
            ->css_url( Config::resource('jquery-ui') );

        return $this->get_template_response( 'add-edit' )
            ->select( 'custom-products', 'add' )
            ->add_title( $title )
            ->set( compact( 'product_id', 'product', 'industries', 'brands', 'date', 'categories', 'attribute_items', 'tags', 'product_images', 'product_attribute_items', 'accounts' ) );
    }

    /**
     * Clone
     *
     * @return RedirectResponse
     */
    protected function clone_product() {
        $product_id = (int) $_GET['pid'];

        if ( empty( $product_id ) )
            return new RedirectResponse('/products/custom-products/');

        $product = new Product;
        $product->clone_product( $product_id, $this->user->id );
        $product->get( $product->id );
        $product->website_id = $this->user->account->id;
        $product->save();

        // Redirect to the new cloned product
        return new RedirectResponse( url::add_query_arg( 'pid', $product->id, '/products/custom-products/add-edit/' ) );
    }

    /***** AJAX *****/

    /**
     * List Products
     *
     * @return DataTableResponse
     */
    protected function list_products() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $product = new Product();

        // Set Order by
        $dt->order_by( 'p.`name`', 'b.`name`', 'p.`sku`', 'c.`name`', 'p.`status`', 'p.`publish_date`' );
        $dt->add_where( " AND p.`website_id` = " . (int) $this->user->account->id );
        $dt->search( array( 'p.`name`' => true, 'b.`name`' => false, 'p.`sku`' => false ) );

        switch ( $_GET['sType'] ) {
        	case 'sku':
        		if ( _('Enter SKU...') != $_GET['s'] )
        			$dt->add_where( " AND p.`sku` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'product':
        		if ( _('Enter Product Name...') != $_GET['s'] )
        			$dt->add_where( " AND p.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;

        	case 'brand':
        		if ( _('Enter Brand...') != $_GET['s'] )
        			$dt->add_where( " AND b.`name` LIKE " . $product->quote( $_GET['s'] . '%' ) );
        	break;
        }

        // Get
        $products = $product->list_custom_products( $dt->get_variables() );
        $dt->set_row_count( $product->count_custom_products( $dt->get_count_variables() ) );

        // Setup Base dete
        $delete_nonce = nonce::create( 'delete' );
        $confirm = _('Are you sure you want to delete this product? This cannot be undone.');
        $data = array();


        // Create output
        if ( is_array( $products ) )
        foreach ( $products as $product ) {
            $date = new DateTime( $product->publish_date );
            $actions = '<a href="' . url::add_query_arg( 'pid', $product->id, '/products/custom-products/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>';
            $actions .= ' | <a href="' . url::add_query_arg( 'pid', $product->id, '/products/custom-products/clone-product/' ) . '" title="' ._('Clone') . '">' . _('Clone') . '</a>';
            $actions .= ' | <a href="' . url::add_query_arg( array( 'pid' => $product->id, '_nonce' => $delete_nonce ), '/products/custom-products/delete/' ) . '" title="' . _('Delete') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';

            $data[] = array(
                $product->name . '<div class="actions">' . $actions . '</div>'
                , $product->brand
                , $product->sku
                , $product->category
                , ucwords( $product->status )
                , $date->format( 'F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Remove All Discontinued Products
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_GET['pid'] ), _('Deleting product failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Initialize objects
        $product = new Product();

        // Delete product
        $product->get( $_GET['pid'] );

        // Make sure they own it
        if ( $product->website_id == $this->user->account->id ) {
            $account_product = new AccountProduct();
            $account_category = new AccountCategory();

            // Remove it from the site
            $account_product->get( $_GET['pid'], $this->user->account->id );
            $account_product->active = 0;
            $account_product->save();

            // Delete the product
            $product->publish_visibility = 'deleted';
            $product->save();

            // Reorganize their categories
            $account_category->reorganize_categories( $this->user->account->id, new Category() );
        }

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

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
        $product->website_id = $this->user->account->id;
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

        $response->check( $product->website_id == $this->user->account->id, _('You do not have permission to modify this product') );

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
        $file->upload_image( $result['file_path'], $new_image_name, 320, 320, $industry_name, 'products/' . $product->id . '/', false, true );
        $file->upload_image( $result['file_path'], $new_image_name, 46, 46, $industry_name, 'products/' . $product->id . '/thumbnail/', false, true );
        $file->upload_image( $result['file_path'], $new_image_name, 200, 200, $industry_name, 'products/' . $product->id . '/small/', false, true );
        $image_name = $file->upload_image( $result['file_path'], $new_image_name, 700, 700, $industry_name, 'products/' . $product->id . '/large/', false, true );

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
     * AutoComplete
     *
     * @return AjaxResponse
     */
    protected function autocomplete() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        $response->check( isset( $_POST['type'], $_POST['term'] ), _('Autocomplete failed') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $ac_suggestions = array();

        // Get the right suggestions for the right type
        switch ( $_POST['type'] ) {
            case 'brand':
                $brand = new Brand;
                $ac_suggestions = $brand->autocomplete_custom( $_POST['term'], $this->user->account->id, true );
            break;

            case 'product':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_POST['term'], 'name', $this->user->account->id, true );
            break;

            case 'sku':
                $account_product = new AccountProduct();
                $ac_suggestions = $account_product->autocomplete_all( $_POST['term'], 'sku', $this->user->account->id, true );
            break;

            default: break;
        }

        // It needs to be empty if nothing else
        $suggestions = array();

        if ( is_array( $ac_suggestions ) )
        foreach ( $ac_suggestions as $acs ) {
            $suggestions[] = array( 'name' => html_entity_decode( $acs['name'], ENT_QUOTES, 'UTF-8' ), 'value' => $acs['value'] );
        }

        // Sent by the autocompleter
        $response->add_response( 'suggestions', $suggestions );

        return $response;
    }
}

