<?php
/**
 * SendGrid - Recipient - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/recipients.html
 *
 */

class SendGridRecipientAPI {
    const PREFIX = 'newsletter/recipients/';

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

    /*****************************************/
    /* Start: SendGrid Recipient API Methods */
    /*****************************************/

    /**
     * Add
     *
     * @param string $list
     * @param string $name
     * @return bool
     */
    public function add( $list, $name ) {
        $this->api( 'add', compact( 'list', 'name' ) );

        return $this->sendgrid->success();
    }

    /**
     * Get
     *
     * @param string $name
     * @return array
     */
    public function get( $name ) {
        $this->api( 'get', compact( 'name' ) );

        return $this->sendgrid->response();
    }

    /**
     * Delete
     *
     * @param string $name
     * @param string $list
     * @return bool
     */
    public function delete( $name, $list ) {
        $this->api( 'delete', compact( 'name', 'list' ) );

        return $this->sendgrid->success();
    }

    /***************************************/
    /* End: SendGrid Recipient API Methods */
    /***************************************/

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