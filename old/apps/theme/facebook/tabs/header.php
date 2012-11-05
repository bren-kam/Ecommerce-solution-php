<?php
/**
 * @package Real Statistics
 * @page Header
 */

global $title, $u, $user;

// Encoded data to get css
list( $css, $ie8 ) = get_css();

if( !empty( $selected ) )
	$$selected = ' class="selected"';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<link type="text/css" rel="stylesheet" href="/css/?files=<?php echo $css; ?>" />
</head>
<body>