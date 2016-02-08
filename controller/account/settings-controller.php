<?php
class SettingsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();

        $this->view_base = 'settings/';
        $this->title = _('Settings');
    }

    /**
     * Modify settings
     *
     * @return TemplateResponse
     */
    protected function index() {
        // Get settings
        if ( $this->user->has_permission( User::ROLE_STORE_OWNER ) && $this->user->account->is_new_template() ) {
            $settings_array = array( 'timezone' );

            $settings = $this->user->account->get_settings( $settings_array );
        }

        $form = new BootstrapForm( 'fSettings' );
        $form->submit( _('Update') );

        $form->add_field( 'title', _('Login Information') );
        $form->add_field( 'row', _('Email') . ':', $this->user->email );

        $form->add_field( 'password', _('Password'), 'tPassword' )
            ->attribute( 'maxlength', 30 );

        $form->add_field( 'password', _('Confirm Password'), 'tRePassword|tPassword' )
            ->attribute( 'maxlength', 30 )
            ->add_validation( 'match', _('The "Password" and "Confirm Password" field must match') );

        $form->add_field( 'blank', '' );
        $form->add_field( 'title', _('Personal Information') );

        $form->add_field( 'text', _('Contact Name'), 'tContactName', $this->user->contact_name )
            ->attribute( 'maxlength', 60 )
            ->add_validation( 'req', _('The "Contact Name" field is required') );

        $store_name = $form->add_field( 'text', _('Store Name'), 'tStoreName', $this->user->store_name )
            ->attribute( 'maxlength', 100 );

        if ( $this->user->role != User::ROLE_AUTHORIZED_USER )
            $store_name->add_validation( 'req', _('The "Store Name" field is required') );

        $form->add_field( 'text', _('Work Phone'), 'tWorkPhone', $this->user->work_phone )
            ->attribute( 'maxlength', 20 )
            ->add_validation( 'phone', _('The "Work Phone" field must contain a valid phone number') );

        $form->add_field( 'text', _('Cell Phone'), 'tCellPhone', $this->user->cell_phone )
            ->attribute( 'maxlength', 20 )
            ->add_validation( 'phone', _('The "Cell Phone" field must contain a valid phone number') );

        if ( $this->user->has_permission( User::ROLE_STORE_OWNER ) && $this->user->account->is_new_template() && isset($settings) ) {
            $form->add_field( 'select', _('Timezone'), 'timezone', $settings['timezone'] )
                ->options( data::timezones( false, false, true ) );
        }

        if ( $form->posted() ) {
            $this->user->contact_name = $_POST['tContactName'];
            $this->user->store_name = $_POST['tStoreName'];
            $this->user->work_phone = $_POST['tWorkPhone'];
            $this->user->cell_phone = $_POST['tCellPhone'];
            $this->user->save();

            if ( !empty( $_POST['tPassword'] ) )
                $this->user->set_password( $_POST['tPassword'] );

            if ( $this->user->has_permission( User::ROLE_STORE_OWNER ) && $this->user->account->is_new_template() && isset($settings_array) ) {
                $new_settings = array();

                foreach ( $settings_array as $k ) {
                    $new_settings[$k] = ( isset( $_POST[$k] ) ) ? $_POST[$k] : '';
                }

                $this->user->account->set_settings( $new_settings );
            }

            $this->notify( _('You have successfully updated your settings!') );
            $this->log( 'update-settings', $this->user->contact_name . ' has updated his settings', $_POST );
        }

        return $this->get_template_response( 'index' )
            ->kb( 116 )
            ->add_title( _('Settings') )
            ->set( array( 'form' => $form->generate_form() ) )
            ->select( 'settings' );
    }

    /**
     * Logo and Phone
     *
     * @return TemplateResponse
     */
    protected function logo_and_phone() {
        if ( $this->verified() ) {
            $new_settings = array();
            $new_settings['logo-title'] = $_POST['tLogoTitle'];
            $new_settings['logo-alt'] = $_POST['tLogoAlt'];
            $new_settings['logo-link'] = $_POST['tLogoLink'];
            $new_settings['phone'] = $_POST['tPhone'];

            $this->user->account->set_settings($new_settings);

            // Clear CloudFlare Cache
            $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

            if ( $cloudflare_zone_id ) {
                library('cloudflare-api');
                $cloudflare = new CloudFlareAPI( $this->user->account );
                $cloudflare->purge( $cloudflare_zone_id );
            }

            // Notification
            $this->notify( _('The "Logo and Phone" section has been updated successfully!' ) );
            $this->log( 'logo-and-phone', $this->user->contact_name . ' has updated logo and phone settings on ' . $this->user->account->title, $_POST['products'] );
        }

        $settings = $this->user->account->get_settings('logo-title', 'logo-alt', 'logo-link', 'website-logo', 'phone');

        $this->resources->javascript( 'fileuploader', 'settings/logo-and-phone' );

        return $this->get_template_response( 'logo-and-phone' )
            ->kb( 117 )
            ->add_title( _('Logo and Phone') )
            ->set( compact( 'settings' ) )
            ->select( 'logo-and-phone' );
    }

    /**
     * Billing Information
     *
     * @return TemplateResponse
     */
    protected function billing_information() {
        $settings = $this->user->account->get_settings('arb-subscription-id', 'arb-subscription-amount', 'arb-subscription-gateway');

        if ( $this->verified() && !empty( $settings['arb-subscription-gateway'] ) ) {
            library('arb-' . $settings['arb-subscription-gateway']);
			
            // Create instance of ARB
            $arb = new arb( $this->user->account->title );

            // Set variables
            $arb->setSubscriptionId($settings['arb-subscription-id']);
            $arb->setAmount( $settings['arb-subscription-amount'] );
            $arb->setTotalOccurrences('9999'); // if omitted, default is 9999(forever)
            $arb->setOrderDetails('Managed Website Monthly Payment');
            $arb->setCustomerId( $this->user->id );
            $arb->setCustomerPhone( $this->user->work_phone );
            $arb->setCustomerEmail( $this->user->email );

            // Set billing information
            $arb->setBillingName( $_POST['first-name'], $_POST['last-name'] );
            $arb->setBillingAddress( $_POST['address'] );
            $arb->setBillingCity( $_POST['city'] );
            $arb->setBillingState( $_POST['state'] ); //full state name can be used (i.e. Massachusetts)
            $arb->setBillingZip( $_POST['zip'] );
            $arb->setBillingCountry('United States'); // optional

            // set the payment details (one of the two options is required)
            $arb->setPaymentDetails( $_POST['ccnum'], $_POST['ccexpy'] . '-' . $_POST['ccexpm'] );

            // Submit the subscription request
            $arb->UpdateSubscriptionRequest();

            // Test and print results
            $success = $arb->success;

            if ( $success ) {
                $this->user->account->set_settings(array('arb-subscription-expiration', $_POST['ccexpm'] . '/' . $_POST['ccexpy']));
                $subject = $this->user->account->title . ' Updated Billing Information';
                $message = $this->user->contact_name . ' has updated the billing information for ' . $this->user->account->title . '.';
                library('sendgrid-api'); SendgridApi::send('david@greysuitretail.com', $subject, $message, 'noreply@greysuitretail.com');
                $this->notify('Your billing information has been successfully updated!');
                $this->log( 'billing-information', $this->user->contact_name . ' updated their billing information on ' . $this->user->account->title );
            } else {
                $this->log( 'billing-information', $this->user->contact_name . ' failed to updated their billing information on ' . $this->user->account->title );
                $this->notify('There was a problem while trying to update your account. A ticket has been submitted and you will be contacted shortly.', false);

                $ticket = new Ticket();
                $ticket->user_id = $this->user->id;
                $ticket->assigned_to_user_id = User::TECHNICAL;
                $ticket->website_id = $this->user->account->id;
                $ticket->summary = 'Billing information update failed';
                $ticket->message = $this->user->contact_name . " tried and failed to update their billing information. The following information is available:\n" . fn::info( $arb->error, false ) . "\n\nMore information:\n" . fn::info( $arb->response );
                $ticket->status = Ticket::STATUS_OPEN;
                $ticket->priority = Ticket::PRIORITY_URGENT;
                $ticket->create();
            }
        }

        return $this->get_template_response( 'billing-information' )
            ->kb( 0 )
            ->add_title( _('Billing Information') )
            ->set( array( 'settings' => $settings ) )
            ->select( 'billing-information' );
    }

    /**
     * Services
     *
     * @return TemplateResponse
     */
    protected function services() {
        $settings = $this->user->account->get_settings('arb-subscription-id', 'arb-subscription-amount', 'arb-subscription-gateway', 'yext-customer-reviews');
        $success = false;

        if ( $this->verified() && $settings['arb-subscription-amount'] > 0 && !empty( $settings['arb-subscription-gateway'] ) ) {
            $services = array(
                'shopping-cart'         => 50
                , 'blog'                => 100
                , 'email-marketing'     => 100
                , 'social-media'        => 99
                , 'geo-marketing'       => 100
                , 'gm-reviews'          => 100
            );

            $new_price = $settings['arb-subscription-amount'];
            $new_services = $old_services = $new_settings = array();

            foreach ( $services as $service => $price ) {
                switch ( $service ) {
                    case 'gm-reviews':
                        if ( '1' == $settings['yext-customer-reviews']  && !isset($_POST[$service] ) ) {
                            $new_price -= $price;
                            $old_services[] = 'GeoMarketing Customer Reviews';
                            $new_settings['yext-customer-reviews'] = '';
                        } elseif ( '1' != $settings['yext-customer-reviews'] && isset($_POST[$service] ) ) {
                            $new_price += $price;
                            $new_services[] = 'GeoMarketing Customer Reviews';
                            $new_settings['yext-customer-reviews'] = '1';
                        }
                    break;

                    default:
                        $service_name = str_replace('-', '_', $service);
                        if ($this->user->account->$service_name && !isset($_POST[$service])) {
                            $new_price -= $price;
                            $old_services[] = ucwords(str_replace('-', ' ', $service));
                            $this->user->account->$service_name = 0;
                        } elseif (!$this->user->account->$service_name && isset($_POST[$service])) {
                            $new_price += $price;
                            $new_services[] = ucwords(str_replace('-', ' ', $service));
                            $this->user->account->$service_name = 1;
                        }
                    break;
                }

            }

            if ( $_POST['new-price'] == $new_price ) {
                library('arb-' . $settings['arb-subscription-gateway']);

                // Create instance of ARB
                $arb = new arb($this->user->account->title);

                // Set variables
                $arb->setSubscriptionId($settings['arb-subscription-id']);
                $arb->setAmount($new_price);
                $arb->setTotalOccurrences('9999'); // if omitted, default is 9999(forever)
                $arb->setOrderDetails('Managed Website Monthly Payment');
                $arb->setCustomerId($this->user->id);
                $arb->setCustomerPhone($this->user->work_phone);
                $arb->setCustomerEmail($this->user->email);

                // Submit the subscription request
                $arb->UpdateSubscriptionRequest();

                // Test and print results
                $success = $arb->success;

                if ( $success ) {
                    $new_settings['arb-subscription-amount'] = $new_price;
                    $this->user->account->set_settings( $new_settings );
                    $this->user->account->save();

                    // Create a ticket with changes
                    $ticket = new Ticket();
                    $ticket->user_id = $this->user->account->id;
                    $ticket->assigned_to_user_id = $this->user->account->os_user_id;
                    $ticket->website_id = $this->user->account->id;
                    $ticket->summary = 'Account Service Change';
                    $ticket->message = "New Services:\n" . implode("\n", $new_services) . "\n\nOld Services:\n" . implode("\n", $old_services);
                    $ticket->status = Ticket::STATUS_OPEN;
                    $ticket->priority = Ticket::PRIORITY_URGENT;
                    $ticket->create();

                    $this->notify('Your services changes have been successfully submitted!');
                    $this->log( 'update-services', $this->user->contact_name . ' changed their services on ' . $this->user->account->title, $_POST );

                    // Reget the settings
                    $settings = $this->user->account->get_settings('arb-subscription-id', 'arb-subscription-amount', 'arb-subscription-gateway');
                } else {
                    $this->notify('There was a problem while trying to update your account. A ticket has been submitted and you will be contacted shortly.', false);
                    $this->log( 'update-services-failed', $this->user->contact_name . ' failed to changed their services on ' . $this->user->account->title, $_POST );

                    $ticket = new Ticket();
                    $ticket->user_id = $this->user->id;
                    $ticket->assigned_to_user_id = User::TECHNICAL;
                    $ticket->website_id = $this->user->account->id;
                    $ticket->summary = 'Service Change update failed';
                    $ticket->message = $this->user->contact_name . " tried and failed to update their billing information. The following information is available:\n" . fn::info( $arb->error, false ) . "\n\nMore information:\n" . fn::info( $arb->response );
                    $ticket->status = Ticket::STATUS_OPEN;
                    $ticket->priority = Ticket::PRIORITY_URGENT;
                    $ticket->create();
                }
            }

        }

        $this->resources->javascript('settings/services');

        return $this->get_template_response( 'services' )
            ->kb( 0 )
            ->add_title( _('Services') )
            ->set( compact( 'settings', 'success' ) )
            ->select( 'services' );
    }

    /***** AJAX *****/

    /**
     * Upload Logo
     *
     * @return AjaxResponse
     */
    protected function upload_logo() {
        // Make sure it's a valid ajax call
        $response = new AjaxResponse( $this->verified() );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        // Get file uploader
        library('file-uploader');

        // Instantiate classes
        $file = new File();
        $account_file = new AccountFile();

        $uploader = new qqFileUploader( array( 'gif', 'jpg', 'jpeg', 'png' ), 6144000 );

        // Upload file
        $result = $uploader->handleUpload( 'gsr_' );

        $response->check( $result['success'], _('Failed to upload image') );

        // If there is an error or now user id, return
        if ( $response->has_error() )
            return $response;

        $logo_name = 'logo';

        // Create the different versions we need
        $logo_dir = $this->user->account->id . '/logo/';
		
        // Normal and large
        $file->upload_image( $result['file_path'], $logo_name, 700, 200, 'websites', $logo_dir );
        $file_url = $file->upload_image( $result['file_path'], $logo_name, 700, 700, 'websites', $logo_dir . 'large/' );
		$file_url = 'http://websites.retailcatalog.us/' . $this->user->account->id . '/logo/' . $file_url;

        // Delete file
        if ( is_file( $result['file_path'] ) )
            unlink( $result['file_path'] );

        // Create account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = $file_url;
        $account_file->create();

        // Update account logo
        $this->user->account->set_settings( array( 'website-logo' => $account_file->file_path ) );

        // Clear CloudFlare Cache
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( $cloudflare_zone_id ) {
            library('cloudflare-api');
            $cloudflare = new CloudFlareAPI( $this->user->account );
            $cloudflare->purge( $cloudflare_zone_id );
        }

        // Add the response
        $response->add_response( 'image', $account_file->file_path );

        $this->log( 'upload-logo', $this->user->contact_name . ' uploaded a new logo on ' . $this->user->account->title );

        return $response;
    }

    /**
     * Edit DNS for an account
     *
     * @return TemplateResponse|RedirectResponse
     */
    protected function domain() {
        // Cloudflare
        library('cloudflare-api');
        $cloudflare = new CloudFlareAPI( $this->user->account );
        $cloudflare_zone_id = $this->user->account->get_settings('cloudflare-zone-id');

        if ( empty( $cloudflare_zone_id ) )
            return new RedirectResponse('/settings/');

        $v = new Validator( 'fEditDNS' );

        // Declare variables
        $domain_name = url::domain( $this->user->account->domain, false );
        $full_domain_name = $domain_name . '.';
        $errs = false;

        // Handle form actions
        if ( $this->verified() ) {
            $errs = $v->validate();

            if ( empty($errs) && isset($_POST['changes']) && is_array($_POST['changes'])) {
                foreach ($_POST['changes'] as $dns_zone_id => $records) {
                    switch ($_POST['changes'][$dns_zone_id]['action']) {
                        default:
                            continue;
                            break;

                        case '1':
                            $cloudflare->create_dns_record($cloudflare_zone_id, $_POST['changes'][$dns_zone_id]['type'], $_POST['changes'][$dns_zone_id]['name'], $_POST['changes'][$dns_zone_id]['content'], $_POST['changes'][$dns_zone_id]['ttl']);
                            break;

                        case '2':
                            $cloudflare->update_dns_record($cloudflare_zone_id, $dns_zone_id, $_POST['changes'][$dns_zone_id]['type'], $_POST['changes'][$dns_zone_id]['name'], $_POST['changes'][$dns_zone_id]['content'], $_POST['changes'][$dns_zone_id]['ttl']);
                            break;

                        case '0':
                            $cloudflare->delete_dns_record($cloudflare_zone_id, $dns_zone_id);
                            continue;
                            break;
                    }
                }

            }
        }

        $records = $cloudflare->list_dns_records( $cloudflare_zone_id );

        // Put out notifications
        if ( isset( $response ) ) {
            if ( $response ) {
                $this->notify( _('Your DNS Zone file has been successfully updated!') );
            } else {
                $errs .= _('There was an error while trying to update your DNS Zone file. Please try again.');
            }
        }

        // Keep the resources that we need
        $this->resources->javascript('settings/dns');

        $zone_details = $cloudflare->zone_details( $cloudflare_zone_id );

        return $this->get_template_response('dns')
            ->add_title( 'DNS | Domain' )
            ->kb( 0 )
            ->set( compact( 'account', 'zone_id', 'cloudflare_zone_id', 'errs', 'domain_name', 'full_domain_name', 'records', 'zone_details' ) );
    }

}


