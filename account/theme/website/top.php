<?php
/**
 * @page Website - Top
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();
	
if ( !empty( $user['website']['logo'] ) )
	$logo = 'http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain'] . '/' . 'custom/uploads/images/' . $user['website']['logo'];

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'top-section' ) ) {
	$w = new Websites;
	$success = $w->update( array( 'phone' => $_POST['tPhone'] ), 's' );
}

css( 'jquery.uploadify' );
javascript( 'swfobject', 'jquery.uploadify', 'website/top');

$selected = "website";
$title = _('Top | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Top'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'top' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<p class="success"><?php echo _('The "Top" section has been updated successfully!'); ?></p>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fTop" action="/website/top/" method="post">
			<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
			<table cellpadding="0" cellspacing="0" class="form">
				<tr>
					<td><label for="tPhone" ><?php echo _('Phone Number'); ?></label></td>
					<td><input type="text" id="tPhone" name="tPhone" class="tb" value="<?php echo $user['website']['phone']; ?>" maxlength="20" /></td>
				</tr>
				<tr>
					<td class="top"><label for="fLogo" ><?php echo _('Logo'); ?></label></td>
					<td>
						<div id="dLogoContent">
							<?php if ( !empty( $logo ) ) { ?>
							<img src="<?php echo $logo; ?>" alt="<?php echo _('Logo'); ?>" style="padding-bottom: 10px;" />
							<br />
							<?php } ?>
						</div>
						<input type="file" id="fLogo" />
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="<?php echo _('Save'); ?>" class="button" /></td>
				</tr>
			</table>
			<?php
			nonce::field( 'upload-logo', '_ajax_upload_logo' );
			nonce::field( 'top-section' );
			?>
		</form>
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