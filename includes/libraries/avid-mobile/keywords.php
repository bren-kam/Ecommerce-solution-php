<?php
/**
 * Avid Mobile (Mobile Marketing) - Keywords API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
library('avid-mobile-api');

class AM_Keywords extends Avid_Mobile_API {
     /**
	 * Construct class will initiate and run everything
	 *
	 * @param int $customer_id
	 */
	public function __construct( $customer_id ) {
        if ( !parent::__construct( $customer_id ) )
            return false;
	}

    /**
	 * Create Keyword
	 *
     * @param string $name
     * @param string $keyword
     * @param string $reply_1
     * @param string $start_time
     * @param string $start_timezone
	 * @return int
	 */
	public function create( $name, $keyword, $reply_1, $start_time, $start_timezone ) {
        // Return the keyword_campaign_id
		return $this->_execute( self::OPERATION_PUT, 'keyword.create', compact( 'name', 'keyword', 'reply_1', 'start_time', 'start_timezone' ) );
	}

    /**
	 * Update Keyword
	 *
     * @param int $id The Keyword Campaign ID
     * @param string $name
     * @param string $keyword
     * @param string $reply_1
     * @param string $start_time
     * @param string $start_timezone
	 * @return bool
	 */
	public function update( $id, $name, $keyword, $reply_1, $start_time, $start_timezone ) {
        // Return the keyword_campaign_id
		$this->_execute( self::OPERATION_PUT, 'keyword.update', compact( 'id', 'name', 'keyword', 'reply_1', 'start_time', 'start_timezone', 'end_time', 'end_timezone' ) );

        // Return success
        return $this->success();
	}

    /**
	 * Delete Keyword
	 *
     * @param int $id The Keyword Campaign ID
	 * @return bool
	 */
	public function delete( $id ) {
        // Return the keyword_campaign_id
		$this->_execute( self::OPERATION_PUT, 'keyword.delete', compact( 'id' ) );
        
        // Return success
        return $this->success();
	}

    /**
     * Check if a keyword is available
     *
     * @param string $keyword
     * @return bool
     */
    public function available( $keyword ) {
        // Return the keyword_campaign_id
        $this->_execute( self::OPERATION_GET, 'keyword.isavailable', compact( 'keyword' ) );

        return $this->success();
    }
}