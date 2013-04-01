// When the page has loaded
jQuery(function($) {
    /********** Page Link  **********/
	// Trigger the check to make sure the slug is available
    $('#tPageTitle').keyup( function() {
        var tPageSlug = $('#tPageSlug');

        if ( tPageSlug.is('input') )
            tPageSlug.val( $(this).val().slug() );
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
