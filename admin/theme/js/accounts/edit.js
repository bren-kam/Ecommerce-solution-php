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