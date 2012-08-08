<?php
class AccountsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        // Tell what is the base for all login
        $this->view_base = 'accounts/';
        $this->section = 'accounts';
    }

    /**
     * List Accounts
     *
     * @return TemplateResponse
     */
    protected function index() {
        $template_response = $this->get_template_response( 'index' );
        $template_response->select( 'accounts', 'view' );

        return $template_response;
    }

    /**
     * Add account
     *
     * @return TemplateResponse
     */
    protected function add() {
        $template_response = $this->get_template_response( 'add' );
        $template_response->select( 'accounts', 'add' );
        $template_response->add_title( _('Add') );

        // Instantiate Objects
        $account = new Account();

        $user_array = $this->user->get_all();
        $os_users[''] = _('-- Select Online Specialist --');
        $users[''] = _('-- Select User --');

        /**
         * @var User $user
         */
        foreach ( $user_array as $user ) {
            if ( $user->role >= 7 )
                $os_users[$user->id] = $user->contact_name;

            $users[$user->id] = $user->contact_name;
        }

        // Create new form table
        $ft = new FormTable( 'fAddAccount' );

        $ft->submit( _('Add') );

        $ft->add_field( 'text', _('Title'), 'tTitle' )
            ->attribute( 'maxlength', 80 )
            ->add_validation( 'req', _('The "Name" field is required') );

        $ft->add_field( 'text', _('Domain'), 'tDomain' )
            ->attribute( 'maxlength', 100 );

        $ft->add_field( 'select', _('User'), 'sUserID' )
            ->options( $users )
            ->add_validation( 'req', _('The "User" field is required') );

        $ft->add_field( 'select', _('Online Specialist'), 'sOnlineSpecialistID' )
            ->options( $os_users )
            ->add_validation( 'req', _('The "Online Specialist" field is required') );

        $ft->add_field( 'select', _('Type'), 'sType' )
            ->options( array(
                _('Furniture') => _('Furniture')
                , _('RTO') => _('RTO')
                , _('EVR') => _('EVR')
                , _('High Impact') => _('High Impact')
            ));

        // Update the account if posted
        if ( $ft->posted() ) {
            $account->user_id = $_POST['sUserID'];
            $account->os_user_id = $_POST['sOnlineSpecialistID'];
            $account->domain = $_POST['tDomain'];
            $account->title = $_POST['tTitle'];
            $account->type = $_POST['sType'];
            $account->create();

            $this->notify( _('Your account was successfully created!') );

            return new RedirectResponse('/accounts/');
        }

        $template_response->set( 'form', $ft->generate_form() );

        return $template_response;
    }

    /**
     * Edit Account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        $account = new Account;
        $account->get( $_GET['aid'] );

        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        $this->resources->javascript('accounts/edit');
        $this->resources->css('accounts/edit');

        $template_response = $this->get_template_response('edit');
        $template_response->select( 'accounts', 'edit' );
        $template_response->set( 'account', $account );

        return $template_response;
    }

    /**
     * Edit DNS for an account
     *
     * @return TemplateResponse|RedirectResponse
     */
    public function dns() {
        // Make sure they can be here
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        if ( !$this->user->has_permission(10) )
            return new RedirectResponse('/accounts/edit/?aid=' . $_GET['aid']);

        // Initialize classes
        $account = new Account;
        $account->get( $_GET['aid'] );

        // Make sure they have permission
        if ( !$this->user->has_permission(8) && $account->company_id != $this->user->company_id )
            return new RedirectResponse('/accounts/');

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

        $v = new Validator( 'fEditDNS' );

        // Declare variables
        $domain_name = url::domain( $account->domain, false );
        $full_domain_name = $domain_name . '.';
        $zone_id = $account->get_settings( 'r53-zone-id' );
        $errs = false;

        // Handle form actions
        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty( $errs ) && isset( $_POST['changes'] ) && is_array( $_POST['changes'] ) ) {
                $changes = array();
                $change_count = count( $_POST['changes']['name'] );

                for( $i = 0; $i < $change_count; $i++ ) {
                    // Get the records
                    $records = format::trim_deep( explode( "\n", $_POST['changes']['records'][$i] ) );

                    switch( $_POST['changes']['action'][$i] ) {
                        default:
                            continue;
                        break;

                        case '1':
                            $action = 'CREATE';
                        break;

                        case '0':
                            $action = 'DELETE';
                        break;
                    }

                    $changes[] = $r53->prepareChange( $action, $_POST['changes']['name'][$i], $_POST['changes']['type'][$i], $_POST['changes']['ttl'][$i], $records );
                }

                $response = $r53->changeResourceRecordSets( $zone_id, $changes );
            }
        }

        // Do an action like create DNS
        if ( isset( $_GET['a'] ) )
        switch ( $_GET['a'] ) {
            case 'create':
                // We already have something, stop!
                if ( !empty( $zone_id ) )
                    break;

                // Create the zone file
                $zone = $r53->createHostedZone( $domain_name, md5(microtime()) );

                $zone_id = $zone['HostedZone']['Id'];

                // Set the settings
                $account->set_settings( array( 'r53-zone-id' => $zone_id ) );

                // Defaults
                $changes = array(
                    $r53->prepareChange( 'CREATE', $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', $full_domain_name, 'MX', '14400', '0 mail.' . $full_domain_name )
                    , $r53->prepareChange( 'CREATE', 'mail.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
                    , $r53->prepareChange( 'CREATE', 'www.' . $full_domain_name, 'CNAME', '14400', $full_domain_name )
                    , $r53->prepareChange( 'CREATE', 'ftp.' . $full_domain_name, 'A', '14400', '199.79.48.137' )
                    , $r53->prepareChange( 'CREATE', 'cpanel.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'whm.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'webmail.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                    , $r53->prepareChange( 'CREATE', 'webdisk.' . $full_domain_name, 'A', '14400', '199.79.48.138' )
                );

                $response = $r53->changeResourceRecordSets( $zone_id, $changes );
            break;

            case 'delete':
                // We don't have something, stop!
                if ( empty( $zone_id ) )
                    break;

                // Delete the zone
                if ( !$r53->deleteHostedZone( $zone_id ) ) {
                    $error = $r53->getError();

                    if ( 'NoSuchHostedZone' != $error['code'] ) {
                        $errs = $error['error'];
                        break;
                    }
                }

                // Update the settings
                $account->set_settings( array( 'r53-zone-id' => '' ) );

                $zone_id = '';
            break;
        }

        if ( !empty( $zone_id ) ) {
            $r53->getHostedZone( $zone_id );
            $records = $r53->listResourceRecordSets( $zone_id );

            if ( is_array( $records['ResourceRecordSets'] ) ) {
                lib( 'misc/dns-sort' );
                new DNSSort( $records['ResourceRecordSets'] );
            }
        }

        // Put out notifications
        if ( isset( $response ) ) {
            if ( $response ) {
                $this->notify( _('Your DNS Zone file has been successfully updated!') );
            } else {
                $errs .= _('There was an error while trying to update your DNS Zone file. Please try again.');
            }
        }


        // Keep the resources that we need
        $this->resources->javascript('accounts/dns');
        $this->resources->css('accounts/dns');

        $template_response = $this->get_template_response('dns');
        $template_response->select( 'accounts', 'edit' );
        $template_response->set( compact( 'account', 'zone_id', 'errs', 'domain_name', 'full_domain_name', 'records' ) );

        return $template_response;
    }

    /***** AJAX *****/

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