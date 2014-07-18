<?php
class CategoriesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'knowledge-base/categories/';
        $this->section = _('Knowledge Base');
    }

    /**
     * List
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        // Must be a super admin
        if ( !$this->user->has_permission( User::ROLE_SUPER_ADMIN ) )
            return new RedirectResponse( '/knowledge-base/articles/' );

        if ( !isset( $_GET['s'] ) )
            url::redirect( url::add_query_arg( 's', KnowledgeBaseCategory::SECTION_ADMIN, '/' . $this->view_base ) );

        $section = ( KnowledgeBaseCategory::SECTION_ADMIN == $_GET['s'] ) ? KnowledgeBaseCategory::SECTION_ACCOUNT : KnowledgeBaseCategory::SECTION_ADMIN;
        $uc_section = ucwords( $section );
        $link = '<a href="' . url::add_query_arg( 's', $section, '/' . $this->view_base ) . '" class="small" title="' . $uc_section . '">(' . _('Switch to') . ' ' . $uc_section . ')</a>';

        $this->resources
            ->javascript( 'knowledge-base/categories/index' )
            ->css( 'products/categories/index' );

        return $this->get_template_response( 'index' )
            ->kb( 32 )
            ->add_title( _('Categories') )
            ->select( 'knowledge-base', 'knowledge-base/categories' )
            ->set( compact( 'link' ) );
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
        if ( $response->has_error() || !isset( $_GET['kbcid'] ) )
            return $response;

        // Setup Category
        $category = new KnowledgeBaseCategory( $_GET['s'] );

        if ( '0' != $_GET['kbcid'] ) {
            $category->get( $_GET['kbcid'] );
            $response->add_response( 'category', $category );
        }

        // Get categories
        $category->get_all();
        $categories = $category->get_by_parent( $_GET['kbcid'] );
        if ( !empty( $categories ) ) {
            $response->add_response( 'categories', $categories );
        }

        if ( '0' != $_GET['kbcid'] ) {
            $parent_categories = $category->get_all_parents( $_GET['kbcid'] );
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
        $category_id = ( isset( $_GET['kbcid'] ) ) ? (int) $_GET['kbcid'] : false;

        // Setup Models
        $category = new KnowledgeBaseCategory( $_GET['s'] );

        // Get Category data
        $categories = $category->sort_by_hierarchy();

        if ( $this->verified() ) {
            // If it exists, get it
            if ( $category_id )
                $category->get( $category_id );

            $category->parent_id = $_POST['sParentID'];
            $category->name = $_POST['tName'];

            if ( $category_id ) {
                $category->save();
            } else {
                $category->create();
            }

            // Reset Categories list
            KnowledgeBaseCategory::$categories = KnowledgeBaseCategory::$categories_by_parent = NULL;

            // Reset Categories list
            Category::$categories = Category::$categories_by_parent = NULL;

            $_GET['kbcid'] = (int)$category->parent_id;
            $_GET['s'] = $category->section;
            $response = $this->get();

            return $response;
        }

        if ( $category_id ) {
            $category->get( $category_id );
            $name = $category->name;
            $parent_id = $category->parent_id;
        } else {
            $name = ( isset( $_POST['tName'] ) ) ? $_POST['tName'] : '';
            $parent_id = ( isset( $_GET['kbpid'] ) ) ? $_GET['kbpid'] : 0;
        }

        $section = $_GET['s'];

        $response = new CustomResponse( $this->resources, 'knowledge-base/categories/add-edit' );
        $response->set( compact( 'category', 'categories', 'name', 'parent_id', 'section' ) )
            ->kb( 28 );

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
        if ( $response->has_error() || !isset( $_GET['kbcid'] ) || 0 == $_GET['kbcid'] )
            return $response;

        // Get the category
        $category = new KnowledgeBaseCategory( $_GET['s'] );
        $category->get( $_GET['kbcid'] );

        $parent_id = (int) $category->parent_id;

        // Deactivate user
        if ( $category->id ) {
            try {
                $category->remove();
            } catch ( ModelException $e ) {
                switch( $e->getCode() ) {
                      case ActiveRecordBase::EXCEPTION_DUPLICATE_ENTRY:
                        $response->check( false, _('This category is being used by an article or page. Please make sure it is unused before deleting.' ) );
                    break;

                    default:
                        $response->check( false, $e->getMessage() );
                    break;
                }

                return $response;
            }

            // Load Parent Category
            $_POST['kbcid'] = (int) $parent_id;
            $_POST['s'] = $_GET['s'];

            $response = $this->get();
        }

        return $response;
    }
}