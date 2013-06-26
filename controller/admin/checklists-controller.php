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

        $this->resources
            ->css( 'checklists/list' )
            ->javascript( 'checklists/list' );

        // Reset any defaults
        unset( $_SESSION['checklists'] );

        return $this->get_template_response( 'index' )
            ->kb( 134 )
            ->select( 'checklists', 'view' );
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


        $this->resources
            ->css( 'checklists/checklist' )
            ->javascript( 'checklists/checklist' );

        return $this->get_template_response( 'checklist' )
            ->kb( 22 )
            ->add_title( _('View') )
            ->select( 'checklists', 'edit' )
            ->set( compact( 'checklist', 'items' ) );
    }

    /**
     * Manage
     *
     * @return TemplateResponse
     */
    protected function manage() {
        // Instantiate classes
        $checklist_section = new ChecklistSection();
        $checklist_item = new ChecklistItem();

        // Get variables
        $checklist_sections = $checklist_section->get_all();
        $checklist_items = $checklist_item->get_all();

        // Declare arrays
        $items = $sections = array();

        // Put all the sections & itemsitems in the proper place

        /**
         * @var ChecklistSection $cs
         */
        foreach ( $checklist_sections as $cs ) {
            $sections[$cs->id] = $cs;
        }
        /**
         * @var ChecklistItem $ci
         */
        foreach ( $checklist_items as $ci ) {
            $items[$ci->checklist_section_id][$ci->id] = $ci;
        }

        // Save any updates
        if ( $this->verified() ) {
            $section_sequences = $item_sequences = array();
            $sequence = 0;

            // Create sections sequencing
            foreach ( $_POST['sections'] as $checklist_section_id => $value ) {
                $section_sequences[$checklist_section_id] = $sequence;

                // Get the section
                if ( !isset( $sections[$checklist_section_id] ) ) {
                    $sections[$checklist_section_id] = new ChecklistSection();
                    $sections[$checklist_section_id]->get( $checklist_section_id );
                }

                $sequence++;
            }

            // Create sequencing for items
            $sequence = 0;
            foreach ( $_POST['items'] as $checklist_section_id => $item_array ) {
                foreach ( $item_array as $checklist_item_id => $value_array ) {
                    $item_sequences[$checklist_section_id][$checklist_item_id] = $sequence;

                    // Get the item
                    if ( !isset( $items[$checklist_section_id][$checklist_item_id] ) ) {
                        $items[$checklist_section_id][$checklist_item_id] = new ChecklistItem();
                        $items[$checklist_section_id][$checklist_item_id]->get( $checklist_item_id );
                    }

                    $sequence++;
                }
            }

            /**
             * @var ChecklistSection $section
             */
            foreach ( $sections as $section ) {
                if ( array_key_exists( $section->checklist_section_id, $_POST['sections'] ) ) {
                    $section->name = $_POST['sections'][$section->checklist_section_id];
                    $section->sequence = $section_sequences[$section->checklist_section_id];
                    $section->status = 1;
                } else {
                    $section->status = 0;
                }

                $section->save();

                // Remove sections
                unset( $_POST['sections'][$section->checklist_section_id] );
            }

            foreach ( $items as $item_array ) {
                /**
                 * @var ChecklistItem $item
                 */
                foreach ( $item_array as $item ) {
                    if ( array_key_exists( $item->checklist_section_id, $_POST['items'] ) && array_key_exists( $item->id, $_POST['items'][$item->checklist_section_id] ) ) {
                        $item->name = $_POST['items'][$item->checklist_section_id][$item->id]['name'];
                        $item->assigned_to = $_POST['items'][$item->checklist_section_id][$item->id]['assigned_to'];
                        $item->sequence = $item_sequences[$item->checklist_section_id][$item->id];
                        $item->status = 1;
                    } else {
                        $item->status = 0;
                    }

                    $item->save();
                }
            }

            // Give notification
            $this->notify( _('The Master Checklist have been successfully updated!') );

            // Reget the sections after sequencing
            $sections = $checklist_section->get_all();
            $checklist_items = $checklist_item->get_all();
            $items = array();

            /**
             * @var ChecklistItem $ci
             */
            foreach ( $checklist_items as $ci ) {
                $items[$ci->checklist_section_id][] = $ci;
            }
        }

        $this->resources
            ->css( 'checklists/manage' )
            ->javascript( 'checklists/manage' );

        // Get response
        return $this->get_template_response( 'manage' )
            ->kb( 23 )
            ->set( compact( 'sections', 'items' ) )
            ->select( 'checklists', 'manage' );

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
    protected function add_note() {
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
    protected function delete_note() {
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

        $checklist_website_item->save();

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
        $dt->order_by( 'days_left', 'w.`title`', 'u2.`contact_name`', 'c.`type`', 'c.`date_created`' );
        $dt->search( array( 'w.`title`' => false ) );

        $not = ( isset( $_SESSION['checklists']['completed'] ) && '1' == $_SESSION['checklists']['completed'] ) ? 'NOT ' : '';

        $dt->add_where( " AND c.`checklist_id` {$not}IN ( SELECT cwi.`checklist_id` FROM `checklist_website_items` AS cwi LEFT JOIN `checklist_items` AS ci ON ( ci.`checklist_item_id` = cwi.`checklist_item_id` ) WHERE cwi.`checked` = 0 AND ci.`status` = 1 )" );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission( User::ROLE_ADMIN ) )
			$dt->add_where( ' AND u.`company_id` = ' . (int) $this->user->company_id );

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
                ,  '<a href="/checklists/checklist/?cid=' . $c->id . '" title="' . _('View Checklist') . '">' . $c->title . '</a>'
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
            $user->save();

            // Redraw the table
            jQuery('.dt:first')->dataTable()->fnDraw();

            // Add the response
            $response->add_response( 'jquery', jQuery::getResponse() );
        }

        return $response;
    }

    /**
     * Manage Checklists - Add Checklist Section
     *
     * @return AjaxResponse
     */
    protected function add_section() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create checklist section
        $checklist_section = new ChecklistSection();
        $checklist_section->status = 0;
        $checklist_section->create();

        jQuery('#section-template')
            ->clone()
            ->attr( 'id', 'section-' . $checklist_section->id )
            ->find( 'input:first' )
                ->attr( 'name', 'sections[' . $checklist_section->id . ']' )
            ->parents( '.section:first' )
            ->find( 'a.add-section-item:first')
                ->attr( 'href', url::add_query_arg( array( '_nonce' => nonce::create( 'add_item' ), 'csid' => $checklist_section->id ), '/checklists/add-item/' ) )
                ->attr( 'ajax', '1' )
            ->parents( '.section:first' )
            ->sortable( array(
		        'items' => '.item'
                , 'cancel' => 'input'
                , 'cursor' => 'move'
                , 'placeholder' => 'item-placeholder'
                , 'forcePlaceholderSize' => true
                , 'handle' => 'a.handle'
	            )
            )
            ->sparrow()
            ->appendTo( '#checklist-sections' );

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }



    /**
     * Manage Checklists - Add Checklist Item
     *
     * @return AjaxResponse
     */
    protected function add_item() {
        // Verify the nonce
        $response = new AjaxResponse( $this->verified() );

        // Make sure we have the proper parameters
        $response->check( isset( $_GET['csid'] ), _('Failed to add checklist item') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Create checklist item
        $checklist_item = new ChecklistItem();
        $checklist_item->checklist_section_id = $_GET['csid'];
        $checklist_item->status = 0;
        $checklist_item->create();

        // Now add it on
        jQuery('#item-template')
            ->clone()
            ->removeAttr('id')
            ->find('input:first')
                ->attr( 'name', 'items[' . $checklist_item->checklist_section_id . '][' . $checklist_item->id . '][name]')
            ->next()
                ->attr( 'name', 'items[' . $checklist_item->checklist_section_id . '][' . $checklist_item->id . '][assigned_to]')
            ->parents('.item:first')
            ->sparrow()
            ->appendTo( '#section-' . $checklist_item->checklist_section_id . ' .section-items:first');

        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }
}