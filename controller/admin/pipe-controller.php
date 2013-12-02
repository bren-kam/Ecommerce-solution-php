<?php
class PipeController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'test/';
    }

    /**
     * Pipe emails
     *
     * @return HtmlResponse
     */
    protected function email() {
        library( 'email/rfc822-addresses' );
        library( 'email/mime-parser-class' );

        $email_content = file_get_contents( 'php://stdin' );

        // Create mime
        $mime = new mime_parser_class();
        $mime->ignore_syntax_errors = 1;

        $mime->Decode( array( 'Data' => $email_content ), $emails );
        $email = $emails[0];

        // Get data
        $subject = $email['Headers']['subject:'];
        $body = ( empty( $email['Body'] ) ) ? $email['Parts'][0]['Body'] : $email['Body'];
        $body = nl2br( substr( $body, 0, strpos( $body, '******************* Reply Above This Line *******************' ) ) );
        $ticket_id = (int) preg_replace( '/.*Ticket #([0-9]+).*/', '$1', $subject );

        // Get Ticket
        $ticket = new Ticket();
        $ticket->get( $ticket_id );

        // Get User
        $user = new User();
        $user->get( $ticket->assigned_to_user_id );

        // Create comment based on email
        $ticket_comment = new TicketComment();
        $ticket_comment->ticket_id = $ticket->id;
        $ticket_comment->user_id = $ticket->user_id;
        $ticket_comment->comment = $body;
        $ticket_comment->create();

        // Set email headers
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'To: ' . $user->email . "\r\n";
        $headers .= 'From: ' . $user->company . ' Support <noreply@' . $user->domain . '>' . "\r\n";

        // Let assigned user know
        $ticket_url = url::add_query_arg( 'tid', $ticket->id, 'http://admin.' . $user->domain . '/tickets/ticket/' );
        mail( $user->email, "New Response on Ticket #{$ticket_id}", "<p>A new response from the client has been received. See message below:</p><p><strong>Original Message:</strong><br />" . $ticket->message . "</p><p><strong>Client Response:</strong><br />{$body}</p><p><a href='{$ticket_url}'>{$ticket_url}</a></p>", $headers );

        return new HtmlResponse( '' );
    }

    /**
     * Pipe reaches
     *
     * @return HtmlResponse
     */
    protected function reach() {
        library( 'email/rfc822-addresses' );
        library( 'email/mime-parser-class' );

        $email_content = file_get_contents( 'php://stdin' );

        // Create mime
        $mime = new mime_parser_class();
        $mime->ignore_syntax_errors = 1;

        $mime->Decode( array( 'Data' => $email_content ), $emails );
        $email = $emails[0];

        // Get data
        $subject = $email['Headers']['subject:'];
        $body = ( empty( $email['Body'] ) ) ? $email['Parts'][0]['Body'] : $email['Body'];
        $body = nl2br( substr( $body, 0, strpos( $body, '******************* Reply Above This Line *******************' ) ) );
        $reach_id = (int) preg_replace( '/.*Reach #([0-9]+).*/', '$1', $subject );

        // Get Reach
        $reach = new WebsiteReach();
        $reach->get_by_id( $reach_id );

        // Get User
        $user = new User();
        $user->get( $reach->assigned_to_user_id );

        // Create comment based on email
        $reach_comment = new WebsiteReachComment();
        $reach_comment->website_reach_id = $reach->id;
        $reach_comment->website_user_id = $reach->website_user_id;
        $reach_comment->comment = $body;
        $reach_comment->create();

        // Set email headers
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'To: ' . $user->email . "\r\n";
        $headers .= 'From: ' . $user->company . ' Support <noreply@' . $user->domain . '>' . "\r\n";

        // Let assigned user know
        $reach_url = url::add_query_arg( 'wrid', $reach->id, 'http://account.' . $user->domain . '/products/reaches/reach/' );
        mail( $user->email, "New Response on Reach #{$reach_id}", "<p>A new response from the customer has been received. See message below:</p><p><strong>Original Message:</strong><br />" . $reach->message . "</p><p><strong>Client Response:</strong><br />{$body}</p><p><a href='{$reach_url}'>{$reach_url}</a></p>", $headers );

        // We don't want any response -- including headers, to be sent out
        exit;

        return new HtmlResponse( '' );
    }

    /**
     * Override login function
     * @return bool
     */
    protected function get_logged_in_user() {
        $this->user = new User();
        return true;
    }
}