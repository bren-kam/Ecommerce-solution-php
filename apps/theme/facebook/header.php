<?php
/**
 * @package Real Statistics
 * @page Header
 */

global $title, $u, $user, $selected;

// Encoded data to get css
list( $css, $ie8 ) = get_css();
javascript( 'jquery', 'fb' );


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="icon" href="http://www.greysuitapps.com/favicon.ico" type="image/x-icon" />
    <link href="http://fonts.googleapis.com/css?family=Ubuntu+Condensed" rel="stylesheet" type="text/css" />
    <link type="text/css" rel="stylesheet" href="http://www.greysuitapps.com/fb/css/style.css" />
    <style>
    	.hidden { display: none; }
    </style>
    <?php head(); ?>
</head>

<body class="apps <?php echo ( $selected ) ? $selected : ''; ?>">
<div id="wrapper">
<div id="page">    

    
    