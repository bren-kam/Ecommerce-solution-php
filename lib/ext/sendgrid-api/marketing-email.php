<?php
/**
 * SendGrid - Marketing Email - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/newsletters.html
 *
 */

class SendGridMarketingEmailAPI {
    const PREFIX = 'newsletter/';

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

    /***********************************************/
    /* Start: SendGrid Marketing Email API Methods */
    /***********************************************/

    /**
     * Add
     *
     * @param string $identity
     * @param string $name
     * @param string $subject
     * @param string $text
     * @param string $html
     * @return bool
     */
    public function add( $identity, $name, $subject, $text, $html ) {
        $this->api( 'add', compact( 'identity', 'name', 'subject', 'text', 'html' ) );

        return $this->sendgrid->success();
    }

    /**
     * Edit
     *
     * @param string $name
     * @param string $newname
     * @param string $identity
     * @param string $subject
     * @param string $text
     * @param string $html
     * @return bool
     */
    public function edit( $name, $newname, $identity, $subject, $text, $html ) {
        $this->api( 'edit', compact( 'name', 'newname', 'identity', 'subject', 'text', 'html' ) );

        return $this->sendgrid->success();
    }

    /*********************************************/
    /* End: SendGrid Marketing Email API Methods */
    /*********************************************/

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