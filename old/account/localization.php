<?php
/**
 * Localization
 *
 * Determines what language to use
 *
 * @package Grey Suit Retail
 * @since 1.0
 */

if ( isset( $_GET["locale"] ) )
	$locale = $_GET["locale"];

// Try to determine the language from the header accept-language
if ( !isset( $locale ) && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
	// We want the middle piece
	$lang = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
	
	// The locale is in the form of 'en_EN'
	$locale = $lang[1] . '_' . strtoupper( $lang[1] );
} else {
	// The locale is in the form of 'en_EN'
	$locale = $locale . '_' . strtoupper( $locale );
}

putenv( "LC_ALL=$locale" );
setlocale( LC_ALL, $locale );
bindtextdomain( "messages", "./includes/locale" );
textdomain( "messages" );