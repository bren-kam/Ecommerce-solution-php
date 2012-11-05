/**
 * Checklists View Page
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
	// Send message in message quee
	$('#fNewMessage').ajaxForm({
		dataType : 'json',
		beforeSubmit : function ( formData, jqForm, options ) {
			$('#taMessage').attr( 'disabled', 'disabled' );
			$('#bSendMessage').attr( 'disabled', 'disabled' );
		},
		success : function( result ){
			if ( result ) {
				load_messages();
				$('#taMessage').val('');
			} else {
				alert( 'There was an error while trying to send your message. Please refresh the page and try again.' );
			}
			$('#taMessage').removeAttr( 'disabled' );
			$('#bSendMessage').removeAttr( 'disabled' );
		}
	});
	
	// Create the note dialog box
	$('#dNotes').dialog({
		width:500,
		height : 400,
		modal : true,
		resizable: false,
		closeOnEscape : false,
		autoOpen: false,
		dialogClass: 'single',
		buttons: {
			'Close': function() {
				$(this).dialog('close');
			}
		}
	});

	$('.note-link').click( function(){
		var itemID = $(this).parent().parent().attr('id').replace( 'dItem', '' ), itemName = $( '#' + $(this).parent().attr('id') + ' strong:first' ).html();
		
		loadNotes( itemID );
		
		$('#hItemId').val( itemID );
		$('#dNotesList').empty();
		$('#dNotes').dialog( 'open' ).dialog( 'option', 'title', 'Notes for ' + itemName );
	});

	// Send note in notes quee
	$('#fNewNote').ajaxForm({
		dataType : 'json',
		beforeSubmit : function ( formData, jqForm, options ) {
			$('#taNote').val('').attr( 'disabled', 'disabled' );
			$('#bSendNote').attr( 'disabled', 'disabled' );
			return true;
		},
		success : function( response ){
			// Handle any error
			if ( !response['result'] ) {
				alert( 'There was an error while trying to add your note. Please refresh the page and try again.' );
				return;
			}
			
			loadNotes();
			$('#taNote').val('');
			
			var noteCount = $('#sNoteCount' + response['result'] );
			
			noteCount.html( ( noteCount.text() * 1 ) + 1 );
			
			$('#taNote, #bSendNote').removeAttr( 'disabled' );
		}
	});

	// Marks an item as checked or not
	$('.item-checkbox').live( 'click', function() {
		var itemID = $(this).val(), state = $(this).is(':checked');
		
		$.post( '/ajax/checklists/view/update-item/', { '_nonce': $('#_ajax_update_item').val(), 'iid' : itemID, 's' : state }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( 'There was an error while trying to update your note. Please refresh the page and try again.' );
				return;
			}
			
			if ( "1" == state ) {
				$('#dItem' + itemID ).addClass( 'done' );
			} else {
				$('#dItem' + itemID ).removeClass( 'done' );
			}
		}, 'json'  );
	});

	// Enable the Delete Note Functionality
	$('.delete-note').live( 'click', function(){
		var noteID = $(this).parents('div.dNote:first').attr('id').replace( 'dNote', '' );
		
		if ( !confirm( 'Are you sure you want to delete the note? ') )
			return;
		
		$.post( '/ajax/checklists/view/delete-note/', { '_nonce': $('#_ajax_delete_note').val(), 'nid' : noteID }, function( response ){
			// Handle any error
			if ( !response['result'] ) {
				alert( 'There was an error while trying to delete your note. Please refresh the page and try again.' );
				return;
			}
			
			var noteCount = $('#sNoteCount' + $('#hItemId').val() );
			noteCount.text( ( noteCount.text() * 1 ) - 1 );
			
			$( '#dNote' + noteID ).remove();
		}, 'json'  );
	} );

	// Enable the edit note functionality
	$('.edit-note').live( 'click', function(){
		$('#dNote' + noteID + ' .note .cancel-edit:first' ).trigger('click');
		
		var noteID = $(this).parents('div.dNote:first').attr('id').replace( 'dNote', '' );
		var dnote = $('#dNote' + noteID + ' .note');
		var dNotehtml = dnote.html();
		
		dnote.html( '<br /><a href="#" class="update-note" >Update</a> | <a href="#" class="cancel-edit" >Cancel</a>' );
		dnote.prepend( '<textarea id="taEditNote' + noteID + '" rows="3" cols="42">' + dNotehtml + '</textarea><div class="hidden cancel-text">' + dNotehtml +'</div>' );
	});

	// Enable the update note functionality
	$('.update-note').live( 'click', function(){
		var noteID = $(this).parents('div.dNote:first').attr('id').replace( 'dNote', '' );	
		
		$.post( '/ajax/checklists/view/update-note/', { '_nonce': $('#_ajax_update_note').val(), 'nid' : noteID, 'n' : $( '#taEditNote' + noteID ).val() }, function( response ){
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			$('#dNote' + noteID + ' .note' ).html( $( '#taEditNote' + noteID ).val() );
		}, 'json'  );
	} );

	// Enable the cancle editing functionality
	$('.cancel-edit').live( 'click', function(){
		var noteID = $(this).parents('div.dNote:first').attr('id').replace( 'dNote', '' ), note = $( '#dNote' + noteID + ' .note' );
		 note.html( $('.cancel-text', note).html() );
	});
}

/**
 * Function to load list of notes
 *
 * @param int $itemID the checklist item id
 */
function loadNotes( itemID ) {
	if ( itemID == null )
		itemID = $('#hItemId').val();
	
	$.post( '/ajax/checklists/view/get-notes/', { '_nonce': $('#_ajax_get_notes').val(), 'iid': itemID }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		var dNotesList = $('#dNotesList'), newNotes = '';
		
 		for ( i = 0; i < response['notes'].length; i++ ) {
			var n = response['notes'][i];
			newNotes += '<div id="dNote' + n['checklist_website_item_note_id'] + '" class="dNote"><div class="title"><strong>' + n['contact_name'] + '</strong><br />' + n['date_created'] + '<br />' + '<a href="#" class="edit-note" title="Edit">Edit</a> | <a href="#" class="delete-note" title="Delete">Delete</a> </div><div class="note">' + n['note'] + '</div></div>';
		}
		
		dNotesList.html( newNotes );
	}, 'json' );
}