<?php
/**
 * @page Website Sale
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;

// Initialize variable
$success = false;

// Update the settings
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-sale-page' ) )
	$success = $w->update_settings( array( 'page_sale-slug' => format::slug( $_POST['tSaleSlug'] ), 'page_sale-title' => $_POST['tSaleTitle'] ) );

$s = $w->get_settings( 'page_sale-slug', 'page_sale-title' );

javascript('website/website' );

$selected = "website";
$title = _('Sale Page | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Sale Page'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'sale' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your sale page has been updated successfully!'); ?></p>
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fSale" action="/website/sale/" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tSaleTitle"><?php echo _('Page Title'); ?>:</label></td>
					<td><input type="text" class="tb slug-title" name="tSaleTitle" id="tSaleTitle" maxlength="50" value="<?php echo $s['page_sale-title']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tSaleSlug"><?php echo _('Page Link'); ?>:</label></td>
					<td>http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain']; ?>/<input type="text" class="tb slug" name="tSaleSlug" id="tSaleSlug" maxlength="50" value="<?php echo $s['page_sale-slug']; ?>" />/</td>
				</tr>
				<tr>
					<td><a href="/ajax/website/remove-sale-items/?_nonce=<?php echo nonce::create('remove-sale-items'); ?>" title="<?php echo _('Remove All Sale Items'); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to remove all sale items?'); ?>"><?php echo _('Remove All Sale Items'); ?></a></td>
					<td><span id="sRemoveAllSaleItems" class="hidden success"><?php echo _('All sale items successfully removed.'); ?></span></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field( 'update-sale-page' ); ?>
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