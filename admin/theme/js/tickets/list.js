/**
 * Tickets List Page
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
	dt = $('#tListTickets').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/tickets/list/',
		'iDisplayLength' : 30,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="30">30</option><option value="50">50</option><option value="100">100</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"aaSorting": [[3, 'asc'],[2, 'asc'],[5, 'asc']],
		"sDom" : '<"top"lfr>t<"bottom"pi>',
		'fnRowCallback' : function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			// Get the waiting variable
			var td = $('td:eq(1)', nRow), data = td.html().split( '|' );
			
			// Give the priority td its own class
			$('td:eq(3)', nRow).addClass('priority');
			
			// Add the waiting class if its waiting
			if( '1' == data[1] ) 
				$(nRow).addClass( 'waiting' );
			
			// Get rid of the number
			td.html( data[0] );
			
			// Make the last column have the class "last"
			$('td:last', nRow).addClass('last');
			
			return nRow;
		},
		fnDrawCallback : function() {
			$('#tListTickets tr:last').addClass('last');
		}
	});
	
	// Update settion status
	$('#sStatuses').change( function() {
		$.post( '/ajax/tickets/change-status/', { '_nonce' : $('#_ajax_change_status').val(), 's' : $(this).val() }, function( response ) {
			if( response['result'] )
				dt.fnDraw();
		}, 'json' );
	});
	
	// Update settion assigned to
	$('#sAssignedTo').change( function() {
		$.post( '/ajax/tickets/change-assigned-to/', { '_nonce' : $('#_ajax_change_assigned_to').val(), 'auid' : $(this).val() }, function( response ) {
			if( response['result'] )
				dt.fnDraw();
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
	if( i['redirect'] ) {
		window.location = '/login/';
	} else {
		serverCallback( i );
	}
}