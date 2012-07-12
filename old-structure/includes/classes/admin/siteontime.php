<?php
/**
 * Handles SiteOnTime API
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class SiteOnTime extends Base_Class {
	const FTP_URL = 'http://www.cmicdata.com/datafeeds/product-data.php';
	const COMPANY_ID = 'B34FF55A-4FC8-4DF7-9FF3-91839248DC7A';

	/**
	 * Creates new Database instance
	 */
	public function __construct() {
		// Load database library into $this->db (can be omitted if not required)
		parent::__construct();

        /*
         * SeriesName & ModelDescription > Title
            MenuHeading > Industry
            Category > Sub Category | Sub Cateogry
            Brand > Brand

            SeriesName & ModelDescription > Description
            KeyFeature1 & KeyFeature2 & KeyFeature3 & KeyFeature4 & KeyFeature5 > Description
            StandardColor > Description
            KeyFeature1 & KeyFeature2 & KeyFeature3 & KeyFeature4 & KeyFeature5 > Product Specs
            SKU > SKU
            LargeImage > Images

         */
	}

    /**
     * Run
     */
    public function run() {
        $arguments = http_build_query( array( 'cid' => self::COMPANY_ID ) );

        // Get products
        $products = curl::get( self::FTP_URL . '?' . $arguments );

        echo $products;
    }

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}