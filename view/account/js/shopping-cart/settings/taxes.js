/**
 * Shopping Cart - Add/Edit Taxes Page
 */

/**
 * When the head is ready
 *
 * Initial load of the page
 *
 * @param $ (jQuery shortcut)
 */
head.ready(function() {
	// Add a new state tax
	$('#aAddTax').click( function() {
		// Define variables
		var sState = $('#sState'), abbr = sState.val(), option = sState.find('option:selected'), name = option.text(), tax = parseFloat( $('#tAmount').val() );
		
		// Make sure we have the proper tax
		if ( tax.length < 5 )
			tax = '0' + tax;
		
		// Validation
		if ( ( !tax && 0 !== tax ) || '' == abbr ) {
			alert( $(this).attr('error') );
			return;
		}
		
		var newTr = '<tr id="trTax' + abbr + '">'
			+ '<td><a href="#" class="zip-codes" title="Edit Tax Zip Codes"><span>' + name + '</span></a><textarea name="zip_codes[' + abbr + ']" class="hidden" col="50" rows="3" placeholder="[Zip] [Cost]"></textarea></td>'
			+ '<td><input id="tState' + abbr + '" name="states[' + abbr + ']" class="state tb" type="text" maxlength="5" value="' + tax + '" /></td>'
			+ '<td><a href="#" class="delete-state" id="aDeleteTax' + abbr + '" title="Delete Tax"><img width="15" height="17" alt="Delete" src="/images/icons/x.png"></a></td></tr>';
		
		// Add it on to the end
		$('#trAddTax').before( newTr );
		$('#trTax' + abbr ).sparrow();

		// Hide the option
		$(option).hide();
		
		// Reset the amounts
		$('#sState, #tAmount').val('').blur();
	});

    // All the "Live"events
    $('#tWebsiteTaxes').on( 'click', 'a.delete-state', function() { // Delete any states
        var abbr = $(this).attr( 'id' ).replace( 'aDeleteTax', '' );

		// Delete the row
		$('#trTax' + abbr).remove();

		// Show the zip
		$( '#sState option[value=' + abbr + ']' ).show();
    }).on( 'click', 'a.zip-codes', function() {// Make it show or hide the textarea
        $(this).next().toggleClass('hidden');
    });
});