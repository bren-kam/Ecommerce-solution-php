<?php
/**
 * Contain all the classes necessary for SOAP implementation -- WSDL interpreter
 */

/**
 * Base Request
 */
abstract class BaseRequest {
    /**
     * @access public
     * @var string 'Create', 'Read', 'Update', 'Delete'
     */
    public $Action;

    /**
     * @access public
     * @var string 'Xml', 'XmlDocument', 'ByteArray', 'Plist'
     */
    public $TransportDataType;
}
/**
 * PackageRequest
 */
class PackageRequest extends BaseRequest {
	/**
	 * @access public
	 * @var PackageCriteria
	 */
	public $Criteria;

	/**
	 * @access public
	 * @var ArrayOfPackageExecuteOption
	 */
	public $ExecuteOptions;

	/**
	 * @access public
	 * @var ServiceData
	 */
	public $Package;

	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackagesCollection;

	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageTemplatesCollection;

	/**
	 * @access public
	 * @var ServiceData
	 */
	public $PackageImagesCollection;

    /**
     * Construct to define properties set by parnet
     */
    public function __construct() {
        $this->TransportDataType = 'Xml';
        $this->Action = 'Read';
    }
}

/**
 * GetPackages
 */
class GetPackages {
	/**
	 * @access public
	 * @var PackageRequest
	 */
	public $request;

    /**
     * Construct to auto assign request
     *
     * @param PackageRequest $request
     */
    public function __construct( $request ) {
        $this->request = $request;
    }
}