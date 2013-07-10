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
        fn::mail( 'kerry@studio98.com', 'unsubscribe', fn::info( $_REQUEST, false ) );
        return new JsonResponse( TRUE );
    }
}