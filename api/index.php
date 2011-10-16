<?php
/**
 * Imagine Retailer API
 *
 * This handles all API calls and responses. While this is based on the 
 * REST method, the same URL will be used for all requests (more like 
 * the SOAP method.)
 *
 * All responses will be returned in JSON
 */

// Include the class
require_once( 'ir-requests.php' );

// See class for functionality
$ir = new IRR();