// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    cache = { 'sku' : {}, 'products' : {}, 'brands' : {} };

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
            $.post( '/products/autocomplete/', { _nonce : $('#_autocomplete').val(), type : cacheType, term : request['term'] }, function( data ) {
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

    // Choose type
    $('#sAutoComplete').change( function() {
        $.post( '/accounts/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'products', 'type' ], value : $(this).val() }, endStoreSession );
    });

	// Search functionality - Trigger (Click)
	$('#aSearch').click( function() {
        $.post( '/accounts/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'products', 'search' ], value : $('#tAutoComplete').val() }, endStoreSession );
    } );

	// State Change - Trigger (Change)
	$('#visibility, #user-option, #user, #cid').change( function() {
        // Change state ajax request
        $.post( '/accounts/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'products', $(this).attr('id') ], value : $(this).val() }, endStoreSession );
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