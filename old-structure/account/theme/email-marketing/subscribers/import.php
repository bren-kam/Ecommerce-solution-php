<?php
/**
 * @page Import Subscribers
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Class
$e = new Email_Marketing;

$email_lists = $e->get_email_lists();

// Initialize variable
$completed = false;

if ( isset( $_POST['_complete_nonce'] ) && nonce::verify( $_POST['_complete_nonce'], 'import-subscribers' ) && !empty( $_POST['hEmailLists'] ) )
	$completed = $e->complete_import( explode( '|', $_POST['hEmailLists'] ) );

css( 'jquery.uploadify' );
javascript( 'swfobject', 'jquery.uploadify', 'email-marketing/subscribers/import' );

$selected = "email_marketing";
$title = _('Import Subscribers | Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Import Subscribers'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'subscribers', 'import_subscribers' ); ?>
	<div id="subcontent">
	<?php if ( $completed ) { ?>
	<p><?php echo _('Your emails have been imported successfully!'); ?></p>
	<?php } else { ?>
	<div id="dUploadedSubscribers" class="hidden">
		<p><?php echo _('Please verify the first email addresses below are correct:'); ?></p>
		<table cellpadding="0" cellspacing="1" id="tUploadedSubcribers" class="generic">
			<tr>
				<th width="50%"><?php echo _('Email'); ?></th>
				<th><?php echo _('Name'); ?></th>
			</tr>
		</table>
		<br /><br />
		<form action="/email-marketing/subscribers/import/" method="post">
		<?php nonce::field( 'import-subscribers', '_complete_nonce' ); ?>
		<input type="hidden" name="hEmailLists" id="hEmailLists" />
		<input type="submit" class="button" value="<?php echo _('Continue'); ?>" />
		</form>
	</div>
	<div id="dDefault">
		<p><?php echo _('On this page you can import a list of subscribers who have requested you email them information.'); ?></p>
		<p><?php echo _('Please make your spreadsheet layout match the example below.'); ?></p>
		<p><?php echo _('Example:'); ?></p>
		<table cellpadding="0" cellspacing="1" class="generic">
			<tr>
				<th width="50%"><?php echo _('Email'); ?></th>
				<th><?php echo _('Name'); ?></th>
			</tr>
			<tr>
				<td><?php echo _('email@example.com'); ?></td>
				<td><?php echo _('John Doe'); ?></td>
			</tr>
			<tr>
				<td><?php echo _('jane@doe.com'); ?></td>
				<td><?php echo _('Jane'); ?></td>
			</tr>
			<tr class="last">
				<td>...</td>
				<td>...</td>
			</tr>
		</table>
		<br /><br />
		<p>
		<?php 
			foreach ( $email_lists as $el ) { 
				$checked = ( 0 == $el['category_id'] ) ? ' checked="checked"' : '';
		?>
			<input type="checkbox" class="cb" value="<?php echo $el['email_list_id']; ?>"<?php echo $checked; ?> /> <?php echo $el['name']; ?><br />
		<?php } ?>
		</p>
		
		<br />
		<?php nonce::field( 'import-subscribers' ); ?>
		<input type="file" name="fUploadSubscribers" id="fUploadSubscribers" />
		<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<br /><br />
		<br /><br />
	</div>
	<?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>