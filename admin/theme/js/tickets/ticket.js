/**
 * View Ticket
 */
jQuery(function($) {
	// Make it possible to priority the status on the fly
	$('#sPriority').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/tickets/update-priority/', { '_nonce' : $('#_ajax_update_priority').val(), 'tid' : $('#hTicketID').val(), 'p' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});
	
	// Make it possible to update the status on the fly
	$('#sStatus').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/tickets/update-status/', { '_nonce' : $('#_nonce').val(), 'tid' : $('#hTicketID').val(), 's' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});

	// Make it possible to update the "assigned to" on the fly
	$('#sAssignedTo').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/tickets/update-assigned-to/', { '_nonce' : $('#_ajax_update_assigned_to').val(), 'tid' : $('#hTicketID').val(), 'atui' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});

	// Comments
	$('#taTicketComments').click( function() {
		if ( 'Write a comment...' == $(this).val() ) {
			$(this).val('').css( 'height', '35px' );
			$('#aAddComment, #dPrivate').show();
		}
	}).blur( function() {
		var value = $(this).val();
		if ( '' == value || 'Write a comment...' == value ) {
			$(this).val( 'Write a comment...' ).css( 'height', '16px' );
			$('#aAddComment, #dPrivate').hide();
		}
	}).autoResize();
	
	// Make it actually add the comment
	$('#aAddComment').click( function() {
		var taComments = $('#taTicketComments'), comment = taComments.val(), attachments = new Array(), private = ( $('#cbPrivate').attr('checked') ) ? 1 : 0;
		
		// Small validation
		if ( 'Write a comment...' == comment || 0 == comment.length )
			return;
		
		// Get attachments
		$('#attachments .attachment').each( function() {
			attachments.push( $(this).attr('id').replace( 'dAttachment', '' ) );
		});
		
		$.post( '/ajax/tickets/add-comment/', { '_nonce' : $('#_ajax_add_comment').val(), 'tid' : $('#hTicketID').val(), 'c' : comment, 'p' : private, 'a' : attachments }, function( response ) {
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Refresh entry list
			addComment( response['comment'], private );
			
			// Restore the add box to defaults
			taComments.val('').trigger('blur');
			$('#cbPrivate').attr( 'checked', false );
			$('#attachments').empty();
		}, 'json' );
	});
	
	// Edit Entries
	$('#dComments .delete-comment').live( 'click', function() {
		// Make sure they actually want to do this
		if ( !confirm( "Are you sure you want to delete this comment? This cannot be undone." ) )
			return;
		
		var c = $(this).parents('.comment:first');
		
		// Send AJAX request to delete the entr
		$.post( '/ajax/tickets/delete-comment/', { '_nonce' : $('#_ajax_delete_comment').val(), 'tcid' : c.attr('id').replace( 'dComment', '' ) }, function( response ) {
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Make the entry go away
			c.slideUp( 'fast', function() {
				$(this).next().remove().end().remove();
			});
		}, 'json' );
	});
	
	// Create the date due datepicker
	$('#tDateDue').datepicker({
		'dateFormat': 'mm-dd-yy',
		'minDate'	: '+0d',
		'onSelect'	: function() {
			// Adjust the server
			$.post( '/ajax/tickets/update-date-due/', { _nonce: $('#_ajax_update_date_due').val(), 'tid' : $('#hTicketID').val(), 'd' : $('#tDateDue').val() }, function( response ) {
				if ( !response['result'] ) {
					alert( response['error'] );
					return;
				}
			}, 'json' );
		}
	});
	
	// Make the upload image icon work with uploadify
	$('#fUploadAttachment').uploadify({
		'auto'      	: true,
		'buttonImg' 	: 'http://admin.imagineretailer.com/images/buttons/attach.png',
		'cancelImg' 	: 'http://admin.imagineretailer.com/images/icons/cancel.png',
		'fileExt'		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps,*.ppt,*.wks,*.bmp,*.gif;*.jpg;*.jpeg;*.png;*.psd;*.tif;*.zip;*.7z;*.rar;*.zipx;*.aiff;*.odt;',
		'fileDesc'		: 'Attachments',
		'multi'			: true,
		'scriptData'	: { '_nonce' : $('#_ajax_upload_attachment').val(), 'wid' : $('#hWebsiteID').val(), 'uid' : $('#hUserID').val() },
		'onComplete'	: function( e, queueID, fileObj, response ) {
			// Add attachment
			$('#attachments').append( response );
		},
		'sizeLimit'		: 10000000,// (10mb) In bytes? Really?
		'script'    	: '/ajax/tickets/upload-attachment/',
		'uploader'  	: '/media/flash/uploadify.swf'
	});
	
	// Delete attachments
	$('.remove-attachment').live( 'click', function() {
		if ( !confirm( 'Are you sure you want to remove this attachment? This cannot be undone.') )
			return false;
		
		// Get variable to use later
		var parent = $(this).parent();
		
		// AJAX call to remove parentt
		$.post( '/ajax/tickets/remove-attachment/', { '_nonce' : $('#_ajax_remove_attachment').val(), 'tuid' : $(this).attr('id').replace( 'aDeleteAttachment', '' ) }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			parent.remove();
		}, 'json' );
	});
});

/**
 * Add a Comment
 */
function addComment( tc, private ) {
	var privateHTML = ( private ) ? '<img src="/images/icons/tickets/lock.gif" width="11" height="15"0 alt="Private" class="private" />' : '';
	var dComments = $('#dComments'), comment = '<div class="comment" id="dComment' + tc['ticket_comment_id'] + '"><p class="name">' + privateHTML + ' ' + tc['name'] + ' <span class="date">' + tc['date'] + '</span><a href="javascript:;" class="delete-comment" title="Delete Ticket Comment"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a></p><p class="message">' + tc['comment'] + '</p><div class="attachments"></div><br clear="left" />';
	
	if ( $('.comment:first', dComments).length ) {
		dComments.prepend( comment + '<div class="divider"></div>' );
	} else {
		dComments.prepend( comment );
	}
	
	var attachments = $('.attachments:first', dComments);
	
	$('#attachments .attachment').each( function() {
		$(this).find('a:first').appendTo( attachments );
	});
}