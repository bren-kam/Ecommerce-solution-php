head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Cache
	var cache = { sku : {}, product : {}, brand : {} };
	
	// Change the text
	$('#sAutoComplete').change( function() {
		var tAutoComplete = $('#tAutoComplete');
		
		tAutoComplete.attr( 'tmpval', tAutoComplete.attr('tmpval').replace( /\s([\w\s]+).../, ' ' + $(this).find('option:selected').text() + '...' ) ).val('').blur();
	});
	
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
			// Get the cache type
			var cacheType = $('#sAutoComplete').val();
			
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in cache[cacheType] ) {
				response( $.map( cache[cacheType][request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/ajax/products/custom-products/autocomplete/', { _nonce : $('#_ajax_custom_products_autocomplete').val(), type : cacheType, 'term' : request['term'], owned : 1 }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[cacheType][request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}));
			}, 'json' );
		}
	});
	
	// Create the search functionality
	$('#aSearch').click( function() {
		$('#tViewProducts').dataTable().fnDraw();
	});
	
	setTimeout( function() {
		$('#tViewProducts').addClass('dt').dataTable({
				aaSorting: [[0,'asc']],
				bAutoWidth: false,
				bProcessing : 1,
				bServerSide : 1,
				iDisplayLength : 20,
				sAjaxSource : '/ajax/products/custom-products/list/',
				sDom : '<"top"lr>t<"bottom"pi>',
				oLanguage: {
						sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>',
						sInfo: "_START_ - _END_ of _TOTAL_"
				},
				fnDrawCallback : function() {
					// Run Sparrow on new content and add the class last to the last row
					sparrow( $(this).find('tr:last').addClass('last').end() );
				},
				fnServerData: function ( sSource, aoData, fnCallback ) {
					aoData.push({ name : 's', value : $('#tAutoComplete').val() });
					aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });
					
					// Get the data
					$.ajax({
						url: sSource,
						dataType: 'json',
						data: aoData,
						success: fnCallback
					});
				},
			});
	}, 500 );
});