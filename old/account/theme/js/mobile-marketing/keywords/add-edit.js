head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=charCount', function() {
    $('#taResponse').keyup( function() {
        $(this).val( $(this).val().replace(/\n/, '') );
    }).charCount({
        css : 'counter bold'
        , cssExceeded : 'error'
        , counterText : 'Characters Left: '
        , allowed : 131
    });

    // Check availibity
    $('#aCheckKeywordAvailability').click( function() {
        $.post( '/ajax/mobile-marketing/keywords/check-availability/', { _nonce : $('#_ajax_check_availability').val(), 'k' : $('#tKeyword').val() }, ajaxResponse, 'json' );
    })
	
	// If they change the keyword we need to make sure that they check the new keyword
	$('#tKeyword').change( function() {
		$('#hKeywordAvailable').val('0');
	});
});