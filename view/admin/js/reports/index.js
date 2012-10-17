// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
    criteria = { 'brand' : {}, 'online_specialist' : {}, 'marketing_specialist' : {}, 'company' : {}, 'billing_state' : {}, 'package' : {}, 'services' : {} };
	cache = { 'brand' : {}, 'online_specialist' : {}, 'marketing_specialist' : {}, 'company' : {}, 'billing_state' : {}, 'package' : {} };

	// Create autocomplete
	$('#tAutoComplete').autocomplete({
		minLength: 1
        , select: function( event, ui ) {
			var type = $('#type option:selected'), typeValue = type.val();

			// Update the query
			criteria[typeValue][ui.item['object_id']] = 1;

            var criterion = $('#criterion-template')
                .clone()
                .removeAttr('id');

            var criterionHtml = criterion.html()
                .replace( '[type-value]', typeValue )
                .replace( '[type-text]', type.text() )
                .replace( '[object-id]', ui.item['object_id'] )
                .replace( '[object-value]', ui.item['value'] );

            criterion.html( criterionHtml );

			// Add criterion for viewing pleasures
			$('#criteria').append( criterion );

			// Update the search to go back to normal
			$('#tAutoComplete').val('').trigger('blur');

            updateCriteria();

			return false;
		}
		, source: function( request, response ) {
            // Get the cache type
            var cacheType = $('#type').val();

            // Find out if they are already cached so we don't have to do another ajax called
            if ( request['term'] in cache[cacheType] ) {
                response( $.map( cache[cacheType][request['term']], function( item ) {
                    return {
                        label : item[cacheType]
                        , value : item[cacheType]
                        , object_id : item['object_id']
                    }
                }) );

                // If it was cached, return now
                return false;
            }

            // It was not cached, get data
            $.post( '/reports/autocomplete/', { _nonce : $('#_autocomplete').val(), type : cacheType, term : request['term'] }, function( data ) {
                // Assign global cache the response data
                cache[cacheType][request['term']] = data['objects'];

                // Return the response data
                response( $.map( data['objects'], function( item ) {
                    return {
                        label : item[cacheType]
                        , value : item[cacheType]
                        , object_id : item['object_id']
                    }
                }));
            }, 'json' );
        }
	});

    // Make services change something too
    $('#services').change( function() {
        var option = $(this).find('option:selected'), optionValue = option.val();

        if ( '' == optionValue )
            return;

        // Update the query
        criteria['services'][option.val()] = 1;

        var criterion = $('#criterion-template')
            .clone()
            .removeAttr('id');

        var criterionHtml = criterion.html()
            .replace( '[type-value]', 'services' )
            .replace( '[type-text]', $(this).attr('rel') )
            .replace( '[object-id]', optionValue )
            .replace( '[object-value]', option.text() );

        criterion.html( criterionHtml );

        $('#criteria').append(criterion);

        option.attr( 'disabled', true );
        $(this).val('');
    });

    // Submit Search - Trigger (Submit)
	$('#fSearch').submit( function() {
        $('#aSearch').click();
        return false;
    } );

    // Handle the search
    $('#aSearch').click( function() {
        $('#table tbody:first')
            .empty()
            .load( '/reports/search/', { _nonce : $('#_search').val(), c : criteria } );
    });

    // Add the remove criterion feature
    $('#criteria').on( 'click', '.remove-criterion', function() {
        var parent = $(this).parent(), type = $('.type:first', parent).attr('rel'), search = $('.search:first', parent).attr('rel');

        // Unset criteria
        delete criteria[type][search];

        // Readded to services
        if ( 'services' == type )
            $('#services option[value=' + search + ']').attr( 'disabled', false );

        parent.remove();
    });
});

/**
 * Makes the criteria have the right classes
 */
function updateCriteria() {
    var criteria = $('#criteria');

    $('.criterion.odd', criteria).removeClass('even');
    $('.criterion', criteria).filter(':even').addClass('even');
}