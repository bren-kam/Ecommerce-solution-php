head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
	// Multiple Location Map toggle
	$('#cbMultipleLocationMap').click( function() {
		$.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'mlm', v : $(this).is(':checked') ? true : false, apid : $('#hAccountPageId').val() }, ajaxResponse, 'json' );
	});
	
	// Hide All Maps toggle
	$('#cbHideAllMaps').click( function() {
		$.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'ham', v : $(this).is(':checked') ? true : false, apid : $('#hAccountPageId').val() }, ajaxResponse, 'json' );
	});

    // Submit a form
    $('#bAddEditProduct').click( function() {
        $('#fAddEditLocation').submit();
    });

    // Add location
    $('#add-location').click( function() {
        $('#fAddEditLocation')[0].reset();

        new Boxy( $('#dAddEditLocation'), {
            title : 'Add Location'
        });
    });

    // Make the click trigger the other one
    $('#bSubmitLocation').click( function() {
        $('#bAddEditLocation').click();
    });

    $('#dContactUsList').on( 'click', 'a.edit-location', function() {
        $.post( '/website/get-location/', { _nonce : $('#_get_location').val(), 'wlid' : $(this).attr('href').replace( '#', '' ) }, function( ajax ) {
            ajaxResponse( ajax );

            new Boxy( $('#dAddEditLocation'), {
                title : 'Update Location'
            });
        });
    }).sortable({
        items		: '.location',
        cancel		: 'a',
        placeholder	: 'location-placeholder',
        forcePlaceholderSize : true,
        update: updateLocations
    });
});

function updateLocations() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123.
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#dContactUsList').sortable('serialize');

	// Use Sidebar's -- it's the same thing
	$.post( '/website/update-location-sequence/', { _nonce : $('#_update_location_sequence').val(), 's' : idList }, ajaxResponse, 'json' );
}