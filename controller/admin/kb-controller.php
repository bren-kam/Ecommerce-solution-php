<?php
class KbController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'kb/';
        $this->section = 'kb';
        $this->title = _('Knowledge Base');
    }

    /**
     * Show dashboard
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        $this->resources->css('kb/home');

        $article = new KnowledgeBaseArticle();
        $articles = $article->get_by_views( KnowledgeBaseCategory::SECTION_ADMIN );

        return $this->get_template_response( 'home' )
            ->set( compact( 'articles' ) );
    }

    /**
     * Show an article
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function article() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse( '/kb/' );

        $article = new KnowledgeBaseArticle();
        $article->get( $_GET['aid'] );

        $category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );
        $category->get( $article->kb_category_id );
        $categories = $category->get_all_parents( $article->kb_category_id );
        $categories[] = $category;

        $page = new KnowledgeBasePage();
        $page->get( $article->kb_page_id );

        // Add a view
        $view = new KnowledgeBaseArticleView();
        $view->kb_article_id = $article->id;
        $view->user_id = $this->user->id;
        $view->create();

        // Get data
        $articles = $article->get_by_page( $article->kb_page_id );

        $this->resources->javascript('kb/article');

        return $this->get_template_response( 'article' )
            ->add_title( $article->title . ' | ' . _('Article') )
            ->set( compact( 'article', 'categories', 'page', 'articles' ) );
    }

    /**
     * Show a page
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function page() {
        if ( !isset( $_GET['pid'] ) )
            return new RedirectResponse( '/kb/' );

        $page = new KnowledgeBasePage();
        $page->get( $_GET['pid'] );

        $article = new KnowledgeBaseArticle();

        $category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );
        $category->get( $page->kb_category_id );
        $categories = $category->get_all_parents( $page->kb_category_id );
        $categories[] = $category;

        // Get data
        $articles = $article->get_by_page( $page->id );
        $pages = $page->get_by_category( $page->kb_category_id );

        $this->resources->css('kb/kb');

        return $this->get_template_response( 'page' )
            ->add_title( $page->name . ' | ' . _('Page') )
            ->set( compact( 'page', 'categories', 'articles', 'pages' ) );
    }

    /**
     * Show a category
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function category() {
        if ( !isset( $_GET['cid'] ) )
            return new RedirectResponse( '/kb/' );

        $category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );
        $category->get( $_GET['cid'] );
        $parent_categories = $category->get_all_parents( $category->id );
        $child_categories = $category->get_all_children( $category->id );
        $sibling_categories = $category->get_by_parent( $category->parent_id );

        $page = new KnowledgeBasePage();
        $article = new KnowledgeBaseArticle();

        // Get items
        $articles = $article->get_by_category( $category->id );
        $pages = $page->get_by_category( $category->id );

        $this->resources->css('kb/kb');

        return $this->get_template_response( 'category' )
            ->add_title( $category->name . ' | ' . _('Category') )
            ->set( compact( 'category', 'parent_categories', 'child_categories', 'sibling_categories', 'articles', 'pages' ) );
    }

    /**
     * Search for categories/pages/articles
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function search() {
        if ( !isset( $_GET['kbs'] ) )
            return new RedirectResponse( '/kb/' );

        // Declare variables
        $category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );
        $page = new KnowledgeBasePage();
        $article = new KnowledgeBaseArticle();

        // Search results
        $categories = $category->search( $_GET['kbs'] );
        $pages = $page->search( $_GET['kbs'] );
        $articles = $article->search( $_GET['kbs'] );

        $search = $_GET['kbs'];

        $this->resources->css('kb/kb');

        return $this->get_template_response( 'search' )
            ->add_title( _('Search') )
            ->set( compact( 'search', 'categories', 'pages', 'articles' ) );
    }

    /**
     * Browser support page
     *
     * @return RedirectResponse|TemplateResponse
     */
    public function browser() {
        $this->resources
            ->css('kb/browser')
            ->javascript('kb/browser');

        $user = new User();
        $user->get( $this->user->id );
        $domain = $user->domain;

        return $this->get_template_response( 'browser' )
            ->set( compact( 'domain' ) )
            ->add_title( _('Browser Support') );
    }

    /***** AJAX *****/

    /**
     * Rate an article
     */
    protected function rate() {
         // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['aid'], $_GET['r'] ), _('An error occurred while trying to rate this page.') );

        if ( $response->has_error() )
            return $response;

        // Create the rating
        $rating = new KnowledgeBaseArticleRating();
        $rating->kb_article_id = $_GET['aid'];
        $rating->user_id = $this->user->id;
        $rating->rating = (int) $_GET['r'];
        $rating->create();

        // If they didn't think it was helpful, let's make note of it so we can help them
        if ( KnowledgeBaseArticleRating::NEGATIVE == $_GET['r'] ) {
            // Get the article
            $article = new KnowledgeBaseArticle();
            $article->get( $_GET['aid'] );

            $category = new KnowledgeBaseCategory( KnowledgeBaseCategory::SECTION_ADMIN );
            $category->get( $article->kb_category_id );
            $categories = $category->get_all_parents( $article->kb_category_id );
            $categories[] = $category;

            $page = new KnowledgeBasePage();
            $page->get( $article->kb_page_id );

            // Create path
            $category_count = count( $categories );
            $path = '';

            for ( $i = 0; $i < $category_count; $i++ ) {
                // Set variables
                $category = $categories[$i];

                $path = $category->name . ' > ';
            }

            $path .= $page->name . ' > ' . $article->title;

            $user = new User();
            $user->get( $this->user->id );

            // Create message
            $message = '<a href="' . url::add_query_arg( 'aid', $article->id, 'http://account.' . $user->domain . '/kb/article/' ) . '" title="' . $article->title . '" target="_blank">' . $path . '</a>';
            $message .= '<br /><br />';
            $message .= $this->user->contact_name . ' found this article unhelpful. Please check to see if he or she needs assistance.';

            // Create a ticket
            $ticket = new Ticket();
            $ticket->user_id = $this->user->id;
            $ticket->assigned_to_user_id = ( $this->user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) ? User::TECHNICAL : $this->user->account->os_user_id;
            $ticket->website_id = $this->user->account->id;
            $ticket->summary = 'Article was not helpful: ' . $article->title;
            $ticket->message = $message;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->priority = Ticket::PRIORITY_NORMAL;
            $ticket->create();
        }

        return $response;
    }
}


