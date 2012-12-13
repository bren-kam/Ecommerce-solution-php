// When the page has loaded
jQuery(function($) {
	// Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
        var tSlug = $('#tSlug');

        if ( tSlug.is('input') )
            tSlug.val( $(this).val().slug() );
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
