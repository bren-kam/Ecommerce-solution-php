<?php
/**
 * CSS
 *
 * Compresses CSS and spits it all out in one file
 *
 * @package Real Statistics
 * @since 1.0
 */

global $w;
$files = url::decode( $_GET['files'] );

header::send( array( 
	'Content-Type' => 'text/css; charset=UTF-8',
	'Vary' => 'Accept-Encoding',
	'Expires' => gmdate( "D, d M Y H:i:s", time() + 864000 ) . ' GMT',
	'Cache-Control' => 'max-age=4320000, public',
	'Connection'	=> 'Keep-Alive'
) );

$css_paths = array( INC_PATH, THEME_PATH );

// Find the CSS files and combine them
foreach ( $files as $css_file ) {
	if ( '/' == $css_file[0] )
		$css_file = substr( $css_file, 1 );
	
	$css_file = ( !stristr( $css_file, '.css' ) ) ? $css_file . '.css' : $css_file;
	
	foreach ( $css_paths as $cssp ) {
	 	$cssf = $cssp . 'css/' . $css_file;
		
		if ( is_file( $cssf ) )
			$css_files[] = $cssf;
	}
}

// Get the cache path
$compressed_css_file_path = INC_PATH . 'cache/css/' . md5( implode( '|', $css_files ) ) . '.css';

// If a cache does not exist, create it, otherwise, read it
if ( !file_exists( $compressed_css_file_path ) ) {
	// Declare variables
	$css = '';
	
	foreach ( $css_files as $css_file ) {
		//$css .= file_get_contents( $css_file ); 
		$css .= compress::css( file_get_contents( $css_file ) );
	}
	
	$compress_css = new Compress_CSS( $css, false, true );
	$css = $compress_css->css;
	
	// Write to file
	if ( LIVE && $fh = @fopen( $compressed_css_file_path, 'w' ) ) {
		fwrite( $fh, $css );
		fclose( $fh );
	}
	
	// Echo the compressed css
	echo $css;
} else {
	echo file_get_contents( $compressed_css_file_path );
}