<?php
/**
 * SendGrid - Customer - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Customer_Subuser_API/subusers.html
 *
 */

class SendGridSubuserAPI {
    const PREFIX = 'customer';

    /**
     * @var SendGridApi
     */
    protected $sendgrid;

	/**
	 * Construct class will initiate and run everything
     *
     * @param SendGridApi $sendgrid
	 */
	public function __construct( SendGridApi $sendgrid ) {
        $this->sendgrid = $sendgrid;
	}

    /***************************************/
    /* Start: SendGrid Subuser API Methods */
    /***************************************/

    /**
     * Add
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $address
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $country
     * @param string $phone
     * @param string $website
     * @param string $company
     * @return bool
     */
    public function add( $username, $password, $email, $first_name, $last_name, $address, $city, $state, $zip, $country, $phone, $website, $company ) {
        $confirm_password = $password;

        $this->api( 'add', compact( 'username', 'password', 'confirm_password', 'email', 'first_name', 'last_name', 'address', 'city', 'state', 'zip', 'country', 'phone', 'website', 'company' ) );

        return $this->sendgrid->success();
    }

    /*************************************/
    /* End: SendGrid Subuser API Methods */
    /*************************************/

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