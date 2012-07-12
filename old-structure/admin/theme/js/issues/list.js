/**
 * Issues List Page
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
	dt = $('#tListIssues').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/issues/list/',
		'iDisplayLength' : 30,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="30">30</option><option value="50">50</option><option value="100">100</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"aaSorting": [[2, 'desc'],[1, 'desc']],
		"sDom" : '<"top"fr>t<"bottom"pi>',
		'fnRowCallback' : function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			// Give the priority td its own class
			$('td:eq(2)', nRow).addClass('priority');
			
			// Make the last column have the class "last"
			$('td:last', nRow).addClass('last');
			
			return nRow;
		},
		fnDrawCallback : function() {
			$('#tListIssues tr:last').addClass('last');
		}
	});
	
	// Update settion status
	$('#sStatuses').change( function() {
		$.post( '/ajax/issues/change-status/', { '_nonce' : $('#_ajax_change_status').val(), 's' : $(this).val() }, function( response ) {
			if ( response['result'] )
				dt.fnDraw();
		}, 'json' );
	});
}