/**
 * Reports List Page
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
	criteria = { 'brand' : {}, 'online_specialist' : {}, 'company' : {}, 'checkboxes' : {} };
	cache = { 'brand' : {}, 'online_specialist' : {}, 'company' : {} };
	
	// Create tmp values
	$('#tSearch').tmpVal( '#929292', '#000' );
	
	// Create autocomplete
	$('#tSearch').autocomplete({
		minLength: 1,
		select: function( event, ui ) {
			var sType = $('#sType'), typeValue = sType.val();
		
			// Update the query
			criteria[typeValue][ui.item['object_id']] = 1;
			
			// Add criterion for viewing pleasures
			$('#dCriteria').append( '<div class="criterion"><span class="type ' + typeValue + '">' + $('option:selected', sType).text() + '</span> - <span class="search ' + ui.item['object_id'] + '">' + ui.item['value'] + '</span><a href="javascript:;" class="remove-criterion" title="Remove"><img src="/images/icons/x.png" width="15" height="17" alt="Remove"></div>' );
			
			// Update the search to go back to normal
			$('#tSearch').val( 'Enter search here...' ).css( 'color', '#929292' );
			
			return false;
		},
		source: autocompleteSuccess
	}).data( "autocomplete" )._renderItem = autocompleteRenderItem;
	
	// Search functionality - Trigger (Click)
	$('#aSearch').click( trSearchClick );
	
	// Remove search criterion
	$('#dCriteria .remove-criterion').live( 'click', function() {
		var criterion = $(this).parents('.criterion:first'), type = $('.type:first', criterion).attr('class').replace( 'type ', '' ), s = $('.search:first', criterion).attr('class').replace( 'search ', '' );
		
		// If there's no search, ignore it
		if ( !type.length )
			return;
		
		// Update the criteria
		delete criteria[type][s];
		
		// Remove it
		criterion.remove();
	});
	
	// Add checkboxes to the criteria
	$('.cb').click( function() {
		if ( $(this).attr('checked') ) {
			criteria['checkboxes'][$(this).val()] = 1;
		} else {
			delete criteria['checkboxes'][$(this).val()];
		}
	});
}

/**
 * trSearchClick
 *
 * The function that performs the website search functionality
 */
function trSearchClick() {
	$.post( '/ajax/reports/search/', { '_nonce' : $('#_ajax_search').val(), 'c' : criteria }, ajaxSearchClick, 'json' );
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
		alert( response['error'] );
		return false;
	}
	
	var trs = '';
	
	for ( var i in response['report'] ) {
		var w = response['report'][i];
		trs += '<tr><td><a href="http://' + w['domain'] + '/" title="' + w['title'] + '" target="_blank">' + w['title'] + '</a></td><td>' + w['company'] + '</td><td>' + w['products'] + '</td><td>' + w['date_created'] + '</td></tr>';
	}
	
	$('#table tbody:first').html( trs );
	$('#sTotal').text( response['report'].length );
	$('#aDownloadExcel').show();
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
	var cacheType = $('#sType').val();

	// Find out if they are already cached so we don't have to do another ajax called
	if ( request['term'] in cache[cacheType] ) {
		response( $.map( cache[cacheType][request['term']], function( item ) {
			return {
				'label' : item[cacheType],
				'value' : item[cacheType],
				'object_id' : item['object_id']
			}
		}) );
		
		// If it was cached, return now
		return;
	}
	
	// It was not cached, get data
	$.post( '/ajax/reports/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( data ) {
		// Assign global cache the response data
		cache[cacheType][request['term']] = data['objects'];
		
		// Return the response data
		response( $.map( data['objects'], function( item ) {
			return {
				'label' : item[cacheType],
				'value' : item[cacheType],
				'object_id' : item['object_id']
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