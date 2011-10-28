<?php
/**
 * handles Data Table responses
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Data_Table extends Base_Class {
	/**
	 * Holds the where information
	 * @var string
	 */
	private $where = '';
	
	/**
	 * Holds the order_by information
	 * @var string
	 */
	private $order_by = '';

	/**
	 * Holds the limit information
	 * @var string
	 */
	private $limit = 100;

	/**
	 * Holds the row count information
	 * @var init
	 */
	private $row_count = 0;

	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		// Make sure the user is signed in, if not spit back nothing
		
		// Get current user
		global $user;
		
		// If user is not logged in
		if ( !$user ) {
			echo json_encode( array( 
				'redirect' => true,
				'sEcho' => intval( $_GET['sEcho'] ),
				'iTotalRecords' => 0,
				'iTotalDisplayRecords' => 0,
				'aaData' => array()
			) );
			exit;
		}
		
		// Set the limit
		$this->limit = ( isset( $_GET['iDisplayStart'] ) ) ? intval( $_GET['iDisplayStart'] ) . ', ' . intval( $_GET['iDisplayLength'] ) : 1000;
	}
	
	/**
	 * Add something to the where string
	 *
	 * @param string $where
	 */
	public function add_where( $where ) {
		$this->where .= $where;
	}
	
	/**
	 * Add something to the where string
	 *
	 * @param array $search
	 */
	public function search( $search ) {
		if ( empty( $_GET['sSearch'] ) )
			return;
		
		// Start where
		$where = '';
		
		// Escape search string
		$search_string = $this->db->escape( $_GET['sSearch'] );
		
		foreach ( $search as $s => $st ) {
			// make sure it's separated if necessary
			if ( !empty( $where ) )
				$where .= ' OR ';
			
			// Add the starting percent
			$start = ( $st ) ? '%' : '';
			
			// Create the where
			$where .= $s . " LIKE '{$start}{$search_string}%'";
		}
		
		// Add on the search to the where
		$this->where .= " AND ( $where ) ";
	}
	
	/**
	 * Set the order by
	 *
	 * @param string the fields to order by -- database fields (unlimited)
	 */
	public function order_by() {
		$fields = func_get_args();
		
		// Loop through the columns
		for ( $i = 0; $i < intval( $_GET['iSortingCols'] ); $i++ ) {
			// Add the necessary comman
			if ( !empty( $this->order_by ) )
				$this->order_by .= ',';
			
			// Compile the fields
			$this->order_by .= $fields[$_GET['iSortCol_' . $i]] . ' ' . $_GET['sSortDir_' . $i];
		}
		
		// If it's not empty
		if ( !empty( $this->order_by ) )
			$this->order_by = ' ORDER BY ' . $this->order_by;
	}

	/**
	 * Return the variables
	 *
	 * return array( $where, $order_by, $limit )
	 */
	public function get_variables() {
		return array( $this->where, $this->order_by, $this->limit );
	}
	
	/**
	 * Return the where
	 *
	 * return string $where
	 */
	public function get_where() {
		return $this->where;
	}
	
	/**
	 * Set row count
	 *
	 * @param int $row_count
	 */
	public function set_row_count( $row_count ) {
		$this->row_count = $row_count;
	}
	
	/**
	 * Get Response
	 *
	 * @return string (json_encoded)
	 */
	public function get_response( $data ) {
		if ( !$data )
			$data = array();
		
		return json_encode( array( 
			'sEcho' => intval( $_GET['sEcho'] ),
			'iTotalRecords' => $this->row_count,
			'iTotalDisplayRecords' => $this->row_count,
			'aaData' => $data
		) );
	}
}