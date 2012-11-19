// When the page has loaded
jQuery(function($) {
    // Auto adjust slug
    $('#tName').click( function() {
        $('#tSlug').val( $(this).val().slug() );
    });

    // Add items to the list
    $('#sProductOptions').change( function() {
        var productOption = $(this).find('option:selected'), productOptionId = $(this).val();

		if ( '' == productOptionId )
			return;

		// Create new div
		var newListItem = '<div class="product-option"><span class="product-option-name">' + productOption.text() + '</span>';

		// Add 'X'
		newListItem += '<a href="#" class="delete-product-option" title="Delete"><img src="/images/icons/x.png" width="15" height="17" alt="" /></a>';

        // Add on hidden element
        newListItem += '<input type="hidden" name="product-options[]" value="' + productOptionId + '" /></div>';

		// Disable that option in the drop down
		productOption.attr('disabled', true);

        $('#product-options-list').append( newListItem );

        // Select first index
		$(this).val('');
    });

    // Remove items from the list
    $('#product-options-list').on( 'click', 'a.delete-product-option', function() {
        var parent = $(this).parent(), productOptionId = $('input:first', parent).val();

        parent.remove();
        $('#sProductOptions option[value=' + productOptionId + ']').attr( 'disabled', false );
    });

    // Make our upload button work
    $('#aUpload, #tImage').click( function() {
        $('#fImage').trigger('click');
        $('#aUpload').focus();
    });

    // Change the file name
    $('#fImage').change( function() {
        var fileName = $(this).val().split('\\');
        fileName = fileName[fileName.length-1];
        var ext = fileName.split('.');

        // We don't want to focus on the textbox
        $('#aUpload').focus();

        switch ( ext[ext.length-1] ) {
            case 'jpeg':
            case 'jpg':
            case 'gif':
            case 'png':
            break;

            default:
                $(this).val('');
                $('#tImage').val('');
                alert( $(this).attr('err') );
                return;
            break;
        }

        $('#tImage').val( fileName );
    });
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); };