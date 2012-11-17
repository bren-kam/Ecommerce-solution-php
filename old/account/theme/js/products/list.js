head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=jquery.boxy', function() {
	// Cache
	var cache = { sku : {}, product : {}, brand : {} };
	
	// Change the text
	$('#sAutoComplete').change( function() {
		var tAutoComplete = $('#tAutoComplete');
		
		tAutoComplete.attr( 'tmpval', tAutoComplete.attr('tmpval').replace( /\s([\w\s]+).../, ' ' + $(this).find('option:selected').text() + '...' ) ).val('').blur();
	});
	
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		source: function( request, response ) {
			// Get the cache type
			var cacheType = $('#sAutoComplete').val();
			
			// Find out if they are already cached so we don't have to do another ajax called
			if( request['term'] in cache[cacheType] ) {
				response( $.map( cache[cacheType][request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}) );
				
				// If it was cached, return now
				return;
			}
			
			// It was not cached, get data
			$.post( '/ajax/products/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'], 'owned' : 1 }, function( autocompleteResponse ) {
				// Assign global cache the response data
				cache[cacheType][request['term']] = autocompleteResponse['suggestions'];
				
				// Return the response data
				response( $.map( autocompleteResponse['suggestions'], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['name']
					}
				}));
			}, 'json' );
		}
	});
	
	// Create the search functionality
	$('#aSearch').click( loadProducts );
	
	// Make the list sortable
	$("#dProductList").sortable( {
		items		: '.product',
		update: function () {
			$.post('/ajax/products/update-website-product-sequence/', { _nonce : $('#_ajax_update_website_product_sequence').val(), s : $('#dProductList').sortable('serialize'), p : $('#hCurrentPage').val(), pp : $('#hPerPage').val() }, ajaxResponse, 'json' );
		},
		scroll: true,
		placeholder: 'product-placeholder'
	});
	
	// @Fix make PHP
	// Edit the product
	$('.edit-product').live( 'click' , function(){
		var productID = $(this).parent().attr( 'id' ).replace( 'pProductAction' , '' );

		// Trigger a click to switch the screens
		$('#aPricingProductInformation').click();
		
		// Switch the buttons to show the correct tab
		$('#dEditProduct .screen-selector').removeClass('selected');
		$('#aPricingProductInformation').addClass('selected');
		
		// Switch the screens
		$('#dEditProduct .screen').hide().removeClass('selected');
		$('#dPricingProductInformation').show().addClass('selected');
		
		$.post( '/ajax/products/get-product-dialog-info/', { _nonce: $('#_ajax_get_product_dialog_info').val(), pid: productID }, function( response ) {
			var p = response['product'];
			var product_options = response['product_options'];
				
			// Assign the product id
			$('#hProductID').val( p['product_id'] );
			
			// Pricing Information
			$('#tAlternatePrice').val( p['alternate_price'] );
			$('#tAlternatePriceName').val( p['alternate_price_name'] );
			$('#tPrice').val( p['price'] );
			$('#tSalePrice').val( p['sale_price'] );
			$('#cbOnSale').attr( 'checked', 1 == p['on_sale'] );
			$('#tPriceNote').val( p['price_note'] );
			
			// Product Information
			$('#taProductNote').val( p['product_note'] );
			$('#tWarrantyLength').val( p['warranty_length'] );
			$('#tWarrantyLength').val( p['warranty_length'] );
			$('#tInventory').val( p['inventory'] );
			$('#cbDisplayInventory').attr( 'checked', 1 == p['display_inventory'] );
			$('#sStatus option[value=' + ( p['status'] * 1 ) + ']').attr( 'selected', true );
			$('#tMetaTitle').val( p['meta_title'] );
			$('#tMetaDescription').val( p['meta_description'] );
			$('#tMetaKeywords').val( p['meta_keywords'] );
			
			// Show the dialog as the first screen is ready
			new Boxy( $('#dEditProduct'), {
				title : 'Edit Product'
			});
			
			// Product Options
			var newOptions = '<option value="">-- Select a Product Option --</option>', newDivs = '', divContent = '';
			
			for( var i in product_options ) {
				newOptions += '<option value="' + i + '">' + product_options[i]['option_name'] + '</option>';
				divContent = '';
				
				if( product_options[i]['option_type'] == 'select' ) {
					divContent += '<div class="row"><div class="cell left">Option</div><div class="cell">Price (Optional)</div></div>';
					
					for( var j in product_options[i]['list_items'] ) {
						divContent += '<div class="row"><div class="cell left"><input type="checkbox" class="list-item-cb cb cb-option-' + i + '" id="cbOption_' + i + '_' + j + '" name="product_list_items[' + i + '][' + j + ']" value="true" /> ' + product_options[i]['list_items'][j] + '</div><div class="cell"><input type="text" class="tb highlight-price" id="tPrice_' + i + '_' + j + '" name="tPrices[' + i + '][' + j +']" maxlength="10" disabled="disabled" /></div></div>';
					}
					
					divContent += '<div class="row"><div class="cell left"><input type="checkbox" class="cb" name="cbRequired' + i + '" id="cbRequired' + i + '" value="true" /> <strong>Required option?</strong></div></div>';
				} else {
					divContent += '<span class="price">Price:</span> <input type="text" class="tb highlight-price" id="tPrice' + i + '" name="tPrice' + i + '" maxlength="10" />';
				}
				
				newDivs += '<div class="row hidden" id="dProductOptionRow' + i + '"><div class="cell left product-option-name"><strong>' + product_options[i]['option_name'] + '</strong><a href="javascript:;" class="delete-product-option" id="aDeleteProductOption' + i + '" title="Delete Product Option"><img src="/images/icons/x.png" width="15" height="17" alt="Delete Product Option" /></a></div><div class="cell right">' + divContent + '</div></div>';
			}
			
			$('#sProductOptions').html( newOptions );
			$('#dProductOptionsList').html( newDivs );
			
			// Add website product options
			if( p['product_options'] )
			for( var i in p['product_options'] ) {
				var option = $('#sProductOptions option[value=' + i + ']'), po_name = option.text();
				
				// If they entered nothing, do nothing
				if( '' == po_name )
					continue;
				
				// Slide it down by cloning it and adding it to the end
				var product_row = $('#dProductOptionRow' + i), product_row_div = product_row.clone();
				product_row.remove();
				
				$('#dProductOptionsList').append( product_row_div ).append( '<input type="hidden" name="product_options[' + i + ']" id="hProductOption' + i + '" value="' + i + '" />' );
				$( '#dProductOptionRow' + i ).slideDown();
				
				// Check the required checkbox if its required
				$( '#cbRequired' + i ).attr( 'checked', 1 == p['product_options'][i]['required'] );
				
				option.attr( 'disabled', true );
				
				// Set the price
				var input_price = $('#tPrice' + i);
				if( typeof( input_price.attr('id') ) != 'undefined' )
					input_price.val( p['product_options'][i]['price'] );
				
				if( typeof( p['product_options'][i]['list_items'] ) != 'undefined' )
				for( var j in p['product_options'][i]['list_items'] ) {
					$( '#cbOption_' + i + '_' + j ).attr( 'checked', true );
					$( '#tPrice_' + i + '_' + j ).val( p['product_options'][i]['list_items'][j] ).attr( 'disabled', false );
				}
			}
			
			// Change price colors - make the price change color when it has a negative sign
			$('.highlight-price').each( function() {
				$(this).css( 'color', ( ( -1 != $(this).val().search( /^-/ ) ) ? '#CC2222' : '' ) );
			});
			
			// Shopping Cart
			$('#tStoreSKU').val( p['store_sku'] );
			$('#tShipsIn').val( p['ships_in'] );
			
			var shipping_type = ( 'Flat Rate' == p['additional_shipping_type'] ) ? 'FlatRate' : 'Percentage';
			$( '#rShippingMethod' + shipping_type ).attr( 'checked', true );
			$( '#tShipping' + shipping_type ).val( p['additional_shipping_amount'] ).css( 'visibility', 'visible' );

            if ( '' == p['additional_shipping_type'] ) {
                $( '#tShippingPercentage, #tShippingFlatRate' ).val('').css( 'visibility', 'hidden' );
            } else {
                $( '#tShipping' + ( 'FlatRate' == shipping_type ) ? 'Percentage' : 'FlatRate' ).val('').css( 'visibility', 'hidden' );
            }

			var protection_type =  ( 'Flat Rate' == p['protection_type'] ) ? 'FlatRate' : 'Percentage';
			$( '#rProtectionMethod' + protection_type ).attr( 'checked', true );
			$( '#tProtection' + protection_type ).val( p['protection_amount'] ).css( 'visibility', 'visible' );

            if ( '' == p['protection_type'] ) {
                $( '#tProtectionPercentage, #tProtectionFlatRate' ).val('').css( 'visibility', 'hidden' );
            } else {
                $( '#tProtection' + ( 'FlatRate' == protection_type ) ? 'Percentage' : 'FlatRate' ).val('').css( 'visibility', 'hidden' );
            }

			$('#tWholesalePrice').val( p['wholesale_price'] );
			$('#tWeight').val( p['weight'] );
			
			/** Coupon section **/
			var coupon_list = $('#dCouponList'), coupons = $('#sCoupons'), coupon_divs = '';
			
			// Remove all coupons
			coupon_list.empty();
			coupons.find('option').attr( 'disabled', false );
			coupons.attr( 'selectedIndex', 0 );

			for( var i in p['coupons'] ) {
				// Create all the divs
				coupon_divs += '<div id="dCoupon' + i + '" class="coupon"><span class="coupon-name">' + p['coupons'][i] + '</span><div style="display:inline;float:right"><a href="javascript:;" class="delete-coupon" title=\'Delete "' + p['coupons'][i] + '" List Item\' id="aDeleteCoupon' + i + '"><img src="/media/images/icons/x.png" /></a></div></div>';
				coupons.find('option[value=' + i + ']').attr( 'disabled', true );
			}
			
			// Add on the new divs
			coupon_list.append( coupon_divs );
			
			// Update list of coupons
			updateCoupons();
		}, 'json' );

        // Change the Master Catalog
        var aMasterCatalog = $('#aMasterCatalog');
        aMasterCatalog.attr( 'href', aMasterCatalog.attr('rel') + productID );
	});
	
	// The Shopping Cart Shipping radio buttons
	$('#dEditProduct .rb-shipping').change( function() {
		if( $(this).attr('checked') ) {
			var value = $(this).val();
			$('.additional-shipping.selected:first').css( 'visibility', 'hidden' ).removeClass('selected');

			switch( value ) {
				case 'Flat Rate':
					$('#tShippingFlatRate').parent().css( 'visibility', 'visible' ).addClass('selected');
				break;
				
				case 'Percentage':
					$('#tShippingPercentage').parent().css( 'visibility', 'visible' ).addClass('selected');
				break;
				
				default:break;
			}
		}
	});
	
	// The Shopping Cart Protection radio buttons
	$('#dEditProduct .rb-protection').change( function() {
		if( $(this).attr('checked') ) {
			var value = $(this).val();
			$('.protection.selected:first').css( 'visibility', 'hidden' ).removeClass('selected');
			
			switch( value ) {
				case 'Flat Rate':
					$('#tProtectionFlatRate').parent().css( 'visibility', 'visible' ).addClass('selected');
				break;
				
				case 'Percentage':
					$('#tProtectionPercentage').parent().css( 'visibility', 'visible' ).addClass('selected');
				break;
				
				default:break;
			}
		}
	});
	
	// Make the tabs in Edit Product dialog work
	$('#dEditProduct .screen-selector').click( function() {
		if( $(this).hasClass('selected') || 'aMasterCatalog' == $(this).attr('id') )
			return;
		
		var screen_selector = $(this).attr('id').replace( /^a/, '' );
		
		// Switch the buttons to show the correct tab
		$('#dEditProduct .screen-selector').removeClass('selected');
		$(this).addClass('selected');
		
		// Fade out the selected screen
		$('#dEditProduct .screen.selected:first').fadeOut('fast').removeClass('selected');
		
		// Fade in the new screen
		setTimeout( function() {
			$( '#d' + screen_selector ).fadeIn().addClass('selected');
		}, 250 );
	});
	
	// The 'Add Coupon' link in the Edit Product dialog box
	$('#aAddCoupon').click( function() {
		var option = $('#sCoupons option:selected'), couponID = option.val(), couponName = option.text();
		
		// If they entered nothing, do nothing
		if( '' == couponID )
			return;
		
		// Append new div
		$('#dCouponList').append( '<div id="dCoupon' + couponID + '" class="coupon"><span class="coupon-name">' + couponName + '</span><div style="display:inline;float:right"><a href="javascript:;" class="delete-coupon" title="Delete Coupon" id="aDeleteCoupon' + couponID + '"><img src="/images/icons/x.png" /></a></div></div>' );
		
		// Reset to default values
		$('#sCoupons').attr( 'selectedIndex', 0 );
		option.attr( 'disabled', true );
		
		// Update list of coupons
		updateCoupons();
	});
	
	// Delete Drop Down List Items
	$('.delete-coupon').live( 'click', function() {
		var couponID = $(this).attr('id').replace( 'aDeleteCoupon', '' );
		
		if( confirm( 'Are you sure you want to delete this coupon?' ) ) {
			$('#dCoupon' + couponID).remove();
			$('#sCoupons option[value=' + couponID + ']').attr( 'disabled', false );
			updateCoupons();
		}
	});
	
	// The 'Add Coupon' link in the Edit Product dialog box
	$('#aAddProductOption').click( function() {
		var option = $('#sProductOptions option:selected'), po_id = option.val(), po_name = option.text();
		
		// If they entered nothing, do nothing
		if( '' == po_id )
			return;
		
		// @Fix Why are we cloning?
		// Slide it down by cloning it and adding it to the end
		var product_row = $('#dProductOptionRow' + po_id), product_row_div = product_row.clone();
		
		product_row.remove();
		
		$('#dProductOptionsList').append( product_row_div ).append( '<input type="hidden" name="product_options[' + po_id + ']" id="hProductOption' + po_id + '" value="' + po_id + '" />' );
		$('#dProductOptionRow' + po_id).slideDown();
		
		// Reset to default values
		$('#sProductOptions').attr( 'selectedIndex', 0 );
		option.attr( 'disabled', true );
	});

	
	// Delete Drop Down List Items
	$('.delete-product-option').live( 'click', function() {
		var po_id = $(this).attr('id').replace( 'aDeleteProductOption', '' );
		
		if( confirm( 'Are you sure you want to delete this product_option?' ) ) {
			$('#dProductOptionRow' + po_id).hide();
			$('#sProductOptions option[value=' + po_id + ']').attr( 'disabled', false );
			$('#hProductOption' + po_id).remove();
		}
	});
	
	// Make the checkboxes enabled/disable the text boxes
	$('#dEditProduct .list-item-cb').live( 'click', function() {
		$( '#tPrice' + $(this).attr('id').replace( 'cbOption', '' ) ).attr( 'disabled', !$(this).attr('checked') );
	});
	
	// Make the price change color when it has a negative sign
	$('.highlight-price').live( 'keyup', function() {
		$(this).css( 'color', ( ( -1 != $(this).val().search( /^-/ ) ) ? '#CC2222' : '' ) );
	});
	
	var dialog_height = $('#dDialogHeight').val();
	if( 500 == dialog_height )
		$('.screen').css( 'height', '340px' ); // Set screen heights

    // Submit a form
    $('#bSaveProduct').click( function() {
        $('#fEditProduct').submit();
    });
});

$.fn.lowerProductCount = function() {
	//Count down the number of products
	var dProductCount = $('#dProductCount'), productCountText = dProductCount.text(), productCount = productCountText.replace( /[^0-9]+([0-9]+).+/, '$1' );
	dProductCount.text( productCountText.replace( /([0-9]+)\//, productCount - 1 + '/' ) );
}

/**
 * Updates a hidden field with the coupons
 */
function updateCoupons() {
	// Start collecting coupon ids
	var couponIDs = '';
	
	$('#dCouponList .coupon').each( function() {
		if( couponIDs.length )
			couponIDs += '|';
		
		couponIDs += $(this).attr('id').replace( 'dCoupon', '' );
	});
	
	// Update hidden element
	$('#hCoupons').val( couponIDs );
}

/*
 * Load Products related to specific category slug
 */
function loadProducts() {
	// Define variables
	var categoryID = $('#sCategory').val(), tAutoComplete = $('#tAutoComplete'), autoComplete = tAutoComplete.val(), hCurrentPage = $("#hCurrentPage"), sProductsPerPage = $('#sProductsPerPage'), cbOnlyDiscontinued = ( $('#cbOnlyDiscontinued').attr('checked') ) ? '1' : '0';
    var itemsPerPage = parseInt( ( sProductsPerPage.length ) ? sProductsPerPage.val() : 20 );
	
	// If we're not supposed to refresh, do stuff
	if ( parseInt( $("#doNotRefresh").val() ) > 0 )
		var currentPage = ( !( hCurrentPage.val() ) ) ? 1 : hCurrentPage.val();
	
	// If current page isn't set, set to default
	if( !currentPage )
		currentPage = 1;
	
	// Enable / disable sortability
	if( categoryID.length && !autoComplete.length || autoComplete == tAutoComplete.attr('tmpval') ) {
		$("#dProductList").sortable('enable');
	} else {
		$("#dProductList").sortable('disable');
	}
	
	// Get the products
	$.post( '/ajax/products/get-products/', { _nonce : $('#_ajax_get_products').val(), cid : categoryID, s : $('#sAutoComplete').val(), v : autoComplete, n : itemsPerPage, p : currentPage, od : cbOnlyDiscontinued }, function( html ) { //trigger this on success
		// Load the content
		$('#dProductList').html( html ).sparrow();
		
		// Fix their images ASAP!
		adjustImageSizes();
		
		// @Fix is this needed? Does it make sense -- setting the value of a droppdown?
		// Purposefully grabbing again -- may not have existed before
		$('#sProductsPerPage').val( itemsPerPage );
		
		// Give it the functions
		$("#sProductsPerPage").change( loadProducts );
		$("#tListRequests_previous").click( previousPage );
		$("#tListRequests_next").click( nextPage );
	}, 'html' );
}

/**
 * Previous Page Function
 */
function previousPage() {
	var hCurrentPage = $("#hCurrentPage"), currentPage = parseInt( hCurrentPage.val() );
	
	if ( currentPage > 1 ) {
		hCurrentPage.val( currentPage - 1 );
		$("#doNotRefresh").val('1');
		loadProducts();
	}
}

/**
 * Next Page function
 */
function nextPage() {
	var hCurrentPage = $("#hCurrentPage"), currentPage = parseInt( hCurrentPage.val() ), itemsPerPage = $('#sProductsPerPage').val();
	
	if ( currentPage * itemsPerPage < parseInt( $("#hWebsiteProductsCount").val() ) ) {
		hCurrentPage.val( currentPage + 1 );
		$("#doNotRefresh").val('1');
		loadProducts();
	}
}

/**
 * Auto adjust the image sizes and make them visible
 */
function adjustImageSizes(){
	var productImages = $( '#dProductList img.product-image' ), productImagesLength = productImages.length, totalImages = 0;
	
	// Cycle through the images
	productImages.error( function() {
        totalImages++;

        // On the last image, adjust the height
        if( totalImages == productImagesLength )
            adjustProductBoxesHeight();
    }).load(function() {
        var height = $(this).height(), width = $(this).width(), newWidth = 150, newHeight = Math.round( ( height * newWidth ) / width );

        // Adjust Heights
        if( newHeight > 100 ) {
            newHeight = 100;
            newWidth = Math.round( ( width * newHeight ) / height );
        }

        $( '#sLoadingMsg' + $(this).attr('id').replace( 'pImage' , '' ) ).hide();
        $(this).width( newWidth ).height( newHeight ).show();

        totalImages++;

        // On the last image, adjust the height
        if( totalImages == productImagesLength )
            adjustProductBoxesHeight();
    });
}

/**
 * Update the padding of boxes to fix the layout problem
 */
function adjustProductBoxesHeight() {
	var maxHeight = 0;
	
	// Find out what the height is of the largest product and adjust it
	$('#dProductList div.product').each( function(){
		newHeight = $(this).height();
		maxHeight = ( maxHeight < newHeight ) ? newHeight : maxHeight;
	}).height( maxHeight );
}