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

        // Temporary Logging stuff
        @file_put_contents('/tmp/ticket_email_pipe.log', "\n". date("Y-m-d H:i:s") . "-----------------------------\n", FILE_APPEND);
        @file_put_contents('/tmp/ticket_email_pipe.log', json_encode($email_content), FILE_APPEND);

        // Create mime
        $mime = new mime_parser_class();
        $mime->ignore_syntax_errors = 1;

        $mime->Decode( array( 'Data' => $email_content ), $emails );
        $email = $emails[0];

        // Get data
        $subject = $email['Headers']['subject:'];
        if ( empty( $email['Body'] ) ) {
            if ( empty( $email['Parts'][0]['Body'] ) ) {
                $body = $email['Parts'][0]['Parts'][0]['Body'];
            } else {
                $body = $email['Parts'][0]['Body'];
            }
        } else {
            $body = $email['Body'];
        }
        $body = preg_replace('/\nOn(.*?)wrote:(.*?)$/si', '', $body);
        $body = preg_replace('/\n> On(.*?)wrote:(.*?)$/si', '', $body);
        $body = preg_replace('/\n\nFrom: (.*?)$/si', '', $body);
        $body = trim($body);
        $body = nl2br($body);
        $ticket_id = (int) preg_replace( '/.*Ticket #([0-9]+).*/', '$1', $subject );
        $from = $email['ExtractedAddresses']['from:'][0]['address'];
        $from_name = isset($email['ExtractedAddresses']['from:'][0]['name']) ? $email['ExtractedAddresses']['from:'][0]['name'] : '';

        // Guess the receivers
        $to_list = [
            $email['ExtractedAddresses']['to:'][0]['address']
        ];
        $valid_received_domains = [
            '@blinkyblinky.me',
            '@imagineretailer.com',
            '@greysuitretail.com'
        ];
        if ( isset($email['Headers']['received:']) && is_array($email['Headers']['received:']) ) {
            foreach ( $email['Headers']['received:'] as $received ) {
                $matches = [];
                preg_match('/for (.*);/i', $received, $matches);
                if ( isset($matches[1]) ) {
                    $received_by = $matches[1];
                    $received_by = str_replace(['<', '>', ' '], '', $received_by);
                    foreach( $valid_received_domains as $domain ) {
                        if ( strpos($received_by, $domain) !== false ) {
                            $to_list[] = $received_by;
                        }
                    }
                }
            }
        }
        if ( isset( $email['ExtractedAddresses']['cc:'][0]['address'] ) ) {
            $to_list[] = $email['ExtractedAddresses']['cc:'][0]['address'];
        }
        if ( isset( $email['ExtractedAddresses']['bcc:'][0]['address'] ) ) {
            $to_list[] = $email['ExtractedAddresses']['bcc:'][0]['address'];
        }

        // attachments
        $attachments = [];
        $upload_dir = tempnam( sys_get_temp_dir(), 'customer-support' );
        unlink($upload_dir);
        if ( !is_dir( $upload_dir ) ) {
            mkdir($upload_dir, 0777, true);
        }
        foreach($email['Parts'] as $part) {
            //check for attachments
            if(isset($part['FileDisposition']) && $part['FileDisposition'] == 'attachment'){
                //format file name (change spaces to underscore then remove anything that isn't a letter, number or underscore)
                $filename = preg_replace('/[^0-9,a-z,\.,_]*/i','',str_replace(' ','_', $part['FileName']));

                //write the data to the file
                $attachment_path = $upload_dir . '/' . $filename;
                $fp = fopen( $attachment_path, 'w');
                $written = fwrite($fp,$part['Body']);
                fclose($fp);

                $attachments[] = [
                    'path' => $attachment_path,
                    'name' => $filename
                ];
            }
        }

        // Ignore email from support, reply, no-reply, etc.
        $matches = [];
        if ( preg_match('/(support|noreply|no-reply|jira)@/i', $from, $matches) ) {
            return new HtmlResponse("Ignoring email from '{$from}'");
        }

        // Get Ticket
        $ticket = new Ticket();
        $ticket->get( $ticket_id );

        // Get from User
        $from_user = new User();
        $from_user->get_by_email( $from, false );

        // Get from User
        $to_user = new User();
        $to = null;
        foreach ( $to_list as $to_address ) {
            $to_user->get_by_email( $to_address, false );
            if ( $to_user->id && $to_user->has_permission(User::ROLE_ONLINE_SPECIALIST)) {
                $to = $to_address;                
                if ($to_user->from_email_address) 
                    $to = $to_user->from_email_address;


                break;
            }
        }

        // Create Use if does not exists
        if ( !$from_user->id ) {
            $from_user->email = $from;
            $from_user->contact_name = $from_name;
            $from_user->role = User::ROLE_AUTHORIZED_USER;
            $from_user->status = User::STATUS_ACTIVE;
            $from_user->company_id = 1;
            $from_user->create();
        }

        if ( $ticket->id ) {
            // Create comment based on email
            $ticket_comment = new TicketComment();
            $ticket_comment->ticket_id = $ticket->id;
            $ticket_comment->user_id = $from_user->user_id;
            $ticket_comment->comment = $body;
            $ticket_comment->to_address = $to;
            $ticket_comment->create();

            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->save();
        } else {
            // We can't create a ticket if we can't assign to anybody
            if ( !$to_user->id ) {
                return new HtmlResponse("We can't create a ticket if we can't assign to anybody '{$to}'");
            }

            // Try to guess the Account
            $account = new Account();
            $accounts = $account->get_by_user( $from_user->id );
            if ( $accounts ) {
                $account = reset($accounts);
            } else {
                $accounts = $account->get_by_authorized_user( $from_user->id );
                $account = reset($accounts);
            }

            // Create Ticket
            $ticket = new Ticket();
            $ticket->summary = $subject;
            $ticket->message = $body;
            $ticket->user_id = $from_user->id;
            $ticket->assigned_to_user_id = $to_user->id;
            $ticket->website_id = $account ? $account->id : null;
            $ticket->create();
        }

        // link attachments to ticket or comment
        $file = new File( 'retailcatalog.us' );

        $ticket_upload_ids = [];
        foreach( $attachments as $attachment ) {
            $ticket_upload = new TicketUpload();
            $directory = $ticket->ticket_id . '/';
            $file_name = $attachment['name'];
            $ticket_upload->key = $directory . $file_name;
            $ticket_upload->create();

            $ticket_upload_ids[] = $ticket_upload->id;
            $file->upload_file( $attachment['path'], $ticket_upload->key, 'attachments/' );
        }

        if ( $ticket_upload_ids ) {
            $ticket_upload = new TicketUpload();
            if ( isset($ticket_comment) ) {
                $ticket_upload->add_comment_relations($ticket_comment->id, $ticket_upload_ids);
            } else {
                $ticket_upload->add_relations($ticket->id, $ticket_upload_ids);
            }
        }
        @rmdir($upload_dir);

        return new HtmlResponse('');
    }

    /**
     * Pipe deploy
     *
     * @return HtmlResponse
     */
    protected function deploy() {
        library( 'email/rfc822-addresses' );
        library( 'email/mime-parser-class' );

        $email_content = file_get_contents( 'php://stdin' );

        // Response
        $response = new HtmlResponse( '' );

        // Create mime
        $mime = new mime_parser_class();
        $mime->ignore_syntax_errors = 1;

        $mime->Decode( array( 'Data' => $email_content ), $emails );
        $email = $emails[0];

        // Make sure it's from Wercker
        if ( 'alerts@wercker.com' != $email['ExtractedAddresses']['from:'][0]['address'] )
            return $response;

        // Get data
        list( $repo, $message ) = explode( ':', $email['Headers']['subject:'] );

        // Make sure the build passed
        if ( 'passed.' != substr( $message, -7 ) )
            return $response;

        // Determine what repo passed
        switch ( $repo ) {
            case 'KerryJones/Imagine-Retailer':
                if ( !stristr( $message, 'release-' ) )
                    return $response;

                $server = new Server();
                $server->get(Server::SERVER_RACKSPACE_WEB_1);

                // SSH Connection
                $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
                ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

                // Build
                ssh2_exec( $ssh_connection, "sudo phing -verbose -f /gsr/build/backend-testing/build.xml" );
            break;

            case 'KerryJones/IMR-Site':
                if ( !stristr( $message, 'development' ) )
                    return $response;

                $server = new Server();
                $server->get(Server::SERVER_RACKSPACE_WEB_1);

                // SSH Connection
                $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
                ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

                // Setup as root
                ssh2_exec( $ssh_connection, "sudo su -" );

                // Build
                ssh2_exec( $ssh_connection, "sudo phing -verbose -f /gsr/build/gsr-site-testing/build.xml" );
            break;

            case 'KerryJones/Grey-Suit-Retail':
                if ( !stristr( $message, 'development' ) )
                    return $response;

                $server = new Server();
                $server->get(Server::SERVER_RACKSPACE_WEB_3);

                // SSH Connection
                $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
                ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

                // Setup as root
                ssh2_exec( $ssh_connection, "sudo su -" );

                // Build
                ssh2_exec( $ssh_connection, "sudo phing -verbose -f /gsr/build/backend-testing/build.xml" );
            break;

            case 'KerryJones/GSR-Site':
                if ( !stristr( $message, 'development' ) )
                    return $response;

                $server = new Server();
                $server->get(Server::SERVER_RACKSPACE_WEB_3);

                // SSH Connection
                $ssh_connection = ssh2_connect( Config::server('ip', $server->ip), Config::server('port', $server->ip) );
                ssh2_auth_password( $ssh_connection, Config::server('username', $server->ip), Config::server('password', $server->ip) );

                // Setup as root
                ssh2_exec( $ssh_connection, "sudo su -" );

                // Build
                ssh2_exec( $ssh_connection, "sudo phing -verbose -f /gsr/build/gsr-site-testing/build.xml" );
            break;

            default:
                return $response;
            break;
        }

        return $response;
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
        $reply_length = strpos( $body, '******************* Reply Above This Line *******************' );
        if ( $reply_length === false ) {
            $reply_length = strlen($body);
        }
        $body = nl2br( substr( $body, 0, $reply_length ) );
        $reach_id = (int) preg_replace( '/.*Reach #([0-9]+).*/', '$1', $subject );
        if ( !$reach_id ) {
            $reach_id = (int) preg_replace( '/.*Quote #([0-9]+).*/', '$1', $subject );
        }

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

        // Send account owner an email
        $account = new Account();
        $account->get( $reach->website_id );
        $account_owner = new User();
        $account_owner->get( $account->user_id );

        mail( $account_owner->email, "New Response on Reach #{$reach_id}", "<p>A new response from the customer has been received. See message below:</p><p><strong>Original Message:</strong><br />" . $reach->message . "</p><p><strong>Client Response:</strong><br />{$body}</p><p><a href='{$reach_url}'>{$reach_url}</a></p>", $headers );

        // We don't want any response -- including headers, to be sent out
        exit;

        return new HtmlResponse( '' );
    }

    /**
     * Pipe Note
     *
     * @return HtmlResponse
     */
    protected function note() {
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
        $from = $email['ExtractedAddresses']['from:'][0];
        $to = preg_replace( '/.+for ([^;]+).+/', '$1', $email['Headers']['received:'] );

        // if Headers Received is array, $to is an array too, we have to get the email from there
        if ( is_array( $to ) ) {
            foreach ( $to as $t ) {
                if ( stripos( $t, 'notes' ) !== false ) {
                    $to = $t;
                    break;
                }
            }
        }

        list( $username, $domain ) = explode ( '@', $to );
        list ( $username, $tag_domain ) = explode( '+', $username );

        fn::mail('gabriel@greysuitretail.com', 'Note PIPE', json_encode(array("from" => $from, "to" => $to, "subject" => $subject, "username" => $username, "domain" => $domain, "tag_domain" => $tag_domain)));

        $body = ( empty( $email['Body'] ) ) ? $email['Parts'][0]['Body'] : $email['Body'];
        $length = strpos( $body, '>> ' );
        $body = ( $length ) ? substr( $body, 0, $length ) : substr( $body, 0 );
        $body = nl2br( $body );

        // Get the first website
        $account = new Account();
        $account->get_by_domain( $tag_domain );

        if ( !$account->id ) {
            // Try variation one
            $tag_domain = str_replace( 'www.', '', $tag_domain );

            $account->get_by_domain( $tag_domain );

            if ( !$account->id ) {
                // Try variation two
                $tag_domain = preg_replace( '/(.+?)(?:\.[a-zA-Z]{2,4}){1,2}$/', '$1', $tag_domain );

                $account->get_by_domain( $tag_domain );

                if ( !$account->id )
                    exit;
            }
        }

        // Try to get the user that sent the email
        $user = new User( 'admin' == SUBDOMAIN );
        $user->get_by_email( $from['address'] );

        // Determine the user id
        if ( !$user->id ) {
            $contact_name = $from['name'];

            if ( empty( $contact_name ) )
                $contact_name = $from['address'];

            $user->contact_name = $contact_name;
            $user->email = $from['address'];
            $user->role = User::ROLE_AUTHORIZED_USER;

            // Create
            $user->create();

            // Set password
            $user->set_password( md5( microtime() ) );
        }

        // Create note
        $account_note = new AccountNote();
        $account_note->website_id = $account->id;
        $account_note->user_id = $user->id;
        $account_note->message = "<strong>Email:</strong> $subject<br /><br />$body";
        $account_note->create();

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