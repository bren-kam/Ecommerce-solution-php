/**
 * Users List Page
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
	listUsers = $('#tListUsers').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/users/list/',
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