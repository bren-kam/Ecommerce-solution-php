<?php
/**
 * @page Bot - Ashley
 * @package Imagine Retailer
 */

error_reporting(E_ALL);
ini_set( 'max_execution_time', 1200 ); // 20 minutes
ini_set( 'memory_limit', '1024M' );
set_time_limit( 1200 );

$file = ( isset( $_GET['f'] ) ) ? $_GET['f'] : NULL;

$a = new Ashley();

$a->run( $file );