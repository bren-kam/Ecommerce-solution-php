<?php
class PagesController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'knowledge-base/pages/';
        $this->section = _('Knowledge Base');
    }

    /**
     * List
     *
     * @return TemplateResponse
     */
    protected function index() {
        $kb_section = ( KnowledgeBaseCategory::SECTION_ADMIN == $_GET['s'] ) ? KnowledgeBaseCategory::SECTION_ACCOUNT : KnowledgeBaseCategory::SECTION_ADMIN;
        $uc_section = ucwords( $kb_section );
        $link = '<a href="' . url::add_query_arg( 's', $kb_section, '/' . $this->view_base ) . '" class="small" title="' . $uc_section . '">(' . _('Switch to') . ' ' . $uc_section . ')</a>';

        $this->resources->javascript( 'knowledge-base/pages/index' );

        return $this->get_template_response( 'index' )
            ->kb( 30 )
            ->add_title( _('Pages') )
            ->set( compact( 'link', 'kb_section' ) )
            ->select( 'knowledge-base', 'knowledge-base/pages/index' );
    }

    /**
     * Add/Edit a user
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Determine if we're adding or editing the user
        $kb_id = ( isset( $_GET['kbpid'] ) ) ? (int) $_GET['kbpid'] : false;

        // Initialize classes
        $kb_page = new KnowledgeBasePage();
        $kb_category = new KnowledgeBaseCategory( $_GET['s'] );

        // Get the user
        if ( $kb_id ) {
            $kb_page->get( $kb_id );
            $kb_category->get( $kb_page->kb_category_id );
            $section = $kb_category->section;
        } else {
            $section = $_GET['s'];
        }

        // Create new form table
        $ft = new BootstrapForm( 'fAddEditPage', url::add_query_arg( array( 's' => $_GET['s'], 'kbpid' => $kb_page->id ), '/knowledge-base/pages/add-edit/' ) );

        $ft->submit( ( $kb_page->id ) ? _('Save') : _('Add') );

        $ft->add_field( 'text', _('Name'), 'tName', $kb_page->name )
            ->attribute( 'maxlength', 100 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $sections = array(
            KnowledgeBaseCategory::SECTION_ADMIN => ucwords( KnowledgeBaseCategory::SECTION_ADMIN )
            , KnowledgeBaseCategory::SECTION_ACCOUNT => ucwords( KnowledgeBaseCategory::SECTION_ACCOUNT )
        );

        $ft->add_field( 'select', _('Section'), 'sSection', $section )
            ->options( $sections );

        $categories = $kb_category->sort_by_hierarchy();
        $categories_array = array();

        foreach( $categories as $category ) {
            $categories_array[$category->id] = str_repeat( '&nbsp;', $category->depth * 5 ) . $category->name;
        }

        $ft->add_field( 'select', _('Category'), 'sCategory', $kb_page->kb_category_id )
            ->options( $categories_array )
            ->add_validation( 'req', _('The "Category" field is required') );

        // Make sure it's posted and verified
        if ( $ft->posted() ) {
            // Update all the fields
            $kb_page->name = $_POST['tName'];
            $kb_page->kb_category_id = $_POST['sCategory'];

            // Update or create
            if ( $kb_page->id ) {
                $kb_page->save();
            } else {
                $kb_page->create();
            }

            $this->notify( _('Your Knowledge Base Page has been successfully created/saved!') );

            return new RedirectResponse( url::add_query_arg( 's', $_GET['s'], '/knowledge-base/pages/' ) );
        }

        // Select page
        $form = $ft->generate_form();

        $this->resources->javascript( 'knowledge-base/pages/add-edit' );

        // Get Page
        return $this->get_template_response( 'add-edit' )
            ->kb( 31 )
            ->select( 'knowledge-base', 'knowledge-base/pages/add' )
            ->add_title( ( ( $kb_page->id ) ? _('Edit') : _('Add') ) . ' ' . _('Page') )
            ->set( compact( 'form' ) );
    }

    /***** AJAX *****/

    /**
     * List
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $kb_page = new KnowledgeBasePage();

        // Set Order by
        $dt->order_by( 'kbp.`name`', 'category' );
        $dt->add_where( ' AND kbc.`section` = ' . $kb_page->quote( $_GET['section'] ) );
        $dt->add_where( ' AND ( kbc2.`section` = ' . $kb_page->quote( $_GET['section'] ) . ' OR kbc2.`section` IS NULL )' );
        $dt->search( array( 'kbp.`name`' => false, 'kbc.`name`' => false, 'kbc2.`name`' => false ) );

        // Get items
        $pages = $kb_page->list_all( $dt->get_variables() );
        $dt->set_row_count( $kb_page->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $delete_nonce = nonce::create( 'delete' );

        /**
         * @var KnowledgeBasePage $page
         */
        if ( is_array( $pages ) )
        foreach ( $pages as $page ) {
            $data[] = array(
                $page->name . '<div class="actions">' .
                    '<a href="' . url::add_query_arg( array( 's' => $_GET['section'], 'kbpid' => $page->id ), '/knowledge-base/pages/add-edit/' ) . '" title="' . $page->name . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'kbpid' => $page->id, '_nonce' => $delete_nonce ), '/knowledge-base/pages/delete/' ) . '" title="' . _('Delete') . '" class="delete-page">' . _('Delete') . '</a></div>'
                , $page->category
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Gets Categories
     *
     * @return AjaxResponse
     */
    protected function get_categories() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_POST['s'] ) )
            return $response;

        // Get the user
        $kb_category = new KnowledgeBaseCategory($_POST['s'] );

        $categories = $kb_category->sort_by_hierarchy();
        $categories_array = array();

        foreach( $categories as $category ) {
            $categories_array[$category->id] = str_repeat( '&nbsp;', $category->depth * 5 ) . $category->name;
        }

        // Add the response
        $response->add_response( 'categories', $categories_array );

        return $response;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['kbpid'] ) )
            return $response;

        // Get the page
        $kb_page = new KnowledgeBasePage();
        $kb_page->get( $_GET['kbpid'] );
        $kb_page->remove();

        return $response;
    }
}