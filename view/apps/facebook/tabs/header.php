<?php
/**
 * @package Grey Suit Retail - Facebook
 * @page Header
 *
 * @var Resources $resources
 * @var Template $template
 */

$resources->css_before( 'style' );
$resources->javascript( 'sparrow', 'fb', 'header' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $template->v('title'); ?></title>
<link type="text/css" rel="stylesheet" href="/resources/css/?f=<?php echo $resources->get_css_file(); ?>" />
<?php echo $resources->get_css_urls(); ?>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/headjs/1.0.2/head.load.min.js"></script>
</head>
<body>