<?php defined('SYSPATH') or die('No direct script access.');
 
class Page_Model extends Model {
	/**
	 * Creates new Database instance
	 *
	 * @return  void
	 */
	public function __construct() {
		// load database library into $this->db (can be omitted if not required)
		parent::__construct();
	}
	
	/**
	  * Gets a metadata for a page
	  *
	  * @param int $website_page_id
	  * @return array|bool
	  */
	 public function get_pagemeta( $website_page_id ) {
		$metadata = $this->db->query( sprintf( "SELECT * FROM `website_pagemeta` WHERE `website_page_id` = %d", $website_page_id ) )->result_array( FALSE );
		
		// unencrypt data
		if ( is_array( $metadata ) )
		foreach ( $metadata as $md ) {
			$new_metadata[$md['key']] = $md['value'];
		}
		
		return ( isset( $new_metadata ) ) ? $new_metadata : array();
	 }
	
	/**
	  * Sets the metadata for a page
	  *
	  * @param array $metadata an associative array of metadata
	  * @param int $website_page_id the website_page_id
	  * @return array|bool
	  */
	public function set_pagemeta( $metadata, $website_page_id ) {
		$original_metadata = $this->get_pagemeta( $website_page_id );
		
		foreach ( $metadata as $md ) {
			// Find out if the the key/page_id combination already exists. If so, update it. If not, insert it.			
			$this->db->query( ( array_key_exists( $md['key'], $original_metadata ) ) ? sprintf( "UPDATE `website_pagemeta` SET `value` = '%s' WHERE `website_page_id` = %d AND `key` = '%s'", format::sql_string( $md['value'] ), $website_page_id, format::sql_string( $md['key'] ) ) : sprintf( "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES ( %d, '%s', '%s' )", $website_page_id, format::sql_string( $md['key'] ), format::sql_string( $md['value'] ) ) );
			
			if ( mysql_errno() )
				return false;
		}
		
		return true;
	 }
	 
	 /**
	  * Gets an attachment's id
	  *
	  * @param int $website_page_id
	  * @param string $key the key of the attachment
	  * @return int
	  */
	 public function attachment_id( $website_page_id, $key ) {
		$row = $this->db->query( sprintf( "SELECT `website_attachment_id` FROM `website_attachments` WHERE `website_page_id` = %d AND `key` = '%s'", $website_page_id, format::sql_string( $key ) ) )->result( FALSE )->current();
		
		return ( isset( $row['website_attachment_id'] ) ) ? $row['website_attachment_id'] : false;
	 }
	
	/**
	  * Sets the metadata for a page
	  *
	  * @param int $website_page_id the website_page_id
	  * @param string $key the key of the attachment
	  * @param string $value the local path to the file
	  * @return array|bool
	  */
	public function set_page_attachment( $website_page_id, $key, $value ) {
		$website_attachment_id = $this->attachment_id( $website_page_id, $key );
		
		// Find out if the the key/page_id combination already exists. If so, update it. If not, insert it.
		$this->db->query( ( $website_attachment_id ) ? sprintf( "UPDATE `website_attachments` SET `value` = '%s' WHERE `website_attachment_id` = %d", format::sql_string( $value ), $website_attachment_id ) : sprintf( "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value` ) VALUES ( %d, '%s', '%s' )", $website_page_id, format::sql_string( $key ), format::sql_string( $value ) ) );
		
		if ( mysql_errno() )
			return false;
		
		return true;
	 }
	
	/**
	 * Update the page information
	 *
	 * @param int $website_page_id the website_page_id
	 * @param array $param of all data fields
	 * @return bool
	 */
	 public function update( $param, $website_page_id ) {
		$param = format::sql_safe_deep( $param );
		
		$sql  = "UPDATE `website_pages` SET  ";
		//$sql .= "`title` = '" . $param['title'] . "',"; They can't update titles right now
		$sql .= "`content` = '" . $param['content'] . "',";
		$sql .= "`meta_title` = '" . $param['meta_title'] . "',";
		$sql .= "`meta_description` = '" . $param['meta_description'] . "',";
		$sql .= "`meta_keywords` = '" . $param['meta_keywords'] . "' WHERE ";
		$sql .= sprintf ("`website_page_id` = %d", $website_page_id );
		
		$this->db->query( $sql )->count();
		
		return ( mysql_errno() ) ? false : true;
	 }
}
 
?>