<?php
/**
 * @package Imagine Retailer
 * @page Header
 */

css( 'jquery.ui' );
javascript( 'jquery', 'jquery.ui', 'jquery.qtip', 'header' );

// Encoded data to get css
list( $css, $ie8 ) = get_css();
global $title, $meta_description, $meta_keywords, $selected, $dynamic, $u, $user;

if( !empty( $selected ) )
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
<?php if( $ie8 ) { ?>
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
		<?php if( $user ) { ?>
		<a href="/logout/" id="aLogout" title="<?php echo _('Log out'); ?>"><?php echo _('Log out'); ?></a>
		
		<div id="links">
			<a href="/account-settings/" title="<?php echo _('Account Settings'); ?>"><?php echo _('Account Settings'); ?></a>
		</div>
		<?php } ?>
	</div>
	<div id="nav">
		<div id="nav-links">
			<?php if( $user ) { ?>
			<a href="/" title="<?php echo _('Home'); ?>"<?php echo $home; ?>><?php echo _('Home'); ?></a>
			<a href="/websites/" title="<?php echo _('Websites'); ?>"<?php echo $websites; ?>><?php echo _('Websites'); ?></a>
			<a href="/products/" title="<?php echo _('Products'); ?>"<?php echo $products; ?>><?php echo _('Products'); ?></a>
			<?php if( $user['role'] >= 7 ) { ?>
            <a href="/users/" title="<?php echo _('Users'); ?>"<?php echo $users; ?>><?php echo _('Users'); ?></a>
            <?php } ?>
			<a href="/checklists/" title="<?php echo _('Checklists'); ?>"<?php echo $checklists; ?>><?php echo _('Checklists'); ?></a>
			<a href="/requests/" title="<?php echo _('Requests'); ?>"<?php echo $requests; ?>><?php echo _('Requests'); ?></a>
			<a href="/craigslist/" title="<?php echo _('Craigslist'); ?>"<?php echo $craigslist; ?>><?php echo _('Craigslist'); ?></a>
			<?php if( $user['role'] >= 7 ) { ?>
			<a href="/reports/" title="<?php echo _('Reports'); ?>"<?php echo $reports; ?>><?php echo _('Reports'); ?></a>
			<?php } ?>
			<a href="/help/" title="<?php echo _('Help'); ?>"><?php echo _('Help'); ?></a>
			<?php } ?>
		</div>
	</div>

/** Body Section **/
$body = <body>;

/** Wrapper **/
$wrapper = <div id="wrapper">;

/** Header Section **/
$margin = 'margin: ' . floor( ( 108 - LOGO_HEIGHT ) / 2 ) . 'px 0';

$header = <div id="header">;
$header->appendChild( <div id="logo"><img src="/images/logos/{DOMAIN}.png" width={LOGO_WIDTH} height={LOGO_HEIGHT} alt={TITLE . ' ' . _('Logo')} style={$margin} /></div> )

if( $user ) {
	$header->appendChild( <a href="/logout/" id="aLogout" title={_('Log out')}>{_('Log out')}</a>
		<div id="links">
			<a href="/account-settings/" title={_('Account Settings')}>{_('Account Settings')}</a>
		</div>
	);
}

/** Navigation Section **/
$nav = <div id="nav">;
$nav_links = <div id="nav-links">;
		
if( $user ) { 
	$nav_links->appendChild( <a href="/" title={_('Home')}{$home}>{_('Home')}</a>
			<a href="/websites/" title={_('Websites')}{$websites}>{_('Websites')}</a>
			<a href="/products/" title={_('Products')}{$products}>{_('Products')}</a>
	);
	
	
	if( $user['role'] >= 7 )
		$nav_links->appendChild( <a href="/users/" title={_('Users')}{$users}>{_('Users')}</a> );
  	
	$nav_links->appendChild( <a href="/checklists/" title={_('Checklists')}{$checklists}>{_('Checklists')}</a>
			<a href="/requests/" title={_('Requests')}{$requests}>{_('Requests')}</a>
			<a href="/craigslist/" title={_('Craigslist')}{$craigslist}>{_('Craigslist')}</a>
	);
	
	
	if( $user['role'] >= 7 )
		$nav_links->appendChild( <a href="/reports/" title={_('Reports')}{$reports}>{_('Reports')}</a> );
	
	$nav_links->appendChild( <a href="/help/" title={_('Help')}>{_('Help')}</a> );
}

$nav->appendChild( $nav_links );

/** Content Section **/
global $content;

<div id="footer">
		<p>
			<?php if( $user ) { ?>
			<a href="/" title="<?php echo _('Home'); ?>"><?php echo _('Home'); ?></a> | 
			<a href="/websites/" title="<?php echo _('Websites'); ?>"><?php echo _('Websites'); ?></a> | 
			<a href="/products/" title="<?php echo _('Product Catalog'); ?>"><?php echo _('Product Catalog'); ?></a> | 
			<a href="/users/" title="<?php echo _('Users'); ?>"><?php echo _('Users'); ?></a> | 
			<a href="/checklists/" title="<?php echo _('Checklists'); ?>"><?php echo _('Checklists'); ?></a> | 
			<a href="/requests/" title="<?php echo _('Requests'); ?>"><?php echo _('Requests'); ?></a>
			<?php if( $user['role'] >= 8 ) { ?>
			<a href="/reports/" title="<?php echo _('Reports'); ?>"><?php echo _('Reports'); ?></a>
			<?php } ?>
			<a href="/help/" title="<?php echo _('Help'); ?>"><?php echo _('Help'); ?></a>
			<?php } ?>
		</p>
		<br /><br />
		<p id="copyright">&copy; <?php echo _('Copyright'); ?> <?php echo date('Y'); ?>. <?php echo _('All Rights Reserved'); ?>.</p>
	</div>
</div>

<!-- End: Footer -->
<?php 
$javascript = get_js();
if( 'eNpLtDKwqq4FXDAGTwH-' != $javascript ) { // That string means it's empty ?>
<script type="text/javascript" src="/js/?files=<?php echo $javascript; ?>"></script>
<?php 
}

footer();
?>
</body>
</html>