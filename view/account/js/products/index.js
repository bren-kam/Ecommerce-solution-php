head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', '/resources/js_single/?f=jquery.boxy', function() {
	// Cache
	var cache = { sku : {}, product : {}, brand : {} };

	// Change the text
	$('#sAutoComplete').change( function() {
		var tAutoComplete = $('#tAutoComplete');

		tAutoComplete.attr( 'placeholder', 'Enter ' + $(this).find('option:selected').text() + '...' ).val('').blur();
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
			$.post( '/products/autocomplete-owned/', { '_nonce' : $('#_autocomplete_owned').val(), 'type' : cacheType, 'term' : request['term'] }, function( autocompleteResponse ) {
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
    $('#fSearch').submit( function() {
        loadProducts();
        return false;
    });

	// Make the list sortable
	$("#dProductList").sortable( {
		items		: '.product',
		update: function () {
			$.post('/products/update-sequence/', { _nonce : $('#_update_sequence').val(), s : $('#dProductList').sortable('serialize'), p : $('#hCurrentPage').val(), pp : $('#hPerPage').val() }, ajaxResponse, 'json' );
		},
		scroll: true,
		placeholder: 'product-placeholder'
	});

	// @Fix make PHP
	// Edit the product
	$('#subcontent').on( 'click', '.edit-product', function(){
		var productID = $(this).parent().attr( 'id' ).replace( 'pProductAction' , '' );

		// Trigger a click to switch the screens
		$('#aPricingProductInformation').click();

		// Switch the buttons to show the correct tab
		$('#dEditProduct .screen-selector').removeClass('selected');
		$('#aPricingProductInformation').addClass('selected');

		// Switch the screens
		$('#dEditProduct .screen').hide().removeClass('selected');
		$('#dPricingProductInformation').show().addClass('selected');

		$.post( '/products/get-product-dialog-info/', { _nonce: $('#_get_product_dialog_info').val(), pid: productID }, function( response ) {
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
                    if( 357 == i ) { // Special product option for G-Force
                        divContent += '<div class="row"><div class="cell left">Sizes</div><div class="cell" style="width:93px;">Reg</div><div class="cell" style="width:93px;">Our Price</div><div class="cell" style="width:93px;">Sale</div></div>';

                        for( var j in product_options[i]['list_items'] ) {
                            divContent += '<div class="row">';
                            divContent += '<div class="cell left"><input type="checkbox" class="list-item-cb cb cb-option-' + i + '" id="cbOption_' + i + '_' + j + '" name="product_list_items[' + i + '][' + j + ']" value="true" /> ' + product_options[i]['list_items'][j] + '</div>';
                            divContent += '<div class="cell"><input type="text" class="tb highlight-price" id="tPrice_' + i + '_' + j + '" name="tPrices[' + i + '][' + j +'][reg]" maxlength="10" disabled="disabled" style="width:75px" /></div>';
                            divContent += '<div class="cell"><input type="text" class="tb highlight-price" id="tOurPrice_' + i + '_' + j + '" name="tPrices[' + i + '][' + j +'][our-price]" maxlength="10" disabled="disabled" style="width:75px" /></div>';
                            divContent += '<div class="cell"><input type="text" class="tb highlight-price" id="tSalePrice_' + i + '_' + j + '" name="tPrices[' + i + '][' + j +'][sale]" maxlength="10" disabled="disabled" style="width:75px" /></div>';
                            divContent += '</div>';
                        }

                        divContent += '<div class="row"><div class="cell left"><input type="checkbox" class="cb" name="cbRequired' + i + '" id="cbRequired' + i + '" value="true" /> <strong>Required option?</strong></div></div>';
                    } else { // All normal product optoins
                        divContent += '<div class="row"><div class="cell left">Option</div><div class="cell">Price (Optional)</div></div>';

                        for( var j in product_options[i]['list_items'] ) {
                            divContent += '<div class="row"><div class="cell left"><input type="checkbox" class="list-item-cb cb cb-option-' + i + '" id="cbOption_' + i + '_' + j + '" name="product_list_items[' + i + '][' + j + ']" value="true" /> ' + product_options[i]['list_items'][j] + '</div><div class="cell"><input type="text" class="tb highlight-price" id="tPrice_' + i + '_' + j + '" name="tPrices[' + i + '][' + j +']" maxlength="10" disabled="disabled" /></div></div>';
                        }

                        divContent += '<div class="row"><div class="cell left"><input type="checkbox" class="cb" name="cbRequired' + i + '" id="cbRequired' + i + '" value="true" /> <strong>Required option?</strong></div></div>';
                    }
				} else {
					divContent += '<span class="price">Price:</span> <input type="text" class="tb highlight-price" id="tPrice' + i + '" name="tPrice' + i + '" maxlength="10" />';
				}

				newDivs += '<div class="row hidden" id="dProductOptionRow' + i + '"><div class="cell left product-option-name"><strong>' + product_options[i]['option_name'] + '</strong><a href="#" class="delete-product-option" id="aDeleteProductOption' + i + '" title="Delete Product Option"><img src="/images/icons/x.png" width="15" height="17" alt="Delete Product Option" /></a></div><div class="cell right">' + divContent + '</div></div>';
			}

            var sProductOptions = $('#sProductOptions'), dProductOptionsList = $('#dProductOptionsList');
			sProductOptions.html( newOptions );
			dProductOptionsList.html( newDivs );

			// Add website product options
			if( p['product_options'] )
			for( var i in p['product_options'] ) {
				var option = sProductOptions.find('option[value=' + i + ']'), po_name = option.text();

				// If they entered nothing, do nothing
				if( '' == po_name )
					continue;

				// Slide it down by cloning it and adding it to the end
				var product_row = $('#dProductOptionRow' + i), product_row_div = product_row.clone();
				product_row.remove();

				dProductOptionsList.append( product_row_div ).append( '<input type="hidden" name="product_options[' + i + ']" id="hProductOption' + i + '" value="' + i + '" />' );
                product_row_div.show();

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

                    if( 357 == i ) {
                        if ( null != p['product_options'][i]['list_items'][j] ) {
                            $( '#tPrice_' + i + '_' + j ).val( p['product_options'][i]['list_items'][j]['alt_price'] ).parent().parent().find('input[type=text]').attr( 'disabled', false );
                            $( '#tOurPrice_' + i + '_' + j ).val( p['product_options'][i]['list_items'][j]['alt_price2'] ).parent().parent().find('input[type=text]').attr( 'disabled', false );
                            $( '#tSalePrice_' + i + '_' + j ).val( p['product_options'][i]['list_items'][j]['price'] ).parent().parent().find('input[type=text]').attr( 'disabled', false );
                        }
                    } else {
                        $( '#tPrice_' + i + '_' + j ).val( p['product_options'][i]['list_items'][j] ).attr( 'disabled', false );
                    }
				}
			}

			// Change price colors - make the price change color when it has a negative sign
			$('.highlight-price').each( function() {
				$(this).css( 'color', ( ( -1 != $(this).val().search( /^-/ ) ) ? '#CC2222' : '' ) );
			});

			// Shopping Cart
			$('#tStoreSKU').val( p['store_sku'] );
			$('#tShipsIn').val( p['ships_in'] );

			var shipping_type = ( 'Percentage' == p['additional_shipping_type'] ) ? 'Percentage' : 'FlatRate';
			$( '#rShippingMethod' + shipping_type ).attr( 'checked', true );
            $( '#tShippingPercentage, #tShippingFlatRate' ).val('');
            $( '#tShipping' + shipping_type ).val( p['additional_shipping_amount'] );

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
				coupon_divs += '<div id="dCoupon' + i + '" class="coupon"><span class="coupon-name">' + p['coupons'][i] + '</span><div style="display:inline;float:right"><a href="javascript:;" class="delete-coupon" title="Delete Coupon" id="aDeleteCoupon' + i + '"><img src="/images/icons/x.png" /></a></div></div>';
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
		$('#dCouponList').append( '<div id="dCoupon' + couponID + '" class="coupon"><span class="coupon-name">' + couponName + '</span><div style="display:inline;float:right"><a href="#" class="delete-coupon" title="Delete Coupon" id="aDeleteCoupon' + couponID + '"><img src="/images/icons/x.png" /></a></div></div>' );

		// Reset to default values
		$('#sCoupons').attr( 'selectedIndex', 0 );
		option.attr( 'disabled', true );

		// Update list of coupons
		updateCoupons();
	});

	// Delete Drop Down List Items
	$('#dCouponList').on( 'click', '.delete-coupon', function() {
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
	$('#dProductOptionsList').on( 'click', '.delete-product-option', function() {
		var po_id = $(this).attr('id').replace( 'aDeleteProductOption', '' );

		if( confirm( 'Are you sure you want to delete this product_option?' ) ) {
			$('#dProductOptionRow' + po_id).hide();
			$('#sProductOptions option[value=' + po_id + ']').attr( 'disabled', false );
			$('#hProductOption' + po_id).remove();
		}
	}).on( 'click', '.highlight-price', 'keyup', function() {
        $(this).css( 'color', ( ( -1 != $(this).val().search( /^-/ ) ) ? '#CC2222' : '' ) );
    });;

	// Make the checkboxes enabled/disable the text boxes
	$('#dEditProduct').on( 'click', '.list-item-cb', function() {
		$( '#tPrice' + $(this).attr('id').replace( 'cbOption', '' ) ).parent().parent().find('input[type=text]').attr( 'disabled', !$(this).is(':checked') );
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
	var categoryID = $('#sCategory').val(), tAutoComplete = $('#tAutoComplete'), autoComplete = tAutoComplete.val(), hCurrentPage = $("#hCurrentPage"), sProductsPerPage = $('#sProductsPerPage'), cbOnlyDiscontinued = ( $('#cbOnlyDiscontinued').is(':checked') ) ? '1' : '0';
    var itemsPerPage = parseInt( ( sProductsPerPage.length ) ? sProductsPerPage.val() : 20 );
    var pricing = $('#sPricing').val();

	// If we're not supposed to refresh, do stuff
	if ( parseInt( $("#doNotRefresh").val() ) > 0 )
		var currentPage = ( !( hCurrentPage.val() ) ) ? 1 : hCurrentPage.val();

	// If current page isn't set, set to default
	if( !currentPage )
		currentPage = 1;

	// Enable / disable sortability
	if( categoryID.length && !autoComplete.length ) {
		$("#dProductList").sortable('enable');
	} else {
		$("#dProductList").sortable('disable');
	}

	// Get the products
	$.post( '/products/search/', { cid : categoryID, s : $('#sAutoComplete').val(), v : autoComplete, n : itemsPerPage, p : currentPage, od : cbOnlyDiscontinued, pr : pricing }, function( html ) { //trigger this on success
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