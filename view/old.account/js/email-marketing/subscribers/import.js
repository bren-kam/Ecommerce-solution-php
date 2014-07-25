jQuery(function(){
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/email-marketing/subscribers/import-subscribers/'
        , allowedExtensions: ['csv', 'xls']
        , element: $('#import-subscribers')[0]
        , sizeLimit: 26214400 // 25 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_import_subscribers').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aImportSubscribers').click( function() {
        if ( $.support.cors ) {
            $('#import-subscribers input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    });

    // Change the email lists
    $('#dDefault input.cb').change( updateEmailLists );

    // To start off with
    updateEmailLists();
});

/**
 * Update email lists function
 */
function updateEmailLists() {
	var emailLists = '';

	// Make a string
	$('#dDefault input.cb').each( function() {
		if( $(this).is(':checked') )
			emailLists += ( '' == emailLists ) ? $(this).val() : '|' + $(this).val();
	});

	// Assign it to hidden value
	$('#hEmailLists').val( emailLists );
}