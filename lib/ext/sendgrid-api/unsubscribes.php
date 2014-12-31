<?php
/**
 * SendGrid - Email - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/emails.html
 *
 */

class SendGridUnsubscribesAPI {
    const PREFIX = 'unsubscribes';

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

    /*************************************/
    /* Start: SendGrid Email API Methods */
    /*************************************/

    /**
     * Add
     *
     * @param string $email
     * @return bool
     */
    public function add( $email ) {
        $this->api( 'add', compact( 'email' ) );

        return $this->sendgrid->success();
    }

    /**
     * Get
     *
     * @param string $list
     * @throws Exception
     */
    public function get( $list ) {
        throw new Exception("Not Implemented");
    }

    /**
     * Delete
     *
     * @param string $email
     * @return bool
     */
    public function delete( $email ) {
        $this->api( 'add', compact( 'email' ) );

        return $this->sendgrid->success();
    }

    /***********************************/
    /* End: SendGrid Email API Methods */
    /***********************************/

    /**
     * API
     *
     * @param string $method
     * @param $params [optional]
	 * @param string $extra [optional]
     * @return stdClass object
     */
    protected function api( $method, $params = array(), $extra = '' ) {
        return $this->sendgrid->execute( self::PREFIX . '.' . $method, $params, SendGridAPI::API_URL, $extra );
    }
}