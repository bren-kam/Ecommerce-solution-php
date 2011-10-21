<?php
/**
 * @page Dashboard
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css('dashboard');

$selected = 'home';
$title = _('Dashboard') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Dashboard'); ?></h1>
	<br clear="all" />
	<br /><br />
	<div id="nav-icons">
		<?php
		if ( $user && isset( $user['website'] ) ) {
            // Need to get settings
            $w = new Websites;
			$settings = $w->get_settings('advertising-url');

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
				
				if ( 'blog' == $k ) {
                    ?>
                    <a href="javascript:document.getElementById('fBlogForm').submit();" title="<?php echo $links[$k][1]; ?>" id="blog"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo $links[$k][1]; ?>" /><br /><?php echo $links[$k][1]; ?></a>
                <?php } else { ?>
                    <a href="/<?php echo $links[$k][0]; ?>/" title="<?php echo $links[$k][1]; ?>" id="<?php echo $links[$k][0]; ?>"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo $links[$k][1]; ?>" /><br /><?php echo $links[$k][1]; ?></a>
                    <?php
				}
                
                if ( !empty( $settings['advertising-url'] ) ) {
				?>
                    <a href="<?php echo $settings['advertising-url']; ?>" title="<?php echo _('Advertising Portal'); ?>" id="advertising-url" target="_blank"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Advertising Portal'); ?>" /><br /><?php echo _('Advertising Portal'); ?></a>
                    <?php
                }
			}
		}
		?>
	</div>
	<br clear="all" />
	
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>