/**
 * Users Edit Page
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
	// Add another user
	$('#aUpdateAnother').click( function() {
		$('#dSuccess').fadeOut( 'fast' );
		
		setTimeout( function() {
			$('#dMainForm').fadeIn();
		}, 250 );
	});
}