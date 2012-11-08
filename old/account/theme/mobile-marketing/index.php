<?php
/**
 * @page Mobile Marketing
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Instantiate Classes
javascript('mobile-marketing/iframe');

$selected = "mobile_marketing";
$title = _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Marketing'); ?></h1>
	<br clear="all" /><br />
        <iframe src="http://greysuitmobile.com" width="100%" height="600" id="iframe"></iframe>
	<br /><br />
</div>

<?php get_footer(); ?>