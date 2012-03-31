<?php
/**
 * @page Social Media - Facebook - Analytics
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure they have access to this page
$w = new Websites;
$social_media_add_ons = @unserialize( $w->get_setting( 'social-media-add-ons' ) );

if ( !is_array( $social_media_add_ons ) || !in_array( 'analytics', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$sm = new Social_Media;

// Get variables 
$analytics = $sm->get_analytics();

if ( !$analytics ) {
	$analytics['key'] = $sm->create_analytics();
	$analytics['token'] = '';
}

$selected = "social_media";
$title = _('Analytics') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Analytics'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if ( empty( $analytics['token'] ) ) { ?>
		<h2 class="title"><?php echo _('Step 1: Go to the Analytics application.'); ?></h2>
			<p><?php echo _('Go to the'); ?> <a href="http://www.facebook.com/apps/application.php?id=179756052091285" title="<?php echo _('Online Platform - Analytics'); ?>" target="_blank"><?php echo _('Analytics'); ?></a> <?php echo _('application page'); ?>.</p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 2: Install on your Fan Page'); ?></h2>
			<p><?php echo _('Click'); ?> <strong><?php echo _('Add to my Page (bottom left of your page).'); ?></strong></p>
			<p><strong><?php echo _('NOTE'); ?>:</strong> <?php echo _("If you do not see this link, it means you either don't have administrative access to any fan pages, or you already have this application installed. (If it is already installed, please ahead skip to Step 4.)"); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step2.jpg" class="image-border" width="884" height="521" alt="<?php echo _('Step 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 3: Click on the Add to Page Button.'); ?></h2>
			<p><?php echo _('Choose the Facebook Fan Page you want to add your app to by clicking on the'); ?> <strong><?php echo _('Add to Page'); ?></strong> <?php echo _('button to the right of the Fan Page name.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step3.jpg" class="image-border" width="883" height="240" alt="<?php echo _('Step 3'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 4: Click on the App.'); ?></h2>
			<p><?php echo _('Go to your Fan Page and click on the App you are installing from the list on the left.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step4.jpg" class="image-border" width="750" height="594" alt="<?php echo _('Step 4 - 1'); ?>" /></p>
			<br />
			<p><?php echo _('Click on'); ?> <strong><?php echo _('Update Settings'); ?></strong> <?php echo _('right under the app name. Note: This is only visible to you because you are the admin for this page.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step4-1.jpg" class="image-border" width="673" height="187" alt="<?php echo _('Step 4 - 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 5: Connect the application with your dashboard account'); ?></h2>
			<p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
			<p><?php echo _('Facebook Connection Key'); ?>: <?php echo $analytics['key']; ?></p>
			<p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step5.jpg" class="image-border" width="750" height="203" alt="<?php echo _('Step 5'); ?>" /></p>
			<br />
			<p><?php echo _('When you click Connect, you will see'); ?> <span class="error"><?php echo _('(Not Connected)'); ?></span> <?php echo _('in red change to'); ?> <span class="success"><?php echo _('(Connected)'); ?></span> <?php echo _('in green.'); ?></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 6: Final App Activation.'); ?></h2>
			<p><?php echo _('Click the activate link to complete the installation process. You will then be able to control all the content for the app from this dashboard.'); ?></p>
			<p><a href="/social-media/facebook/about-us/" title="<?php echo _('Activate'); ?>"><?php echo _('Activate'); ?></a></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/step6.jpg" class="image-border" width="489" height="187" alt="<?php echo _('Step 6'); ?>" /></p>
			<br /><br />
		<?php
		} else {
			echo '<p>', _('Your app is currently active.'), '</p>';
		}
		?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>