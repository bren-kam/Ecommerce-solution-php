<?php
/**
 * @package Real Statistics
 * @page Header
 */

javascript( 'sparrow', 'header' );

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
<script type="text/javascript" src="/javascript/head.js"></script>
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
		
		<div id="links"><a href="/settings/" title="<?php echo _('Account Settings'); ?>"><?php echo _('Account Settings'); ?></a></div>
		<?php } ?>
	</div>
	<div id="nav">
		<div id="nav-links">
			<?php if ( $user ) { ?>
				<a href="/" title="<?php echo _('Home'); ?>"<?php if ( isset( $home ) ) echo $home; ?>><?php echo _('Home'); ?></a>
				<?php
				if ( isset( $user['website'] ) ) { 
					$links = array( 
						'pages'				=> array( 'website', _('Website') )
						, 'product_catalog'	=> ( 'High Impact' == $user['website']['type'] ) ? array( 'products/top-brands', _('Brands') ) : array( 'products', _('Products') )
						, 'live' 				=> array( 'analytics', _('Analytics') )
						, 'blog'				=> array( '', 'Blog' )
						, 'email_marketing'	=> array( 'email-marketing', _('Email Marketing') )
						, 'shopping_cart'		=> array( 'shopping-cart/users', _('Shopping Cart') )
						, 'craigslist'		=> array( 'craigslist', _('Craigslist Ads') )
						, 'social_media'	=> array( 'social-media', _('Social Media') )
					);
					
					$keys = array_keys( $links );
					
					foreach ( $user['website'] as $k => $v ) {
						// Don't need to deal with antyhing other keys
						if ( 'email_marketing' != $k && ( !in_array( $k, $keys ) || !$v ) )
							continue;
						
						$selectedClass = ( $k == $selected ) ? ' class="selected"' : '';
						
						if ( 'blog' == $k ) {
						?>
						<form action="http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain']; ?>/blog/wp-login.php" target="_blank" method="post" id="fBlogForm">
							<input type="hidden" name="log" value="<?php echo security::decrypt( base64_decode( $user['website']['wordpress_username'] ), ENCRYPTION_KEY ); ?>" />
							<input type="hidden" name="pwd" value="<?php echo security::decrypt( base64_decode( $user['website']['wordpress_password'] ), ENCRYPTION_KEY ); ?>" />
						</form>
						<a href="javascript:document.getElementById('fBlogForm').submit();" title="<?php echo $links[$k][1]; ?>"<?php echo $selectedClass; ?>><?php echo $links[$k][1]; ?></a>
						<?php } else { ?>
						<a href="/<?php echo $links[$k][0]; ?>/" title="<?php echo $links[$k][1]; ?>"<?php echo $selectedClass; ?>><?php echo $links[$k][1]; ?></a>
						<?php
						}
					}
				}
			}
			?>
		</div>
		<div id="site">
			<?php 
			if ( $user && $user['website'] ) {
				?> 
				<a href="http://<?php echo $user['website']['domain']; ?>/" title="<?php echo $user['website']['title']; ?>" target="_blank"><?php echo '<span class="highlight">', _('Site:'), '</span> ', $user['website']['title']; ?></a>
				<?php if ( count( $user['websites'] ) > 1 ) { ?>
				<span class="highlight">(</span> <a href="/dialogs/change-website/#dChangeWebsite" title="<?php echo _('Change Active Site'); ?>" rel="dialog"><?php echo _('Change'); ?></a> <span class="highlight">)</span>
				<?php
				}
			}
			?>
		</div>
	</div>