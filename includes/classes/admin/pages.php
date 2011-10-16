<?php
/**
 * Handles all the pages
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Pages extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets a metadata for a page
	 *
	 * @param int $website_page_id
	 * @return array
	 */
	public function get_pagemeta( $website_page_id ) {
		$metadata = $this->db->get_results( 'SELECT * FROM `website_pagemeta` WHERE `website_page_id` = ' . (int) $website_page_id, ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get page meta.', __LINE__, __METHOD__ );
			return false;
		}
		
		// unencrypt data
		if( is_array( $metadata ) )
		foreach( $metadata as $md ) {
			$new_metadata[$md['key']] = $md['value'];
		}
		
		return ( isset( $new_metadata ) ) ? $new_metadata : array();
	}
	
	/**
	 * Sets the metadata for a page
	 *
	 * @param array $metadata
	 * @param int $website_page_id
	 * @return array
	 */
	public function set_pagemeta( $metadata, $website_page_id ) {
		// Typecast
		$website_page_id = (int) $website_page_id;
		
		$original_metadata = $this->get_pagemeta( $website_page_id );
		
		// @Fix
		foreach( $metadata as $md ) {
			if( array_key_exists( $md['key'], $original_metadata ) ) {
				$this->db->update( 'website_pagemeta', array( 'value' => $md['value'] ), array( 'website_page_id' => $website_page_id, 'key' => $md['key'] ), 's', 'is' );
				
				// Handle any error
				if( $this->db->errno() ) {
					$this->err( 'Failed to update page meta.', __LINE__, __METHOD__ );
					return false;
				}
			} else {
				$this->db->insert( 'website_pagemeta', array( 'website_page_id' => $website_page_id, 'key' => $md['key'], 'value' => $md['value'] ), 'iss' );
				
				// Handle any error
				if( $this->db->errno() ) {
					$this->err( 'Failed to insert page meta.', __LINE__, __METHOD__ );
					return false;
				}
			}
		}
		
		/*
		// Prepare statement
		$statement = $this->db->prepare( "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES ( $website_page_id, ?, ? ) ON DUPLICATE KEY UPDATE `value` = ?" );
		$statement->bind_param( 'sss', $key1, $value, $key2 );
		
		foreach( $metadata as $md ) {
			$key1 = $key2 = $md['key'];
			$value = $md['value'];
			
			$statement->execute();
			
			// Handle any error
			if( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( "Failed to set page meta.", __LINE__, __METHOD__ );
				return false;
			}
		}*/
		
		return true;
	}
	
	/**
	 * Gets an attachment's id
	 *
	 * @param int $website_page_id
	 * @param string $key
	 * @return int
	 */
	public function attachment_id( $website_page_id, $key ) {
		$website_attachment_id = $this->db->prepare( 'SELECT `website_attachment_id` FROM `website_attachments` WHERE `website_page_id` = ? AND `key` = ?', 'is', $website_page_id, $key )->get_var('');
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get website attachment id.', __LINE__, __METHOD__ );
			return false;
		}
				
		return $website_attachment_id;
	}
	
	/**
	 * Sets the metadata for a page
	 *
	 * @param int $website_page_id
	 * @param string $key
	 * @param string $value the local path to the file
	 * @return array|bool
	 */
	public function set_page_attachment( $website_page_id, $key, $value ) {
		$website_attachment_id = $this->attachment_id( $website_page_id, $key );
		
		if( $website_attachment_id ) {
			$this->db->update( 'website_attachments', array( 'value' => $value ), array( 'website_attachment_id' => $website_attachment_id ), 's', 'i' );
			
			// Handle any error
			if( $this->db->errno() ) {
				$this->err( 'Failed to update website attachment.', __LINE__, __METHOD__ );
				return false;
			}
		} else {
			$this->db->insert( 'website_attachments', array( 'website_page_id' => $website_page_id, 'key' => $key, 'value' => $value ), 'iss' );
			
			// Handle any error
			if( $this->db->errno() ) {
				$this->err( 'Failed to insert website attachment.', __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Update the page information
	 *
	 * @param int $website_page_id
	 * @param array $param
	 * @return bool
	 */
	public function update( $param, $website_page_id ) {
		$this->db->update( 'website_pages', array( 'content' => $param['content'], 'meta_title' => $param['meta_title'], 'meta_description' => $param['meta_description'], 'meta_keywords' => $param['meta_keywords'] ), array( 'website_page_id' => $website_page_id ), 'ssss', 'i' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update website page.', __LINE__, __METHOD__ );
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