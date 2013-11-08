<?php
/**
 * SendGrid - List - API Library
 *
 * Library based on documentation available on 08/11/2013 from
 * @url http://sendgrid.com/docs/API_Reference/Marketing_Emails_API/lists.html
 *
 */

class SendGridListAPI {
    const PREFIX = 'newsletter/lists/';

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
    /* Start: SendGrid List API Methods */
    /************************************/

    /**
     * Add
     *
     * @return int
     *
     * @param string $list
     * @return bool
     */
    public function add( $list ) {
        $this->api( 'add', compact( 'list' ) );

        return $this->sendgrid->success();
    }

    /**
     * Edit
     *
     * @param string $list
     * @param string $newlist
     * @return bool
     */
    public function edit( $list, $newlist ) {
        $this->api( 'edit', compact( 'list', 'newlist' ) );

        return $this->sendgrid->success();
    }

    /**
     * Delete
     *
     * @param string $list
     * @return bool
     */
    public function delete( $list ) {
        $this->api( 'delete', compact( 'list' ) );

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
        return $this->sendgrid->execute( self::PREFIX . $method, $params );
    }
}