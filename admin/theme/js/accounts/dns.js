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
		$(this).parents('tr:first').before( newRecord );
	});
    
	// Temporary Value
	tmpval('input[tmpval],textarea[tmpval]');
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