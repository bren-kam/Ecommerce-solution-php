/**
 * Craigslist - Headlines List Page
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
	listCraigslistHeadlines = $('#tListCraigslistHeadlines').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/craigslist/headlines/list/',
		'iDisplayLength' : 30,
		"oLanguage": {
			"sLengthMenu": '<select><option value="30">30</option><option value="50">50</option><option value="100">100</option></select>',
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
		"sDom" : '<"top"Tlr>t<"bottom"pi>'
	});
	
	// Delete a craigslist headline
	$('.delete-headline').live( 'click', function() {
		var craigslistHeadlineID = $(this).attr('id').replace( 'aDelete', '' );
		
		if ( !confirm( "Are you sure you want to delete this headline? This action cannot be undone." ) )
			return;
		
		$.post( '/ajax/craigslist/headlines/delete/', { '_nonce': $('#_ajax_delete_craigslist_headline').val(), 'chid': craigslistHeadlineID }, function( response ) {
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			listCraigslistHeadlines.fnDraw();
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