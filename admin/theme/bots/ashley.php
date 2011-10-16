<?php
/**
 * @page Bot - Ashley
 * @package Imagine Retailer
 */

error_reporting(E_ALL);
ini_set( 'max_execution_time', 600 ); // 10 minutes
ini_set( 'memory_limit', '512M' );
set_time_limit( 600 );


$a = new Ashley();
$a->run( $_GET['f'] );