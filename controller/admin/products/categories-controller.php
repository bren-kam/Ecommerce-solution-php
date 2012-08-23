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
        $template_response = $this->get_template_response( 'index' )
            ->add_title( _('Categories') )
            ->select( 'categories', 'view' );

        $this->resources
            ->javascript( 'products/categories/index' )
            ->css( 'products/categories/index' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * Get the categories
     *
     * @return AjaxResponse
     */
    public function get() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_POST['cid'] ) )
            return $response;

        // Setup Category
        $category = new Category();

        // Get categories
        $categories = $category->get_by_parent( $_POST['cid'] );

        // Define html
        $html = '';

        /**
         * @var $c Category
         */
        if ( is_array( $categories ) ) {
            $delete_nonce = nonce::create('delete');
            $delete_confirmation = _('Are you sure you want to delete this category? This cannot be undone.');

            foreach ( $categories as $c ) {
                $delete_url = url::add_query_arg( array( '_nonce' => $delete_nonce, 'cid' => $c->id ), '/products/categories/delete/' );
                $edit_url = url::add_query_arg( array( 'cid' => $c->id ), '/products/categories/add-edit/' );

                $html .= '<div id="cat' . $c->id . '" class="category">';
                $html .= '<h4>';
                $html .= '<a href="#" title="' . $c->name . '" id="pc' . $c->id . '" class="parent-category">' . $c->name . '</a>';

                if ( 0 == $c->parent_category_id )
                    $html .= ' <span class="gray-small">(' . _('Parent Category') . ')</span>';

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

        jQuery('#categories-list')
            ->html( $html )
            ->sparrow();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Add/Edit a Category
     *
     * @return CustomResponse
     */
    public function add_edit() {
        // Get the company_id if there is one
        $category_id = ( isset( $_GET['cid'] ) ) ? (int) $_GET['cid'] : false;

        $response = new CustomResponse( $this->resources, 'products/categories/add-edit' );
        $response->set( compact( 'category_id' ) );

        return $response;
    }
}