// When the page has loaded
jQuery(function($) {
    // Marks an item as checked or not
	$('#list').on( 'click', '.cb', function() {
		$.post( '/checklists/update-item/', { _nonce: $('#_update_item').val(), cwiid : $(this).val(), checked : $(this).is(':checked') }, ajaxResponse, 'json'  );
	});
});