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
	
	// Make it have a temporary value
	$('#tTitle').tmpVal( '#929292', '#000000' );
	
	// Configure WYSIWYG editor
	$('#taDescription').ckeditor({
		bodyId : 'ckEditorWindow',
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
	
	// Make sure its not gray
	if ( 'Template Title' != $('#tTitle').val() )
		$('#tTitle').css( 'color', '#000000' );

	// If its an edited product
	if ( $('#hCraigslistID').val().length ) {
		// Update the categories dropdown (sUpdateCategory)
	}
	
	// Make sure the category selector is set to the proper category
	if ( $("#hCategory").val().length ){
		var Category = parseInt( $('#hCategory').val() );
		var targetIndex = $('#sCraigslistCategory').children('option[value='+ Category +']');		
		targetIndex = targetIndex[0].index;
		$('#sCraigslistCategory').attr('selectedIndex', targetIndex);
	}
	
	$('#aRefreshPreview').live( 'click', function(){
		var categoryID = $("#sCraigslistCategory").val();
		var editorHTML = CKEDITOR.instances.taDescription.getData();
		var titleHTML = $("#tTitle").val();
		var store_logo = "<p align='center'><a href='http://199.47.222.27/' title='Generic Store' target='_blank'><img src='http://199.47.222.27/custom/uploads/images/logo.jpg' /></a></p>";
		
		if ( !categoryID ) {
			alert( 'Please select a category.' );
			return;
		}
		
		var currentCategory = $("#dPreviewCategoryID").val();
		var productID = $("#dPreviewProductID").val();
				
		if ( categoryID != currentCategory ) productID = '';
		
		$.post( '/ajax/craigslist/get-preview-data/', { '_nonce': $('#_ajax_preview_craigslist').val(), 'cid' : categoryID, 'pid' : productID }, function( response ) {
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			} else {
				titleHTML = "<h2><b>" + titleHTML + "</b></h2><hr/>Date: 2011-4-25, 11:35 CST<br/>Reply to: <a href='mailto:test@test.com'>sale-rgf3-2123432@craigslist.org</a><hr/>";
				editorHTML = titleHTML + editorHTML;
				$("#dPreviewProductID").val( response['result']['product_id'] );
				$("#dPreviewCategoryID").val( categoryID ); //response['result']['category_id'] );
				editorHTML = editorHTML.replace( '[Product Name]', response['result']['product_name'] );
				editorHTML = editorHTML.replace( '[Store Name]', "Generic Furniture Store" );
				editorHTML = editorHTML.replace( '[Store Logo]', store_logo );
				editorHTML = editorHTML.replace( '[Category]', response['result']['category'] );
				editorHTML = editorHTML.replace( '[Brand]', response['result']['brand'] );
				editorHTML = editorHTML.replace( '[Product Description]', response['result']['product_description'] );
				editorHTML = editorHTML.replace( '[Product Specs]', response['result']['product_specs'] );
				editorHTML = editorHTML.replace( '[SKU]', response['result']['sku'] );
				
				var photoIndex = 0;
				var photoData = new Object();
				var photoURL = '';
				while(1){					
					if ( editorHTML.indexOf('[Photo]') < 0 ) break;
					else {
						photoData = response['result']['photos'][photoIndex];
						photoURL = "<img src='http://" + photoData['industry'] + ".retailcatalog.us/products/" + photoData['product_id'] + "/large/" + photoData['image'] + "' />";
						editorHTML = editorHTML.replace('[Photo]', photoURL);
						photoIndex++;
						if ( photoIndex >= ( response['result']['photos'] ).length) photoIndex = 0;
					}
				}
				$("#dPreviewArea").html( editorHTML );
			}
		}, 'json' );
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