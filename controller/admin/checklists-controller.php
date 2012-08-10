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

        $this->resources->css( 'checklists/list' );
        $this->resources->javascript( 'checklists/list' );

        // Reset any defaults
        unset( $_SESSION['checklists'] );

        return $template_response;
    }

    /***** AJAX *****/

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
                ,  '<a href="/checklists/view/?cid=' . $c->id . '" title="' . _('View Checklist') . '">' . $c->title
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