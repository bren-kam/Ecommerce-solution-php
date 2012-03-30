<?php
/**
 * @page Edit Account
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate classes
$v = new Validator();
$w = new Websites;

// Minimal validation
$v->form_name = 'fNewNote';
$v->add_validation( 'taNoteContents', 'req', _('The note may not be empty') );

// Check to see if they are creating a note
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-note' ) ) {
	$errs = $v->validate();
	
	// Create the note if there are no errors
	if ( empty( $errs ) )
		$w->create_note( $_GET['wid'], $user['user_id'], $_POST['taNoteContents'] );
}

// Get the notes
$notes = $w->get_notes( $_GET['wid'] );

// Get account
$website = $w->get_website( $_GET['wid'] );

// Get CSS/JS
css( 'accounts/notes' );
javascript( 'validator', 'jquery', 'accounts/notes' );

$selected = 'accounts';
$title = _('Account Notes') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $website['title'], ' ', _('Notes'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'accounts/', 'accounts' ); ?>
	<div id="subcontent">
		<?php
		nonce::field( 'delete-note', '_delete_note_nonce' );
		nonce::field( 'update-note', '_update_note_nonce' );
		?>
		<form name="fNewNote" method="post" action="/accounts/notes/?wid=<?php echo $_GET['wid']; ?>">
			<textarea name="taNoteContents" id="taNoteContents" cols="50" rows="3"></textarea><br />
			<input type="submit" class="button" id="aAddNote" value="Add Note" />
			<?php nonce::field( 'add-note' ); ?>
		</form>
		<?php add_footer( $v->js_validation() ); ?>
		<br /><br />
		<div id="dNotes">
			<?php
			if ( is_array( $notes ) )
			foreach ( $notes as $n ) {
			?>
			<div id="dNote<?php echo $n['website_note_id']; ?>" class="dNote">
				<div class="title">
					<strong><?php echo $n['contact_name']; ?></strong>
					<br /><?php echo dt::date( 'M j Y', $n['date_created'] ); ?>
					<?php
					if ( $n['user_id'] == $user['user_id'] )
						echo '<br /><a href="#" class="edit-note" title="Edit">Edit</a> | <a href="#" class="delete-note" title="Delete">Delete</a>';
					?>
				</div>
				<div class="note"><?php echo $n['message']; ?></div>
			</div>
			<?php } ?>
		</div>
		<br clear="left" />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>