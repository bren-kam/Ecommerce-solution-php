<?php
/**
 * Studio98 Framework Config
 *
 * @package Studio98 Framework
 * @since 1.0
 */

// Framework Path
//define( 'FWPATH', 'replace_me' );

// Framework URL
define( 'FWURL', '/s98_fw/' );

// Debug
define( 'DEBUG', true );
define( 'DEBUG_EMAIL', 'replace_me' );

// Keys ( http://framework.studio98.com/keys/1.0/ )
define( 'SECRET_KEY',	 	'Y#t(dW-(=&^K{,[^.LU;M&{^w@vEm&ags}R,oyrvxhdi;-mP_},FYQgw*wka[AJW' );
define( 'ENCRYPTION_KEY', 	'Yyse?{}W@/oWohaKvN.U+q:$t?%hM@;L|"]xBUl]=LA(JAW=K}!xdj|=O$%So-X/' );
define( 'NONCE_KEY', 		'k=OA(pOUh;l*Ka+S-L;:d,uPdMHh=t*AQ_KNM?p^_$^H.:D&UQv?E#mZ!c})rL"t' );

// Modules
$modules = array( 'validator' );

// Options
define( 'START_SESSIONS', true ); // start sessions when included

/***** Don't edit below this line *****/

// Definitions
if ( !defined('FWPATH') )
	define('FWPATH', dirname(__FILE__) . '/');

if ( !defined('MODPATH') )
	define('MODPATH', FWPATH . 'modules/');

if ( !defined('MODURL') )
	define('MODURL', FWURL . 'modules/');

if( !defined('NONCE_DURATION') )
	define( 'NONCE_DURATION' , 21600 ); // 21600 makes link or form good for 6 hours from time of generation
