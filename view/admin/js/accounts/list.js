// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
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
            $.post( '/accounts/autocomplete/', { _nonce : $('#_autocomplete').val(), type : cacheType, term : request['term'] }, function( data ) {
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
	});

    // Submit Search - Trigger (Submit)
	$('#fSearch').submit( function() {
        $('#aSearch').click();
        return false;
    } );

	// Search functionality - Trigger (Click)
	$('#aSearch').click( function() {
        $.post( '/accounts/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'accounts', 'search' ], value : $('#tAutoComplete').val() }, endStoreSession, 'JSON' );
    } );

	// State Change - Trigger (Change)
	$('#state').change( function() {
        // Change state ajax request
        $.post( '/accounts/store-session/', { '_nonce' : $('#_store_session').val(), keys : [ 'accounts', 'state' ], value : $(this).val() }, endStoreSession, 'JSON' );
    } );
});

/**
 * The function to end store session AJAX call
 * @param response
 */
function endStoreSession( response ) {
    if ( response.success )
        $('.dt:first').dataTable().fnDraw();
}