<?php
class SubscribersController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'email-marketing/subscribers/';
        $this->section = 'email-marketing';
        $this->title = _('Subscribers') . ' | ' . _('Email Marketing');
    }

    /**
     * List Subscribers
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function index() {
        if ( !$this->user->account->email_marketing )
            $this->notify( _('You are only able to manage your subscribers. To have full use of the Email Marketing section you can sign up for it by calling our Online Specialists at (800) 549-9206.'), false );

        return $this->get_template_response( 'index' )
            ->kb( 75 )
            ->add_title( _('Subscribers') )
            ->select( 'subscribers', 'subscribed' );
    }

    /**
     * List Unsubscribed
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function unsubscribed() {
        return $this->get_template_response( 'unsubscribed' )
            ->kb( 76 )
            ->add_title( _('Unsubscribers') )
            ->select( 'subscribers', 'unsubscribed' );
    }

    /**
     * Add/Edit
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function add_edit() {
        // Get Coupon
        $email = new Email();
        $email_list = new EmailList();

        $email_id = ( isset( $_GET['eid'] ) ) ? $_GET['eid'] : false;

        if ( $email_id )
            $email->get( $email_id, $this->user->account->id );

        $form = new FormTable( 'fAddEditSubscriber' );

        if ( !$email->id )
            $form->submit( _('Add') );

        $form->add_field( 'title', _('Basic Information') );

        $form->add_field( 'text', _('Name'), 'tName', $email->name )
            ->attribute( 'maxlength', 80 );

        $form->add_field( 'text', _('Email'), 'tEmail', $email->email )
            ->attribute( 'maxlength', 200 )
            ->add_validation( 'req', _('The "Email" field is required') )
            ->add_validation( 'email', _('The "Email" field must contain a valid email') );

        $form->add_field( 'text', _('Phone'), 'tPhone', $email->phone )
            ->attribute( 'maxlength', 20 )
            ->add_validation( 'phone', _('The "Phone" field must contain a valid phone number') );

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Email List Subscriptions') );

        $email_lists = $email_list->get_by_account( $this->user->account->id );
        $lists = $email->get_associations();

        foreach ( $email_lists as $el ) {
            $value = ( in_array( $el->id, $lists ) ) ? '1' : false;
            $form->add_field( 'checkbox', $el->name, 'email-lists[' . $el->id . ']', $value );
        }

        if ( $form->posted() ) {
            $success = true;

            $old_email = $email->email;
            $email->name = $_POST['tName'];
            $email->email = $_POST['tEmail'];
            $email->phone = $_POST['tPhone'];

            $email_list_ids = array_keys( $_POST['email-lists'] );

            // Set Sendgrid
            $settings = $this->user->account->get_settings( 'sendgrid-username', 'sendgrid-password' );
            library('sendgrid-api');
            $sendgrid = new SendGridAPI( $this->user->account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
            $sendgrid->setup_email();

            $email_list = new EmailList();

            if ( $email->id ) {
                $email->save();

                // Delete existing lists
                $email_lists = $email_list->get_by_email( $email->id, $this->user->account->id );

                // Delete frmo Sendgrid
                foreach ( $email_lists as $email_list ) {
                    $sendgrid->email->delete( $email_list->name, $old_email );
                }
            } else {
                $test_email = new Email();
                $test_email->get_by_email( $this->user->account->id, $email->email );

                if ( $test_email->id && 2 == $test_email->status ) {
                    $success = false;
                    $this->notify( _('This email has been unsubscribed by the user.') );
                } else {
                    $email->website_id = $this->user->account->id;
                    $email->status = 1;
                    $email->create();
                }
            }

            if ( $success ) {
                // Get the email lists
                $email_lists = $email_list->get_by_ids( $email_list_ids, $this->user->account->id );

                // Add to Sendgrid
                foreach ( $email_lists as $email_list ) {
                    $sendgrid->email->add( $email_list->name, array( $email->email ) );
                }

                // Add here
                if ( isset( $_POST['email-lists'] ) )
                    $email->add_associations( $email_list_ids );

                $this->notify( _('Your email has been added/updated successfully!') );
                return new RedirectResponse('/email-marketing/subscribers/');
            }
        }

        $form = $form->generate_form();
        $title = ( $email->id ) ? _('Edit') : _('Add');

        return $this->get_template_response( 'add-edit' )
            ->kb( 77 )
            ->select( 'subscribers', 'add-edit' )
            ->add_title( $title . ' ' . _('Subscriber') )
            ->set( compact( 'email', 'form' ) );
    }

    /**
     * Export
     *
     * @return CsvResponse
     */
    protected function export() {
        // Get the email list ID
        $email_list_id = ( isset( $_GET['elid'] ) ) ? $_GET['elid'] : 0;

        $where = ' AND e.`status` = 1 AND e.`website_id` = ' . (int) $this->user->account->id;

        if ( $email_list_id )
            $where .= ' AND ea.`email_list_id` = ' . (int) $email_list_id;

        // Get subscribers
        $email = new Email();
        $subscribers = $email->list_all( array(
            $where
            , array()
            , 'ORDER BY e.`date_created` ASC'
            , 100000
        ) );

        $output[]  = array( 'Email', 'Name', 'Phone' );

        foreach ( $subscribers as $subscriber ) {
            $output[] = array( $subscriber->email, $subscriber->name, $subscriber->phone );
        }

        return new CsvResponse( $output, format::slug( $this->user->account->title ) . '-email-subscribers.csv' );
    }

    /**
     * Import
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function import() {
        $email_list = new EmailList();
        $email_lists = $email_list->get_by_account( $this->user->account->id );

        if ( $this->verified() ) {
            $email = new Email();

            // Set Sendgrid
            $settings = $this->user->account->get_settings( 'sendgrid-username', 'sendgrid-password' );
            library('sendgrid-api');
            $sendgrid = new SendGridAPI( $this->user->account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
            $sendgrid->setup_email();

            $email->complete_import( $this->user->account->id, $sendgrid, explode( '|', $_POST['hEmailLists'] ) );

            $this->notify( _('Your emails have been imported successfully!') );
            return new RedirectResponse( '/email-marketing/subscribers/' );
        }

        $this->resources
            ->css( 'email-marketing/subscribers/import' )
            ->javascript( 'fileuploader', 'email-marketing/subscribers/import' );

        return $this->get_template_response( 'import' )
            ->kb( 78 )
            ->select( 'subscribers', 'import' )
            ->add_title( _('Import') )
            ->set( compact( 'email_lists' ) );
    }

    /***** AJAX *****/

    /**
     * List All
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        $email = new Email();

        $status = (int) $_GET['s'];

        // Set Order by
        $dt->order_by( 'e.`email`', 'e.`name`', 'e.`date_created`' );
        $dt->add_where( " AND e.`status` = $status" );
        $dt->add_where( ' AND e.`website_id` = ' . (int) $this->user->account->id );

        if ( isset( $_GET['elid'] ) )
            $dt->add_where( " AND ea.`email_list_id` = " . (int) $_GET['elid'] );

        $dt->search( array( 'e.`email`' => false, 'e.`name`' => false ) );

        // Get items
        $subscribers = $email->list_all( $dt->get_variables() );
        $dt->set_row_count( $email->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( $status ) {
        	$confirm = _('Are you sure you want to unsubscribe this email? This cannot be undone.');
        	$unsubscribe_nonce = nonce::create( 'unsubscribe' );
        } else {
            $confirm = $unsubscribe_nonce = '';
        }

        /**
         * @var Email $subscriber
         */
        if ( is_array( $subscribers ) )
        foreach ( $subscribers as $subscriber ) {
            $actions = ( $status ) ? ' | <a href="' . url::add_query_arg( array( 'eid' => $subscriber->id, '_nonce' => $unsubscribe_nonce ), '/email-marketing/subscribers/unsubscribe/' ) . '"  title="' . _('Unsubscribe') . '" ajax="1" confirm="' . $confirm . '">' . _('Unsubscribe') . '</a>' : '';
            $date = new DateTime( $subscriber->date );

            $data[] = array(
                $subscriber->email . '<br /><div class="actions"><a href="' . url::add_query_arg( 'eid', $subscriber->id, '/email-marketing/subscribers/add-edit/' ) . '" title="' . _('Edit') . '">' . _('Edit') . '</a>' . $actions . '</div>'
                , $subscriber->name
                , $date->format( 'F jS, Y g:ia' )
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete
     *
     * @return AjaxResponse
     */
    protected function unsubscribe() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have everything right
        $response->check( isset( $_GET['eid'] ), _('You cannot unsubscribe this subscriber') );

        if ( $response->has_error() )
            return $response;

        // Remove
        $email = new Email();
        $email->get( $_GET['eid'], $this->user->account->id );
        $email->remove_all( $this->user->account );

        // Set Sendgrid
        $settings = $this->user->account->get_settings( 'sendgrid-username', 'sendgrid-password' );
        library('sendgrid-api');
        $sendgrid = new SendGridAPI( $this->user->account, $settings['sendgrid-username'], $settings['sendgrid-password'] );
        $sendgrid->setup_email();

        $email_list = new EmailList();
        $email_lists = $email_list->get_by_email( $email->id, $this->user->account->id );

        foreach ( $email_lists as $email_list ) {
            $sendgrid->email->delete( $email_list->name, $email->email );
        }

        // Redraw the table
        jQuery('.dt:first')->dataTable()->fnDraw();

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Upload File
     *
     * @return AjaxResponse
     */
    protected function import_subscribers() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Upload file
        $uploader = new qqFileUploader( array( 'csv', 'xls' ), 26214400 );
        $result = $uploader->handleUpload( 'gsrs_' );

        // Setup variables
        $file_extension = strtolower( f::extension( $_GET['qqfile'] ) );

        switch ( $file_extension ) {
        	case 'xls':
        		// Load excel reader
        		library('Excel_Reader/Excel_Reader');
        		$er = new Excel_Reader();
        		// Set the basics and then read in the rows
        		$er->setOutputEncoding('ASCII');
        		$er->read( $result['file_path'] );

        		$rows = $er->sheets[0]['cells'];
        		$index = 1;
        	break;

        	case 'csv':
        		// Make sure it's opened properly
        		$response->check( $handle = fopen( $result['file_path'], "r"), _('An error occurred while trying to read your file.') );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;

        		// Loop through the rows
        		while( $row = fgetcsv( $handle ) ) {
        			$rows[] = $row;
        		}

        		// Close the file
        		fclose( $handle );
        		$index = 0;
        	break;

        	default:
        		// Display an error
        		$response->check( false, _('Only CSV and Excel file types are accepted. File type: ') . $file_extension );

                // If there is an error or now user id, return
                if ( $response->has_error() )
                    return $response;
            break;
        }

        $response->check( is_array( $rows ), _('There were no emails to import') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        /**
         * Loop through emails
         *
         * @var int $index
         * @var string $name_column
         * @var array $rows
         * @var array $emails
         */
        foreach ( $rows as $r ) {
            // Determine the column being used for name or email
            if ( !isset( $email_column ) || !isset( $name_column ) )
            if ( stristr( $r[0 + $index], 'name' ) && stristr( $r[1 + $index], 'email' ) ) {
                $email_column = 1 + $index;
                $name_column =  0 + $index;
                continue;
            } else {
                $email_column = 0 + $index;
                $name_column = 1 + $index;

                if ( stristr( $r[0 + $index], 'email' ) && stristr( $r[1 + $index], 'name' ) )
                    continue;
            }

            // If there is an invalid email, skip it
            if ( empty( $r[$email_column] ) || 0 == preg_match( "/^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)\$/", $r[$email_column] ) )
                continue;

            // Create emails
            $emails[] = array( 'email' => $r[$email_column], 'name' => ( isset( $r[$name_column] ) ? $r[$name_column] : '' ) );
        }

        $email = new Email();
        $email->import_all( $this->user->account->id, $emails );

        // Set variables
        $last_ten_emails = array_slice( $emails, 0, 10 );
        $email_html = '';

        // Create HTML
        foreach ( $last_ten_emails as $e ) {
        	$email_html .= '<tr><td>' . $e['email'] . '</td><td>' . $e['name'] . '</td></tr>';
        }

        // Assign it to the table
        jQuery('#tUploadedSubcribers')->append( $email_html );

        // Hide the main view
        jQuery('#dDefault')->hide();

        // Show the next table
        jQuery('#dUploadedSubscribers')->show();

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}