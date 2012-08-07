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
     * Edit Account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function edit() {
        if ( !isset( $_GET['aid'] ) )
            return new RedirectResponse('/accounts/');

        $account = new Account;
        $account->get( $this->user, $_GET['aid'] );

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
        $account->get( $this->user, $_GET['aid'] );

        library('r53');
        $r53 = new Route53( Config::key('aws_iam-access-key'), Config::key('aws_iam-secret-key') );

        $v = new Validator( $this->resources, 'fEditDNS' );

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
}