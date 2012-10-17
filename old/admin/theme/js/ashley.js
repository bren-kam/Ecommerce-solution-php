/**
 * Feedback
 */
jQuery(function($) {
	cache = {};
	
	$('#tNewSKU').autocomplete({
		minLength: 1,
		select: function( event, ui ) {
			$.post( '/ajax/ashley/get-product/', { _nonce : $('#_ajax_get_product').val(), pid : ui['item']['product_id'] }, function ( response ) {
				// Make sure there was no error
				if ( !response['result'] ) {
					alert( response['error'] );
					return false;
				}
				
				var product = response['product'];
				
				// Replace everything
				$('#name').text( product['name'] );
				$('#product_id').text( product['product_id'] );
				$('#slug').text( product['slug'] );
				$('#images').html( product['images'] );
				$('#description').text( product['description'] );
				$('#status').text( product['status'] );
				$('#brand').text( product['brand'] );
				$('#sku').text( product['sku'] );
				$('#industry').text( product['industry'] );
				$('#weight').text( product['weight'] );
				$('#categories').html( product['categories'] );
				$('#product_specifications').html( product['product_specifications'] );
				$('#tags').html( product['tags'] );
				$('#attributes').html( product['attributes'] );
				$('#publish_visibility').text( product['publish_visibility'] );
				$('#publish_date').text( product['publish_date'] );
				
				// Last
				$('#hNewProductID').val( product['product_id'] );
			}, 'json' );
		},
		source: function( request, response ) {
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in cache ) {
				response( $.map( cache[request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name'],
						'product_id' : item['product_id']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/ajax/ashley/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'term' : request['term'] }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name'],
						'product_id' : item['product_id']
					}
				}));
			}, 'json' );
		}
	});
});