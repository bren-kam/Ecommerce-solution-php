jQuery(function(){
	// Trigger the dialog
	$('#aTicket').click( function() {
		var a = $(this);
		
		if ( a.hasClass('loaded') ) {
			new Boxy( $('#dTicketPopup'), {
				title : a.attr('title')
			});
			
			return;
		}

		head.js( '/js2/?f=jquery.boxy', '/js2/?f=jquery.form', '/js2/?f=swfobject', '/js2/?f=jquery.uploadify', function() {
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
					
					if ( !summary.length || summary == tTicketSummary.attr('tmpval') ) {
						alert( tTicketSummary.attr('error') );
						return false;
					}
					
					if ( !message.length || message == taTicket.attr('tmpval') ) {
						alert( taTicket.attr('error') );
						return false;
					}
					
					return true;
				},
				success			: ajaxResponse
			});
			
			// Make the upload image icon work with uploadify
			$('#fTicketUpload').uploadify({
				auto      	: true,
				multi		: true,
				displayData	: 'speed',
				buttonImg 	: '/images/buttons/add-attachment.png',
				cancelImg 	: '/images/icons/cancel.png',
				fileExt		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*.mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps;*.ppt;*.wks;*.bmp;*.gif;*.jpg;*.jpeg;*.png;*.psd;*.tif;*.zip;*.7z;*.rar;*.zipx;*.aiff;*.odt;',
				fileDesc	: 'Valid File Formats', // @Fix needs to be put in PHP
				onComplete	: function( e, queueID, fileObj, response ) {
					ajaxResponse( $.parseJSON( response ) );
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
	});
});