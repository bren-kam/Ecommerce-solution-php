/**
 * Common header
 */
jQuery(function($) {
    // Set the notifications up
    $('.notification').notify();

	// Stop hash tags from appearing in URLs
	$('body').on( 'click', 'a[href^=#]', function(e) { e.preventDefault(); } );

	// Trigger the dialog
	$('#aTicket, a.support-ticket').click( function() {
		var a = $(this);

        if ( a.hasClass('support-ticket') )
            window.scrollTo( 0, 0 );

		if( a.hasClass('loaded') ) {
			new Boxy( $('#dTicketPopup'), {
				title : a.attr('title')
			});
			
			return;
		}

		head.load( '/resources/js_single/?f=jquery.boxy', '/resources/js_single/?f=jquery.form', '/resources/js_single/?f=fileuploader', function() {
			a.addClass('loaded');

			// If exists, and they want to cache it use it
			new Boxy( $('#dTicketPopup'), {
				title : a.attr('title')
			});

			// Add the Form first
			$('#fCreateTicket').addClass('ajax').ajaxForm({
				dataType		: 'json',
				beforeSubmit	: function() {
                    var tTicketSummary = $('#tTicketSummary'), summary = $.trim( tTicketSummary.val() ), taTicket = $('#taTicketMessage'), message = $.trim( taTicket.val() );

					if( !summary.length ) {
						alert( tTicketSummary.attr('error') );
						return false;
					}

					if( !message.length ) {
						alert( taTicket.attr('error') );
						return false;
					}

					return true;
				},
				success			: ajaxResponse
			});

            // Setup File Uploader
            var ticketUploader = new qq.FileUploader({
                action: '/tickets/upload-to-ticket/'
                , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt']
                , element: $('#upload-ticket-attachment')[0]
                , sizeLimit: 10485760 // 10 mb's
                , onSubmit: function( id, fileName ) {
                    ticketUploader.setParams({
                        _nonce : $('#_upload_to_ticket').val()
                        , tid : $('#hSupportTicketId').val()
                    })
                }
                , onComplete: function( id, fileName, responseJSON ) {
                    ajaxResponse( responseJSON );
                }
            });
		});
	});

    // Submit a form
    $('#bCreateTicket').click( function() {
        $('#fCreateTicket').submit();
    });

    /**
     * Make the uploader work
     */
    $('#aUploadTicketAttachment').click( function() {
        if ( $.support.cors ) {
            $('#upload-ticket-attachment input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    });

    $('body')
        .on( 'click', '.boxy-footer .button[rel]', function() {
            $('#' + $(this).attr('rel')).submit();
        }
    );
});