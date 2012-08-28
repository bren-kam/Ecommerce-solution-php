// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Make attributes sortable
	$("#items-list").sortable({
        forcePlaceholderSize : true
        , placeholder: 'list-item-placeholder'
    });

    // The 'Add List Item' link
	$('#add-list-item').click( function() {
		var listItemValue = $('#list-item-value'), itemNames = listItemValue.val().split(','), tmpValue = $(this).attr('tmpval'), listItemTemplate = $('#list-item-template'), itemsList = $('#items-list');

		for ( var i in itemNames ) {
			var itemName = itemNames[i];

			// If they entered nothing, do nothing
			if ( '' == itemName || tmpValue == itemName )
				return;

			// Start creating new div
			var newListItem = listItemTemplate
                .clone()
                .removeClass('hidden')
                .removeAttr('id');

            newListItem.find('input:first').val( itemName );

			// Append it
			itemsList.append( newListItem );
		}

		// Reset to default values
        listItemValue.val('').trigger('blur');

		// Update list items
		updateListItems();
	});

    /**
     * Delete an item from the list
     */
    $('#items-list').on( 'click', 'a.delete-list-item', function() {
        if ( confirm( $(this).attr('confirm') ) )
            $(this).parent().remove();
    });

    updateListItems();
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }


/**
 * Updates a hidden field with the list of attributes
 */
function updateListItems() {
    // Update the hidden input
	$('#items-list .list-item input').each( function() {
		//var hiddenInput = $(this);
		//hiddenInput.val( hiddenInput.val().replace( /[^|]*(\|[\d]*)?/, $(this).prev().text() + '$1' ) );
	});
}