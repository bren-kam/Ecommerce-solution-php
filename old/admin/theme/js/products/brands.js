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
			$.post( '/ajax/products/brands/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'term' : request['term'] }, function( autocompleteResponse ) {
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
			$.post( '/ajax/products/brands/add-brand/', { _nonce : $('#_ajax_add_brand').val(), bid : ui['item']['value'], s : $('#brands .brand').length }, ajaxResponse, 'json' );

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
	
	// Remove brand
	$('.remove-brand').live( 'click', function() {
		$(this).parents('.product').remove().next().remove();
	});
	
	$('#cbLinkBrands').click( function() { 
		$.post( '/ajax/products/brands/set-link/', { _nonce: $('#_ajax_set_link').val(), 'checked' : ( 'checked' == $(this).attr('checked') ) ? 1 : 0 }, ajaxResponse, 'json' );
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
	$.post( '/ajax/products/brands/update-sequence/', { _nonce : $('#_ajax_update_sequence').val(), 's' : idList }, ajaxResponse, 'json' );
}

$.fn.updateBrandsSequence = updateBrandsSequence;