<?php
/**
 * Template Functions
 *
 * This contains all the functions you can use within a template page
 *
 * @package Real Statistics
 * @since 1.0
 */

/**
 * Properly includes the header
 *
 * @param string $prefix
 */
function get_header( $prefix = '' ) {
	theme_inc( $prefix . 'header', true );
	flush();
}

/**
 * Properly includes the print header
 */
function get_print_header() {
	theme_inc( 'header-print', true );
}

/**
 * Properly includes the header
 *
 * @param string $prefix (optional|)
 * @param string %seleced (optional|)
 */
function get_sidebar( $prefix = '', $selected = '') {
	if ( !empty( $selected ) )
		$$selected = true;
	
	$file_path = theme_inc( $prefix . 'sidebar' );
	
	if ( $file_path )
		require( $file_path );
}

/**
 * Properly includes the header
 *
 * @param string $prefix
 */
function get_footer( $prefix = '' ) {
	theme_inc( $prefix . 'footer', true );
}

/**
 * Properly includes the header
 */
function get_print_footer() {
	theme_inc( 'footer-print', true );
}

/**
 * Gets the CSS include string
 *
 * @return array ( $css, $ie8 )
 */
function get_css() {
	global $t;
	
	$browser = fn::browser();
	
	// Do we need to include IE?
	$ie8 = ( 'Msie' == $browser['name'] && version_compare( '7', $browser['version'], '<' ) && count( $t->css_ie8 ) > 0 ) ? url::encode( $t->css_ie8 ) : false;
	
	return array( url::encode( $t->css ), $ie8 );
}

/**
 * Gets the Javascript include string
 *
 * @param bool $extra (optional|false)
 * @return $javascript
 */
function get_js( $extra = false ) {
	global $t;
	
	return ( $extra ) ? array( url::encode( $t->javascript ), $t->before_javascript, $t->javascript_callback ) : url::encode( $t->javascript );
}

/**
 * Gets the Javascript callback
 *
 * @return $javascript
 */
function get_js_callback() {
	global $t;
	
	return $t->javascript_callback;
}

/**
 * Processes head hook
 */
function head() {
	global $t;
	// Do stuff
	
	echo $t->head;
}

/**
 * Add head string
 */
function add_head( $string ) {
	global $t;
	
	$t->head .= $string;
}

/**
 * Processes footer hook
 */
function footer( $echo = true ) {
	global $t;
	
	if ( $echo ) 
		echo $t->footer;
	
	return $t->footer;
}

/**
 * Add footer string ddata
 *
 * @param string $string
 */
function add_footer( $string ) {
	global $t;
	
	$t->footer .= $string;
}

/**
 * Add javascript callback
 *
 * @param string $string
 */
function add_javascript_callback( $string ) {
	global $t;
	
	$t->javascript_callback .= $string;
}

/**
 * Add javascript before the rest is loaded
 *
 * @param string $string
 */
function add_before_javascript( $string ) {
	global $t;
	
	$t->before_javascript .= $string;
}

/**
 * Get theme data
 *
 * @param string $key the key for the array of data
 * @param bool $echo (optional) whether to display the information or not
 * @return string|int
 */
function theme( $key, $echo = true ) {
	switch ( $key ) {
		case 'path':
			$data = THEME_PATH;
			break;
		
		case 'url':
			$data = THEME_URL;
			break;
		
		default:
			break;
	}
	
	if ( $echo )
		echo $data;
	
	return $data;
}

/**
 * Processes top hook (directly after body)
 */
function top() {
}

/**
 * Add javascript data to queue
 *
 * @param args ( $file, $file2, $file3 )
 * @return string|int
 */
function javascript() {
	global $t;
	
	$files = func_get_args();
		
	foreach ( $files as $f ) {
		if ( !in_array( $f, $t->javascript ) )
			$t->javascript[] = $f;
	}
}


/**
 * Add CSS data to queue
 *
 * @param args ( $file, $file2, $file3 )
 * @return string|int
 */
function css() {
	global $t;
	
	$files = func_get_args();
		
	foreach ( $files as $f ) {
		if ( !in_array( $f, $t->css ) )
			$t->css[] = $f;
	}
}

/**
 * Add IE8 CSS data to queue
 *
 * @param args ( $file, $file2, $file3 )
 * @return string|int
 */
function css_ie8() {
	global $t;
	
	$files = func_get_args();
		
	foreach ( $files as $f ) {
		if ( !in_array( $f, $t->css_ie8 ) )
			$t->css_ie8[] = 'ie8/' . $f;
	}
}

/**
 * Access sidebar variables
 *
 * @param string $key the key of the variables
 * @param bool $echo (optional)
 * @return string
 */
function sidebar( $key ) {
	global $cache;
	
	$sidebar = $cache->get( 'sidebar' );
	
	if ( empty( $sidebar ) ) {
		global $w;
		
		$sidebar_page = $w->get_page( 'sidebar' );
		$sidebar = $w->get_page_attachments( $sidebar_page['website_page_id'] );
		
		$cache->add( 'sidebar', $sidebar );
	}
	
	return $sidebar[$key];
}

/**
 * Redirects to the login page with referer credentials
 */
function login() {
	$referer = ( isset( $_SERVER['REDIRECT_URL'] ) ) ? $_SERVER['REDIRECT_URL'] : '';

    if ( !empty( $_SERVER['QUERY_STRING'] ) )
        $referer .= '?' . $_SERVER['QUERY_STRING'];

	url::redirect( '/login/?r=' . urlencode( $referer ) );
}