/**
 * Craigslist - Add/Edit Page
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Setup cache
	cache = { sku : {}, product : {} };
	
	// Change the text
	$('#sAutoComplete').change( function() {
		var tAutoComplete = $('#tAutoComplete');
		
		tAutoComplete.attr( 'tmpval', tAutoComplete.attr('tmpval').replace( /\s([\w\s]+).../, ' ' + $(this).find('option:selected').text() + '...' ) ).val('').blur();
	});
	
	$('#tAutoComplete').autocomplete({
		minLength: 1,
		select: function( event, ui ) {
			$.post( '/ajax/craigslist/set-product/', { '_nonce' : $('#_ajax_set_product').val(), 'pid' : ui.item.value }, ajaxResponse, 'json' );
			
			$('#hProductID').val( ui.item.value );
			$('#tAutoComplete').val( ui.item.label );
			
			return false;
		},
		source: function( request, response ) {
			// Get the cache type
			var cacheType = $('#sAutoComplete').val();
			
			// Find out if they are already cached so we don't have to do another ajax called
			if( request['term'] in cache[cacheType] ) {
				response( $.map( cache[cacheType][request['term']], function( item ) {
					return {
						'label' : item['name'],
						'value' : item['value']
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
						'value' : item['value']
					}
				}));
			}, 'json' );
		}
	});

    // If they click create an ad, what do we do?
	$("#aCreateAd").click( function() {
        // Hide the preview template
		$("#dPreviewTemplate").hide();
		$("#hTemplateID").val('-1');

		openEditorAndPreview();
	});

    // Post this ad to the Craigslist API
    $('#aPostAd').click( function() {
        $('#hPostAd').val('1');
    });

    // Refresh the preview
	$("#aRefresh").click( refreshPreview );

    // See if an ad is there to load
	checkAdStatus();	
});

/**
 * If there is an ad in the titlebar, load it using AJAX.
 */
function checkAdStatus(){
	craigslistAdID = parseInt( $('#hCraigslistAdID').val() );

	if( craigslistAdID )
        $.post( '/ajax/craigslist/set-product/', { '_nonce' : $('#_ajax_set_product').val(), 'caid' : craigslistAdID }, ajaxResponse, 'json' );
}

/**
 *  Opens the editor area.
 */
function openEditorAndPreview() {
	$('#dPreviewTemplate').hide();
	$('#dCreateAd, #dPreviewAd').show();
}

$.fn.openEditorAndPreview = openEditorAndPreview;

/**
 * Gets the next product in the lineup for sampling purposes
 */
function refreshPreview() {
	var productName = $("#hProductName").val(), storeName = $("#hStoreName").val(), storeLogo = $("#hStoreLogo").val(), sku = $("#hProductSKU").val();
	var storeURL = $('#hStoreURL').val(), category = $("#hProductCategoryName").val(), brand = $("#hProductBrandName").val(), productDescription = $("#hProductDescription").val();

	storeLogo = ( storeLogo.search( /http:/i ) > -1 ) ? storeLogo : storeURL + '/custom/uploads/images/' + storeLogo;

	//get the contents of the tinyMCE editor and replace tags with actual stuff.
	var newContent = CKEDITOR.instances.taDescription.getData();

    newContent = newContent.replace( '[Product Name]', productName );
    newContent = newContent.replace( '[Store Name]', storeName );
    newContent = newContent.replace( '[Store Logo]', '<img src="' + storeLogo + '" alt="" />' );
    newContent = newContent.replace( '[Category]', category );
    newContent = newContent.replace( '[Brand]', brand );
    newContent = newContent.replace( '[Product Description]', productDescription );
    newContent = newContent.replace( '[SKU]', sku );

	var photos = document.getElementsByClassName( 'hiddenImage' ), photoHTML = '', index = 0;
	
	if ( photos.length ) {
		while ( newContent.indexOf( '[Photo]' ) >= 0 ) {
			if ( index >= photos.length ) 
				index = 0;
			
			photoHTML = '<img src="' + photos[index]['src'] + '" width="320" height="320" />';
			newContent = newContent.replace( "[Photo]", photoHTML );
			index++;
		}
	}
	
	$("#dCraigslistCustomPreview").html( newContent );
}