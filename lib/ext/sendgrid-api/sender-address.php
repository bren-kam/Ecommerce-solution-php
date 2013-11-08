<?php
/**
 * SendGrid - Sender Address - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/sender_address.html
 *
 */

class SendGridSenderAddressAPI {
    const PREFIX = 'newsletter/identity/';

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

    /**********************************************/
    /* Start: SendGrid Sender Address API Methods */
    /**********************************************/

    /**
     * Add
     *
     * @param string $identity
     * @param string $name
     * @param string $email
     * @param string $address
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     * @return bool
     */
    public function add( $identity, $name, $email, $address, $city, $state, $zip, $country ) {
        $this->api( 'add', compact( 'identity', 'name', 'email', 'address', 'city', 'state', 'zip', 'country' ) );

        return $this->sendgrid->success();
    }

    /********************************************/
    /* End: SendGrid Sender Address API Methods */
    /********************************************/

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