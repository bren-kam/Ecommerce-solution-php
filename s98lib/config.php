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
define( 'DEBUG', TRUE );
define( 'DEBUG_EMAIL', 'serveradmin@imagineretailer.com' );		// Used to be Info

// Email
define( 'FROM_EMAIL', 'noreply@imagineretailer.com' );
define( 'FROM_NAME', 'ImagineRetailer.com' );

// Keys ( http://framework.studio98.com/keys/1.0/ )
define( 'SECRET_KEY',	 	'iSZuFFf:owshCCmU]@~$s!fzCFljweRx[*/{~VdaOOj(?djJ@?H_VAJX**(iLmuW' );
define( 'ENCRYPTION_KEY', 	'#JH-ka$IXz,vVyR$sbtUk]Hn@waMLcPwX"$?&:eJinqCz--]-g^^&|],Zt/Rs("B' );
define( 'NONCE_KEY', 		'R~?K|}B&YcePu);)$rzQ$gYG@p+JhtcFJaI$]._a*GQ!ZbNl}QPNx?wlKrw"UU")' );
define( 'POST_KEY', 	'x{k)aOt$OY@YCu:V(#GK%X&m/Il*xQAx"qINkUtwTdyt@IEA/(cWyBGt-qb-A/H&' );
define( 'COOKIE_KEY', 		'wE[D..ffx&IP%!ICpm"~++}/Db&aP"WP#{|(-xi?)VDPzT+pomwtWNx!!d[HZk~e' );
define( 'PAYMENT_DECRYPTION_KEY',	'@p(-PzQ/AD%oQybq$xgGOo)(|d/(@Pbf_@?DCIx,PqLa=IJ#JJl*Iv,toiAus!xH' );

// Settings
if ( !defined( 'DEFAULT_TIMEZONE' ) )
    define( 'DEFAULT_TIMEZONE', 'America/Chicago' );

// Modules
$modules = array( 'validator' );

// Options
define( 'START_SESSIONS', TRUE ); // start sessions when included
define( 'AUTOLOAD', FALSE ); // autoload these classes - if set to false, call s98_fw_classes( $class_name )
define( 'SAVE_QUERIES', FALSE ); // this will save information about every SQL query
define( 'COOKIE_PATH', '/home/develop4/public_html/includes/cookies.txt' );

/***** Don't edit below this line *****/
// Definitions
if ( !defined('FWPATH') )
	define('FWPATH', dirname(__FILE__) . '/');

if ( !defined('MODPATH') )
	define('MODPATH', FWPATH . 'modules/');

if ( !defined('MODURL') )
	define('MODURL', FWURL . 'modules/');

if ( !defined('NONCE_DURATION') )
	define( 'NONCE_DURATION' , 43200 ); // 43200 makes link or form good for 12 hours from time of generation