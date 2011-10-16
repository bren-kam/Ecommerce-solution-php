/**
 * Products - Add/Edit Page
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
	// Setup cache
	cache = {};
	
	// Make the form verify that the images is a proper field
	$('#fAddEdit').submit( function() {
		if( ( $('.remove-product-image') ).length < 1 ) {
			alert( 'Products require at least one image to publish' );
			return false;
		};
	});
	
	// Make it have a temporary value
	$('#tName, #tAddSpecName, #taAddSpecValue, #tSKU, #tPrice, #tListPrice, #tWeight').tmpVal( '#929292', '#000000' );
	
	// Configure WYSIWYG editor
	$('#taDescription').ckeditor({
		autoGrow_minHeight : 200,
		resize_minHeight: 200,
		height: 200,
		toolbar : [
			[ 'Bold', 'Italic', 'Underline' ],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['NumberedList','BulletedList'],
			['Format'],
			['Link','Unlink'],
			['Source']
		]
	});

	// Add according displays with arrows
	$('.arrow-left').live( 'click', function() {
		$(this).removeClass('arrow-left round-bottom').addClass('arrow-down').next().slideDown();
	});

	$('.arrow-down').live( 'click', function() {
		var obj = $(this);
		obj.removeClass('arrow-down').addClass('arrow-left').next().slideUp('normal', function() { obj.addClass('round-bottom') } );
	});
	
	/********* Trigger aAddCategory so that it adds a category when the product is published ********/
	$('#iPublish').click(function() {
		//if( $('#sProductCategory').val() != 0 ) $('a#aAddCategory').click();
	});
	
	/********** Categories Link  **********/
	$('a#aAddCategory').click(function() {
		// Make sure they actually put something in
		if( 0 == $('#sProductCategory').val() ) {
			alert('Please select a category');
		} else {
			// Declare options
			var option = $('#sProductCategory option:selected'), categoryID = option.val();

			// It already exists, give an alert
			if( '' != $('#dCategory' + categoryID).text() ) {
				alert('That category has already been selected');
				return;
			}

			var categoryName = option.html().replace( /&nbsp;/g, '' );
	
			// Add the category and stripe it
			$('#dCategoryList').append( '<div id="dCategory' + categoryID + '" class="product-category" style="display:none;">' + categoryName + '<a href="#" class="delete-category" id="aDel' + categoryID + '" title=\'Delete "' + categoryName + '"\'><img class="delete-category" src="/images/icons/x.png" width="15" height="17" alt=\'Delete "' + categoryName + '"\' /></a></div>' ).stripe('product-category');

			// Slide it down
			$('#dCategory' + categoryID).slideDown();

			// Remove the state from the drop down
			option.css( 'color', '#929292' ).attr( 'disabled', true );

			// Set the value to nothing
			$('#sProductCategory').attr( 'selectedIndex', 0 );

			// Update product link
			categoryChangeProductLink();
			
			// Update the hidden input value
			updateCategories();
		}
	});
	
	// Delete buttons
	$('a.delete-category').live('click', function(){
		// Set variables
		var categoryID = $(this).attr('id').replace( 'aDel', '');

		$('#sProductCategory option[value=' + categoryID + ']').css( 'color', '' ).attr( 'disabled', false );

		// Remove self
		$(this).parents('div:first').remove();

		// Restripe
		$('#dCategoryList').stripe('product-category');

		// Update product link
		categoryChangeProductLink();

		// Update the hidden input value
		updateCategories();
	});

	/********** Product Link  **********/
	// Trigger the check to make sure the slug is available
	$('#tName').change( function() { 
		if( 'Product Name' == $(this).val() || '' == $(this).val().replace(/\s/g, '') ) {
			$('#dProductSlug, #pProductSlugError').slideUp();
		} else {
			// Get slugs
			var productSlug = $(this).val().slug(), categorySlug = $('.product-category:first').text().slug();

			// If no categories selected, put it in the products category
			if( '' == categorySlug )
				var categorySlug = 'products';
			
			// Create the product ID
			if( '' == $('#hProductID').val() ) {
				$.post( '/ajax/products/create/', { '_nonce' : $('#_ajax_create_product').val() }, function( response ) {
					// Handle any errors
					if( !response['result'] ) {
						alert( response['error'] );
						return;
					}
					
					$('#hProductID').val( response['result'] );
					$('#fUploadImages').uploadifySettings( 'scriptData', { '_nonce' : $('#_ajax_upload_image').val(), 'pid' : $('#hProductID').val() } );
				}, 'json');
			}
			
			var sProductSlug = $('#sProductSlug');
			
			// Makes sure it only changes the name when you first write the title
			if( '' == sProductSlug.text() ) {
				// Assign the slugs
				sProductSlug.text( productSlug );
				$('#tProductSlug').val( productSlug );
			}

			$('#sCategorySlug').text( categorySlug );
			$('#hCategorySlug').val( categorySlug );

			// Show the text
			$('#dProductSlug').slideDown();

			// Update the hidden input value
			updateCategories();
		}
	});

	// The "Edit" slug button
	$('#aEditProductSlug').click( function() {
		// Fade out the slug
		$('#sProductSlug, #aEditProductSlug').fadeOut('fast');

		// Fade in the other buttons
		setTimeout( function() {
			$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').fadeIn();
		 }, 200 );
	});

	// The "Save" slug button
	$('#aSaveProductSlug').click( function() {
		var productSlug = $('#tProductSlug').val().slug();

		// Assign the slugs
		$('#sProductSlug').text( productSlug );
		$('#tProductSlug').val( productSlug );

		// Fade out buttons
		$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').fadeOut('fast');

		// Fade in the other buttons
		setTimeout( function() {
			$('#sProductSlug, #aEditProductSlug').fadeIn();
		 }, 200 );
	});
	
	// The "Cancel" slug link
	$('#aCancelProductSlug').click( function() {
		// Assign the slugs
		$('#tProductSlug').val( $('#sProductSlug').text() );

		// Fade out buttons
		$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').fadeOut('fast');
		
		setTimeout( function() {
			$('#sProductSlug, #aEditProductSlug').fadeIn();
		 }, 200 );
	});

	/********** Tags **********/
	$('a#aAddTags').click(function() {
		// Make sure they actually put something in
		if( '' != $('#tAddTags').val().trim() ) {
			// Declare options
			var tags = $('#tAddTags').val().split(',');

			for( var i in tags ) {
				var t = tags[i].trim(), tSlug = t.slug();

				// It already exists, continue
				if( '' != $('#dTag_' + tSlug).text().trim() )
					continue;
	
	
				// Add the slug
				$('#dTagList').append( '<div id="dTag_' + tSlug + '" class="product-tag" style="display:none;">' + t + '<a href="#" class="delete-tag" id="aDel_' + tSlug + '" title=\'Delete "' + t + '"\'><img class="delete-tag" src="/images/icons/x.png" width="15" height="17" alt=\'Delete "' + t + '"\' /></a></div>' ).stripe('product-tag');

				// Slide it down
				$('#dTag_' + tSlug).slideDown();
			}

			// Set the value to nothing
			$('#tAddTags').val( '' );

			// Update tags
			updateTags();
		}
	});
	
	// Delete buttons
	$('a.delete-tag').live('click', function(){
		// Remove self
		$(this).parents('div:first').remove();
		
		// Restripe
		$('#dTagList').stripe('product-tag');
		
		// Update tags
		updateTags();
	});
	
	// Create tags autocomplete
	var tagsAC = $('#tAddTags').autocomplete({
		minLength: 1,
		source: autocompleteSuccess
	}).data( "autocomplete" )._renderItem = autocompleteRenderItem;
	
	/********** Specifications  **********/
	$('#aAddSpec').click( function() {
		var specName = $('#tAddSpecName').val().trim().replace( /[|`]/g, '' ), specValue = $('#taAddSpecValue').val().trim().replace( /[|`]/g, '' ), specSlug = ( specName + specValue ).slug();

		// ake sure it's a valid entry
		if( 'Name' == specName || '' == specName || 'Value' == specValue || '' == specValue ) // || '' != $('#dSpec_' + specSlug + ' div:first').text()
			return;

		if( 'na' == specName.toLowerCase() )
			specName = '';
		
		var values = specValue.split( /\n/ );
		
		for( var i in values ) {
			specValue = values[i].trim();
			specSlug = ( specName + specValue ).slug();
			
			//  style="display:none;"
			
			// Append it to the list
			$('#dSpecificationsList').append( '<div class="specification" id="dSpec_' + specSlug + '"><div class="specification-name">' + specName + '</div><div class="specification-value">' + specValue + '</div><a href="#" class="delete-spec" id="aDel_spec_' + specSlug + '" title=\'Delete "' + specName + '" Specification\'><img src="/images/icons/x.png" width="15" height="17" alt=\'Delete "' + specName + '" Specification\' /></a></div>');
	
			// Slide it down
			$('#dSpec_' + specSlug).slideDown();
		}

		// Add stripes
		$('#dSpecificationsList').stripe('specification');

		// Updates specifications
		updateSpecs();
		
		// Reset values
		$('#tAddSpecName').val('Name').css( 'color', '#929292' );
		$('#taAddSpecValue').val('Value').css( 'color', '#929292' );
	});
	
	
	// Make categories sortable
	$("#dSpecificationsList").sortable( {
		forcePlaceholderSize : true,
		update: function() {
			updateSpecs();
			$('#dSpecificationsList').stripe('specification');
		},
		placeholder: 'spec-placeholder'
	});
	
	// Delete the specs
	$('.delete-spec').live( 'click', function() {
		// Parent removed
		$(this).parents('div:first').remove();

		// Add stripes
		$('#dSpecificationsList').stripe('specification');

		//Update specs
		updateSpecs();
	});
	
	/********** Attributes **********/
	$('a#aAddAttribute').click(function() {
		$('#sAttributes option:selected').each( function() {
			var option = $(this), attributeItemID = option.val();
		
			// Make sure they actually put something in
			if( '' != attributeItemID ) {
				// Declare variables
				var attributeItemName = option.text();
	
				// Add the slug
				$('#dAttributeList').append( '<div id="dAttributeItem_' + attributeItemID + '" class="attribute" style="display:none;"><strong>' + option.parents('optgroup:first').attr('label') + ' &ndash;</strong> ' + attributeItemName + '<a href="#" class="delete-attribute" id="aDel_' + attributeItemID + '" title=\'Delete "' + attributeItemName + '"\'><img class="delete-attribute" src="/images/icons/x.png" width="15" height="17" alt=\'Delete "' + attributeItemName + '"\' /></a></div>' ).stripe('attribute');
	
				// Slide it down
				$('#dAttributeItem_' + attributeItemID).slideDown();
	
				// Set the value to nothing
				$('#sAttributes option:first').attr('selected', true);
		
				// Deselect the option
				option.attr('disabled', true);
			}
		});

		// Update tags
		updateAttributeItems();
	});
	
	// Delete attributes
	$('a.delete-attribute').live('click', function(){
		var parent = $(this).parents('div:first');
		
		// Enable option
		$('#sAttributes option[value=' + parent.attr('id').replace( 'dAttributeItem_', '' ) + ']').attr('disabled', false);
		
		// Remove attribute
		parent.remove();
		
		// Restripe
		$('#AttributeList').stripe('attribute');
		
		// Update tags
		updateAttributeItems();
	});
	
	/********** Basic Product Info **********/
	// Prices should only allow positive numbers
	$('#tPrice, #tListPrice').change( function() {
		$(this).val( number_format( $(this).val().replace( /[^0-9.]/g, '' ), 2 ) );
	});
	
	// Changes the product industry on the fly
	$('#sIndustry').change( function() {
		$.post( '/ajax/products/change-industry/', { '_nonce': $('#_ajax_change_industry').val(), 'pid': $('#hProductID').val(), 'iid' : $('#sIndustry option:selected').val() }, function( response ) {
			// Handle any errors
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
	});
	
	// Make the upload image icon work with uploadify
	$('#fUploadImages').uploadify({
		'auto'      	: true,
		'displayData'	: 'speed',
		'buttonImg' 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
		'cancelImg' 	: 'http://sm2.rethinktraining.com/images/icons/cancel.png',
		'fileExt'		: '*.jpg;*.gif;*.png',
		'fileDesc'		: 'Common Image Files',
		'multi'			: true,
		'scriptData'	: { '_nonce' : $('#_ajax_upload_image').val(), 'pid' : $('#hProductID').val() },
		'onSelect'		: function( e, queueID, fileObj ) {
			if( '' == $("#tName").val() || 'Product Name' == $("#tName").val() ) { alert( "You must enter a product name before uploading images" ); $('#fUploadImages').uploadifyCancel(); return false; }
			$('#dUploadedImages').append('<div class="product-image"><span id="sLoading' + queueID + '" class="image-loading"><br/><img src="/images/ajax-loader.gif"/><br/><br/><br/>Loading</span></div>');
		},
		'onComplete'	: function( e, queueID, fileObj, response ) {
			var currentImages = ( document.getElementsByName( 'hProductImages[]' ) ).length;
			var variables = response.split('|'), hiddenProductImageID = 'hProductImage_' + variables[1].replace( /\.[a-zA_Z]{3,4}$/g, '' ).replace( /^[^\/]+\//, '' ).replace( /[\/\.]/g, '' );
			
			//$('#dUploadedImages').append('<div class="product-image"><img src="' + variables[0] + '" width="50" /><a href="' + variables[0].replace( 'thumbnail/', 'large/' ) + '" title="View Image" target="_blank">View</a><br /><a href="javascript:;" class="remove-product-image" title=\'Remove Image\' extra="' + hiddenProductImageID + '">Remove</a></div>');
			$('#sLoading' + queueID ).replaceWith( '<img src="' + variables[0] + '" width="50" /><a href="' + variables[0].replace( 'thumbnail/', 'large/' ) + '" title="View Image" target="_blank">View</a><br /><a href="javascript:;" class="remove-product-image" title=\'Remove Image\' extra="' + hiddenProductImageID + '">Remove</a>' );
			// Added the .replace to hiddenProductIMageID, because it wasn't removing the forward slashes and I feel unsafe messing around too much w/ the regex
			$('#fAddEdit').append('<input type="hidden" class="hidden-value" name="hProductImages[]" id="' + hiddenProductImageID.replace( /[\/\.]/g, '' ) + '" value="' + variables[1].replace( '/', '' ) +  '|' + currentImages + '" />');
			$('#hProductImages-tmp').remove();
			updateImageSequence();
		},
		'sizeLimit'		: 6144000,// (6mb) In bytes? Really?
		'script'    	: '/ajax/products/upload-image/',
		'uploader'  	: '/media/flash/uploadify.swf'
	});
	
	// Remove product images
	$('.remove-product-image').live( 'click', function() {
		// Make sure they want to remove it
		if( !confirm( 'Are you sure you want to remove this image? This cannot be undone.' ) )
			return false;
		
		parent = $(this).parents('.product-image:first');
		
		// Remove hidden value
		// $( '#' + $(this).attr('extra') ).remove();
		var field = $(this).attr('extra');
		$( '#' + field.replace( /[\/\.]/g, '' ) ).remove();
		if( ( $( '.hidden-value' ) ).length <= 0 )
			$( "#fAddEdit" ).after( '<input type="hidden" name="hProductImages" id="hProductImages-tmp" />' );
		
		// Remove image
		parent.remove();
		
		// AJAX remove image
		$.post( '/ajax/products/remove-image/', { '_nonce': $('#_ajax_remove_image').val(), 'pid' : $('#hProductID').val(), 'image' : parent.find('img:first').attr('src') }, function( response ) {
			// Handle any errors
			if( !response['result'] ) {
				alert( response['error'] );
				return;
			}
		}, 'json' );
				
		// Update sequence
		updateImageSequence();
	});

	/********** Page Load  **********/
	// If you refresh the page, make sure you check the product name
	if( 'Product Name' != $('#tName').val() ) {
		// Get slugs
		var productSlug = $('#tProductSlug').val(), categorySlug = $('.product-category:first').text().slug();

		// If no categories selected, put it in the products category
		if( '' == categorySlug )
			var categorySlug = 'products';

		// Assign the slugs
		$('#sProductSlug').text( productSlug );
		$('#tProductSlug').val( productSlug );
		$('#sCategorySlug').text( categorySlug );
		$('#hCategorySlug').val( categorySlug );

		// Show the text
		$('#dProductSlug').slideDown();
	}

	// Set temporary values
	$('#tAddSpecName').attr( 'tmpVal', 'Name' ).val( 'Name' );
	$('#taAddSpecValue').attr( 'tmpVal', 'Value' ).val( 'Value' );
	$('#tSKU').attr( 'tmpVal', 'SKU' );
	$('#tPrice').attr( 'tmpVal', 'Price' );
	$('#tWeight').attr( 'tmpVal', 'Weight' );
	$('#tListPrice').attr( 'tmpVal', 'List Price (Optional)' );
	
	// Make sure its not gray
	if( 'Product Name' != $('#tName').val() )
		$('#tName').css( 'color', '#000000' );

	// Make sure its not gray
	if( 'SKU' != $('#tSKU').val() )
		$('#tSKU').css( 'color', '#000000' );

	// Make sure its not gray
	if( 'Price' != $('#tPrice').val() )
		$('#tPrice').css( 'color', '#000000' );
	
	// Make sure its not gray
	if( 'List Price (Optional)' != $('#tListPrice').val() )
		$('#tListPrice').css( 'color', '#000000' );
	
	// If its an edited product
	if( $('#hProductID').val().length ) {
		// Updates specifications
		updateSpecs();
		
		// Stripe them
		$('#dSpecificationsList').stripe('specification');
		
		// Update the hidden input value
		updateCategories();
		
		// Stripe them
		$('#dCategoryList').stripe('product-category');
		
		// Update tags
		updateTags();
		
		// Stripe them
		$('#dTagList').stripe('product-tag');
		
		// Update attribute items
		updateAttributeItems();
		
		$('.img').each( function() {
			var imageArray = $(this).val().split('|'), industry =$('#hIndustryName').val(), productID = $('#hProductID').val();
			
			for( var i in imageArray ) {
				var imageName = imageArray[i], hiddenProductImageID = 'hProductImage_' + imageName.replace( /\.[a-zA_Z]{3,4}$/, '' ).replace( /^[^\/]+\//, '' );
				
				var imagePath = 'http://' + industry + '.retailcatalog.us/products/' + productID + '/thumbnail/' + imageName;
				$('#dUploadedImages').append('<div class="product-image"><div class="outer" style="height:50px;"><div class="middle"><div class="inner"><img src="' + imagePath + '" /></div></div></div><a href="' + imagePath.replace( 'thumbnail/', 'large/' ) + '" title="View Image" target="_blank">View</a><br /><a href="javascript:;" class="remove-product-image" title="Remove Image" extra="' + hiddenProductImageID + '">Remove</a></div>');
				
				$('#fAddEdit').append('<input type="hidden" class="hidden-value" name="hProductImages[]" id="' + hiddenProductImageID + '" value="' + imageName + '" />');
			}
		});
		
		updateImageSequence();

		$('.product-category').each( function() {
			$('#sProductCategory option[value=' + $(this).attr('id').replace('dCategory', '') + ']').attr( 'disabled', true );
		});
	}
	
	$('#dUploadedImages').sortable({
		update: function() {
			updateImageSequence();
		},
		placeholder: 'image-placeholder'
	});
}

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

/**
 * autocompleteSuccess
 *
 * The success response to the AJAX call for autocompleting
 *
 * @param array request
 * @param array response
 * @return array
 */
function autocompleteSuccess( request, response ) {
	// Find out if they are already cached so we don't have to do another ajax called
	if( request['term'] in cache ) {
		response( $.map( cache[request['term']], function( item ) {
			return {
				'label' : item,
				'value' : item
			}
		}) );
		
		// If it was cached, return now
		return;
	}
	
	// It was not cached, get data
	$.post( '/ajax/products/autocomplete-tags/', { '_nonce' : $('#_ajax_autocomplete_tags').val(), 'term' : request['term'] }, function( data ) {
		// Assign global cache the response data
		cache[request['term']] = data['objects'];
		
		// Return the response data
		response( $.map( data['objects'], function( item ) {
			return {
				'label' : item['value'],
				'value' : item['value']
			}
		}));
	}, 'json' );
}

/**
 * autocompleteRenderItem
 *
 * The function to render each item in the autocomplete list
 *
 * @return ul
 */
function autocompleteRenderItem( ul, item ) {
	return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( '<a href="javascript:;">' + item['label'] + '</a>' )
		.appendTo( ul );
}

/**
 * Function updates the product link when a category changes
 */
function categoryChangeProductLink() {
	// Update product slug
	var categorySlug = $('.product-category:first').text().slug();

	// If no categories selected, put it in the products
		if( '' == categorySlug )
			var categorySlug = 'products';

	// Assign the values
	$('#sCategorySlug').text( categorySlug );
	$('#hCategorySlug').val( categorySlug );
}

jQuery.fn.stripe = function(className) {
	$(this).find('.' + className + ':even').removeClass('even odd').addClass('odd').end().find('.' + className + ':odd').removeClass('even odd').addClass('even');
}

/**
 * Store the categories in a hidden field
 *
 * Also calculates new attributes
 */
function updateCategories() {
	var categories = '';

	// Update the hidden input
	$('.product-category').each( function() {
		if( categories.length )
			categories += '|';
		
		categories += $(this).attr('id').replace( 'dCategory', '');
	});
	
	$('#hCategories').val( categories );
	
	// Load attribute items
	$.post( '/ajax/products/category-attribute-items/', { '_nonce': $('#_ajax_category_attribute_items').val(), 'c' : categories.replace( '|', ',' ) }, function( response ) {
		// Handle any errors
		if( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		var disableAttributes = $('#hAttributes').val().split('|'), aOptions = '<option value="">-- Select an Attribute --</option>', attributeList = array_keys( response['attributes'] );
		
		for( i in attributeList ) {
			var a = attributeList[i];
			
			aOptions += '<optgroup label="' + a + '">';
			
			for( j in response['attributes'][a] ) {
				var ai = response['attributes'][a][j];
				var disabled = ( in_array( ai['attribute_item_id'], disableAttributes ) ) ? ' disabled="disabled"' : '';
				aOptions += '<option value="' + ai['attribute_item_id'] + '"' + disabled + '>' + ai['attribute_item_name'] + '</option>';
			}
			
			aOptions += '</optgroup>';
		}
		
		$('#sAttributes').html( aOptions );
	}, 'json' );
}


/**
 * Store the tags in a hidden field
 */
function updateTags() {
	var tags = '';
	
	// Update the hidden input
	$('.product-tag').each( function() {
		if( tags.length )
			tags += '|';
		
		tags += $(this).attr('id').replace( 'dTag_', '' );
	});
	
	// Reset
	$('#hTags').val( tags );
}

/**
 * Store the specs
 */
function updateSpecs() {
	// Reset
	$('#hSpecs').val('');

	var specList = $("#dSpecificationsList").sortable('toArray');
	
	if( specList.length > 0 ) {
		var i = 1, hiddenValues = '';
		
		// Get the values
		for( var sequence in specList ) {
			hiddenValues += $('#' + specList[sequence] + ' .specification-name').text() + '`' + $('#' + specList[sequence] + ' .specification-value').text()  + '`' + sequence + '|';
		}
		
		$('#hSpecs').val( hiddenValues );
	}
}

/**
 * Store the attribute items in a hidden field
 */
function updateAttributeItems() {
	var attributes = '';

	// Update the hidden input
	$('.attribute').each( function() {
		if( attributes.length )
			attributes += '|';
		
		attributes += $(this).attr('id').replace( 'dAttributeItem_', '' );
	});

	// Assign new values
	$('#hAttributes').val( attributes );
}

/**
 * Store the no-colors sequence
 */
function updateImageSequence() {
	var sequence = 0;
	
	$("#dUploadedImages .product-image").each( function() {
		var hiddenImage = $('#' + $(this).find('.remove-product-image').attr('extra') );
		
		if( typeof hiddenImage != 'undefined' && typeof hiddenImage.val() != 'undefined' ) {
			hiddenImage.val( hiddenImage.val().replace( /([^|"]+)(?:\|[\d]+)?/, '$1' ) + '|' + sequence );
			sequence++;
		}
	});
}

function number_format( number, decimals, dec_point, thousands_sep ) {
    // Formats a number with grouped thousands
    //
    // version: 906.1806
    // discuss at: http://phpjs.org/functions/number_format
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +     input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +     improved by: davook
    // +     improved by: Brett Zamir (http://brett-zamir.me)
    // +     input by: Jay Klehr
    // +     improved by: Brett Zamir (http://brett-zamir.me)
    // +     input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    var n = number, prec = decimals;

    var toFixedFix = function (n,prec) {
        var k = Math.pow(10,prec);
        return (Math.round(n*k)/k).toString();
    };

    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

    var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

    var abs = toFixedFix(Math.abs(n), prec);
    var _, i;

    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;

        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }

    var decPos = s.indexOf(dec);
    if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
        s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
    }
    else if (prec >= 1 && decPos === -1) {
        s += dec+new Array(prec).join(0)+'0';
    }
    return s;
}

function array_keys( input, search_value, argStrict ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: array_keys( {firstname: 'Kevin', surname: 'van Zonneveld'} );
    // *     returns 1: {0: 'firstname', 1: 'surname'}
    
    var tmp_arr = {}, strict = !!argStrict, include = true, cnt = 0;
    var key = '';
    
    for (key in input) {
        include = true;
        if (search_value != undefined) {
            if (strict && input[key] !== search_value){
                include = false;
            } else if (input[key] != search_value){
                include = false;
            }
        }
        
        if (include) {
            tmp_arr[cnt] = key;
            cnt++;
        }
    }
    
    return tmp_arr;
}

function in_array(needle, haystack, argStrict) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: vlado houba
    // +   input by: Billy
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true
    // *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
    // *     returns 2: false
    // *     example 3: in_array(1, ['1', '2', '3']);
    // *     returns 3: true
    // *     example 3: in_array(1, ['1', '2', '3'], false);
    // *     returns 3: true
    // *     example 4: in_array(1, ['1', '2', '3'], true);
    // *     returns 4: false

    var key = '', strict = !!argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}