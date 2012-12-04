<?php
class WebsiteController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'website/';
        $this->title = 'Website';
    }

    /**
     * List Website Pages
     *
     * @return TemplateResponse
     */
    protected function index() {
        return $this->get_template_response( 'index' )
            ->select( 'website', 'pages' );
    }

    /**
     * List Website Categories
     *
     * @return TemplateResponse
     */
    protected function categories() {
        $category = new Category();
        $account_category = new AccountCategory();

        $categories_array = $category->sort_by_hierarchy();
        $website_category_ids = $account_category->get_all_ids( $this->user->account->id );
        $categories = array();

        /**
         * @var Category $category
         */
        foreach ( $categories_array as $category ) {
            if ( !$category->has_parent() || !in_array( $category->id, $website_category_ids ) )
                continue;

            $categories[] = $category;
        }

        return $this->get_template_response( 'categories' )
            ->select( 'website', 'categories' )
            ->set( compact( 'categories' ) );
    }

    /***** AJAX *****/

    /**
     * List Pages
     *
     * @return DataTableResponse
     */
    protected function list_pages() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_page = new AccountPage();

        // Set Order by
        $dt->order_by( '`title`', '`status`', '`date_updated`' );
        $dt->search( array( '`title`' => false ) );
        $dt->add_where( " AND `website_id` = " . (int) $this->user->account->id );

        // Get account pages
        $account_pages = $account_page->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_page->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        $can_delete = $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST );

        if ( $can_delete ) {
            $confirm = _('Are you sure you want to delete this page? This cannot be undone.');
            $delete_page_nonce = nonce::create( 'delete-page' );
        }

        $dont_show = array( 'sidebar', 'furniture', 'brands' );
        $standard_pages = array( 'home', 'financing', 'current-offer', 'contact-us', 'about-us', 'products' );

        /**
         * @var AccountPage $page
         * @var string $confirm
         * @var string $delete_page_nonce
         */
        if ( is_array( $account_pages ) )
        foreach ( $account_pages as $page ) {
            // We don't want to show all the pages
            if ( in_array( $page->slug, $dont_show ) )
                continue;

            $actions = '';

            if ( $can_delete && !in_array( $page->slug, $standard_pages ) ) {
                $url = url::add_query_arg( array(
                    '_nonce' => $delete_page_nonce
                    , 'wpid' => $page->id
                ), '/website/delete-page/' );

               $actions = ' | <a href="' .  $url . '" title="' . _('Delete Page') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
            }

            $title = ( empty( $page->title ) ) ? format::slug_to_name( $page->slug ) . ' (' . _('No Name') . ')' : $page->title;

            $date_update = new DateTime( $page->date_updated );

            $data[] = array(
                $title . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . '/' . $page->slug . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'wpid', $page->id, '/website/edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions .
                    '</div>'
                , ( $page->status ) ? _('Visible') : _('Not Visible')
                , $date_update->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * List Cateogires
     *
     * @return DataTableResponse
     */
    protected function list_categories() {
        // Get response
        $dt = new DataTableResponse( $this->user );
        $account_category = new AccountCategory();
        $category = new Category();

        $parent_category_id = ( isset( $_GET['pcid'] ) ) ? $_GET['pcid'] : 0;

        // Set Order by
        $dt->order_by( 'title', 'wc.`date_updated`' );
        $dt->add_where( ' AND wc.`website_id` = ' . (int) $this->user->account->id . ' AND c.`parent_category_id` = ' . (int) $parent_category_id );
        $dt->search( array( 'title' => false ) );

        // Get account pages
        $account_categories = $account_category->list_all( $dt->get_variables() );
        $dt->set_row_count( $account_category->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        /**
         * @var AccountCategory $account_category
         */
        if ( is_array( $account_categories ) )
        foreach ( $account_categories as $account_category ) {
            $date_update = new DateTime( $account_category->date_updated );

            $data[] = array(
                $account_category->title . '<div class="actions">' .
                    '<a href="http://' . $this->user->account->domain . '/' . $category->get_url( $account_category->category_id ) . '/" title="' . _('View') . '" target="_blank">' . _('View') . '</a> | ' .
                    '<a href="' . url::add_query_arg( 'cid', $account_category->category_id, '/website/edit-category/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' .
                    '</div>'
                , $date_update->format('F jS, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}


