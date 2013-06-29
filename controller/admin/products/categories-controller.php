<?php
class CategoriesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'products/categories/';
        $this->section = 'products';
    }

    /**
     * Show top level categories
     *
     * @return TemplateResponse
     */
    protected function index() {
        $this->resources
            ->javascript( 'products/categories/index' )
            ->css( 'products/categories/index' );

        return $this->get_template_response( 'index' )
            ->kb( 13 )
            ->add_title( _('Categories') )
            ->select( 'categories', 'view' );
    }

    /***** AJAX *****/

    /**
     * Get the categories
     *
     * @return AjaxResponse
     */
    protected function get() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_POST['cid'] ) )
            return $response;

        // Setup Category
        $category = new Category();

        // Get categories
        $categories = $category->get_by_parent( $_POST['cid'] );

        if ( '0' != $_POST['cid'] ) {
            $category->get( $_POST['cid'] );
            $parent_category = $category->name;
        } else {
            $parent_category = _('Parent Category');
        }

        // Define html
        $html = '';
        $delete_nonce = nonce::create('delete');

        /**
         * @var $c Category
         */
        if ( is_array( $categories ) ) {
            $delete_confirmation = _('Are you sure you want to delete this category? This cannot be undone.');

            foreach ( $categories as $c ) {
                $delete_url = url::add_query_arg( array( '_nonce' => $delete_nonce, 'cid' => $c->id ), '/products/categories/delete/' );
                $edit_url = url::add_query_arg( array( 'cid' => $c->id, 'pcid' => $c->parent_category_id ), '/products/categories/add-edit/' ) . '#dAddEditCategory';

                $html .= '<div id="cat_' . $c->id . '" class="category">';
                $html .= '<h4>';
                $html .= '<a href="#" title="' . $c->name . '" id="pc' . $c->id . '" class="parent-category">' . $c->name . '</a>';
                $html .= ' <span class="gray-small">(' . $parent_category . ')</span>';
                $html .= '</h4>';
                $html .= '<p class="category-actions">';
                $html .= '<a href="' . $edit_url . '#dAddEditCategory" title="' . _('Edit Category') . '" rel="dialog" cache="0">' . _('Edit') . '</a>';
                $html .= ' | <a href="' . $delete_url . '" title="' . _('Delete') . '" ajax="1" confirm="' . $delete_confirmation . '">' . _('Delete') . '</a>';
                $html .= '</p>';

                $url = 'http://admin.' . DOMAIN . "/" . $c->slug . '/';
                $html .= '<a href="' . $url . '" class="url" id="aURL' . $c->id . '" title="' . _('View') . '" target="_blank" >' . $url . '</a>';
                $html .= '</div>';
            }
        }

        if ( empty ( $html ) ) {
            jQuery('#categories-list')->hide();
            jQuery('#no-sub-categories')->show();
        } else {
            jQuery('#no-sub-categories')->hide();

            jQuery('#categories-list')
                ->html( $html )
                ->show()
                ->sparrow();
        }

        if ( 0 == $_POST['cid'] ) {
            $category_name = _('Main Categories');
            $breadcrumb = _('Main Categories');

            jQuery('#edit-delete-category')->hide();
        } else {
            $category_name = $category->name;
            jQuery('#edit-category')->attr( 'href', url::add_query_arg( array( 'cid' => $category->id, 'pcid' => $category->id ), '/products/categories/add-edit/' ) . '#dAddEditCategory' );
            jQuery('#delete-category')->attr( 'href', url::add_query_arg( array( '_nonce' => $delete_nonce, 'cid' => $category->id ), '/products/categories/delete/' ) );
            jQuery('#edit-delete-category')->show();

            $parent_categories = $category->get_all_parents( $_POST['cid'] );

            $breadcrumb = '<a href="#" id="bc0">' . _('Main Categories') . '</a>';

            foreach ( $parent_categories as $pc ) {
                $breadcrumb .= '<span> &raquo; </span>';
                $breadcrumb .= '<a href="#" id="bc' . $pc->id . '">' . $pc->name .'</a>';
            }

            $breadcrumb .= '<span> &raquo; </span><span>' . $category_name . '</span>';
        }

        jQuery('#current-category span:first')
            ->text( $category_name )
            ->attr( 'rel', $_POST['cid'] );

        jQuery('#breadcrumb')->html( $breadcrumb );

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Add/Edit a Category
     *
     * @return CustomResponse|AjaxResponse
     */
    protected function add_edit() {
        // Get the company_id if there is one
        $category_id = ( isset( $_GET['cid'] ) ) ? (int) $_GET['cid'] : false;

        // Setup Models
        $category = new Category();
        $attribute = new Attribute();

        // Get Attributes
        $attributes_array = $attribute->get_all();

        foreach ( $attributes_array as $aa ) {
            $attributes[$aa->id] = $aa;
        }

        // Get Category data
        $categories = $category->sort_by_hierarchy();
        $category_attribute_ids = ( isset( $_GET['cid'] ) ) ? $attribute->get_category_attribute_ids( $_GET['cid'] ) : array();

        if ( $this->verified() ) {
            // If it exists, get it
            if ( $category_id )
                $category->get( $category_id );

            $category->parent_category_id = $_POST['sParentCategoryID'];
            $category->name = $_POST['tName'];
            $category->slug = $_POST['tSlug'];

            if ( $category_id ) {
                $category->save();
                $message = _('Your category has been successfully updated!');
                $parent_category_id = $_GET['pcid'];

                $attribute->delete_category_relations( $category_id );
            } else {
                $category->create();
                $message = _('Your category has been successfully created!');
                $parent_category_id = $category->parent_category_id;
            }

            $attribute->add_category_relations( $category_id, explode( '|', $_POST['hAttributes'] ) );

            // Reset Categories list
            Category::$categories = Category::$categories_by_parent = NULL;

            $notification_html = '<div class="notification sticky hidden">';
            $notification_html .= '<a class="close" href="#"><img src="/images/icons/close.png" alt="' . _('Close') . '" /></a>';
            $notification_html .= "<p>$message</p>";
            $notification_html .= '</div>';

            $_POST['cid'] = $parent_category_id;
            $response = $this->get();

            jQuery('div.boxy-wrapper a.close:visible:first')->click();
            jQuery('body')->append( $notification_html );
            jQuery('.notification:last')->notify();

            $response->add_response( 'jquery', jQuery::getResponse() );

            return $response;
        }

        if ( $category_id ) {
            $category->get( $category_id );
            $name = $category->name;
            $slug = $category->slug;
            $parent_category_id = $category->parent_category_id;
        } else {
            $name = ( isset( $_POST['tName'] ) ) ? $_POST['tName'] : '';
            $slug = ( isset( $_POST['tSlug'] ) ) ? $_POST['tSlug'] : '';
            $parent_category_id = 0;
        }

        $response = new CustomResponse( $this->resources, 'products/categories/add-edit' );
        $response->set( compact( 'category', 'attributes', 'category_attribute_ids', 'categories', 'name', 'slug', 'parent_category_id' ) );

        return $response;
    }

    /**
     * Delete a category
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['cid'] ) || 0 == $_GET['cid'] )
            return $response;

        // Get the category
        $category = new Category();
        $category->get( $_GET['cid'] );

        $parent_category_id = $category->parent_category_id;

        // Deactivate user
        if ( $category->id ) {
            $category->delete();

            // Load Parent Category
            $_POST['cid'] = $parent_category_id;

            $response = $this->get();
        }

        return $response;
    }

    /**
     * Update Sequence
     *
     * @return AjaxResponse
     */
    protected function update_sequence() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_POST['pcid'] ) || !isset( $_POST['sequence'] ) )
            return $response;

        $category = new Category();
        $category->update_sequence( $_POST['pcid'], explode( '&cat[]=', substr( $_POST['sequence'], 6 ) ) );

        return $response;
    }
}