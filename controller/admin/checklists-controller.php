<?php
class ChecklistsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'checklists/';
        $this->section = 'checklists';
    }

    /**
     * List Checklists
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' )
            ->select( 'checklists', 'view' );

        $this->resources
            ->css( 'checklists/list' )
            ->javascript( 'checklists/list' );

        // Reset any defaults
        unset( $_SESSION['checklists'] );

        return $template_response;
    }

    /**
     * Checklist
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function checklist() {
        // Determine if there is a checklist
        $checklist_id = ( isset( $_GET['cid'] ) ) ? (int) $_GET['cid'] : false;

        if ( !$checklist_id )
            return new RedirectResponse('/checklists/');

        // Instantiate classes
        $checklist = new Checklist();
        $checklist_item = new ChecklistItem();

        // Get models
        $checklist->get( $checklist_id );
        $items_array = $checklist_item->get_by_checklist( $checklist_id );

        $items = array();

        /**
         * @var ChecklistItem $ia
         */
        foreach ( $items_array as $ia ) {
            $items[$ia->section][] = $ia;
        }

        $template_response = $this->get_template_response( 'checklist' )
            ->add_title( _('View') )
            ->select( 'checklists', 'edit' )
            ->set( compact( 'checklist', 'items' ) );

        $this->resources
            ->css( 'checklists/checklist' )
            ->javascript( 'checklists/checklist' );

        return $template_response;
    }

    /***** AJAX *****/

    /**
     * Display Notes
     *
     * @return CustomResponse
     */
    protected function notes() {
        // Get the company_id if there is one
        $checklist_website_item_id = ( isset( $_GET['cwiid'] ) ) ? (int) $_GET['cwiid'] : false;

        $verified = $this->verified() && $checklist_website_item_id;

        // Send them away -- awkward but we don't care
        if ( !$verified )
            return new RedirectResponse('/checklists/');

        // Setup Models
        $checklist_website_item_note = new ChecklistWebsiteItemNote();

        // Get notes
        $notes = $checklist_website_item_note->get_by_checklist_website_item( $checklist_website_item_id );

        $response = new CustomResponse( $this->resources, 'checklists/notes' );
        $response->set( compact( 'notes', 'checklist_website_item_id' ) );

        return $response;
    }

    /**
     * Add Note
     *
     * @return AjaxResponse
     */
    public function add_note() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['note'] ) && isset( $_POST['hChecklistWebsiteItemId'] ), _('Failed to add note') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create the note
        $checklist_website_item_note = new ChecklistWebsiteItemNote();
        $checklist_website_item_note->checklist_website_item_id = $_POST['hChecklistWebsiteItemId'];
        $checklist_website_item_note->user_id = $this->user->id;
        $checklist_website_item_note->note = $_POST['note'];
        $checklist_website_item_note->create();

        // Add it on
        $date = new DateTime( $checklist_website_item_note->date_created );
        $confirmation = _('Are you sure you want to delete this note? This cannot be undone.');

        $note = '<div id="note-' . $checklist_website_item_note->id . '" class="note">';
        $note .= '<div class="title">';
        $note .= '<strong>' . $this->user->contact_name . '</strong>';
        $note .= '<br />' . $date->format( 'F j, Y g:ia' ) . '<br />';
        $note .= '<a href="' . url::add_query_arg( array( '_nonce' => nonce::create('delete_note'), 'cwinid' => $checklist_website_item_note->id ), '/checklists/delete-note/' ) . '" class="delete-note" title="' . _('Delete') . '" ajax="1" confirm="' . $confirmation . '">' . _('Delete') . '</a>';
        $note .= '</div>';
        $note .= '<div class="note-note">' . $checklist_website_item_note->note . '</div>';
        $note .= '</div>';

        jQuery('#notes')->prepend( $note );

        // Reset form
        jQuery("#note")->val('');

        // Add jQuery response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Delete Note
     */
    public function delete_note() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['cwinid'] ), _('Failed to delete note') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get note
        $checklist_website_item_note = new ChecklistWebsiteItemNote();
        $checklist_website_item_note->get( $_GET['cwinid'] );

        // Delete note from page
        jQuery('#note-' . $checklist_website_item_note->id )->remove();

        // Delete note
        $checklist_website_item_note->delete();

        // Add jquery
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * Update Item
     */
    protected function update_item() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_POST['cwiid'] ) && isset( $_POST['checked'] ), _('Failed to check item') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get checklist website item
        $checklist_website_item = new ChecklistWebsiteItem();
        $checklist_website_item->get( $_POST['cwiid'] );

        // Update it
        if ( ( 'true' == $_POST['checked'] ) ) {
            $checklist_website_item->checked = 1;
            $checklist_website_item->date_checked = dt::now();
        } else {
            $checklist_website_item->checked = 0;
        }

        $checklist_website_item->update();

        // Add jQuery Response
        jQuery('#item-' . $checklist_website_item->id)->toggleClass('done');

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'days_left', 'b.`title`', 'a.`type`', 'a.`date_created`' );
        $dt->search( array( 'b.`title`' => false ) );

        $not = ( isset( $_SESSION['checklists']['completed'] ) && '1' == $_SESSION['checklists']['completed'] ) ? 'NOT ' : '';

        $dt->add_where( " AND a.`checklist_id` {$not}IN ( SELECT `checklist_id` FROM `checklist_website_items` WHERE `checked` = 0 )" );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission(8) )
			$dt->add_where( ' AND c.`company_id` = ' . (int) $this->user->company_id );

        $checklist = new Checklist();

        // Get accounts
        $checklists = $checklist->list_all( $dt->get_variables() );
        $dt->set_row_count( $checklist->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;

        if ( is_array( $checklists ) )
        foreach ( $checklists as $c ) {
            // Determined which color should be used for days left
            switch ( $c->days_left ) {
                case ( $c->days_left < 10 ):
                    $color = 'red';
                break;

                case ( $c->days_left < 20 ):
                    $color = 'orange';
                break;

                default:
                    $color = 'green';
                break;
            }

            $date = new DateTime( $c->date_created );

            $data[] = array(
                '<span class="' . $color . '">' . $c->days_left . '</span>'
                ,  '<a href="/checklists/checklist/?cid=' . $c->id . '" title="' . _('View Checklist') . '">' . $c->title
                , $c->online_specialist
                , $c->type
                , $date->format('F j, Y')
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }

    /**
     * Delete a user
     *
     * @return AjaxResponse
     */
    protected function delete() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() || !isset( $_GET['uid'] ) )
            return $response;

        // Get the user
        $user = new User();
        $user->get( $_GET['uid'] );

        // Deactivate user
        if ( $user->id && 1 == $user->status ) {
            $user->status = 0;
            $user->update();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }
}