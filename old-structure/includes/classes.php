<?php
/**
 * Loads each class as it is called
 *
 * Tries to load from custom first, then core directory
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
if ( !function_exists( '__autoload' ) ) :
function __autoload( $class_name ) {
	$class_name = str_replace( '_', '-', strtolower( $class_name ) );
	
	// Need to include the classes correctly
	$shopping_cart = ( in_array( $class_name, array( 'cart', 'order', 'shipping', 'payment' ) ) ) ? 'shopping_cart/' : '';
	
	if ( defined('ACCOUNT') && ACCOUNT ) {
		$folder = 'account';
	} elseif ( defined('ADMIN') && ADMIN ) {
		$folder = 'admin';
	} elseif ( defined('APPS') && APPS ) {
		$folder = 'apps';
	} elseif( defined('API') && API ) {
		$folder = 'api';
    }
	
	if ( !inc( 'classes/' . $shopping_cart . $class_name ) && !inc( "classes/$folder/{$shopping_cart}{$class_name}" ) )
		s98lib_classes( $class_name );
}
endif;