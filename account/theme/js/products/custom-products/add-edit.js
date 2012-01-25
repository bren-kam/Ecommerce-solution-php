/**
 * Products - Add/Edit Page
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Setup cache
	cache = {};
	
	// Make it a date picker
	$('#tPublishDate').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	/********** Categories Link  **********/
	$('a#aAddCategory').click(function() {
		// Make sure they actually put something in
		if ( 0 == $('#sProductCategory').val() ) {
			alert( $(this).attr('error') );
			return;
		}
		
		// Declare options
		var option = $('#sProductCategory option:selected'), categoryID = option.val();

		// It already exists, give an alert
		if ( '' != $('#dCategory' + categoryID).text() )
			return;

		var categoryName = option.html().replace( /&nbsp;/g, '' );

		// Add the category and stripe it
		$('#dCategoryList').append( '<div id="dCategory' + categoryID + '" class="product-category">' + categoryName + '<a href="javascript:;" class="delete-category" id="aDel' + categoryID + '" title=\'Delete Category\'><img class="delete-category" src="/images/icons/x.png" width="15" height="17" alt=\'Delete Category\' /></a></div>' ).stripe('product-category');

		// Remove the state from the drop down
		option.css( 'color', '#929292' ).attr( 'disabled', true );
		
		// Set the value to nothing
		$('#sProductCategory').attr( 'selectedIndex', 0 );

		// Update product link
		categoryChangeProductLink();
		
		// Update the hidden input value
		updateCategories();
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
		if ( $(this).attr('tmpval') == $(this).val() || '' == $(this).val().replace(/\s/g, '') ) {
			$('#dProductSlug, #pProductSlugError').hide();
			return;
		}
		
		// Get slugs
		var productSlug = $(this).val().slug(), categorySlug = $('.product-category:first').text().slug();

		// If no categories selected, put it in the products category
		if ( '' == categorySlug )
			var categorySlug = 'products';
		
		// Create the product ID
		if ( '' == $('#hProductID').val() )
			$.post( '/ajax/products/custom-products/create/', { _nonce : $('#_ajax_create_custom_product').val() }, ajaxResponse, 'json');
		
		var sProductSlug = $('#sProductSlug');
		
		// Makes sure it only changes the name when you first write the title
		if ( '' == sProductSlug.text() ) {
			// Assign the slugs
			sProductSlug.text( productSlug );
			$('#tProductSlug').val( productSlug );
		}

		$('#sCategorySlug').text( categorySlug );
		$('#hCategorySlug').val( categorySlug );

		// Show the text
		$('#dProductSlug').show();

		// Update the hidden input value
		updateCategories();
	});

	// The "Edit" slug button
	$('#aEditProductSlug').click( function() {
		// Hide the slug
		$('#sProductSlug, #aEditProductSlug').hide();

		// Show the other buttons
		$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').show();
	});

	// The "Save" slug button
	$('#aSaveProductSlug').click( function() {
		var productSlug = $('#tProductSlug').val().slug();
		
		// Assign the slugs
		$('#sProductSlug').text( productSlug );
		$('#tProductSlug').val( productSlug );

		// Hide the buttons
		$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').hide();
		
		// Show the slug
		$('#sProductSlug, #aEditProductSlug').show();
	});
	
	// The "Cancel" slug link
	$('#aCancelProductSlug').click( function() {
		// Assign the slugs
		$('#tProductSlug').val( $('#sProductSlug').text() );

		// Hide the buttons
		$('#tProductSlug, #aSaveProductSlug, #aCancelProductSlug').hide();
		
		// Show the slug
		$('#sProductSlug, #aEditProductSlug').show();
	});

	/********** Tags **********/
	$('a#aAddTags').click(function() {
		// Make sure they actually put something in
		if ( '' != $('#tAddTags').val().trim() ) {
			// Declare options
			var tags = $('#tAddTags').val().split(',');
			
			for ( var i in tags ) {
				var t = $.trim( tags[i] ), tSlug = t.slug();
				
				// It already exists, continue
				if ( '' != $.trim( $('#dTag_' + tSlug).text() ) )
					continue;
	
				// Add the slug
				$('#dTagList').append( '<div id="dTag_' + tSlug + '" class="product-tag">' + t + '<a href="javascript:;" class="delete-tag" id="aDel_' + tSlug + '" title="Delete Tag"><img class="delete-tag" src="/images/icons/x.png" width="15" height="17" alt=\'Delete Tag"\' /></a></div>' ).stripe('product-tag');
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
		source: function( request, response ) {
			// Find out if they are already cached so we don't have to do another ajax called
			if ( request['term'] in cache ) {
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
			$.post( '/ajax/products/custom-products/autocomplete-tags/', { _nonce : $('#_ajax_autocomplete_tags').val(), term : request['term'] }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['value'],
						'value' : item['value']
					}
				}));
			}, 'json' );
		}
	});
	
	/********** Specifications  **********/
	$('#aAddSpec').click( function() {
		var tAddSpecName = $('#tAddSpecName'), specName = tAddSpecName.val().trim().replace( /[|`]/g, '' ), taAddSpecValue = $('#taAddSpecValue'), specValue = taAddSpecValue.val().trim().replace( /[|`]/g, '' ), specSlug = ( specName + specValue ).slug();

		// ake sure it's a valid entry
		if ( tAddSpecName.attr('tmpval') == specName || '' == specName || taAddSpecValue.attr('tmpval') == specValue || '' == specValue ) // || '' != $('#dSpec_' + specSlug + ' div:first').text()
			return;

		if ( 'na' == specName.toLowerCase() )
			specName = '';
		
		var values = specValue.split( /\n/ );
		
		for ( var i in values ) {
			specValue = values[i].trim();
			specSlug = ( specName + specValue ).slug();
			
			//  style="display:none;"
			
			// Append it to the list
			$('#dSpecificationsList').append( '<div class="specification" id="dSpec_' + specSlug + '"><div class="specification-name">' + specName + '</div><div class="specification-value">' + specValue + '</div><a href="javascript:;" class="delete-spec" id="aDel_spec_' + specSlug + '" title=\'Delete Specification\'><img src="/images/icons/x.png" width="15" height="17" alt=\'Delete Specification\' /></a></div>');
		}

		// Add stripes
		$('#dSpecificationsList').stripe('specification');

		// Updates specifications
		updateSpecs();
		
		// Reset values
		tAddSpecName.val('').blur();
		taAddSpecValue.val('').blur();
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
		var dAttributeList = $('#dAttributeList');
		
		$('#sAttributes option:selected').each( function() {
			var option = $(this), attributeItemID = option.val();
		
			// Make sure they actually put something in
			if ( '' != attributeItemID ) {
				// Declare variables
				var attributeItemName = option.text();
	
				// Add the slug
				dAttributeList.append( '<div id="dAttributeItem_' + attributeItemID + '" class="attribute"><strong>' + option.parents('optgroup:first').attr('label') + ' &ndash;</strong> ' + attributeItemName + '<a href="javascript:;" class="delete-attribute" id="aDel_' + attributeItemID + '" title="Delete Attribute"><img class="delete-attribute" src="/images/icons/x.png" width="15" height="17" alt="Delete Attribute" /></a></div>' ).stripe('attribute');
				
				// Deselect the option
				option.attr('selected', false).attr('disabled', true);
			}
		});
		
		dAttributeList.stripe('attribute');
		
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
		$.post( '/ajax/products/custom-products/change-industry/', { _nonce: $('#_ajax_change_industry').val(), pid: $('#hProductID').val(), iid : $('#sIndustry').val() }, ajaxResponse, 'json' );
	});
	
	// Make the upload image icon work with uploadify
	$('#fUploadImages').uploadify({
		auto      	: true,
		multi		: true,
		displayData	: 'speed',
		buttonImg 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.jpg;*.gif;*.png',
		fileDesc	: 'Common Image Files', // @Fix need to put this in PHP
		scriptData	: { _nonce : $('#_ajax_upload_image').val(), pid : $('#hProductID').val(), wid: $('#hWebsiteID').val() },
		onSelect	: function( e, queueID, fileObj ) {
			$('#dUploadedImages').append('<div class="product-image loading"><span class="image-loading"><br /><img src="/images/ajax-loader.gif" width="16" height="16" /></span></div>');
		},
		onComplete	: function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		sizeLimit		: 6144000,// (6mb) In bytes? Really?
		script    	: '/ajax/products/custom-products/upload-image/',
		uploader  	: '/media/flash/uploadify.swf'
	});

	var tName = $('#tName');

    // Remove product images
	$('.remove-product-image').live( 'click', function() {
		// Make sure they want to remove it
		if ( !confirm( 'Are you sure you want to remove this image? This cannot be undone.' ) )
			return false;

		parent = $(this).parents('.product-image:first');

		// Remove hidden value
		// $( '#' + $(this).attr('extra') ).remove();
		var field = $(this).attr('extra');
		$( '#' + field.replace( /[\/\.]/g, '' ) ).remove();
		if ( ( $( '.hidden-value' ) ).length <= 0 )
			$( "#fAddEdit" ).after( '<input type="hidden" name="hProductImages" id="hProductImages-tmp" />' );

		// Remove image
		parent.remove();

		// AJAX remove image
		$.post( '/ajax/products/remove-image/', { _nonce: $('#_ajax_remove_image').val(), pid : $('#hProductID').val(), i : parent.find('img:first').attr('src') }, ajaxResponse, 'json' );

		// Update sequence
		updateImageSequence();
	});

	/********** Page Load  **********/
	// If you refresh the page, make sure you check the product name
	if ( tName.attr('tmpval') != tName.val() ) {
		// Get slugs
		var productSlug = $('#tProductSlug').val(), categorySlug = $('.product-category:first').text().slug();

		// If no categories selected, put it in the products category
		if ( '' == categorySlug )
			var categorySlug = 'products';
		
		// Assign the slugs
		$('#sProductSlug').text( productSlug );
		$('#tProductSlug').val( productSlug );
		$('#sCategorySlug').text( categorySlug );
		$('#hCategorySlug').val( categorySlug );

		// Show the text
		$('#dProductSlug').show();
	}
	
	// If its an edited product
	if ( $('#hProductID').val().length ) {
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
		
		// Stripe them
		$('#dAttributeList').stripe('attribute');
		
		$('.img').each( function() {
			var imageArray = $(this).val().split('|'), industry =$('#hIndustryName').val(), productID = $('#hProductID').val();
			
			for ( var i in imageArray ) {
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
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

jQuery.fn.stripe = function(className) {
	$(this).find('.' + className + ':even').removeClass('even odd').addClass('odd').end().find('.' + className + ':odd').removeClass('even odd').addClass('even');
}

/**
 * Function updates the product link when a category changes
 */
function categoryChangeProductLink() {
	// Update product slug
	var categorySlug = $('.product-category:first').text().slug();

	// If no categories selected, put it in the products
		if ( '' == categorySlug )
			var categorySlug = 'products';

	// Assign the values
	$('#sCategorySlug').text( categorySlug );
	$('#hCategorySlug').val( categorySlug );
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
		if ( categories.length )
			categories += '|';
		
		categories += $(this).attr('id').replace( 'dCategory', '');
	});
	
	$('#hCategories').val( categories );
	
	// Load attribute items
	$.post( '/ajax/products/custom-products/category-attribute-items/', { _nonce: $('#_ajax_category_attribute_items').val(), da : $('#hAttributes').val().split('|'), c : categories.replace( '|', ',' ) }, ajaxResponse, 'json' );
}


/**
 * Store the tags in a hidden field
 */
function updateTags() {
	var tags = '';
	
	// Update the hidden input
	$('.product-tag').each( function() {
		if ( tags.length )
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
	
	if ( specList.length > 0 ) {
		var i = 1, hiddenValues = '';
		
		// Get the values
		for ( var sequence in specList ) {
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
		if ( attributes.length )
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
		
		if ( typeof hiddenImage != 'undefined' && typeof hiddenImage.val() != 'undefined' ) {
			hiddenImage.val( hiddenImage.val().replace( /([^|"]+)(?:\|[\d]+)?/, '$1' ) + '|' + sequence );
			sequence++;
		}
	});
	
	return this;
}

$.fn.updateImageSequence = updateImageSequence;

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