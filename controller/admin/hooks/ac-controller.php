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

    /**
     * The campaign is sent
     *
     * @return TemplateResponse
     */
    protected function sent_campaign() {
        $email_message = new EmailMessage();

        fn::mail( 'kerry@studio98.com', 'campaign_hook', fn::info( $_POST ) );

        $email_message->get( $_POST['list'], $_GET['aid'] );
        $email_message->status = EmailMessage::STATUS_SENT;
        $email_message->save();

        return new JsonResponse( TRUE );
    }
}