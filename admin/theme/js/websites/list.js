/**
 * Websites List Page
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
	cache = { 'domain' : {}, 'store_name' : {}, 'title' : {} };
	
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
	
	// State Change - Trigger (Change)
	$('#sState').change( trStateChange );
	
	// Initialize Data Tables
	TableToolsInit.sSwfPath = "/media/flash/ZeroClipboard.swf";
	listWebsites = $('#tListWebsites').dataTable({
		'bProcessing': true,
		'bServerSide': true,
		'bAutoWidth': false,
		'sAjaxSource': '/ajax/websites/list/',
		'iDisplayLength' : 100,
		"oLanguage": {
			"sLengthMenu": 'Rows: <select><option value="100">100</option><option value="250">250</option><option value="500">500</option></select>',
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
		"aaSorting": [[1, 'asc']],
		"sDom" : '<"top"Tlr>t<"bottom"pi>'
	});
}

/**
 * This callback makes sure the user is still logged in
 *
 * @param json i (the JSON returned by the server)
 */
function secureCallback( i ) {
	// Call the global one if we're still logged in
	if( i['redirect'] ) {
		window.location = '/login/';
	} else {
		serverCallback( i );
	}
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
	$.post( '/ajax/websites/search/', { '_nonce' : $('#_ajax_search').val(), 's' : $('#tAutoComplete').val() }, ajaxSearchClick, 'json' );
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
	if( !response['result'] ) {
		alert( response['error'] );
		if( response['redirect'] ) window.location = '/login/';
		return false;
	}
	
	// The settings have been set, now have the table redraw itself
	listWebsites.fnDraw();
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
 * trStateChange
 *
 * If you change the state of the websites you are looking for (All, Live, Staging) it will reload them
 */
function trStateChange() {
	// Change state ajax request
	$.post( '/ajax/websites/change-state/', { '_nonce' : $('#_ajax_change_state').val(), 's' : $(this).val() }, ajaxStateChange, 'json' );
}

/**
 * ajaxStateChange
 *
 * AJAX response to trStateChange
 *
 * @param json response
 */
function ajaxStateChange( response ) {
	// Make sure there was no error
	if( !response['result'] ) {
		alert( response['error'] );
		return false;
	}
	
	listWebsites.fnDraw();
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
	if( request['term'] in cache[cacheType] ) {
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
	$.post( '/ajax/websites/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( data ) {
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