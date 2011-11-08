<?php
/**
 * @package Real Statistics
 * @page Header
 */

css( 'form', 'jquery.ui' );
javascript( 'swfobject', 'jquery', 'jquery.ui', 'jquery.qtip', 'jquery.boxy', 'jquery.form', 'jquery.uploadify', 'header' );

// Encoded data to get css
list( $css, $ie8 ) = get_css();
global $title, $meta_description, $meta_keywords, $selected, $dynamic, $u, $user;

if ( !empty( $selected ) )
	$$selected = ' class="selected"';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo $meta_description; ?>" />
<meta name="keywords" content="<?php echo $meta_keywords; ?>" />
<link type="text/css" rel="stylesheet" href="/css/?files=<?php echo $css; ?>" />
<?php if ( $ie8 ) { ?>
<!--[if IE 8]>
<link type="text/css" rel="stylesheet" href="/css/?files=<?php echo $ie8; ?>" />
<![endif]-->
<?php } ?>
<link rel="icon" href="<?php echo ( 'imagineretailer.com' == DOMAIN ) ? '/favicon.ico' : '/images/favicons/' . DOMAIN . '.ico'; ?>" type="image/x-icon" />
<?php head(); ?>
</head>
<body>
<?php top(); ?>
<div id="wrapper">
	<div id="header">
		<?php $margin = floor( ( 108 - LOGO_HEIGHT ) / 2 ); ?>
		<div id="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" height="<?php echo LOGO_HEIGHT; ?>" alt="<?php echo TITLE, ' ', _('Logo'); ?>" style="margin: <?php echo $margin; ?>px 0" /></div>
		<?php if ( $user ) { ?>
		<a href="/logout/" id="aLogout" title="<?php echo _('Log out'); ?>"><?php echo _('Log out'); ?></a>
		<?php } ?>
	</div>
	<div id="nav">
		<div id="nav-links">
			<?php if ( $user ) { ?>
			<a href="/" title="<?php echo _('Home'); ?>"<?php if ( isset( $home ) ) echo $home; ?>><?php echo _('Home'); ?></a>
			<a href="/websites/" title="<?php echo _('Websites'); ?>"<?php if ( isset( $websites ) ) echo $websites; ?>><?php echo _('Websites'); ?></a>
			<a href="/products/" title="<?php echo _('Products'); ?>"<?php if ( isset( $products ) ) echo $products; ?>><?php echo _('Products'); ?></a>
			<?php if ( $user['role'] >= 7 ) { ?>
            <a href="/users/" title="<?php echo _('Users'); ?>"<?php if ( isset( $users ) ) echo $users; ?>><?php echo _('Users'); ?></a>
            <?php } ?>
			<a href="/checklists/" title="<?php echo _('Checklists'); ?>"<?php if ( isset( $checklists ) ) echo $checklists; ?>><?php echo _('Checklists'); ?></a>
			<a href="/tickets/" title="<?php echo _('Tickets'); ?>"<?php if ( isset( $tickets ) ) echo $tickets; ?>><?php echo _('Tickets'); ?></a>
			<a href="/craigslist/" title="<?php echo _('Craigslist'); ?>"<?php if ( isset( $craigslist ) ) echo $craigslist; ?>><?php echo _('Craigslist'); ?></a>
			<?php if ( $user['role'] >= 7 ) { ?>
			<a href="/reports/" title="<?php echo _('Reports'); ?>"<?php if ( isset( $reports ) ) echo $reports; ?>><?php echo _('Reports'); ?></a>
			<?php } ?>
			<a href="http://admin.<?php echo str_replace( 'testing.', '', DOMAIN ); ?>/help/" title="<?php echo _('Help'); ?>"><?php echo _('Help'); ?></a>
			<?php } ?>
		</div>
		<?php /*if ( $user ) { ?>
		<div id="site-info">
			<img src="/images/icons/world.png" width="20" height="20" alt="Site" />
			<a href="#" title="Site: Master">Site: <span class="highlight">Master</span></a>
			<?php } ?>
		</div>
		*/ ?>
	</div>