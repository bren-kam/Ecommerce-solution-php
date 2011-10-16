<?php
/**
 * @page Logout
 * @package Real Statistics
 */
global $u;
$u->logout();

// Redirect to login page
url::redirect('/login/');