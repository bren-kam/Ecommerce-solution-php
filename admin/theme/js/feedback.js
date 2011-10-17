/**
 * Feedback
 */
jQuery(function($) {
	TableToolsInit.sSwfPath = "/media/flash/ZeroClipboard.swf";
	dt = $('#tFeedback').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/feedback/list/',
		'iDisplayLength' : 50,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="50">50</option><option value="250">250</option><option value="500">500</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"aaSorting": [[3, 'asc'],[2, 'desc'],[5, 'asc']],
		"sDom" : '<"top"Tlfr>t<"bottom"pi>',
		'fnRowCallback' : function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			// Make the last column have the class "last"
			$('td:last', nRow).addClass('last');
			
			return nRow;
		}
	});
	
	// Update settion status
	$('.status').change( function() {
		$.post( '/ajax/feedback/change-status/', { '_nonce' : $('#_ajax_change_status').val(), 's' : $(this).val() }, function( response ) {
			if ( response['result'] )
				dt.fnDraw();
		}, 'json' );
	});
});