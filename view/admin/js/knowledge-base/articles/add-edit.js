// When the page has loaded
jQuery(function($) {
   	$('#sSection').change( function() {
        $.post( '/knowledge-base/articles/get-categories/', { _nonce : $('#_get_categories').val(), s : $(this).val() }, function( JSON ) {
            ajaxResponse( JSON );
            $('#sCategory').trigger('change');
        });
    });

    $('#fAddEditArticle').on( 'change', '#sCategory', function() {
        $.post( '/knowledge-base/articles/get-pages/', { _nonce : $('#_get_pages').val(), kbcid : $(this).val(), kbpid : $('#sPage').val() }, ajaxResponse );
    });

    $('#sCategory').trigger('change');

    // Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
        // Get slugs
        var tSlug = $('#tSlug');

        // Change slug
        if ( '' == tSlug.val() )
            tSlug.val( $(this).val().slug() );
    });

    /**
     * Make sure it also contains a proper slug
     */
    $('#tSlug').change( function() {
        $(this).val( $(this).val().slug() );
    });
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); };