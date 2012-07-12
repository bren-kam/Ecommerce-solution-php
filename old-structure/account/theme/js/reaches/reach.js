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
	
	// Expand comment box
	$("#taReachComment").focus( function() {
		
		$this = $(this);
		$this.animate( { 'height': '100px' }, 'fast' );
		$("#dPrivate, #aAddComment").fadeIn( 'fast' );
		
	});
	
	
	$("#taReachComment").blur( function() {
		$this = $(this);
		if ( $this.attr( 'tmpval') == $this.val() || '' == $this.val() ) {
			$this.animate( { 'height': '16px' }, 'fast' );
			$("#dPrivate, #aAddComment").fadeOut( 'fast' );
		}
	});
	
});
