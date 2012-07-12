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

        $this->begin_transaction();
        try {
            /**
             * Here I'll do lots of DB operations, inserts, updates, bla bla bla
             */
            $this->commit(); //If the code reaches this point, everything worked so far, so send it to db
        } catch( ModelException $mex ) {
            $this->rollback(); //Something went wrong, what exactly we don't know, but this does not matter for the user too, then abort every DB operation
            $template_response->add_error('Some Error message here');
        }

        return $template_response;
    }
}


