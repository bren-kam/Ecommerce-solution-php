/**
 * Websites Edit Page
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
	// Add Another Website functionality - Trigger (Click)
	$('#aContinueEditingWebsite').click( aContinueEditingWebsite );
	
	// Make the user email change.
	$('#sUserID option').click( function() {
		$('#tUserEmail').val( ( $(this).attr('email') ) );
	});

    // Cancel Website
    $('#aCancelWebsite').click( function(e) {
        e.preventDefault();

        if ( !confirm( 'Are you sure you want to cancel this website? This cannot be undone.') )
            return;

        // Delete the website
        window.location = $(this).attr('href');
    });
}

/**
 * aContinueEditingWebsite
 *
 * If someone wants to continue editing a website, they can click here
 */
function aContinueEditingWebsite() {
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