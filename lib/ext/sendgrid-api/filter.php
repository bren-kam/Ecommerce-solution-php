<?php
/**
 * SendGrid - List - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/lists.html
 *
 */

class SendGridFilterAPI {
    const PREFIX = 'filter';

    /**
     * @var SendGridApi $sendgrid
     */
    protected $sendgrid;


	/**
	 * Construct class will initiate and run everything
     *
     * @param SendGridApi $sendgrid
	 */
	public function __construct( SendGridAPI $sendgrid ) {
        $this->sendgrid = $sendgrid;
	}

    /************************************/
    /* Start: SendGrid Filter API Methods */
    /************************************/

    /**
     * Event Notify
     * @param int $processed
     * @param int $dropped
     * @param int $deferred
     * @param int $delivered
     * @param int $bounce
     * @param int $click
     * @param int $open
     * @param int $unsubscribe
     * @param int $spamreport
     * @param string $url
     * @return string
     */
    public function event_notify( $processed = 0, $dropped = 0, $deferred = 0, $delivered = 0, $bounce = 0, $click = 0, $open = 0, $unsubscribe = 1, $spamreport = 0, $url = '' ) {
        $name = 'eventnotify';
        $version = 3;
        $this->api( 'setup', compact( 'name', 'processed', 'dropped', 'deferred', 'delivered', 'bounce', 'click', 'open', 'unsubscribe', 'spamreport', 'url', 'version' ) );

        return $this->sendgrid->success();
    }

    /**********************************/
    /* End: SendGrid List API Methods */
    /**********************************/

    /**
     * API
     *
     * @param string $method
     * @param $params [optional]
     * @return stdClass object
     */
    protected function api( $method, $params = array() ) {
        return $this->sendgrid->execute( self::PREFIX . '.' . $method, $params );
    }
}