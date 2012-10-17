// Turns text into a slug
;String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); };

/**
 * Dashboard
 */
jQuery(function($) {
	// Stop hash tags from appearing in URLs
	$('a[href^=#]').live( 'click', function(e) { e.preventDefault(); } );
	
	// Properly hide objects
	$('.hidden').hide().removeClass('hidden');
	
	// Trigger the dialog
	$('#aTicket').click( function() {
		var a = $(this);
		
		if( a.hasClass('loaded') ) {
			new Boxy( $('#dTicketPopup'), {
				title : a.attr('title')
			});
			
			return;
		}

		a.addClass('loaded');
		
		// If exists, and they want to cache it use it
		new Boxy( $('#dTicketPopup'), {
			title : a.attr('title')
		});
		
		// Add the Form first
		$('#fCreateTicket').addClass('ajax').ajaxForm({
			dataType		: 'json',
			beforeSubmit	: function() {
				var tTicketSummary = $('#tTicketSummary'), summary = tTicketSummary.val(), taTicket = $('#taTicket'), message = taTicket.val();
				
				if( !summary.length || summary == tTicketSummary.attr('tmpval') ) {
					alert( tTicketSummary.attr('error') );
					return false;
				}
				
				if( !message.length || message == taTicket.attr('tmpval') ) {
					alert( taTicket.attr('error') );
					return false;
				}
				
				return true;
			},
			success			: function( response ) {
				// Test for success
				if( !response['success'] ) {
					alert( response['error'] );
					return
				}
				
				// Close the window
				$('a.close:first').click();
				
				// Don't want the attachments coming up next time
				$('#ticket-attachments').empty();
				
				// Reset the two fields
				$('#tTicketSummary, #taTicket').val('').blur();
			}
		});
		
		// Make the upload image icon work with uploadify
		$('#fTicketUpload').uploadify({
			auto      	: true,
			multi		: true,
			displayData	: 'speed',
			buttonImg 	: '/images/buttons/add-attachment.png',
			cancelImg 	: '/images/icons/x.png',
			fileExt		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*.mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps;*.ppt;*.wks;*.bmp;*.gif;*.jpg;*.jpeg;*.png;*.psd;*.eps;*.tif;*.zip;*.7z;*.rar;*.zipx;*.aiff;*.odt;',
			fileDesc	: 'Valid File Formats', // @Fix needs to be put in PHP
			onComplete	: function( e, queueID, fileObj, response ) {
				response = $.parseJSON( response );
				
				// Test for success
				if( !response['success'] ) {
					alert( response['error'] );
					return;
				}
				$('#hTicketID').val( response['ticket_id'] );
				
				// Add the new link and apply sparrow to it
				$('#ticket-attachments')
					.show()
					.append( '<p id="pAttachment' + response['ticket_upload_id'] + '">' + response['attachment_name'] + '<input type="hidden" name="hTicketImages[]" value="' + response['ticket_upload_id'] + '" /> <a href="/ajax/support/delete-attachment/?_nonce=' + response['delete_attachment_nonce'] + '&amp;tuid=' + response['ticket_upload_id'] + '" title="Remove Attachment" ajax="1" confirm="Are you sure you want to remove this attachment? This cannot be undone."><img src="/images/icons/x.png" width="15" height="17" alt="Remove Attachment" /></a></p>' );
				
				// Make anchors support AJAX calls
				$('#ticket-attachments a[ajax]:first', context).click( function( e ) {
					// Prevent the click
					e.preventDefault();
					
					// Should have another way to do confirm boxes from dialogs
					var confirmQuestion = $(this).attr('confirm');
					
					if( confirmQuestion && !confirm( confirmQuestion ) )
						return
					
					$.get( $(this).attr('href'), function( response ) {
						// Test for success
						if( !response['success'] ) {
							alert( response['error'] );
							return;
						}
						
						$('#pAttachment' + response['ticket_upload_id']).remove();
					}, 'json' );
				}).removeAttr('ajax'); // Prevent it from getting called again
			},
			onSelect	: function() {
				$('#fTicketUpload').uploadifySettings( 'scriptData', { _nonce : $('#_ajax_ticket_upload').val(), uid : $('#hUserID').val(), wid : $('#hTicketWebsiteID').val(), tid : $('#hTicketID').val() } );
				return true;
			},
			sizeLimit	: 6291456,// (6mb) In bytes? Really?
			script    	: '/ajax/support/ticket-upload/',
			uploader  	: '/media/flash/uploadify.swf',
			width		: 124,
			height		: 34
		});
	});
	
	// Temporary Values
	$('input[tmpval],textarea[tmpval]').each( function() {
		/**
		 * Sequence of actions:
		 *		1) Set the value to the temporary value (needed for page refreshes
		 *		2) Add the 'tmpval' class which will change it's color
		 * 		3) Set the focus function to empty the value under the right conditions and remove the 'tmpval' class
		 *		4) Set the blur function to fill the value with the temporary value and add the 'tmpval' class
		 */
		$(this).focus( function() {
			// If the value is equal to the temporary value when they focus, empty it
			if( $(this).val() == $(this).attr('tmpval') )
				$(this).val('').removeClass('tmpval');
		}).blur( function() {
			// Set the variables so they don't have to be grabbed twice
			var value = $(this).val(), tmpValue = $(this).attr('tmpval');
			
			// Fill in with the temporary value if it's empty or if it matches the temporary value
			if( 0 == value.length || value == tmpValue ) 
				$(this).val( tmpValue ).addClass('tmpval');
		});
		
		// If there is no value, set it to the correct value
		if( !$(this).val().length )
			$(this).val( $(this).attr('tmpval') ).addClass('tmpval');
	});
});

/**
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 * Docs: http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
function info(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += info(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

// Generate random string
function rnd() {
	return String( ( new Date() ).getTime() ).replace( /\D/gi,'' );
}