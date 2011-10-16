<?php
/**
 * @page Logout
 * @package Imagine Retailer
 */
global $u;
$u->logout();

// Redirect to login page
url::redirect('/login/');