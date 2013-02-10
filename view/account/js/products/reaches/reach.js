// When the page has loaded
jQuery(function($) {
    // Make it possible to priority the status on the fly
	$('#sPriority').change( function() {
		$.post( '/products/reaches/update-priority/', { _nonce : $('#_update_priority').val(), wrid : $('#hReachId').val(), priority : $(this).val() }, ajaxResponse, 'json' );
	});

    // Make it possible to update the status on the fly
	$('#sStatus').change( function() {
        // Update the status when they change it
        $.post( '/products/reaches/update-status/', { _nonce : $('#_update_status').val(), wrid : $('#hReachId').val(), status : $(this).val() }, ajaxResponse, 'json' );
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
    var comment = $('#comment');

	comment.click( function() {
        var value = $(this).val();
		if ( $(this).attr('tmpval') == value || '' == value ) {
			$(this).val('').css( 'height', '39px' );
			$('#add-comment, #private-wrapper').show();
		}
	}).blur( function() {
		var value = $(this).val(), tmpVal = $(this).attr('tmpval');
		if ( '' == value || tmpVal == value ) {
			$(this).val( tmpVal ).css( 'height', '19px' );
			$('#add-comment, #private-wrapper').hide();
		}
	}).autoResize();

    // Make sure it works even if they click on it super fast
    if ( comment.is(':focus') ) {
        var value = $(this).val();
		if ( comment.attr('tmpval') == value || '' == value ) {
			comment.val('').css( 'height', '39px' );
			$('#add-comment, #private-wrapper').show();
		}
    }
});

// Update assigned to
function updateAssignedTo( userId ) {
    // Update the status when they change it
    $.post( '/products/reaches/update-assigned-to/', { _nonce : $('#_update_assigned_to').val(), wrid : $('#hReachId').val(), auid : userId }, ajaxResponse, 'json' );
}