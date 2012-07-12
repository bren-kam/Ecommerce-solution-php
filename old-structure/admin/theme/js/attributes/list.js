/**
 * Attributes List Page
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
	listAttributes = $('#tListAttributes').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/attributes/list/',
		'iDisplayLength' : 100,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="100">100</option><option value="250">250</option><option value="500">500</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			// Set a global variable
			serverCallback = fnCallback;
			
			// Get the data
			$.ajax({
  				url: sSource,
				dataType: 'json',
				data: aoData,
				success: secureCallback
			});
		},
		"aaSorting": [[0, 'asc']],
		"sDom" : '<"top"Tlfr>t<"bottom"pi>'
	});
	
	// Deletes an attribute
	$('.delete-attribute').live( 'click', function() {
		var attributeID = $(this).attr('id').replace( 'aDeleteAttribute', '' );
		
		// Make sure they really want to delete it
		if ( !confirm( 'Are you sure you want to delete "' + $(this).parents('td:first').find('span:first').text() + '"? This action cannot be undone.' ) )
			return;
		
		$.post( '/ajax/attributes/delete/', { _nonce: $('#_ajax_delete_attribute').val(), 'aid' : attributeID }, function( response ) {
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Redraw table
			listAttributes.fnDraw();
		}, 'json' );
	});
}

/**
 * This callback makes sure the user is still logged in
 *
 * @param json i (the JSON returned by the server)
 */
function secureCallback( i ) {
	// Call the global one if we're still logged in
	if ( i['redirect'] ) {
		window.location = '/login/';
	} else {
		serverCallback( i );
	}
}