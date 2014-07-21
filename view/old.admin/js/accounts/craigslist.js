/**
 * Link Market
 */
jQuery(function($) {
	// Load Categories on the fly
	$('#sMarketId').change( function() {
        var sCategories = $('#sCLCategoryId');

		// First let them know that it is loading
        sCategories.empty().append('<option value="">-- ' + sCategories.attr('rel') + ' --</option>');
		
		$.post( '/accounts/get_craigslist_market_categories/', { _nonce : $('#_get_craigslist_market_categories').val(), clmid : $(this).find('option:selected').attr('rel'), aid : $('#hAccountId').val() }, ajaxResponse, 'json' );
	});
});