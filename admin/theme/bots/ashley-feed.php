<?php
/**
 * @page Bot - Ashley
 * @package Imagine Retailer
 */

error_reporting(E_ALL);
ini_set( 'max_execution_time', 600 ); // 10 minutes
ini_set( 'memory_limit', '512M' );
set_time_limit( 600 );


$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;

$a = new Ashley_Feed();
//$a->run( 371, $file );
$a->run_all();