/**
 * Accounts Add Page
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
	$('#aAddAnother').click( aAddAnotherClick );
}

/**
 * aAddAnotherClick
 *
 * If someone wants to add another website, they can just click here
 */
function aAddAnotherClick() {
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