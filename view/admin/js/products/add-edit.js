// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=jquery.form', function() {
    // Date Picker
	$('#tPublishDate').datepicker({
		dateFormat: 'MM d, yy'
        , altField: ('#hPublishDate')
        , altFormat: 'yy-mm-dd'
	});

    // Handle all the lists
    $('#right .list').each( function() {
        applyListClasses( $(this) );
    });

    // The 'Add Spec' button
    $('#add-product-spec').click( function() {
        var tAddSpecName = $('#tAddSpecName'), specName = tAddSpecName.val().trim().replace( /[|`]/g, ''), tmpSpecName = tAddSpecName.attr('tmpval');
        var taAddSpecValue = $('#taAddSpecValue'), specValue = taAddSpecValue.val().trim().replace( /[|`]/g, ''), tmpSpecValue = taAddSpecValue.attr('tmpval');
        var productSpecsList = $('#product-specs-list'), productSpecTemplate = $('#product-spec-template');

		// ake sure it's a valid entry
		if ( tmpSpecName == specName || '' == specName || tmpSpecValue == specValue || '' == specValue )
			return;

		var values = specValue.split( /\n/ );

		for ( var i in values ) {
			specValue = values[i].trim();

            var newProductSpec = productSpecTemplate
                .clone()
                .removeAttr('id');

            newProductSpec
                .find('div.specification-name')
                    .text( specName )
                    .end()
                .find('div.specification-value')
                    .text( specValue );

			productSpecsList.append( newProductSpec );
		}

		// Reset values
		$('#tAddSpecName, #taAddSpecValue').val('').trigger('blur');

        // Update the list
        applyListClasses( productSpecsList );
    });

    // The 'Add Tag' button
	$('#add-tag').click( function() {
		var tagValue = $('#tTags'), tagValues = tagValue.val().split(','), tmpValue = $(this).attr('tmpval'), tagTemplate = $('#tag-template'), tagsList = $('#tags-list');

		for ( var i in tagValues ) {
			var tag = tagValues[i];

			// If they entered nothing, do nothing
			if ( '' == tag || tmpValue == tag )
				return;

			// Start creating new div
			var newTag = tagTemplate
                .clone()
                .removeAttr('id');

            newTag.prepend(tag).find('input:first').val( tag );

			// Append it
			tagsList.append( newTag );
		}

		// Reset to default values
        tagValue.val('').trigger('blur');

        // Update the list
        applyListClasses( tagsList );
	});

    // The 'Add Attribute' button
	$('#add-attribute').click( function() {
        var attributeItemTemplate = $('#attribute-item-template'), attributeItemsList = $('#attribute-items-list'), sAttributes = $('#sAttributes');

		sAttributes.find('option:selected').each( function() {
			var option = $(this), attributeItemID = option.val();

			// Make sure they actually put something in
			if ( '' == attributeItemID )
                return;

            var newAttributeItem = attributeItemTemplate
                .clone()
                .removeAttr('id');

            newAttributeItem
                .find('strong:first')
                    .prepend( option.parents('optgroup:first').attr('label') )
                    .after( option.text() );

            attributeItemsList.append( newAttributeItem );

            // Deselect the option
            option.attr('disabled', true);
		}).val('');

        // Update the list
        applyListClasses( attributeItemsList );
	});

    // Make delete functions work
    $('#right').on( 'click', 'a.delete', function() {
        // Get the list
        var list = $(this).parents('.list:first');

        // Remove the parent
        $(this).parent().remove();

        // Make it look good
        applyListClasses( list );
    })
});

// Apply the classes
function applyListClasses( list ) {
    $('.item', list).removeClass('even').filter(':even').addClass('even');
}