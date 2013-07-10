<?php
class AcController extends BaseController {
    /**
     * Setup the base for Active Campaign
     */
    public function __construct() {
        // Pass in the base for all the views
        parent::__construct();
    }

    /**
     * Unsubscribe
     *
     * @return TemplateResponse
     */
    protected function unsubscribe() {
        $email = new Email();

        $email->get_by_email( $_GET['aid'], $_POST['contact']['email'] );
        $email->status = Email::STATUS_UNSUBSCRIBED;
        $email->save();

        return new JsonResponse( TRUE );
    }
}