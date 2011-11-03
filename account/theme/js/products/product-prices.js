/**
 * @page Product Prices
 */
jQuery(function($) {
    // Grab the products for each brand
    $('#sBrands').change( function() {

    });

	// Save the data
	$('#bSave, #bSave2').click( function() {
		var values = {}, datePieces = $('#date').val().split( '-' ), extra = $(this).attr('id').replace( 'bSave', '' );
		
		// Create the values
		$('input.graph-value').each( function() {
			var value = $(this).val();
			
			// Make sure we don't add empty values
			if ( !value.length )
				return;
			
			// Add the rest of the values
			values[$(this).parents('tr:first').attr('id').replace( 'tr', '' )] = $(this).attr('id').replace( 'tGraphValue', '' ) + '|' + $(this).val();
		});
		
		$.post( '/ajax/summary/set-values/', { _nonce : $('#_ajax_set_values').val(), v : values, gids : $('#hGraphIDs').val(), 'd' : datePieces[2] + '-' + datePieces[0] + '-' + datePieces[1] }, function( response ) {
			// Handle any error
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Refresh the list
			getValues();
			
			// Get the element so we don't have to find it twice
			var saveMessage = $('#sSaveMessage');
			
			saveMessage.show();
			
			setTimeout( function() {
				saveMessage.hide();			 
			}, 5000 );
		}, 'json' );
	});
});

/**
 * Gets values
 */
function getValues() {
	var datePieces = $('#date').val().split( '-' );
			
	// Send AJAX to get new values
	$.post( '/ajax/summary/get-graphs/', { '_nonce' : $('#_ajax_get_summary_graphs').val(), 'd' : datePieces[2] + '-' + datePieces[0] + '-' + datePieces[1] }, function( response ) {
		// Handle any error
		if( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		// Declare variable
		var trs = '', j = 0;
		
		// Loop through graphs and get new values
		for( var i in response['graphs'] ) {
			j++;
			
			var g = response['graphs'][i], classes = ( 1 == j % 2 ) ? 'odd' : 'even';
					
			if( '1' == g['advanced_graph'] )
				classes += ' advanced';
				
			trs += '<tr id="tr' + g['graph_id'] + '" class="' + classes + '"><td>' + g['name'] + '</td><td id="td' + g['graph_value_id'] + '" class="last">';
			
			if ( '1' == g['advanced_graph'] ) {
				trs += g['value'];
			} else {
				trs += '<input type="text" id="tGraphValue' + g['graph_value_id'] + '" class="graph-value" value="' + g['value'] + '" />';
			}
			
			trs += '</td></tr>';
		}
		
		// Replaces old values with new values
		$('#tSummary tbody:first').html( trs );
		
		// Add the last class to the last one
		$('#tSummary tr:last').addClass('last');
	}, 'json' );
}