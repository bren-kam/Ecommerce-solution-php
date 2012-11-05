/**
 * Attributes Edit Page
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
	// Make it have a temporary value
	$('#tListItemValue').tmpVal( '#929292', '#000000' );
        
        
	/********** Attributes Section **********/
	// The 'Add List Item' link
	$('#aAddListItem').click( function() {
		var itemNames = $('#tListItemValue').val().split(',');
		
		for ( i in itemNames ) {
			var itemName = itemNames[i], itemSlug = itemName.slug() + '_' + i;
			
			// If they entered nothing, do nothing
			if ( '' == itemName || 'Item Name' == itemName )
				return;
			
			// Start creating new div
			var newListItem = '<div extra="' + itemSlug + '" id="dListItem_' + itemSlug + '" style="display:none;" class="list-item-container"><div class="list-item"><span class="list-item-name">' + itemName + '</span><input type="hidden" name="hListItems[]" value="' + itemName + '" />';
			
			// Add 'X' and edit button
			newListItem += '<div style="display:inline;float:right"><a href="javascript:;" title=\'Edit "' + itemName + '" List Item\' class="edit-list-item"><img src="/images/icons/edit.png" alt=\'Edit "' + itemName + '" List Item\' width="15" height="17" /></a><a href="javascript:;" class="delete-list-item" title=\'Delete "' + itemName + '" List Item\'><img src="/media/images/icons/x.png" width="15" height="17" /></a></div></div></div>';
	
			// Append it
			$('#dItemsList').append( newListItem );
			
			// Slide it down
			$('#dListItem_' + itemSlug).slideDown();
		}
		
		// Reset to default values
		$('#tListItemValue').val('Item Name').css('color', '#929292');

		// Update list items
		updateListItems();
	});
        
	// Delete List Items
	$('.delete-list-item').live( 'click', function() {
		$(this).parents('.list-item-container:first').remove();

		updateListItems();
	});
        
	// Edit the List Items
	$('.edit-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first');
		
		parent.find('span, div, a').fadeOut('fast');
		
		setTimeout( function() {
			parent.append('<input type="text" class="tb edit-attribute-name" value="' + parent.find('span:first').text() + '" maxlength="100" /><p style="float:right"><a href="javascript:;" class="save-list-item button" title="Save" style="display:none">Save</a> <a href="#" class="cancel-list-item" title="Cancel" style="display:none;">Cancel</a></p>');
			parent.find('input[type=text], a.save-list-item, a.cancel-list-item').fadeIn();
		}, 200 );
		
		parent.css( 'borderColor', '#F00' ).animate({ height: '40px' }, 200 );
	});
	
	// Cancel an editing of a list item
	$('.cancel-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first');

		parent.css( 'borderColor', '' ).animate( { height: '17px' }, 200 );
		parent.find('input[type=text], p').remove();
		parent.find('span, a, div').fadeIn();
	});
	
	// Save a profile link
	$('.save-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first'), attributeNameField = parent.find('.edit-attribute-name'), attributeName = attributeNameField.val();
		
		// Make sure it's not an empty profile_name
		if ( '' == attributeName ) {
			alert( 'Please enter an attribute name' );
			attributeNameField.focus();
			return;
		}
		
		// Make sure it's within 15 characters
		if ( attributeName.length > 100 ) {
			alert( 'The attribute name may only have up to 100 characters' );
			attributeNameField.focus();
			return;
		}
		
		parent.find('.list-item-name').text( attributeName );
		
		updateListItems( 'Edit' );
		
		parent.css( 'borderColor', '' ).animate( { height: '17px' }, 200 );
		parent.find('input[type=text], p').remove();
		parent.find('span, a, div').fadeIn();
	});
	
	$('#dItemsList').sortable( {
		forcePlaceholderSize : true,
		update: function(event, ui) {
			updateListItems( extra );
		}, 
		placeholder: 'list-item-placeholder'
	});
}

/**
 * Updates a hidden fields with correct ID
 */
function updateListItems() {
	// Update the hidden input
	$('#dItemsList .list-item').each( function() {
		var hiddenInput = $(this).find('input[type=hidden]');
		hiddenInput.val( hiddenInput.val().replace( /[^|]*(\|[\d]*)?/, $(this).find('.list-item-name').text() + '$1' ) );
	});
}