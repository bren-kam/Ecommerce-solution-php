<?php
/**
 * Spits out the code for a good dialog
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Dialog {
	/**
	 * The type of dialog
	 * @var string
	 */
	private $type = '';
	
	/**
	 * Content
	 * @var string
	 */
	private $html = '';

	/**
	 * Buttons
	 * @var array
	 */
	private $buttons = array();
	
	/**
	 * Construct initializes data
	 *
	 * @param string $nonce
	 * @param string $key
	 * @param bool $override (optional|false)
	 */
	public function __construct( $type = 'html' ) {
		$this->type = $type;
		
		return $this;
	}
	
	/**
	 * Set the content
	 *
	 * @param string $html
	 */
	public function content( $html ) {
		// Set the variable
		$this->html = $html;
		
		return $this;
	}
	
	/**
	 * Display
	 */
	public function display() {
		echo $this->html;
	}
	
	/**
	 * Confirm Box
	 */
	public function confirm( $href, $classes = '', $attributes = '' ) {
		echo $this->html;
	}
}
