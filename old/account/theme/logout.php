<?php
/**
 * @page Logout
 * @package Grey Suit Retail
 */
global $u;
$u->logout();

// Redirect to login page
url::redirect('/login/');