<?php
/**
 * @page Social Media - Facebook - Email Sign Up
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;
$facebook_url = $w->get_setting('facebook-url');
	
css('social-media/facebook/main');
$selected = "social_media";
$title = _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Facebook'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<p class="sm">
			<a href="/social-media/facebook/email-sign-up/" title="<?php echo _('Email Sign Up'); ?>"><img src="/images/social-media/facebook/email-sign-up.jpg" width="75" height="75" alt="<?php echo _('Email Sign Up'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/email-sign-up/" title="<?php echo _('Email Sign Up'); ?>"><?php echo _('Email Sign Up'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/fan-offer/" title="<?php echo _('Fan Offer'); ?>"><img src="/images/social-media/facebook/fan-offer.jpg" width="75" height="75" alt="<?php echo _('Fan Offer'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/fan-offer/" title="<?php echo _('Fan Offer'); ?>"><?php echo _('Fan Offer'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/sweepstakes/" title="<?php echo _('Sweepstakes'); ?>"><img src="/images/social-media/facebook/sweepstakes.jpg" width="75" height="75" alt="<?php echo _('Sweepstakes'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/sweepstakes/" title="<?php echo _('Sweepstakes'); ?>"><?php echo _('Sweepstakes'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/share-and-save/" title="<?php echo _('Share and Save'); ?>"><img src="/images/social-media/facebook/share-and-save.jpg" width="75" height="75" alt="<?php echo _('Share and Save'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/share-and-save/" title="<?php echo _('Share and Save'); ?>"><?php echo _('Share and Save'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/facebook-site/" title="<?php echo _('Fan Offer'); ?>"><img src="/images/social-media/facebook/facebook-site.jpg" width="75" height="75" alt="<?php echo _('Facebook Site'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/facebook-site/" title="<?php echo _('Facebook Site'); ?>"><?php echo _('Facebook Site'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/contact-us/" title="<?php echo _('Contact Us'); ?>"><img src="/images/social-media/facebook/contact-us.jpg" width="75" height="75" alt="<?php echo _('Contact Us'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/contact-us/" title="<?php echo _('Contact Us'); ?>"><?php echo _('Contact Us'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/about-us/" title="<?php echo _('About Us'); ?>"><img src="/images/social-media/facebook/about-us.jpg" width="75" height="75" alt="<?php echo _('About Us'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/about-us/" title="<?php echo _('About Us'); ?>"><?php echo _('About Us'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/products/" title="<?php echo _('Products'); ?>"><img src="/images/social-media/facebook/products.jpg" width="75" height="75" alt="<?php echo _('Products'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/products/" title="<?php echo _('Products'); ?>"><?php echo _('Products'); ?></a>
		</p>
		<p class="sm">
			<a href="/social-media/facebook/current-ad/" title="<?php echo _('Current Ad'); ?>"><img src="/images/social-media/facebook/current-ad.jpg" width="75" height="75" alt="<?php echo _('Current Ad'); ?>" /></a>
			<br />
			<a href="/social-media/facebook/current-ad/" title="<?php echo _('Current Ad'); ?>"><?php echo _('Current Ad'); ?></a>
		</p>
		<?php if ( !empty( $facebook_url ) ) { ?>
		<p class="sm">
			<a href="<?php echo $facebook_url; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><img src="/images/social-media/facebook/analytics.jpg" width="75" height="75" alt="<?php echo _('Analytics'); ?>" /></a>
			<br />
			<a href="<?php echo $facebook_url; ?>" title="<?php echo _('Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a>
		</p>
		<?php } ?>
        <p class="sm">
            <a href="/social-media/facebook/posting/" title="<?php echo _('Posting'); ?>"><img src="/images/social-media/facebook/posting.jpg" width="75" height="75" alt="<?php echo _('Posting'); ?>" /></a>
            <br />
            <a href="/social-media/facebook/posting/" title="<?php echo _('Posting'); ?>"><?php echo _('Posting'); ?></a>
        </p>
		<br clear="all" /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>