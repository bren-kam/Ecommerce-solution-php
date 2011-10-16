/**
 * Brands Add Page
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
	// Brand name changes slugs
	$('#tName').keyup( function() {
		var slug = $('#tSlug');
		
		if( !slug.data('changed') )
			slug.val( $(this).val().slug() );
	});
	
	// Slug
	$('#tSlug').keyup( function() {
		$(this).data( 'changed', true ).val( $(this).val().slug() );
	});
	
	// Add attributes
	$('#aAddProductOption').click( function() {
		var productOption = $('#sProductOptions option:selected'), productOptionTitle = productOption.text(), productOptionID = productOption.val();
		
		if( '' == productOptionID )
			return;
			
		// Append new div
		$('#dProductOptionsList').append( '<div extra="' + productOptionTitle.slug() + '" id="dProductOption' + productOptionID + '" style="display:none;" class="product-option-container"><div class="product-option"><span class="product-option-name">' + productOptionTitle + '</span><div style="display:inline;float:right"><a href="javascript:;" class="delete-product-option" title=\'Delete "' + productOptionTitle + '" Product Option\'><img class="delete-product-option" src="/images/icons/x.png" width="15" height="17" /></a></div></div></div>' );
		
		// Slide it down
		$('#dProductOption' + productOptionID).slideDown();
		
		// Disable that option in the drop down
		productOption.attr('disabled', true);
		
		$('#sProductOptions option:first').attr( 'selected', true );
		
		// Update product options
		updateProductOptions();
	});
	
	// Deletes an attribute
	$('.delete-product-option').live( 'click', function() {
		var parent = $(this).parents('div.product-option-container:first');
		
		// Enable the drop down
		$('#sProductOptions option[value=' + parent.attr('id').replace( 'dProductOption', '' ) + ']').attr( 'disabled', false );
		
		// Remove the parent
		parent.remove();

		// Update attributes
		updateProductOptions();
	});
	
	// Add another user
	$('#aAddAnother').click( function() {
		$('#dSuccess').fadeOut( 'fast' );
		
		setTimeout( function() {
			$('#dMainForm').fadeIn();
		}, 250 );
	});
}

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

/**
 * Updates a hidden field with the list of product_options
 */
function updateProductOptions() {
	var hiddenField = $('#hProductOptions');
	hiddenField.val('');
	
	$('#dProductOptionsList .product-option-container').each( function() {
		hiddenField.val( hiddenField.val() + $(this).attr('id').replace('dProductOption', '') + '|' );
	});
}
