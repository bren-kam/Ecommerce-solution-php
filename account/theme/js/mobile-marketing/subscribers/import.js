jQuery( function() {
	// Make the upload image icon work with uploadify
	$('#fUploadSubscribers').uploadify({
		auto      	: true,
		displayData	: 'speed',
		buttonImg 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.csv;*.xls',
		fileDesc	: 'Excel/CSV Files', // @Fix needs to be put in PHP
		scriptData	: { '_nonce' : $('#_complete_nonce').val(), 'wid' : $('#hWebsiteID').val() },
		onComplete : function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		sizeLimit	: 26214400,// (25mb) In bytes? Really?
		script    	: '/ajax/mobile-marketing/subscribers/import/',
		uploader  	: '/media/flash/uploadify.swf'
	});
	
	// Change the email lists
	$('#dDefault input.cb').change( updateMobileLists );
	
	// To start off with
	updateMobileLists();
});


/**
 * Update email lists function
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