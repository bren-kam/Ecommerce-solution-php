// When the page has loaded
jQuery(function($) {
    // Add Record function
	$('#aAddRecord').click( function() {
		// Find parent tr and add a new one before it
		var newRecord = $('#original tr:first').clone( true, true );
		$('input.action', newRecord).val('1');
		$(this).parents('tr:first').before( newRecord );
	});

    /**
     * CNAME and A record validation
     */
    $('#fEditDNS').submit( function() {
        var success = true, form = $(this);

        $(this).find('.changes-type:visible').each( function() {
            var changeType = $(this).val(), records = $(this).parents('tr:first').find('.changes-records:first');

            if ( !validateType( changeType, records.val().split("\n") ) ) {
                alert( form.attr('err') );
                records.focus();
                success = false;
                return false;
            }
        });

        return success;
    }).on( 'click', 'a.delete-record', function() {
        // Delete a record if they agree
		if ( !confirm( 'Are you sure you want to delete this record? This cannot be undone.' ) )
			return false;

		var parentTR = $(this).parents('tr:first');
		$('input.action', parentTR).val('0');
		parentTR.addClass('hidden');
		$('input.disabled, select.disabled, textarea.disabled', parentTR).removeClass('disabled').attr( 'disabled', false );
	}).on( 'click', 'a.edit-record', function() {
        // Edit a record
        var parentTR = $(this).parents('tr:first'), deleteTR = parentTR.clone();
		deleteTR.addClass('hidden');
		$('input.action', deleteTR).val('0');
		parentTR.before( deleteTR );

		$('input.disabled, select.disabled, textarea.disabled', parentTR).removeClass('disabled').attr( 'disabled', false );
		$('input.disabled, select.disabled, textarea.disabled', deleteTR).removeClass('disabled').attr( 'disabled', false );
		$('input.action', parentTR).val('1');
    });
});

/**
 * Validates the DNS type for A records and CNAME's for any validation.
 *
 * @param changeType
 * @param records
 */
function validateType( changeType, records ) {
    switch ( changeType ) {
        case 'A':
            // Check for IPs
            var regex = /^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/;
        break;

        case 'CNAME':
            // Check domains
            var regex = /^(?:[-a-zA-Z0-9]+\.)*([-a-zA-Z0-9]+\.[a-zA-Z]{2,3}){1,2}$/;
        break;

        default:
            return true;
        break;
    }

    for ( var i in records ) {
        var charPos = records[i].match( regex );

        if ( null == charPos )
            return false;
    }

    return true;
}