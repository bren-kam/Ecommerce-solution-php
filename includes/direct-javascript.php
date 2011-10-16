<?php
/**
 * Javascript
 *
 * Compresses Javascript and spits it all out in one file
 *
 * @package Real Statistics
 * @since 1.0
 */

global $w;
$js_file = $_GET['f'];

header::send( array( 
	'Content-Type' => 'text/javascript; charset=UTF-8',
	'Last-Modified' => 'Fri, 3 Jun 2011 00:29:23 GMT', // ?
	'Expires' => gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT',
	'X-Content-Type-Options' =>	'nosniff',
	'Cache-Control' => 'max-age=4320000, public',
	'Connection'	=> 'Keep-Alive',
	'Pragma'		=> 'public'
) );

// header_remove('Pragma'); Requires PHP 5.3

$js_paths = array( INC_PATH, THEME_PATH );

if( '/' == $js_file[0] )
	$js_file = substr( $js_file, 1 );

$js_file .= '.js';

foreach( $js_paths as $jsp ) {
	$jsf = $jsp . 'js/' . $js_file;
	
	if( is_file( $jsf ) ) {
		$js = $jsf;
		break;
	}
}

// Get the cache path
$compressed_js_file_path = INC_PATH . 'cache/js/' . $js_file;

// If a cache does not exist, create it, otherwise, read it
if( !file_exists( $compressed_js_file_path ) ) {
	$js = compress::javascript( file_get_contents( $js ) );
	
	// Write to file
	if( LIVE && $fh = @fopen( $compressed_js_file_path, 'w' ) ) {
		fwrite( $fh, $js );
		fclose( $fh );
	}
	
	// Echo the compressed Javascript
	echo $js;
} else {
	echo file_get_contents( $compressed_js_file_path );
}