<?php
class AccountsAjaxController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

    /**
     * List Accounts
     *
     * @return DataTableResponse
     */
    protected function list_all() {
        // Get Models
        $account = new Account();
        $checklist = new Checklist();

        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'b.`company_id`', 'a.`title`', 'b.`contact_name`', 'c.`contact_name`' );

        // Add Where's
        if ( isset( $_SESSION['accounts']['state'] ) ) {
            // Live accounts
            $dt->add_where( ( -1 == $_SESSION['accounts']['state'] ) ? ' AND a.`status` = 0' : ' AND a.`status` = 1 AND a.`live` = ' . $_SESSION['accounts']['state'] );
        } else {
            $dt->add_where( ' AND a.`status` = 1' );
        }

        // Add search
        if ( isset( $_SESSION['accounts']['search'] ) ) {
            $_GET['sSearch'] = $_SESSION['accounts']['search'];
            $dt->search( array( 'a.`title`' => false, 'a.`domain`' => false, 'b.`contact_name`' => false, 'c.`contact_name`' => false ) );
        }

        if ( 251 == $this->user->id ) {
            $dt->add_where( ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $this->user->company_id . ' )' );
        } else {
            // If they are below 8, that means they are a partner
            if ( !$this->user->has_permission(8) )
                $dt->add_where( ' AND b.`company_id` = ' . $this->user->company_id );
        }

		// What other sites we might need to omit
		$omit_sites = ( !$this->user->has_permission(8) ) ? ', 96, 114, 115, 116' : '';

		// Form the where
		$dt->add_where( " AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" );


        // Get accounts
        $accounts = $account->list_all( $dt->get_variables() );
        $dt->set_row_count( $account->count_all( $dt->get_count_variables() ) );

        // Get account ids with incomplete checklists
        $incomplete_checklists = $checklist->get_incomplete();

        // Set initial data
        $data = false;

        if ( is_array( $accounts ) )
        foreach ( $accounts as $a ) {
            $image = '<img src="/images/icons/companies/' . $a->company_id . '.gif" alt="" width="24" height="24" />';

            // Get the store name if necessary
            $store_name = ( $a->title == $a->store_name || empty( $a->store_name ) ) ? '' : ' (' . $a->store_name . ')';

            // Get the phone
            $contact_title = ( empty( $a->phone ) ) ? _('No Phone') : $a->phone;

            $title = '<a href="http://' . $a->domain . '/" target="_blank"><strong title="' . $a->domain . ' - ' . $a->online_specialist . '">' . $a->title . $store_name . '</strong></a><br />';
            $title .= '<span class="web-actions" style="display: block"><a href="/accounts/edit/?aid=' . $a->id . '" title="' . _('Edit') . ' ' . $a->title . '">' . _('Edit') . '</a> | ';
            $title .= '<a href="/accounts/control/?aid=' . $a->id . '" title="' . _('Control') . ' ' . $a->title . '" target="_blank">' . _('Control Account') . '</a> | ';
            $title .= '<a href="/users/control/?uid=' . $a->user_id . '" title="' . _('Control User') . '" target="_blank">' . _('Control User') . '</a> | ';
            $title .= '<a href="/accounts/notes/?aid=' . $a->id . '" title="' . _('Notes') . '" target="_blank">' . _('Notes') . '</a>';

            if ( isset( $incomplete_checklists[$a->id] ) )
                $title .= ' | <a href="/checklists/view/?cid=' . $incomplete_checklists[$a->id] . '" title="' . _('Checklists') . '" target="_blank">' . _('Checklist') . '</a>';

            $title .= '</span>';

            $data[] = array(
                $image
                , $title
                , '<a href="/users/edit/?uid=' . $a->user_id . '" title="' . $contact_title . '">' . $a->contact_name . '</a>'
                , $a->online_specialist
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}