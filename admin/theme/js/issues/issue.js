/**
 * View Issues
 */
jQuery(function($) {
	// Make it possible to update the status on the fly
	$('#sStatus').change( function() {
		// Update the status when they change it
		
		$.post( '/ajax/issues/update-status/', { '_nonce' : $('#_nonce').val(), 'ik' : $('#hIssueKey').val(), 's' : $(this).val() }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});
	
	// Comments
	$('#taIssueComments').click( function() {
		if ( 'Write a comment...' == $(this).val() ) {
			$(this).val('').css( 'height', '35px' );
			$('#aAddComment').show();
		}
	}).blur( function() {
		var value = $(this).val();
		if ( '' == value || 'Write a comment...' == value ) {
			$(this).val( 'Write a comment...' ).css( 'height', '16px' );
			$('#aAddComment').hide();
		}
	}).autoResize();
	
	// Make it actually add the comment
	$('#aAddComment').click( function() {
		var taComments = $('#taIssueComments'), comment = taComments.val();
		
		// Small validation
		if ( 'Write a comment...' == comment || 0 == comment.length )
			return;
		
		$.post( '/ajax/issues/add-comment/', { '_nonce' : $('#_ajax_add_comment').val(), 'ik' : $('#hIssueKey').val(), 'c' : comment }, function( response ) {
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Refresh entry list
			addComment( response['comment'] );
			
			// Restore the add box to defaults
			taComments.val('').trigger('blur');
		}, 'json' );
	});
	
	// Edit Entries
	$('#dComments .delete-comment').live( 'click', function() {
		// Make sure they actually want to do this
		if ( !confirm( "Are you sure you want to delete this comment? This cannot be undone." ) )
			return;
		
		var c = $(this).parents('.comment:first');
		
		// Send AJAX request to delete the entr
		$.post( '/ajax/issues/delete-comment/', { '_nonce' : $('#_ajax_delete_comment').val(), 'icid' : c.attr('id').replace( 'dComment', '' ) }, function( response ) {
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
});

/**
 * Add a Comment
 */
function addComment( ic, private ) {
	var dComments = $('#dComments'), comment = '<div class="comment" id="dComment' + ic['issue_comment_id'] + '"><p class="name">' + ic['name'] + ' <span class="date">' + ic['date'] + '</span><a href="javascript:;" class="delete-comment" title="Delete Issue Comment"><img src="/images/icons/x.png" alt="X" width="16" height="16" /></a></p><p class="message">' + ic['comment'] + '</p><br clear="left" />';
	
	if ( $('.comment:first', dComments).length ) {
		dComments.prepend( comment + '<div class="divider"></div>' );
	} else {
		dComments.prepend( comment );
	}
}