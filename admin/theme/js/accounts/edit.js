/**
 * Accounts Edit Page
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
	// Add Another Account functionality - Trigger (Click)
	$('#aContinueEditingAccount').click( aContinueEditingAccount );
	
	// Make the user email change.
	$('#sUserID option').click( function() {
		$('#tUserEmail').val( ( $(this).attr('email') ) );
	});

    // Cancel Account
    $('#aCancelAccount').click( function(e) {
        e.preventDefault();

        if ( !confirm( 'Are you sure you want to cancel this account? This cannot be undone.') )
            return;

        // Delete the account
        window.location = $(this).attr('href');
    });

    // Delete Category and Products
    $('#aDeleteProducts').click( function(e) {
        e.preventDefault();

        // Get the paragraph
        var p = $(this).parent();

        if ( !confirm( 'Are you sure you want to delete ALL categories and products? This cannot be undone.') )
            return;

        $.post( '/ajax/accounts/delete-products/', { _nonce : $('#_ajax_delete_products').val(), wid: $(this).attr('rel') }, function ( response ) {
            // Make sure there was no error
            if ( !response['result'] ) {
                alert( response['error'] );
                return false;
            }

            // Remove any success message
            $('#pSuccessMessage').remove();

            // Show them success message
            p.after('<p id="pSuccessMessage" class="success">The categories and products have been successfully removed.</p>');

            // Remove the message after five seconds
            setTimeout( function() {
                $('#pSuccessMessage').remove();
            }, 5000 );
        }, 'json' );
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

    // Make the social media work
    $('#cbSocialMedia').click( function() {
        if( $(this).is(':checked') ) {
            // Show the Social Media Options
            $('#dSocialMedia').show();
        } else {
            // Hide Social media Options
            $('#dSocialMedia').hide();
        }
    });

    // Make the social media work
    $('#sSocialMedia').change( function() {
        if ( $(this).find('option:checked').is('option') )
            return;

        $('#cbSocialMedia').attr( 'checked' , false );
        $('#dSocialMedia').hide();
    });

    // Make it possible to install a package
    $('#aInstallPackage').click( function(e) {
		e.preventDefault();
		
        if ( !confirm( $(this).attr('confirm') ) )
            return;

        $.post( '/ajax/accounts/install-package/', { _nonce : $('#_ajax_install_package'), wid : $(this).attr('rel'), cpid : $('#sCompanyPackageID').val() }, function ( response ) {
            if ( !response['success'] ) {
                alert( response['error'] );
                return;
            }

            alert( response['message'] )
        }, 'ajax' );
    });
}

/**
 * aContinueEditingAccount
 *
 * If someone wants to continue editing an account, they can click here
 */
function aContinueEditingAccount() {
	$('#dSuccess').fadeOut( 'fast' );
	
	setTimeout( timeoutMainFormFadeIn, 250 );
}

/**
 * timeoutMainFormFadeIn
 *
 * Called by aAddAnotherClick timeout
 *
 */
function timeoutMainFormFadeIn() {
	$('#dMainForm').fadeIn();
}