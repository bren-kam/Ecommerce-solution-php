<?php
/**
 * @page: 404 Error
 * @package Real Statistics
 */

$title = _('Error: 404 - Page Not Found') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('404 not found'); ?></h1>
	<br clear="all" />
	<br />
	<p><?php echo _("Welcome to a 404 page. You are here because the page you have requested doesn't exist, at least not at this location."); ?></p>
	<br clear="all" />
	<br /><br />
	<br /><br />
</div>

<?php 
get_sidebar();
get_footer();
?>