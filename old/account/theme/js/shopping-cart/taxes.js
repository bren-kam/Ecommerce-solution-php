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
		if ( !tax || '' == abbr ) {
			alert( $(this).attr('error') );
			return
		}
		
		var newTr = '<tr id="trTax' + abbr + '">'
			+ '<td><a href="/dialogs/edit-tax-zip-codes/?state=' + abbr + '#dEditTaxZipCodes' + abbr + '" class="zip-codes" title="Edit Tax Zip Codes" rel="dialog" ajax="1" cache="0" ><span>' + name + '</span></a></td>'
			+ '<td><input id="tState' + abbr + '" name="states[' + abbr + ']" class="state tb" type="text" maxlength="5" value="' + tax + '" /></td>'
			+ '<td><a href="javascript:;" class="delete-state" id="aDeleteTax' + abbr + '" title="Delete Tax"><img width="15" height="17" alt="Delete" src="/images/icons/x.png"></a></td></tr>';
		
		// Add it on to the end
		$('#trAddTax').before( newTr );
		$('#trTax' + abbr ).sparrow();
		
		// Hide the option
		$(option).hide();
		
		// Reset the amounts
		$('#sState, #tAmount').val('').blur();
	});
	
	// Delete any states
	$('.delete-state').live( 'click', function(){
		var abbr = $(this).attr( 'id' ).replace( 'aDeleteTax', '' );
		
		// Delete the row
		$('#trTax' + abbr).remove();
		
		// Show the zip
		$( '#sState option[value=' + abbr + ']' ).show();
	});
	
	// Add new zip code
	$('#aNewTaxZipCode').live( 'click', function(){
		var abbr = $("#hState").val(), zip = $("#tNewTaxZipCode").val(), cost = $("#tNewTaxZipCost").val();
		
		// Validation
		if ( !zip || !cost ) {
			alert( $(this).attr('error') );
			return false;
		}
		
		var newTr = '<tr>';
		newTr += '<td>' + zip + '</td>';
		newTr += '<td><input type="text" class="tb zip-code-cost" id="tZipCost' + zip + '" value="' + cost + '" /></td>';
		newTr += '<td><a href="javascript:;" class="remove-zip"><img src="/images/icons/x.png" width="15" height="17" alt="Delete" /></a></td>';
		newTr += '</tr>';
		
		// Add the TR
		$('#trAddZipCode').before(newTr);
		
		$("#tNewTaxZipCost, #tNewTaxZipCode").val('').blur();
	});
	
	// Remove zip code
	$('.remove-zip').live( 'click', function() {
		$(this).parents('tr:first').remove();
	});
	
	// Save Zip Codes
	$('#aSaveTaxZips').live( 'click', function() {
		var fTaxes = $('#fTaxes'), abbr = $("#hState").val();
		
		// Remove all existing zip codes
		fTaxes.find('input.zip-' + abbr).remove();
		
		// Add a field to the form for each zip code
		$('#tEditZipCodes input.zip-code-cost').each( function() {
			fTaxes.append('<input type="hidden" class="zip-' + abbr + '" name="zip_codes[' + abbr + '][' + $(this).attr('id').replace( 'tZipCost', '' ) + ']" value="' + $(this).val() + '" />');
		});
		
		// Close the dialog
		$('a.close:first').click();
	});
});