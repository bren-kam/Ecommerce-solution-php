<?php
/**
 * Timer class, times functions
 *
 * @package Studio98 Library
 * @since 1.0
 */

class timer extends Base_Class {
	/**
     * Record time data
     */
    private $_start = 0;
    private $_stop = 0;
    private $_elapsed = 0;
	
	/**
     * Constructor will start timer automatically by default
     *
     * @param bool $start [optional]
     * @return timer
     */
    public function __construct( $start = true ) {
        if ( $start )
            $this->start();
    }

    /**
     * Start timer
     *
     * @return float
     */
    public function start() {
        // We want a float
        $this->_start = microtime(true);

        return $this->_start;
    }

    /**
     * Stop timer
     *
     * @return float
     */
    public function stop() {
        $this->_stop = microtime(true);
        $this->_elapsed = $this->_stop - $this->_start;

        return $this->_elapsed;
    }

    /**
     * Get Start
     *
     * @return float
     */
    public function get_start() {
        return $this->_start;
    }

    /**
     * Get End
     *
     * @return float
     */
    public function get_stop() {
        return $this->_stop;
    }

    /**
     * Get Elapsed
     *
     * @return float
     */
    public function get_elapsed() {
        return $this->_elapsed;
    }
}