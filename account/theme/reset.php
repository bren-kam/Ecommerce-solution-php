<?php
/**
 * @page Reset image dimensions
 * @package Imagine Retailer
 */
$w = new Websites;

echo ( $w->delete_image_dimensions( $_GET['url'] ) ) ? 'Success' : 'Failure';