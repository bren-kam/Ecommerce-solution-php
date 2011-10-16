<?php
/**
 * Template hooks
 *
 * @package Imagine Retailer
 * @since 1.0
 */

class Template extends Base_Class {
	/**
	 * Sets the variable to hold CSS files
	 * @var array
	 */
	public $css = array( 'style' );

	/**
	 * Sets the variable to hold CSS files for IE 8
	 * @var array
	 */
	public $css_ie8 = array();

	/**
	 * Sets the variable to hold Javascript files
	 * @var array
	 */
	public $javascript = array();
	
	/**
	 * Sets the variable to hold footer strings
	 * @var string
	 */
	public $footer = '';
	
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
		
		inc( 'template' );
		
		if( 'www' != SUBDOMAIN )
			$this->css[] = 'labels/' . DOMAIN;
	}
	
	public function header() {
		// Header hook
	}
}