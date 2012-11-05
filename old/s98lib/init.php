<?php
/**
 * Initalizes Studio98 Framework
 *
 * @package Studio98 Framework
 * @since 1.0
 */

// Include config
require dirname(__FILE__) . '/config.php';

// Should we start the session?
if ( START_SESSIONS )
	session_start();

// Include classes
require_once FWPATH . 'classes.php';
?>