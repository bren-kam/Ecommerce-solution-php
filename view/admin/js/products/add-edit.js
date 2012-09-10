// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
    // Make the form verify that the images is a proper field
	$('#fAddEditProduct').submit( function() {
		if ( 'public' == $('#sStatus').val() && ( $('#images-list .image') ).length < 1 ) {
			alert( $(this).attr('err') );
			return false;
		}
	});

    // Trigger the check to make sure the slug is available
	$('#tName').change( function() {
		if ( $(this).attr('tmpval') == $(this).val() || '' == $(this).val().replace(/\s/g, '') )
            return;

        // Get slugs
        var tProductSlug = $('#tProductSlug');

        // Change slug
        if ( '' == tProductSlug.val() )
            tProductSlug.val( $(this).val().slug() );

        // Create the product ID
        if ( '' == $('#fAddEditProduct').attr('action') )
            $.post( '/products/create/', { _nonce : $('#_create_product').val() }, ajaxResponse , 'json');
	});

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

    // Make Specs sortable
    $('#product-specs-list').sortable({
        forcePlaceholderSize : true
        , placeholder: 'list-item-placeholder'
        , update: function() {
            applyListClasses( $('#product-specs-list') );
        }
    });

    // Make Images sortable
    $('#images-list').sortable({
        forcePlaceholderSize : true
        , placeholder: 'image-placeholder'
    });

    // The 'Add Spec' button
    $('#add-product-spec').click( function() {
        var tAddSpecName = $('#tAddSpecName'), specName = tAddSpecName.val().trim().replace( /[|`]/g, ''), tmpSpecName = tAddSpecName.attr('tmpval');
        var taAddSpecValue = $('#taAddSpecValue'), specValue = taAddSpecValue.val().trim().replace( /[|`]/g, ''), tmpSpecValue = taAddSpecValue.attr('tmpval');
        var productSpecsList = $('#product-specs-list'), productSpecTemplate = $('#product-spec-template');

        if ( tmpSpecName == specName )
            specName = '';

        if ( tmpSpecValue == specValue )
            specValue = '';

		// ake sure it's a valid entry
		if ( '' == specName && '' == specValue )
			return;

		var values = specValue.split( /\n/ );

		for ( var i in values ) {
			specValue = values[i].trim();

            var newProductSpec = productSpecTemplate
                .clone()
                .removeAttr('id');

            newProductSpec
                .find('span.specification-name')
                    .text( specName )
                    .end()
                .find('span.specification-value')
                    .text( specValue )
                    .end()
                .find('input:first')
                    .val( specName + '|' + specValue );

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
			var option = $(this), attributeItemId = option.val();

			// Make sure they actually put something in
			if ( '' == attributeItemId )
                return;

            var newAttributeItem = attributeItemTemplate
                .clone()
                .removeAttr('id');

            newAttributeItem
                .find('strong:first')
                    .prepend( option.parents('optgroup:first').attr('label') )
                    .after( option.text() )
                    .end()
                .find('input:first')
                    .val( attributeItemId );

            attributeItemsList.append( newAttributeItem );

            // Deselect the option
            option.attr('disabled', true).prop('selected', false);
		});

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
    });

    // Make Images delete work
    $('#images-list').on( 'click', 'a.delete', function() {
        if ( confirm( $(this).attr('confirm') ) )
            $(this).parent().parent().remove();
    });

    // Make attribute items delete work
    $('#attribute-items-list').on( 'click', 'a.delete-attribute-item', function() {
        var attributeItemId = $(this).next().val();

        // Remove parent
        $(this).parent().remove();

        // Enable item in drop down
        $('#sAttributes option[value=' + attributeItemId + ']').attr('disabled', false);
    });

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/products/upload-image/'
        , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
        , element: $('#upload-image')[0]
        , sizeLimit: 10485760 // 10 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload').val()
                , tid : $('#hTicketID').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUpload').click( function() {
        $('#upload-image input:first').click();
    });

    // Make attributes specific to a category
    $('#sCategory').change( updateAttributes );

    // We need to update them once
    updateAttributes();
});

// Apply the classes
function applyListClasses( list ) {
    $('.item', list).removeClass('even').filter(':even').addClass('even');
}

// Update attributes
function updateAttributes() {
    var categoryId = parseInt( $('#sCategory').val() );

    if ( categoryId <= 0 )
        return;

    // Load attribute items
	$.post( '/products/get-attribute-items/', { _nonce: $('#_get_attribute_items').val(), cid : categoryId }, ajaxResponse, 'json' );
}

// Disable attributes
$.fn.disableAttributes = function() {
    var sAttributes = $('#sAttributes');

    $('#attribute-items-list input').each( function() {
        $('option[value=' + $(this).val() + ']', sAttributes).attr( 'disabled', true );
    });
}

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }