<?php
/**
 * cPanel - API Library
 *
 * Library based on documentation available on 04/26/2012 from
 * @url http://docs.cpanel.net/twiki/bin/view/ApiDocs/Api2/WebHome
 *
 */

if ( !class_exists( 'WHM_API' ) )
	library('whm-api');

class cPanel_API extends WHM_API {
	/**
	 * Hold the username
	 *
	 * @var string
	 */
	private $username;
	 
	/**
	 * Construct class will initiate and run everything
     *
     * @param string $username
	 */
	public function __construct( $username ) {
		$this->username = $username;
	}

	/*****************************/
	/* Start: cPanel API Methods */
	/*****************************/

	/**
	 * Add Subdomain
	 *
	 * @param string $rootdomain
	 * @param string $domain
	 * @param string $dir
     *
     * @return object
	 */
	public function add_subdomain( $rootdomain, $domain, $dir = NULL ) {
		if ( is_null( $dir ) )
			$dir = 'public_html/' . $domain;
		
		$response = $this->_call( 'SubDomain', 'addsubdomain', compact( 'dir', 'rootdomain', 'domain' ) );
		
		return ( $this->success() ) ? $response : false;
	}

	/***************************/
	/* END: cPanel API Methods */
	/***************************/

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $module The module being used
	 * @param string $function The function being called
	 * @param array $params an array of the parameters to be sent
     * @return stdClass object
	 */
	private function _call( $module, $function, $params = array() ) {
		$response = $this->_execute( 'cpanel', array_merge( array( 'cpanel_jsonapi_module' => $module, 'cpanel_jsonapi_func' => $function, 'cpanel_jsonapi_version' => '2', 'user' => $this->username ), $params ) );
		
		return $response->cpanelresult;
	}
}