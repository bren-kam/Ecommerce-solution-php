<?php
/**
 * @page Website Room Planner
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
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-room-planner' ) )
	$success = $w->update_settings( array( 'page_room-planner-slug' => format::slug( $_POST['tRoomPlannerSlug'] ), 'page_room-planner-title' => $_POST['tRoomPlannerTitle'] ) );

$s = $w->get_settings( 'page_room-planner-slug', 'page_room-planner-title' );

javascript('website/website');

$selected = "website";
$title = _('Room Planner | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Room Planner Page'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'room_planner' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your room planner page has been updated successfully!'); ?></p>
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fRoomPlanner" action="/website/room-planner/" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tRoomPlannerTitle"><?php echo _('Page Title'); ?>:</label></td>
					<td><input type="text" class="tb slug-title" name="tRoomPlannerTitle" id="tRoomPlannerTitle" maxlength="50" value="<?php echo $s['page_room-planner-title']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tRoomPlannerSlug"><?php echo _('Page Link'); ?>:</label></td>
					<td>http://<?php echo ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $user['website']['domain']; ?>/<input type="text" class="tb slug" name="tRoomPlannerSlug" id="tRoomPlannerSlug" maxlength="50" value="<?php echo $s['page_room-planner-slug']; ?>" />/</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field( 'update-room-planner' ); ?>
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