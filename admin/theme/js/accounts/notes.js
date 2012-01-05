/**
 * Websites Notes
 */

// When the page has loaded
jQuery( postLoad );

/**
 * postLoad
 *
 * Initial load of the page
 *
 * @param $ (jQuery shortcut)
 */
function postLoad( $ ) {
	// Enable the edit note functionality - Trigger (Click)
	$('.edit-note').live( 'click', aEditNoteClick );
	
	// Enable the update note functionality - Trigger (Click)
	$('.update-note').live( 'click', aUpdateNoteClick );

	// Delete Notes - Trigger Click
	$('.delete-note').live( 'click', aDeleteNoteClick );
	
	// Enable the cancle editing functionality
	$('.cancel-edit').live( 'click', aCancelEditClick );
}


/**
 * edit-note
 *
 * Edit note functionality
 */
function aEditNoteClick() {
	// Get variables
	var noteID = $(this).parents('.dNote:first').attr('id').replace( 'dNote', '' ), dNote = $('#dNote' + noteID + ' .note:first'), dNoteHTML = dNote.html();
	
	// If it's already being edited, skip
	if ( dNote.hasClass( 'editing' ) ) 
		return false;
	
	dNote.html( '<br /><a href="#" class="update-note" title="Update">Update</a> | <a href="#" class="cancel-edit" title="Cancel">Cancel</a>' ).prepend( '<textarea id="taEditNote' + noteID + '" rows="3" cols="42">' + dNoteHTML + '</textarea><div class="hidden cancel-text">' + dNoteHTML +'</div>' ).addClass( 'editing' );
}

/**
 * update-note
 *
 * Saves the note information after editing
 */
function aUpdateNoteClick() {
	// Get variables
	var noteID = $(this).parents('.dNote:first').attr('id').replace( 'dNote', '' );
	
	// AJAX update call
	$.post( '/ajax/websites/update-note/', { '_nonce': $('#_update_note_nonce').val(), 'nid' : noteID, 't' : $( '#taEditNote' + noteID ).val() }, function( response ){
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		// Set HTML
		$('#dNote' + noteID + ' .note:first' ).html( $( '#taEditNote' + noteID ).val() ).removeClass( 'editing' );
	}, 'json' );
}

/**
 * delete-note
 *
 * Deletes a note via AJAX
 */
function aDeleteNoteClick() {
	// Make sure they want to delete it
	if ( !confirm( 'Are you sure you want to delete this note?' ) ) 
		return false;
	
	// Define variables
	var noteID = $(this).parents('.dNote:first').attr('id').replace( 'dNote', '' );
	
	// Delete note
	$.post( '/ajax/websites/delete-note/', { '_nonce': $('#_delete_note_nonce').val(), 'nid' : noteID }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		// Remove HTML
		$('#dNote' + noteID ).remove();
	}, 'json' );
}

/**
 * cancel-edit
 *
 * Cancels editing a note
 */
function aCancelEditClick() {
	// Define variables
	var noteID = $(this).parents('.dNote:first').attr('id').replace( 'dNote', '' ), dNote = $( '#dNote' + noteID + ' .note:first' );
	
	// Restore original text and remove editing class
	dNote.html( $('.cancel-text:first', dNote).html() ).removeClass( 'editing' );
}