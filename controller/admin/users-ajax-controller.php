<?php
class UsersAjaxController extends BaseController {
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
        // Get response
        $dt = new DataTableResponse( $this->user );

        // Set Order by
        $dt->order_by( 'a.`contact_name`', 'a.`email`', 'phone', 'b.`domain`', 'a.`role`' );
        $dt->search( array( 'a.`contact_name`' => true, 'a.`email`' => true, 'b.`domain`' => true ) );

        // If they are below 8, that means they are a partner
		if ( !$this->user->has_permission(8) )
			$dt->add_where( ' AND a.`company_id` = ' . (int) $this->user->company_id );

        // Get accounts
        $users = $this->user->list_all( $dt->get_variables() );
        $dt->set_row_count( $this->user->count_all( $dt->get_count_variables() ) );

        // Set initial data
        $data = false;
        $confirm_delete = _('Are you sure you want to delete this user? This cannot be undone.');
        $delete_user_nonce = nonce::create( 'delete-user' );

        if ( is_array( $users ) )
        foreach ( $users as $u ) {
            switch ( $u->role ) {
                case 1:
                    $role = _('Authorized User');
                break;

                case 5:
                    $role = _('Basic Account');
                break;

                case 6:
                    $role = _('Marketing Specialist');
                break;

                case 7:
                    $role = _('Online Specialist');
                break;

                case 8:
                    $role = _('Admin');
                break;

                case 10:
                    $role = _('Super Admin');
                break;

                default:
                    $role = 'Unknown - ' . $u->role;
                break;
            }

            $data[] = array(
                $u->contact_name . '<div class="actions">' .
                    '<a href="/users/edit/?uid=' . $u->id . '" title="' . $u->contact_name . '">' . _('Edit') . '</a> | ' .
                    '<a href="' . url::add_query_arg( array( 'uid' => $u->id, '_nonce' => $delete_user_nonce ), '/users-ajax/delete/' ) . '" title="' . _('Delete User') . '" ajax="1" confirm="' . $confirm_delete . '">' . _('Delete') . '</a></div>'
                , '<a href="mailto:' . $u->email . '" title="' . _('Email User') . '">' . $u->email . '</a>'
                , $u->phone
                , '<a href="http://' . $u->domain . '/" target="_blank">' . $u->domain . "</a>"
                , $role
            );
        }

        // Send response
        $dt->set_data( $data );

        return $dt;
    }
}