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
        // Get email message
        $email_message = new EmailMessage();
        $email_message->get_by_ac_campaign_id( $_POST['campaign']['id'], $_GET['aid'] );

        // Load library to get static values
        library('ac-api/campaign');

        // Find out what we're changing the status to
        switch ( $_POST['campaign']['status'] ) {
            case ActiveCampaignCampaignAPI::STATUS_PENDING_APPROVAL:
            case ActiveCampaignCampaignAPI::STATUS_SENDING:
            case ActiveCampaignCampaignAPI::STATUS_COMPLETED:
                $email_message->status = EmailMessage::STATUS_SENT;
            break;

            default:
            case ActiveCampaignCampaignAPI::STATUS_DRAFT:
            case ActiveCampaignCampaignAPI::STATUS_PAUSED:
                $email_message->status = EmailMessage::STATUS_DRAFT;
            break;

            case ActiveCampaignCampaignAPI::STATUS_SCHEDULED:
                $email_message->status = EmailMessage::STATUS_SCHEDULED;
            break;
        }

        $email_message->save();

        return new JsonResponse( TRUE );
    }
}