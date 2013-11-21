<?php
/**
 * SendGrid - Schedule - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/schedule.html
 *
 */

class SendGridScheduleAPI {
    const PREFIX = 'newsletter/schedule/';

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

    /****************************************/
    /* Start: SendGrid Schedule API Methods */
    /****************************************/

    /**
     * Add
     *
     * @param string $name
     * @param array $at [optional]
     * @return bool
     */
    public function add( $name, $at = null ) {
        $this->api( 'add', compact( 'name', 'at' ) );

        return $this->sendgrid->success();
    }
    /**
     * Delete
     *
     * @param string $name
     * @return bool
     */
    public function delete( $name ) {
        $this->api( 'delete', compact( 'name' ) );

        return $this->sendgrid->success();
    }

    /**************************************/
    /* End: SendGrid Schedule API Methods */
    /**************************************/

    /**
     * API
     *
     * @param string $method
     * @param $params [optional]
     * @return stdClass object
     */
    protected function api( $method, $params = array() ) {
        return $this->sendgrid->execute( self::PREFIX . $method, $params );
    }
}