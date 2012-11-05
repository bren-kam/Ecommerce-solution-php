<?php
/**
 * @page Website Add Page
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Validation
$v = new Validator();
$v->form_name = 'fAddPage';

$v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
$v->add_validation( 'tSlug', 'req', _('The "URL" field is required') );

// Add validation
add_footer( $v->js_validation() );

$w = new Websites;

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-page' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) )
		$success = $w->create_page( $_POST['tSlug'], $_POST['tTitle'] );
}

javascript('website/website');

$selected = "website";
$title = _('Add Page') . ' | ' . _('Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Page'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'add_page' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your page has been successfully added!'); ?></p>
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddPage" action="/website/add-page/" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tTitle"><?php echo _('Page Title'); ?>:</label></td>
					<td><input type="text" class="tb slug-title" name="tTitle" id="tTitle" maxlength="100" value="<?php if ( !$success && isset( $_POST['tTitle'] ) ) echo $_POST['tTitle']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tSlug"><?php echo _('URL'); ?>:</label></td>
					<td><input type="text" class="tb slug" name="tSlug" id="tSlug" maxlength="100" value="<?php if ( !$success && isset( $_POST['tSlug'] ) ) echo $_POST['tSlug']; ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Add Page'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field( 'add-page' ); ?>
		</form>
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