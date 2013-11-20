<?php
/**
 * SendGrid - Category - API Library
 *
 * Library based on documentation available on 20/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/categories.html
 *
 */

class SendGridCategoryAPI {
    const PREFIX = 'newsletter/category/';

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
    /* Start: SendGrid Category API Methods */
    /****************************************/

    /**
     * Create
     *
     * @param string $category
     * @return bool
     */
    public function create( $category ) {
        $this->api( 'create', compact( 'category' ) );

        return $this->sendgrid->success();
    }

    /**
     * Add
     *
     * @param string $category
     * @param string $name
     * @return bool
     */
    public function add( $category, $name ) {
        $this->api( 'add', compact( 'category', 'name' ) );

        return $this->sendgrid->success();
    }

    /**************************************/
    /* End: SendGrid Category API Methods */
    /**************************************/

    /**
     * API
     *
     * @param string $method
     * @param $params [optional]
	 * @param string $extra [optional]
     * @return stdClass object
     */
    protected function api( $method, $params = array(), $extra = '' ) {
        return $this->sendgrid->execute( self::PREFIX . $method, $params, SendGridAPI::API_URL, $extra );
    }
}