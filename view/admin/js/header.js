/**
 * Common header
 */
jQuery(function($) {
    // Set the notifications up
    $('.notification').notify();

	// Stop hash tags from appearing in URLs
	$('body').on( 'click', 'a[href^=#]', function(e) { e.preventDefault(); } );

	// Trigger the dialog
	$('#aTicket').click( function() {
		var a = $(this);
		
		if( a.hasClass('loaded') ) {
			new Boxy( $('#dTicketPopup'), {
				title : a.attr('title')
			});
			
			return;
		}

		head.js( '/resources/js_single/?f=jquery.boxy', '/resources/js_single/?f=jquery.form', function() {
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
				success			: ajaxResponse
			});
		});
	});

    // Submit a form
    $('#bCreateTicket').click( function() {
        $('#fCreateTicket').submit();
    });

    // Expandercontractor
    $('#dTicketPopup .expander').click( function() {
        if ( $(this).hasClass('open') ) {
            $(this).removeClass('open').addClass('close');

            $('#' + $(this).attr('rel')).show();
        } else {
            $(this).removeClass('close').addClass('open');
            $('#' + $(this).attr('rel')).hide();
        }
    });

    $('body')
        .on( 'click', '.boxy-footer .button[rel]', function() {
            $('#' + $(this).attr('rel')).submit();
        }
    );
});