/**
 * View Feedback
 */
jQuery(function($) {
	// Make it possible to priority the status on the fly
	$('#sPriority').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/feedback/update-priority/', { '_nonce' : $('#_ajax_update_priority_nonce').val(), 'fid' : $('#hFeedbackID').val(), 'p' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});
	
	// Make it possible to update the status on the fly
	$('#sStatus').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/feedback/update-status/', { '_nonce' : $('#_nonce').val(), 'fid' : $('#hFeedbackID').val(), 's' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});

	// Make it possible to update the "assigned to" on the fly
	$('#sAssignedTo').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/feedback/update-assigned-to/', { '_nonce' : $('#_ajax_update_assigned_to_nonce').val(), 'fid' : $('#hFeedbackID').val(), 'atui' : $(this).find('option:selected').val() }, function( response ) {
			// Handle any error
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});

	// Comments
	$('#taFeedbackComments').click( function() {
		if( 'Write a comment...' == $(this).val() ) {
			$(this).val('').css( 'height', '35px' );
			$('#dTAFeedbackCommentsWrapper').css( 'marginLeft', '70px' );
			$('#iFeedbackCommentsImage, #aAddComment, #dPrivate, #dFeedbackCommentsDivider').show();
		}
	}).blur( function() {
		var value = $(this).val();
		if( '' == value || 'Write a comment...' == value ) {
			$(this).val( 'Write a comment...' ).css( 'height', '16px' );
			$('#dTAFeedbackCommentsWrapper').css( 'marginLeft', '0' );
			$('#iFeedbackCommentsImage, #aAddComment, #dPrivate, #dFeedbackCommentsDivider').hide();
		}
	}).autoResize();
	
	// Make it actually add the comment
	$('#aAddComment').click( function() {
		var taComments = $('#taFeedbackComments'), comment = taComments.val(), private = ( $('#cbPrivate').attr('checked') ) ? 1 : 0;
		
		// Small validation
		if( 'Write a comment...' == comment || 0 == comment.length )
			return;
		
		$.post( '/ajax/feedback/add-comment/', { '_nonce' : $('#_ajax_add_comment').val(), 'fid' : $('#hFeedbackID').val(), 'c' : comment, 'p' : private }, function( response ) {
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Restore the add box to defaults
			taComments.val('').trigger('blur');
			
			// Refresh entry list
			addComment( response['comment'] );
		}, 'json' );
	});
	
	// Edit Entries
	$('#dComments .delete-comment').live( 'click', function() {
		// Make sure they actually want to do this
		if( !confirm( "Are you sure you want to delete this comment? This cannot be undone." ) )
			return;
		
		var c = $(this).parents('.comment:first');
		
		// Send AJAX request to delete the entr
		$.post( '/ajax/feedback/delete-comment/', { '_nonce' : $('#_ajax_delete_comment').val(), 'fcid' : c.attr('id').replace( 'dComment', '' ) }, function( response ) {
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Make the entry go away
			c.slideUp( 'fast', function() {
				$(this).next().remove().end().remove();
			});
		}, 'json' );
	});
});

/**
 * Add a Comment
 *
 * @returns event XML
 */
function addComment( fc ) {
	var dComments = $('#dComments'), comment = '<div class="comment" id="dComment' + fc['feedback_comment_id'] + '"><img src="http://manage.realstatistics.com/images/';
	
	// Add the correct picture
	comment += ( fc['picture'].length ) ? 'users/' + fc['user_id'] + '/icon/' + fc['picture'] : 'icons/person.png';
	
	// Make sure the name works
	comment += '" class="avatar" width="60" height="60" alt="' + fc['name'] + '" /><div class="comment-content"><p class="name">' + fc['name'] + '<a href="javascript:;" class="delete-comment" title="Delete Feedback Comment"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a></p><p>' + fc['comment'] + '</p><p class="date">' + fc['date'] + '</p></div><br clear="left" /></div>';
	
	if( $('.comment:first', dComments).length ) {
		dComments.prepend( comment + '<div class="divider"></div>' );
	} else {
		dComments.prepend( comment );
	}
}