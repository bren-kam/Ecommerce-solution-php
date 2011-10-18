<?php
/**
 * @page QUnit Testing Page
 * @package Imagine Retailer
 */

global $user;

if ( !$user )
	login();

css( 'qunit' );
javascript( 'qunit', 'tests/sparrow', 'tests/' . $_GET['f'] );

// Encoded data to get css
list( $css, $ie8 ) = get_css();
$javascript = get_js();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>>QUnit Testing <?php echo $_GET['f']; ?></title>
<link type="text/css" rel="stylesheet" href="/css/?files=<?php echo $css; ?>" />
<script type="text/javascript" src="/javascript/head.js"></script>
<link rel="icon" href="<?php echo ( 'imagineretailer.com' == DOMAIN ) ? '/favicon.ico' : '/images/favicons/' . DOMAIN . '.ico'; ?>" type="image/x-icon" />
</head>
<body>
<input type="hidden" id="f" value="<?php echo $_GET['f']; ?>" />
<div id="wrapper">
	<div id="header">
		<?php $margin = floor( ( 108 - LOGO_HEIGHT ) / 2 ); ?>
		<div id="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" height="<?php echo LOGO_HEIGHT; ?>" alt="<?php echo TITLE, ' ', _('Logo'); ?>" style="margin: <?php echo $margin; ?>px 0" /></div>
	</div>
	<div id="nav">
		<div id="nav-links"></div>
	</div>
	<div id="content">
		<div id="qunit-header-wrapper">
		<h1 id="qunit-header">QUnit Testing <?php echo $_GET['f']; ?></h1>
		</div>
		<h2 id="qunit-banner"></h2>
		<div id="qunit-testrunner-toolbar"></div>
		<h2 id="qunit-userAgent"></h2>
		<ol id="qunit-tests"></ol>
		<div id="qunit-fixture"></div>
	</div>
	<div id="footer">
		<p></p>
		<br /><br />
		<p id="copyright">&copy; <?php echo _('Copyright'), ' ', dt::date('Y'), '. ', _('All Rights Reserved'); ?>.</p>
	</div>
</div>

<!-- End: Footer -->
<script type="text/javascript">head.js( 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', '/js/?files=<?php echo $javascript; ?>' );</script>
</body>
</html>