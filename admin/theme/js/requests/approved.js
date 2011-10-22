/**
 * Requests - APproved List Page
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
	// Initialize Data Tables
	TableToolsInit.sSwfPath = "/media/flash/ZeroClipboard.swf";
	listRequests = $('#tListRequests').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/requests/list/?status=1',
		'iDisplayLength' : 100,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="100">100</option><option value="250">250</option><option value="500">500</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"aaSorting": [[0, 'asc']],
		"sDom" : '<"top"Tlfr>t<"bottom"pi>'
	});
	
	// Delete a request
	$('.delete-request').live( 'click', function() {
		var requestID = $(this).attr('id').replace( 'aDelete', '' );
		
		if ( !confirm( "Are you sure you want to delete this request? This action cannot be undone." ) ) 
			return;
		
		$.post( '/ajax/requests/delete/', { '_nonce': $('#_ajax_delete_request').val(), 'rid': requestID }, function( response ) {
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			listRequests.fnDraw();
		}, 'json' );
	});
}