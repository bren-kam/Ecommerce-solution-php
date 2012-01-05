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