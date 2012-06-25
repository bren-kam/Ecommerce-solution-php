/**
 * Link Market
 */
jQuery(function($) {
	// Load Categories on the fly
	$('#sMarketID').change( function() {
        var sCategories = $('#sCLCategoryID');

		// First let them know that it is loading
        sCategories.empty().append('<option value="">-- Loading --</option>');
		
		$.post( '/ajax/craigslist/accounts/get-market-categories/', { _nonce : $('#_ajax_get_market_categories').val(), clmid : $(this).find('option:selected').attr('rel'), wid : $('#hWebsiteID').val() }, function( response ) {
			// Handle any error
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}

            var categoryOptions = '<option value="">-- Select a Category --</option>';

            for ( var i in response['categories'] ) {
                categoryOptions += '<option value="' + i + '">' + response['categories'][i] + '</option>';
            }

            sCategories.empty().append(categoryOptions);
		}, 'json' );
	});
});