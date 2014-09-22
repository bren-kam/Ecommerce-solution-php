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
            ->javascript_url( Config::resource( 'jqueryui-js' ) )
            ->javascript( 'products/categories/index' )
            ->css( 'products/categories/index' );

        return $this->get_template_response( 'index' )
            ->kb( 13 )
            ->add_title( _('Categories') )
            ->select( 'products', 'products/categories' );
    }

    /**
     * Show list of categories
     *
     * @return TextResponse
     */
    protected function list_text() {
        $category = new Category();
        $categories = $category->get_all();
        $categories_list = array();

        /**
         * @var Category $category
         */
        foreach ( $categories as $category ) {
            if ( $category->has_children() )
                continue;

            $category_string = $category->name;
            $parents = $category->get_all_parents( $category->id );

            foreach ( $parents as $parent_category ) {
                $category_string = $parent_category->name . ' > ' . $category_string;
            }

            $categories_list[] = $category_string;
        }

        sort( $categories_list );

        return new TextResponse( implode( "\n", $categories_list ) );
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
        if ( $response->has_error() || !isset( $_GET['cid'] ) )
            return $response;

        // Setup Category
        $category = new Category();

        // Get Current Category
        if ( '0' != $_GET['cid'] ) {
            $category->get( $_GET['cid'] );
            $response->add_response( 'category', $category );
        }

        // Get children categories
        $category->get_all();
        $categories = $category->get_by_parent( $_GET['cid'] );
        if ( !empty( $categories ) ) {
            $response->add_response( 'categories', $categories );
        }

        // Get Parent Categories
        if ( '0' != $_GET['cid'] ) {
            $parent_categories = $category->get_all_parents( $_GET['cid'] );
            if ( !empty( $parent_categories ) ) {
                $response->add_response( 'parent_categories', $parent_categories );
            }
        }

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
            $category->google_taxonomy = $_POST['tGoogleTaxonomy'];

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

            $attribute->add_category_relations( $category_id, isset( $_POST['hAttributes'] ) ? $_POST['hAttributes'] : array() );

            // Reset Categories list
            Category::$categories = Category::$categories_by_parent = NULL;

            $_GET['cid'] = (int)$_GET['pcid'];
            $response = $this->get();
            $response->notify( $message );

            return $response;
        }

        if ( $category_id ) {
            $category->get( $category_id );
            $name = $category->name;
            $slug = $category->slug;
            $google_taxonomy = $category->google_taxonomy;
            $parent_category_id = $category->parent_category_id;
        } else {
            $name = ( isset( $_POST['tName'] ) ) ? $_POST['tName'] : '';
            $slug = ( isset( $_POST['tSlug'] ) ) ? $_POST['tSlug'] : '';
            $google_taxonomy = ( isset( $_POST['tGoogleTaxonomy'] ) ) ? $_POST['tGoogleTaxonomy'] : '';;
            $parent_category_id = 0;
        }

        $response = new CustomResponse( $this->resources, 'products/categories/add-edit' );
        $response->set( compact( 'category', 'attributes', 'category_attribute_ids', 'categories', 'name', 'slug', 'google_taxonomy', 'parent_category_id' ) );

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

        // Deactivate user
        if ( $category->id ) {
            $category->remove();
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
        $category->update_sequence( $_POST['pcid'], explode( '|', $_POST['sequence'] ) );

        return $response;
    }
}