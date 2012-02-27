/**
 * Reach Detail
 */

jQuery( function($) {
	
	// AJAX selects
	$("#sAssignedTo, #sStatus, #sPriority").change( function() {
		$this = $(this);
		switch ( $this.attr('id') ) {
			case "sAssignedTo": var method = 'update-assigned-to'; break;
			case "sStatus": var method = 'update-status'; break;
			case "sPriority": var method = 'update-priority'; break;
		}
		
		$.get( '/ajax/reaches/' + method + '/', { '_nonce': $('#_ajax-' + method).val(), 'val': $this.val(), 'rid': $('#hReachID').val() }, ajaxResponse, 'json' );
		
	});
	
});
