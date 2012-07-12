<?php
class AccountsController extends BaseController {
    /**
     * Setup the base for creating template responses
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct( 'account/' );
    }
    /**
     * Setup a new account
     * @return TemplateResponse
     */
    protected function new_account() {
        //Here I'll prepare the objects, getting HTTP data(GET,POST,etc) and creating objects(filling required data) from it

        $error_message = NULL;
        $template_response = $this->get_template_response('new-account');

        try {
            /**
             * Here I'll do lots of DB operations, inserts, updates, bla bla bla
             */
        } catch( ModelException $mex ) {
            $template_response->add_error('Some Error message here');
        }

        return $template_response;
    }
}


