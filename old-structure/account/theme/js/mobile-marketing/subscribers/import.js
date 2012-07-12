jQuery( function() {
	// Change the mobile lists
	$('#dDefault input.cb').change( updateMobileLists );
	
	// To start off with
	updateMobileLists();
});


/**
 * Update mobile lists function
 */
function updateMobileLists() {
	var mobileLists = '';
	
	// Make a string
	$('#dDefault input.cb').each( function() {
		if( $(this).attr('checked') )
			mobileLists += ( '' == mobileLists ) ? $(this).val() : '|' + $(this).val();
	});
	
	// Assign it to hidden value
	$('#hMobileLists').val( mobileLists );
}