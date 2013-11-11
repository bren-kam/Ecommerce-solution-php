<?php
/**
 * SendGrid - Email - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/emails.html
 *
 */

class SendGridEmailAPI {
    const PREFIX = 'newsletter/lists/email/';

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
     * @param string $list
     * @param array $emails
     * @return bool
     */
    public function add( $list, array $emails ) {
        $data = array();

        foreach ( $emails as $email ) {
            $data[] = array( 'email' => $email );
        }

        $data = json_encode( $data );

        $this->api( 'add', compact( 'list', 'data' ) );

        return $this->sendgrid->success();
    }

    /**
     * Delete
     *
     * @param string $list
     * @param string $email
     * @return bool
     */
    public function delete( $list, $email ) {
        $this->api( 'delete', compact( 'list', 'email' ) );

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
     * @return stdClass object
     */
    protected function api( $method, $params = array() ) {
        return $this->sendgrid->execute( self::PREFIX . $method, $params );
    }
}