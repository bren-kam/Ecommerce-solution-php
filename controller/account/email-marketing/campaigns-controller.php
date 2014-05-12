<?php

class CampaignsController extends BaseController {

    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        parent::__construct();

//        if ( !$this->user->account->email_marketing )
//            return new RedirectResponse('/email-marketing/subscribers/');

        $this->view_base = 'email-marketing/campaigns/';
        $this->section = 'email-marketing';
        $this->title = _('Campaigns') . ' | ' . _('Email Marketing');
    }

    public function index() {
        return $this->create();
    }

    public function create() {
        $email_list = new EmailList();
        $email_lists = $email_list->get_count_by_account( $this->user->account->id );

        $settings = $this->user->account->get_settings( 'timezone', '' );

        $timezones = data::timezones( false, false, true );

        $account_file = new AccountFile();
        $files = $account_file->get_by_account( $this->user->account->id );

        $this->resources->css( 'email-marketing/campaigns/create', 'jquery.timepicker' )
            ->css_url( Config::resource('jquery-ui') )
            ->javascript( 'jquery.timepicker' , 'email-marketing/campaigns/create', 'jquery.idTabs', 'fileuploader', 'gsr-media-manager' );

        return $this->get_template_response( 'create' )
            ->kb( 0 )
            ->add_title( _('Campaigns') )
            ->select( 'campaigns', 'create' )
            ->set( compact( 'email_lists', 'settings', 'timezones', 'files' ) );
    }

}