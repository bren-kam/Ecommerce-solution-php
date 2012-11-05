head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Cache
	var cache = {};

	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in cache ) {
				response( $.map( cache[request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['value']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/ajax/products/brands/autocomplete/', { _nonce : $('#_ajax_brands_autocomplete').val(), term : request['term'] }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['value']
					}
				}));
			}, 'json' );
		},
		select: function( event, ui ) {
			// Update the hidden brand
			$('#hBrandID').val( ui['item']['value'] );
			
			// Show them what theyv'e selected
			$('#tAutoComplete').val( ui['item']['label'] );
			
			return false;
		}
	});
});