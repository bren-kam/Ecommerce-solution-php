/**
 * Accounts Edit Page
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
	// Add Record function
	$('#aAddRecord').click( function() {
		// Find parent tr and add a new one before it
		var newRecord = $('#original tr:first').clone( true, true );
		$('input.action', newRecord).val('1');
		$(this).parents('tr:first').before( newRecord );
	});
	
	// Change the inputs to being editable
	$('.edit-record').live( 'click', function() {
		var parentTR = $(this).parents('tr:first'), deleteTR = parentTR.clone();
		deleteTR.addClass('hidden');
		$('input.action', deleteTR).val('0');
		parentTR.before( deleteTR );
		
		$('input.disabled, select.disabled, textarea.disabled', parentTR).removeClass('disabled').attr( 'disabled', false );
		$('input.disabled, select.disabled, textarea.disabled', deleteTR).removeClass('disabled').attr( 'disabled', false );
		$('input.action', parentTR).val('1');
	});
	
	// Change the inputs to being editable
	$('.delete-record').live( 'click', function() {
		if ( !confirm( 'Are you sure you want to delete this record? This cannot be undone.' ) )
			return false;
		
		var parentTR = $(this).parents('tr:first');
		$('input.action', parentTR).val('0');
		parentTR.addClass('hidden');
		$('input.disabled, select.disabled, textarea.disabled', parentTR).removeClass('disabled').attr( 'disabled', false );
	});

    /**
     * CNAME and A record validation
     */
    $('#fEditDNS').submit( function() {
        var success = true;

        $(this).find('.changes-type:visible').each( function() {
            var changeType = $(this).val(), records = $(this).parents('tr:first').find('.changes-records:first');

            if ( !validateType( changeType, records.val().split("\n") ) ) {
                alert( 'The records you have entered do not match the type you have selected.' );
                records.focus();
                success = false;
                return false;
            }
        });

        return success;
    });

	// Temporary Value
	tmpval('input[tmpval],textarea[tmpval]');
}

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

/**
 * Temporary Values
 */

function tmpval( selectors ) {
	// Temporary Values
	$(selectors).each( function() {
		/**
		 * Sequence of actions:
		 *		1) Set the value to the temporary value (needed for page refreshes
		 *		2) Add the 'tmpval' class which will change it's color
		 * 		3) Set the focus function to empty the value under the right conditions and remove the 'tmpval' class
		 *		4) Set the blur function to fill the value with the temporary value and add the 'tmpval' class
		 */
		$(this).focus( function() {
			// If the value is equal to the temporary value when they focus, empty it
			if( $(this).val() == $(this).attr('tmpval') )
				$(this).val('').removeClass('tmpval');
		}).blur( function() {
			// Set the variables so they don't have to be grabbed twice
			var value = $(this).val(), tmpValue = $(this).attr('tmpval');

			// Fill in with the temporary value if it's empty or if it matches the temporary value
			if( 0 == value.length || value == tmpValue )
				$(this).val( tmpValue ).addClass('tmpval');
		});

		// If there is no value, set it to the correct value
		if( !$(this).val().length )
			$(this).val( $(this).attr('tmpval') ).addClass('tmpval');
	});
}