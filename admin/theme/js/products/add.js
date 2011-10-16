head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
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
			$.post( '/ajax/products/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'] }, function( autocompleteResponse ) {
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
	$('#aSearch').click( function() {
		$('#tAddProducts').dataTable().fnDraw();
	});
	
	// @Fix skip sparrow
	setTimeout( function() {
		// Add the datatable
		$('#tAddProducts').addClass('dt').dataTable({
			aaSorting: [[0,'asc']],
			bAutoWidth: false,
			bProcessing : 1,
			bServerSide : 1,
			iDisplayLength : 20,
			sAjaxSource : '/ajax/products/list-add-products/',
			sDom : '<"top"lr>t<"bottom"pi>',
			oLanguage: {
					sLengthMenu: 'Rows: <select><option value="100">100</option><option value="300">300</option><option value="500">500</option></select>',
					sInfo: "_START_ - _END_ of _TOTAL_"
			},
			fnDrawCallback : function() {
				// Run Sparrow on new content and add the class last to the last row
				sparrow( $(this).find('tr:last').addClass('last').end() );
			},
			fnServerData: function ( sSource, aoData, fnCallback ) {
				aoData.push({ name : 's', value : $('#tAutoComplete').val() });
				aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });
				aoData.push({ name : 'c', value : $('#sCategory').val() });
				
				// Get the data
				$.ajax({
					url: sSource,
					dataType: 'json',
					data: aoData,
					success: fnCallback
				});
			},
		});
	}, 500 );
	
	// Add Request to Request List
	$('#aAddRequest').click( function() {
		var sRequestBrand = $('#sRequestBrand'), tRequestSKU = $('#tRequestSKU'), tRequestSKUValue = tRequestSKU.val(), tCollection = $('#tCollection');
		
		// Validation
		if ( '' == sRequestBrand.val() ) {
			alert( sRequestBrand.attr('error') );
			return;
		}
		
		if (  '' == tRequestSKUValue ) {
			alert( tRequestSKU.attr('error') );
			return
		}
		
		if ( '' == tCollection.val() ) {
			alert( tCollection.attr('error') );
			return;
		}
		
		// Check if the product already exists
		$.post( '/ajax/products/sku-exists/', { _nonce: $('#_ajax_sku_exists').val(), sku : tRequestSKUValue }, function( response ) {
			// Handle any error
			if( !response['success'] ) {
				alert( response['error'] );
				return;
			}
			
			if( response['product'] ) {
				if( confirm( response['confirm'] ) ) {
					addProductToList( response['product']['product_id'], response['product']['name'] );
					$('#tRequestSKU, #tCollection').val('');
				}
			} else {
				// Add the request
				addProductRequest();
			}
		}, 'json' );
	});
	
	// Delete Request Items from list
	$( '.delete-request' ).live( 'click' , function(){
		$(this).parent();
	});
	
	// Add products to the right hand side bar and create hidden elements
	$('.add-product').live( 'click', function() {
		addProductToList( $(this).attr('id').replace( 'aAddProduct', '' ), $(this).attr('name') );
	});
	
	
	// The delete product functionality
	$('.delete-product').live( 'click', function() {
		removeProduct( $(this).attr('id').replace( 'aDel', '' ) );
		
		// Decrease number of count
		changeCount(-1);
	});
});

// Remove a product from the list and hidden element
function removeProduct( productID ) {
	$('#dProduct' + productID + ', #hProduct' + productID).remove();
	$('#dProductsList').stripe('added-product');
}

/**
 * Add product to Product List
 */
function addProductToList( productID, productName ) {
	var currentProductCount = parseInt( $('#sProductCount').text().replace( /[^0-9]/, '' ) ), allowedProducts = parseInt( $('#sAllowedProducts').text().replace( /[^0-9]/, '' ) );
	
	// Check if they ran out of products
	if( currentProductCount >= allowedProducts ) {
		alert( $('#pAdditionalProducts').text() );
		return;
	}
	
	// Needs to be done in PHP
	$('#dProductsList').append( '<div id="dProduct' + productID + '" class="added-product">' + productName + '<a href="javascript:;" class="delete-product" id="aDel' + productID + '" title="Delete Product"><img src="/images/icons/x.png" width="15" height="17" alt="Delete Product" /></a></div>' ).stripe('added-product');
	$('#fAddProducts').append( '<input type="hidden" name="products[]" class="hidden-product" id="hProduct' + productID + '" value="' + productID + '" />' );
	
	//Increase number of count
	changeCount(1);
}

/**
 * Add Product request 
 */
function addProductRequest(){
	// Verify it doesn't contain pipes (|)
	var brand = $('#sRequestBrand option:selected').text().replace( /|/, '' ), sku = $('#tRequestSKU').val().replace( /|/, '' ), collection = $('#tCollection').val().replace( /|/, '' );
	
	var requestItem = '<div class="dRequestItem">' + brand + ' - ' + sku + ' - ' + collection;
	requestItem += '<a href="javascript:;" class="delete-request" title="Delete"><img src="/images/icons/x.png" width="15" height="17" alt="Delete" /></a>'; // PHP needs to do this
	requestItem += '<input type="hidden" name="requests[]" value="' + brand + '|' + sku + '|' + collection + '" /></div>';
	
	$('#dRequestList').append(requestItem).stripe('dRequestItem');
	
	$('#tRequestSKU, #tCollection').val('');
}

jQuery.fn.stripe = function(className) {
	$(this).find('.' + className + ':even').removeClass('even odd').addClass('odd').end().find('.' + className + ':odd').removeClass('even odd').addClass('even');
}

// Change the count
function changeCount( number ) {
	// Set variables
	var currentProductCount = parseInt( $('#sProductCount').text().replace( /[^0-9]/, '' ) ), allowedProducts = parseInt( $('#sAllowedProducts').text().replace( /[^0-9]/, '' ) ), newProductCount = currentProductCount + number;
	
	$('#sProductCount').text( number_format( newProductCount ) );
	
	if( newProductCount >= allowedProducts ) {
		$('#pAdditionalProducts').show();
	} else {
		$('#pAdditionalProducts').hide();
	}
	
	updateProductList();
}

// Fadeout the message or put it back, depending if there are any products
function updateProductList() {
	var addProductCount = $('#fAddProducts input.hidden-product').length;
	
	if( addProductCount > 0 ) {
		$('#pNewCount').find('span:first').text( addProductCount ).end().show();
		$('#bAddProducts').show();
		$('#pNoProducts').hide();
	} else {
		$('#pNewCount, #bAddProducts').hide();
		$('#pNoProducts').show();
	}
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
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
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'

    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}