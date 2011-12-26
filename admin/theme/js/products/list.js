/**
 * Products List Page
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
	cache = { 'sku' : {}, 'products' : {}, 'brands' : {} };
	
	// Create tmp values
	$('#tAutoComplete').tmpVal( '#929292', '#000' );
	
	// Create autocomplete
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: autocompleteSuccess
	}).data( "autocomplete" )._renderItem = autocompleteRenderItem;
	
	// Submit Search - Trigger (Submit)
	$('#fSubmitSearch').submit( trSubmitSearch );
	
	// Search functionality - Trigger (Click)
	$('#aSearch').click( trSearchClick );
	
	// Reset Search - Trigger (Click)
	$('#aResetSearch').click( trResetSearchClick );
	
	// If they change what's being viewed, refresh the current view
	$('#sVisibility').change( function() {
		setProductSession( 'visibility', $(this).find('option:selected').val() );
	});
	
	// If they change the product status
	$('#sProductStatus').change( function() {
		setProductSession( 'product-status', $(this).find('option:selected').val() );
	});
	
	// If they change what's being viewed, refresh the current view
	$('#sUsers').change( function() {
		setProductSession( 'user', $(this).find('option:selected').val() );
	});

	// If they change the autocomplete drop down, refresh the current view
	$('#sAutoComplete').change( function() {
		setProductSession( 'type', $(this).find('option:selected').val() );
	});
	
	// Initialize Data Tables
	TableToolsInit.sSwfPath = "/media/flash/ZeroClipboard.swf";
	listProducts = $('#tListProducts').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/products/list/',
		'iDisplayLength' : 100,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="0">All</option></select>',
			"sInfo": "Records: _START_ - _END_ of _TOTAL_"
		},
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			// Set a global variable
			serverCallback = fnCallback;
			
			// Get the data
			$.ajax({
  				url: sSource,
				dataType: 'json',
				data: aoData,
				success: secureCallback
			});
		},
		"aaSorting": [[0, 'asc']],
		"sDom" : '<"top"Tlr>t<"bottom"pi>'
	});
	
	// Delete a product
	$('.delete-product').live( 'click', function() {
		var productID = $(this).attr('id').replace( 'aDelete', '' );
		
		if ( !confirm( "Are you sure you want to delete this product? This action cannot be undone." ) ) 
			return;
		
		$.post( '/ajax/products/delete/', { '_nonce': $('#_ajax_delete_product').val(), 'pid': productID }, function( response ) {
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			listProducts.fnDraw();
		}, 'json' );
	});
}

/**
 * This callback makes sure the user is still logged in
 *
 * @param json i (the JSON returned by the server)
 */
function secureCallback( i ) {
	// Call the global one if we're still logged in
	if ( i['redirect'] ) {
		window.location = '/login/';
	} else {
		serverCallback( i );
	}
}

/**
 * Set Product Session variable
 *
 * @param string $key
 * @param string $value
 * @return bool
 */
function setProductSession( key, value ) {
	$.post( '/ajax/products/set-session/', { '_nonce' : $('#_ajax_set_session').val(), 'key' : key, 'value' : value }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( 'An error occurred while trying change state. Please refresh the page and try again.' );
			return false;
		}
		
		// Redraw the table
		listProducts.fnDraw();
	}, 'json' );
}

/**
 * trSubmitSearch
 *
 * If someone hits enter while in the form, it will trigger the same thing as clicking on the search button
 */
function trSubmitSearch() {
	$('#aSearch').click();
	return false;
}

/**
 * trSearchClick
 *
 * The function that performs the website search functionality
 */
function trSearchClick() {
	$.post( '/ajax/products/search/', { '_nonce' : $('#_ajax_search').val(), 's' : $('#tAutoComplete').val() }, ajaxSearchClick, 'json' );
}

/**
 * ajaxSearchClick
 *
 * AJAX response function for trSearchClick
 *
 * @param response
 */
function ajaxSearchClick( response ) {
	// Make sure there was no error
	if ( !response['result'] ) {
		alert( 'An error occurred while trying to search. Please refresh the page and try again.' );
		return false;
	}
	
	// The settings have been set, now have the table redraw itself
	listProducts.fnDraw();
}

/**
 * trResetSearchClick
 *
 * Resets the search by refreshing the page
 */
function trResetSearchClick() {
	// Refresh page
	window.location.reload();
	
	// Do a quick change to refresh what's in the search box (will happen before the page refreshes)
	$('#tAutoComplete').val( $('#tAutoComplete').attr('tmpval') ).css( 'color', '#929292' );
}

/**
 * autocompleteSuccess
 *
 * The success response to the AJAX call for autocompleting
 *
 * @param array request
 * @param array response
 * @return array
 */
function autocompleteSuccess( request, response ) {
	// Get the cache type
	var cacheType = $('#sAutoComplete').val();
	
	// Find out if they are already cached so we don't have to do another ajax called
	if ( request['term'] in cache[cacheType] ) {
		response( $.map( cache[cacheType][request['term']], function( item ) {
			return {
				'label' : item[cacheType],
				'value' : item[cacheType]
			}
		}) );
		
		// If it was cached, return now
		return;
	}
	
	// It was not cached, get data
	$.post( '/ajax/products/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( data ) {
		// Assign global cache the response data
		cache[cacheType][request['term']] = data['objects'];
		
		// Return the response data
		response( $.map( data['objects'], function( item ) {
			return {
				'label' : item[cacheType],
				'value' : item[cacheType]
			}
		}));
	}, 'json' );
}

/**
 * autocompleteRenderItem
 *
 * The function to render each item in the autocomplete list
 *
 * @return ul
 */
function autocompleteRenderItem( ul, item ) {
	return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( '<a href="javascript:;">' + item['label'] + '</a>' )
		.appendTo( ul );
}