<?php
/**
 * Ashley - API Library
 *
 * Library based on documentation available on 04/26/2012 from
 * @url http://api.ashleyfurniture.com/Ashley.ProductKnowledge.Maintenance.NewService/Services/ProductKnowledgeService.asmx
 *
 */

class Ashley_API {
    /**
	 * Constant paths to include files
	 */
	const URL_API = 'http://api.ashleyfurniture.com/';
    const URL_WSDL = 'http://api.ashleyfurniture.com/Ashley.ProductKnowledge.Maintenance.NewService/Services/ProductKnowledgeService.asmx?WSDL';
	const DEBUG = false;

    /**
	 * A few variables that will determine the basic status
	 */
    private $_method = NULL;
	private $_message = NULL;
	private $_success = false;
	private $_request_headers = NULL;
	private $_request = NULL;
	private $_response_headers = NULL;
	private $_raw_response = NULL;
	private $_full_response = NULL;
	private $_response = NULL;

    /**
     * Ashley Client
     *
     * @acccess private
     * @var SoapClient
     */
    private $ashley;

    /**
     * Construct to setup SOAP module
     */
    public function __construct() {
        // Get classes we need
        library('ashley-api/product-knowledge');

        // Initiate the client
        $this->ashley = new SoapClient( self::URL_WSDL, array( 'trace' => 1 ) );
    }

	/*************************/
	/* Start: Ashley Methods */
	/*************************/

    /**
     * Get Packages
     *
     * @return object
     */
    public function get_packages() {
        // Give it time to load

        set_time_limit(300);

        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'PackageExecuteOption' => 'LoadPackages' );

        // Execute the response
        $this->_execute( 'GetPackages', new GetPackages( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        $packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->PackagesCollection->XmlData ) );

        return $packages;
    }

    /**
     * Get Package Templates
     *
     * @param string $template_id
     * @param string $series_number
     * @return object
     */
    public function get_package_templates( $template_id = NULL, $series_number = NULL ) {
         // Setup the package request
        $package_request = new PackageRequest();

        if ( is_null( $template_id ) || is_null( $series_number ) ) {
            $package_request->ExecuteOptions = array( 'PackageTemplateExecuteOption' => 'LoadAllPackageTemplates' );
            $all = true;
        } else {
            $package_request->ExecuteOptions = array( 'PackageTemplateExecuteOption' => 'LoadPackageTemplate' );
            $package_request->Criteria = array( 'TemplateId' => $template_id, 'SeriesNo' => $series_number );
            $all = false;
        }

        // Execute the response
        $this->_execute( 'GetPackageTemplates', new GetPackageTemplates( $package_request ) );

        if ( !$this->_success )
            return false;

        // Determine what we're grabbing
        $template_packages = ( $all ) ? $this->_response->PackageTemplatesCollection->XmlData : $this->_response->PackageTemplate->XmlData;

        // SimpleXML errors out if it thinks its reading utf-16
        $template_packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $template_packages ) );
		
        return ( $all ) ? $template_packages->PackageTemplate : $template_packages;
    }

    /**
     * Get Categories
     *
     * @return object
     */
    public function get_categories() {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'CategoryExecuteOption' => 'LoadAllCategories' );

        // Execute the response
        $this->_execute( 'GetCategories', new GetCategories( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        $template_packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->CategoriesCollection->XmlData ) );

        return $template_packages->Category;
    }

    /**
     * Get Series
     *
     * @return object
     */
    public function get_series() {
        ini_set( 'memory_limit', '512M' );
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'SeriesExecuteOption' => 'LoadAllSeries' );

        // Execute the response
        $this->_execute( 'GetSeries', new GetSeries( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        $template_packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->SeriesCollection->XmlData ) );

        return $template_packages->Series;
    }

    /**
     * Get Series
     *
     * @return object
     */
    public function get_groupings() {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'GroupingExecuteOption' => 'LoadAllGroupings' );

        // Execute the response
        $this->_execute( 'GetGroupings', new GetGroupings( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        $template_packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->GroupingsCollection->XmlData ) );

        return $template_packages->Grouping;
    }

    /**
     * Get Dimensions
     *
     * @param string $items
     * @return object
     */
    public function get_dimensions( $items ) {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'DimensionExecuteOption' => 'LoadAllDimensions' );
        $package_request->Criteria = array( 'ListOfItems' => $items );

        // Execute the response
        $this->_execute( 'GetDimensions', new GetDimensions( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        return simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->DimensionsCollection->XmlData ) );
    }

    /**
     * Get Friendly Descriptions
     *
     * @return object
     */
    public function get_friendly_descriptions() {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'FriendlyDescriptionExecuteOption' => 'LoadAllFriendlyDescriptions' );

        // Execute the response
        $this->_execute( 'GetFriendlyDescriptions', new GetFriendlyDescriptions( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        return simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->FriendlyDescriptionsCollection->XmlData ) );
    }

    /**
     * Get Items
     *
     * Other options for item execute option
     *      LoadItems
     *      LoadAllItemCategories
     *
     * @param string|array $item_execute_option [optional]
     * @return object
     */
    public function get_items( $item_execute_option = 'LoadItems' ) {
        ini_set( 'memory_limit', '512M' );
        set_time_limit(3000);
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'ItemExecuteOption' => $item_execute_option );

        // Execute the response
        $this->_execute( 'GetItems', new GetItems( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        return simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->GetItemsCollection->XmlData ) );
    }

    /**
     * Get Item Features
     *
     * @return object
     */
    public function get_item_features() {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'ItemFeaturesExecuteOption' => 'LoadAllProductFeatures' );

        // Execute the response
        $this->_execute( 'GetItemFeatures', new GetItemFeatures( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        return simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->ItemFeaturesCollection->XmlData ) );
    }

	/***********************/
	/* END: Ashley Methods */
	/***********************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->_message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->_success;
    }

    /**
     * Get private request_headers variable
     *
     * @return string
     */
    public function request_headers() {
        return $this->_request_headers;
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->_request;
    }

    /**
     * Get private response_headers variable
     *
     * @return string
     */
    public function response_headers() {
        return $this->_response_headers;
    }

    /**
     * Get private raw response variable
     *
     * @return stdClass Object
     */
    public function _raw_response() {
        return $this->_raw_response;
    }

    /**
     * Get private full response variable
     *
     * @return stdClass Object
     */
    public function _full_response() {
        return $this->_full_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->_response;
    }

    /**
     * Display debug information
     */
    public function debug() {
        //echo "<h1>Method</h1>\n<p>" . $this->_method . "</p>\n<hr />\n<br /><br />\n";
        //echo "<h1>Request Headers</h1>\n<pre>", $this->_request_headers, "</pre>\n<hr />\n<br /><br />\n";
        //echo "<h1>Request</h1>\n\n<textarea style='width:100%;height:150px;' cols='50' rows='5'>", $this->_request, "</textarea>\n<hr />\n<br /><br />\n";
        //echo "<h1>Response Headers</h1>\n<pre>", $this->_response_headers, "</pre>\n<hr />\n<br /><br />\n";
        //echo "<h1>Raw Response</h1>\n<textarea style='width:100%;height:300px;' cols='50' rows='5'>", $this->_raw_response, "</textarea>\n<hr />\n<br /><br />\n";
        echo "<h1>Full Response</h1>\n<pre>", var_export( $this->_full_response, true ), "</pre>\n<hr />\n<br /><br />\n";
        //echo "<h1>Response</h1>\n<pre>", var_export( $this->_response, true ), "</pre>\n<hr />\n<br /><br />\n";
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $method The method being called
	 * @param mixed $params an array of the parameters to be sent
     * @return stdClass object
	 */
	protected function _execute( $method, $params = array() ) {
        // Set the method
        $this->_method = $method;

        // Do the call and get the response
        $this->_full_response = $this->ashley->$method( $params );

        // Set Request Parameters
		$this->_request_headers = $this->ashley->__getLastRequestHeaders();
		$this->_request = $this->ashley->__getLastRequest();

        // Set Response Parameters
        $this->_response_headers = $this->ashley->__getLastResponseHeaders();
		$this->_raw_response = $this->ashley->__getLastResponse();

        $result = $method . 'Result';

        $this->_response = $this->_full_response->$result;

        $this->_success = 'Failure' != $this->_response->Acknowledge;
        $this->_message = $this->_response->Message;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG )
            $this->debug();
	}
}