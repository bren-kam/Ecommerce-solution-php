/**
 * Common header
 */
jQuery(function($) {
	// Stop hash tags from appearing in URLs
	$('body').on( 'click', 'a[href^=#]', function(e) { e.preventDefault(); } );
});