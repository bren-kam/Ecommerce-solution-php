/**
 * For the Add Edit Autoresponder page
 */

// When the page has loaded
jQuery(function($) {
	// Send test link
	$('#aSendTest').click( function() {
		var text = $(this).html();
		
		if ( text.search( /\+/ ) > 0 ) {
			$(this).html( text.replace( '+', '&ndash;' ) );
			
			// Show
			$('#dSendTest').show();
		} else {
			$(this).text( text.replace( /\[[^\]]+\]/, '[ + ]' ) );
			
			// Hide
			$('#dSendTest').hide();
		}
	});
	
	// Send test funtionality
	$('#bSendTest').click( function() {
		var email = $('#tTestEmail').val(), currentOffer = $('#cbCurrentOffer').attr('checked');
		
		$.post( '/ajax/email-marketing/autoresponders/test/', { _nonce : $('#_ajax_test_autoresponder').val(), 'e' : email, 's' : $('#tSubject').val(), 'm' : $('#taMessage').val(), 'co' : currentOffer }, ajaxResponse, 'json' );
	});
});