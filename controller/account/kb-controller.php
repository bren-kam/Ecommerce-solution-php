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
        if ( !$this->user->account->email_marketing )
            return new RedirectResponse('/email-marketing/subscribers/');

        $email_message = new EmailMessage();
        $messages = $email_message->get_dashboard_messages_by_account( $this->user->account->id );

        $email = new Email();
        $subscribers = $email->get_dashboard_subscribers_by_account( $this->user->account->id );

        // Setup variables
        $email = new AnalyticsEmail();
        $email_count = count( $messages );
        $i = 0;

        if ( is_array( $messages ) ) {
        	// Get the analytics data
        	while ( $i < $email_count && !$email->mc_campaign_id ) {
                $message = $messages[$i];

                try {
                    $email->get_complete( $message->mc_campaign_id, $this->user->account->id );
                } catch( ModelException $e ) {
                    $this->notify( _('An error occurred while trying to get your email') . ', "' . $message->subject . '". ' . _('Please contact an online specialist for assistance.'), false );
                }

        		$i++;
        	}
        }

        $bar_chart = Analytics::bar_chart( $email );

        $this->resources
            ->css( 'email-marketing/dashboard' )
            ->javascript( 'swfobject', 'email-marketing/dashboard');

        return $this->get_template_response( 'index' )
            ->add_title( _('Dashboard') )
            ->select( 'email-dashboard' )
            ->set( compact( 'messages', 'subscribers', 'email', 'bar_chart', 'email_count' ) );
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

        $category = new KnowledgeBaseCategory();
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

        $this->resources->css('kb/article');

        return $this->get_template_response( 'article' )
            ->add_title( $article->title . ' | ' . _('Article') )
            ->set( compact( 'article', 'categories', 'page' ) );
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

            $category = new KnowledgeBaseCategory();
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

        jQuery('#helpful p:visible')->hide();
        jQuery('#thanks')->show();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}


