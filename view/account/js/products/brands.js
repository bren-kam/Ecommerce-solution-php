head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
	// Cache
	var brands = {};
	
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in brands ) {
				response( $.map( brands[request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['value']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/products/autocomplete/', { _nonce : $('#_autocomplete').val(), term : request['term'], type : 'brand' }, function( autocompleteResponse ) {
				// Assign global cache the response data
				brands[request['term']] = autocompleteResponse['suggestions'];
				
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
			// AJAX call to get the offer box
			$.post( '/products/add-brand/', { _nonce : $('#_add_brand').val(), bid : ui['item']['value'], s : $('#brands .brand').length }, ajaxResponse, 'json' );

			return false;
		}
	});
	
	$('#brands').sortable({
		items		: '.brand',
		cancel		: 'a',
		placeholder	: 'brand-placeholder',
		revert		: true,
		forcePlaceholderSize : true,
		update		: updateBrandsSequence
	});

	$('#cbLinkBrands').click( function() {
		$.post( '/products/set-brand-link/', { _nonce: $('#_set_brand_link').val(), 'checked' : ( $(this).is(':checked') ) ? 1 : 0 }, ajaxResponse, 'json' );
	});
});

function updateBrandsSequence() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123. 
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#brands').sortable('serialize');
	
	// Use Sidebar's -- it's the same thing
	$.post( '/products/update-brand-sequence/', { _nonce : $('#_update_brand_sequence').val(), 's' : idList }, ajaxResponse, 'json' );
}

$.fn.updateBrandsSequence = updateBrandsSequence;