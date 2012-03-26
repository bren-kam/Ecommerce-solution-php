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

// Redirect to accounts
url::redirect('/accounts/');
exit;

css( 'dashboard' );
css_ie8( 'dashboard' );

javascript( 'jquery', 'jquery.tmp-val', 'home' );

$selected = 'home';
$title = _('Dashboard') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Manage Account Home'); ?></h1>
	<p id="description"><?php echo _('To get started, please select the service you would like<br />to manage from the icons below.'); ?></p>
	<br clear="all" />
	<br /><br />
	<div id="nav-icons">
		<a href="/accounts/" title="<?php echo _('Accounts'); ?>" id="accounts"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Accounts'); ?>" /><br /><?php echo _('Accounts'); ?></a>
		<a href="/products/" title="<?php echo _('Products'); ?>" id="products"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Products'); ?>" /><br /><?php echo _('Products'); ?></a>
		<?php if ( $user['role'] >= 7 ) { ?>
        <a href="/users/" title="<?php echo _('Users'); ?>" id="users"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Users'); ?>" /><br /><?php echo _('Users'); ?></a>
		<?php } ?>
        <a href="/checklists/" title="<?php echo _('Checklists'); ?>" id="checklists"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Checklists'); ?>" /><br /><?php echo _('Checklists'); ?></a>
		<a href="/craigslist/" title="<?php echo _('Craigslist'); ?>" id="craigslist"><img src="/images/trans.gif" width="130" height="112" alt="<?php echo _('Craigslist'); ?>" /><br /><?php echo _('Craigslist'); ?></a>
	</div>
	<br clear="all" />
	
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php 
get_sidebar();
get_footer();
?>