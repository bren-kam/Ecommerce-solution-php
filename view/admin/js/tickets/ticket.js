// When the page has loaded
jQuery(function($) {
    // Make it possible to priority the status on the fly
	$('#sPriority').change( function() {
		$.post( '/tickets/update-priority/', { _nonce : $('#_update_priority').val(), tid : $('#hTicketID').val(), priority : $(this).val() }, ajaxResponse, 'json' );
	});

    // Make it possible to update the status on the fly
	$('#sStatus').change( function() {
        // Update the status when they change it
        $.post( '/tickets/update-status/', { _nonce : $('#_update_status').val(), tid : $('#hTicketID').val(), status : $(this).val() }, ajaxResponse, 'json' );
	});

	// Make it possible to update the "assigned to" on the fly
	$('#sAssignedTo').change( function() {
        updateAssignedTo( $(this).val() );
	});

    // Make it so links can assign people too
    $('.assign-to').click( function() {
        updateAssignedTo( $(this).attr('rel') );
    });

    // Comments
	$('#comment').click( function() {
        var value = $(this).val();
		if ( $(this).attr('tmpval') == value || '' == value ) {
			$(this).val('').css( 'height', '39px' );
			$('#add-comment, #private-wrapper, #attach').show();
		}
	}).blur( function() {
		var value = $(this).val(), tmpVal = $(this).attr('tmpval');
		if ( '' == value || tmpVal == value ) {
			$(this).val( tmpVal ).css( 'height', '19px' );
			$('#add-comment, #private-wrapper, #attach').hide();
		}
	}).autoResize();

    // Edit Entries
	$('#comments-list').on( 'click', '.delete-comment', function() {
		// Make sure they actually want to do this
		if ( !confirm( $(this).attr('confirm') ) )
			return;

		var c = $(this).parents('.comment:first');

		// Send AJAX request to delete the entr
		$.post( '/tickets/delete-comment/', { _nonce : $('#_delete_comment').val(), tcid : c.attr('id').replace( 'comment-', '' ) }, ajaxResponse, 'json' );
	});
});

// Update assigned to
function updateAssignedTo( userId ) {
    // Update the status when they change it
    $.post( '/tickets/update-assigned-to/', { _nonce : $('#_update_assigned_to').val(), tid : $('#hTicketID').val(), atui : userId }, ajaxResponse, 'json' );
}