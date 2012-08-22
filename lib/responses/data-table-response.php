<?php
/**
 * Handles Data Table responses
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class DataTableResponse extends AjaxResponse {
	/**
	 * Holds the where information
	 * @var string
	 */
	protected $where = '';

    /**
     * Hold values
     *
     * @param array $values
     */
    protected $values = array();
	
	/**
	 * Holds the order_by information
	 * @var string
	 */
	protected $order_by = '';

	/**
	 * Holds the limit information
	 * @var string
	 */
	protected $limit = 100;

	/**
	 * Holds the row count information
	 * @var int
	 */
	protected $row_count = 0;

    /**
     * Hold data
     *
     * @var array
     */
    protected $data;

    /**
     * Hold error
     * @var bool
     */
    protected $error = false;

	/**
	 * Construct initializes data
     *
     * @param User $user
	 */
	public function __construct( $user ) {
		// If user is not logged in
		if ( !$user )
			$this->error = true;

        // Set display length
        $display_length = ( -1 == $_GET['iDisplayLength'] ) ? 1000 : intval( $_GET['iDisplayLength'] );

		// Set the limit
		$this->limit = ( isset( $_GET['iDisplayStart'] ) ) ? (int) $_GET['iDisplayStart'] . ', ' . $display_length : 1000;
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

		foreach ( $search as $s => $st ) {
			// make sure it's separated if necessary
			if ( !empty( $where ) )
				$where .= ' OR ';
			
			// Add the starting percent
			$start = ( $st ) ? '%' : '';
			
			// Create the where
			$where .= $s . " LIKE ?";

            $this->values[] = $start . $_GET['sSearch'] . '%';
		}
		
		// Add on the search to the where
		$this->where .= " AND ( $where ) ";
	}
	
	/**
	 * Set the order by
	 *
	 * @param string $arg1..., $arg2...
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
	 * @return array( $where, $values, $order_by, $limit )
	 */
	public function get_variables() {
		return array( $this->where, $this->values, $this->order_by, $this->limit );
	}

	/**
	 * Return the variables for counting
	 *
	 * @return array( $where, $values )
	 */
	public function get_count_variables() {
		return array( $this->where, $this->values );
	}
	
	/**
	 * Return the where
	 *
	 * @return string $where
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
     * Set data
     *
     * @param array $data
     */
    public function set_data( $data ) {
        $this->data = ( $data ) ? $data : array();
    }

	/**
	 * Get Response
	 */
	public function respond() {
        // Set it to JSON
        header::type('json');

        if ( $this->has_error() ) {
            echo json_encode( array(
                'redirect' => true,
                'sEcho' => intval( $_GET['sEcho'] ),
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                'aaData' => array()
            ) );
        } else {
            echo json_encode( array(
                'sEcho' => intval( $_GET['sEcho'] ),
                'iTotalRecords' => $this->row_count,
                'iTotalDisplayRecords' => $this->row_count,
                'aaData' => $this->data
            ) );
        }
	}
}