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

// Secure the section
if ( !$user['website']['mobile-marketing'] )
    url::redirect('/');

// Instantiate Class
$m = new Mobile_Marketing;

$mobile_lists = $m->get_mobile_lists();

// Initialize variable
$completed = false;

if ( isset( $_POST['_complete_nonce'] ) && nonce::verify( $_POST['_complete_nonce'], 'import-subscribers' ) && !empty( $_POST['hMobileLists'] ) ) {
	$completed = $m->import( explode( '|', $_POST['hMobileLists'] ) );
}

css( 'jquery.uploadify' );
javascript( 'swfobject', 'jquery.uploadify', 'mobile-marketing/subscribers/import' );

$selected = "mobile_marketing";
$title = _('Import Subscribers') . ' | ' . _('Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Import Subscribers'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'subscribers', 'import_subscribers' ); ?>
	<div id="subcontent">
	<?php if ( $completed ) { ?>
    	<p><?php echo _('Your subscribers have been imported successfully!'); ?></p>
	<?php } else { ?>
		<p><?php echo _('On this page you can import a list of subscribers who have requested you to send them mobile information.'); ?></p>
		<p><?php echo _('Please make your spreadsheet layout match the example below.'); ?></p>
		<p><?php echo _('Example:'); ?></p>
		<table cellpadding="0" cellspacing="1" class="generic">
			<tr><th width="50%">818-555-1234</th></tr>
			<tr><td>727-555-4321</td></tr>
			<tr><td>412-555-1324</td></tr>
			<tr class="last"><td>...</td></tr>
		</table>
		<br /><br />
		<p>
		<?php 
			foreach ( $mobile_lists as $ml ) {
				$checked = ( 0 == $ml['category_id'] ) ? ' checked="checked"' : '';
		?>
			<input type="checkbox" class="cb" value="<?php echo $ml['mobile_list_id']; ?>"<?php echo $checked; ?> /> <?php echo $ml['name']; ?><br />
		<?php } ?>
		</p>
		
		<br />
		<?php nonce::field( 'import-subscribers' ); ?>
		<input type="file" name="fUploadSubscribers" id="fUploadSubscribers" />
		<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<br /><br />
		<br /><br />
	<?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>