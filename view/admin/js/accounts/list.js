// When the page has loaded
jQuery(function($) {
    cache = { 'domain' : {}, 'store_name' : {}, 'title' : {} };

	// Create autocomplete
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
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
                return 0;
            }

            // It was not cached, get data
            $.post( '/accounts/autocomplete/', { _nonce : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( data ) {
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
	}).data( "autocomplete" )._renderItem = function() {
        return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( '<a href="#">' + item['label'] + '</a>' )
		.appendTo( ul );
    };

    // Submit Search - Trigger (Submit)
	$('#fSubmitSearch').submit( function() {
        $('#aSearch').click();
        return false;
    } );

	// Search functionality - Trigger (Click)
	$('#aSearch').click( function() {
        $.post( '/accounts/search/', { _nonce : $('#_ajax_search').val(), 's' : $('#tAutoComplete').val() }, ajaxResponse, 'json' );
    } );

	// State Change - Trigger (Change)
	$('#sState').change( function() {
        // Change state ajax request
        $.post( '/accounts/store-session/', { '_nonce' : $('#_ajax_change_state').val(), 's' : $(this).val() }, ajaxResponse, 'json' );
    } );
});