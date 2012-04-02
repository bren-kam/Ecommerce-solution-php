<?php
/**
 * Handles all the archives
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Archives extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
	}
	
	/**
	 * Archives a page
	 *
	 * @param int $website_page_id
	 * @param int $request_id
	 * @param array $param of all data fields
	 * @return bool
	 */
	 public function add_page( $website_page_id, $request_id, $param ) {
		//Fix for arrays
		$this->db->insert( 'archive_pages', array('website_page_id' => $website_page_id, 'request_id' => $request_id, 'content' => nl2br( $param['content'] ), 'meta_title' => $param['meta_title'], 'meta_description' => $param['meta_description'], 'meta_keywords' => $param['meta_keywords'], 'date_created' => dt::date('Y-m-d H:i:s') ), 'iisssss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to archive page.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	 }

	/**
	  * Archives the metadata for a page
	  *
	  * @param int $archive_page_id
	  * @param array $metadata an associative array of metadata
	  * @return bool
	  */
	public function add_pagemeta( $archive_page_id, $metadata ) {
		// Typecast
		$archive_page_id = (int) $archive_page_id;
		
		$values = '';
		
		foreach ( $metadata as $k => $v ) {
			if ( !empty( $values ) )
				$values .= ',';
			
			$values .= "( $archive_page_id, '" . $this->db->escape( $k ) . "', '" . $this->db->escape( $v ) . "' )";
		}
		
		// Insert archive page meta
		$this->db->query( "INSERT INTO `archive_pagemeta` ( `archive_page_id`, `key`, `value` ) VALUES $values" );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->err( 'Failed to add archive page meta.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	 }

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}