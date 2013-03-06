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
        $form = new FormTable( 'fSettings' );
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


        if ( $form->posted() ) {
            $this->user->contact_name = $_POST['tContactName'];
            $this->user->store_name = $_POST['tStoreName'];
            $this->user->work_phone = $_POST['tWorkPhone'];
            $this->user->cell_phone = $_POST['tCellPhone'];
            $this->user->save();

            if ( !empty( $_POST['tPassword'] ) )
                $this->user->set_password( $_POST['tPassword'] );

            $this->notify( _('You have successfully updated your settings!') );
        }

        return $this->get_template_response( 'index' )
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
            $this->user->account->phone = $_POST['tPhone'];
            $this->user->account->save();

            $this->notify(_('The "Logo and Phone" section has been updated successfully!' ) );
        }

        $this->resources->javascript( 'fileuploader', 'settings/logo-and-phone' );

        return $this->get_template_response( 'logo-and-phone' )
            ->add_title( _('Logo and Phone') )
            ->select( 'logo-and-phone' );
    }

    /***** AJAX *****/

    /**
     * Upload Logo
     *
     * @return AjaxResponse
     */
    public function upload_logo() {
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

        $logo_name =  'logo.' . str_replace( 'jpeg', 'jpg', f::extension( $_GET['qqfile'] ) );

        // Create the different versions we need
        $logo_dir = $this->user->account->id . '/logo/';

        // Normal and large
        $logo = $file->upload_image( $result['file_path'], $logo_name, 700, 200, 'websites', $logo_dir );
        $file->upload_image( $result['file_path'], $logo_name, 700, 700, 'websites', $logo_dir . 'large/' );

        // Create account file
        $account_file->website_id = $this->user->account->id;
        $account_file->file_path = 'http://websites.retailcatalog.us/' . $logo_dir . $logo;
        $account_file->create();

        // Update account logo
        $this->user->account->logo = $account_file->file_path;
        $this->user->account->save();

        jQuery('#dLogoContent')->html('<img src="' . $account_file->file_path . '" style="padding-bottom:10px" alt="' . _('Logo') . '" /><br />' );

        // Add the response
        $response->add_response( 'jquery', jQuery::getResponse() );

        return $response;
    }

}


