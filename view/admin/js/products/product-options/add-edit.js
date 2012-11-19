// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    // Change Screens
    $('#subcontent a.screen').click( function() {
        $('#subcontent div.screen').hide();
        $('#' + $(this).attr('rel')).show();
    });

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
	});

    /**
     * Delete an item from the list
     */
    $('#items-list').on( 'click', 'a.delete-list-item', function() {
        if ( confirm( $(this).attr('confirm') ) )
            $(this).parent().remove();
    });
});