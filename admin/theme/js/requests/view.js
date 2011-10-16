/**
 * Requests View
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
	// Handle the page content original/change
	$('a.requested, a.original').live( 'click', function() {
		// Determine what is being toggled
		var data = $(this).attr('id').match( /a(Original|Requested)([a-zA-Z0-9]+)/ );
		
		// Get the name of whatever is being toggled
		var name = $(this).text().replace( /View (?:Original|Requested)(.+)/, '$1' );
		
		if( 'Original' == data[1] ) {
			// Change the class on the td
			$(this).parents('tr:first').find('td.changed:first').removeClass( 'changed' ).addClass( 'original' );
			
			// Replace with new anchor
			$(this).replaceWith( '<a href="#" id="aRequested' + data[2] + '" class="requested" title="View Requested ' + name + '">View Requested ' + name + '</a>' );
			
			// Switch content
			$('#dRequested' + data[2]).fadeOut( 'fast' );
			
			setTimeout( function() {
				$('#dOriginal' + data[2]).fadeIn();
			}, 250 );
			
		} else {
			$(this).parents('tr:first').find('td.original:first').removeClass( 'original' ).addClass( 'changed' );
			$(this).replaceWith( '<a href="#" id="aOriginal' + data[2] + '" class="original" title="View Original ' + name + '">View Original ' + name + '</a>' );
			
			$('#dOriginal' + data[2]).fadeOut( 'fast' );
			
			setTimeout( function() {
				$('#dRequested' + data[2]).fadeIn();
			}, 250 );
		}
	});
	
	// Approve button
	$('#bApprove').click( function() {
		$('#hAction').val( 'approve' );
		
		$('#fUpdateRequest').submit();
	});

	// Disapprove button
	$('#bDisapprove').click( function() {
		$("#dSendDisapproveMessage").dialog( {
			modal: true,
			bgiframe: true,
			height: 250,
			width: 400,
			draggable: false,
			resizable: false,
			title: 'Send Disapproval Message'
		});
		
		$('#fSendMessage').ajaxForm({
			dataType: 'json',
			success: function( response ) {
				// Handle any errors
				if( !response['result'] ) {
					alert( response['error'] );
					return;
				}
				
				$('#hAction').val( 'disapprove' );
	
				$('#fUpdateRequest').submit();
			}
		});
	});

	
	// Send message in message quee
	$('#fNewMessage').ajaxForm({
		dataType : 'json' ,
		beforeSubmit : function ( formData, jqForm, options ) {
			$('#taMessage, #bSendMessage').attr( 'disabled' , 'disabled' );
		} ,
		success : function( response ){
			// Handle any errors
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			loadMessages();
			$('#taMessage').val('');
			
			$('#taMessage, #bSendMessage').removeAttr( 'disabled' );
		}
	});

	// Send message in message quee
	$('#fUpdateRequest').ajaxForm({
		dataType : 'json' ,
		success : function( response ){
			// Handle any errors
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			// Refresh page
			window.location = window.location;
		}
	});

	// Load Initial Messages
	loadMessages();
}

/**
 * Function to load list of messages
 * List of messages posted to specific request
 */
function loadMessages( requestID ){
	if ( requestID == null )
		requestID = $('#hRequestID').val();
	
	$.post( '/ajax/requests/get-messages/', { '_nonce' : $('#_ajax_get_messages').val(), 'rid' : requestID }, function( response ) {
		// Handle any errors
		if( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		
		var html = '';
			
		for( i = 0; i < response['messages'].length; i++ ){
			var m = response['messages'][i];
			
			html += '<div class="dMessage"><div class="title"><strong>' + m['contact_name'] + '</strong><br />' + m['date_created'] + '<br />' + m['time'] + '</div><div class="msg">' + m['message'] + '</div></div>';
		}
		
		$('#dMessageList').html( html );
	}, 'json' );
}