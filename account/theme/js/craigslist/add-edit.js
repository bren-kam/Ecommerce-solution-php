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
			
			/*
			if( '1' == $("#hPublishType").val() ) {
				openEditorAndPreview();
			} else {
				getFirstTemplate();
			}*/
			
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
			$.post( '/ajax/products/autocomplete/', { '_nonce' : $('#_ajax_autocomplete').val(), 'type' : cacheType, 'term' : request['term'], 'owned' : 1 }, function( autocompleteResponse ) {
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
	
	// Create the "paginating" functionality
	$("#aNextTemplate").live( 'click', function(){
		var templateNumber = parseInt( $('#hTemplateIndex').val() );
		var numberOfTemplates = parseInt( $("#hTemplateCount").val() );
		if( templateNumber >= numberOfTemplates) return;
		$('#hTemplateIndex').attr( 'value', templateNumber + 1 );
		$("#dAdPaging").html( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );
		loadTemplatePreview(1);
	});
	
	$("#aPrevTemplate").live( 'click', function(){
		var templateNumber = parseInt( $('#hTemplateIndex').val() );
		if( templateNumber <= 1) return;
		$('#hTemplateIndex').val( templateNumber - 1 );
		$("#dAdPaging").html( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );
		loadTemplatePreview(-1);
	});
				
	$('#aPublish, #aSelectTemplate').click( function() {
		$("#dNarrowSearch").hide();
		$("#hPublishConfirm").val( 1 );	
		var template = $("#dPreviewTemplate").is(':visible');
		
		if( template ){
			$("#dPreviewTemplate").hide();
			
			var editorHTML = $("#hTemplateDescription").val(), iItemName = $("#hProductName").val(), iItemStoreName = $("#hStoreName").val(), iItemStoreLogo = $("#hStoreLogo").val(), iItemCategory = $("#hProductCategoryName").val();
			var iItemBrand = $("#hProductBrandName").val(), iItemProductDescription = $("#hProductDescription").html(), iItemSpecs = '', iItemSKU = $("#hProductSKU").val();
			var storeURL = $('#hStoreURL').val();
			
			// Set the text area, so it submits properly
			$("#hCraigslistAdDescription").val( editorHTML );
			
			editorHTML = editorHTML.replace( '[Product Name]', iItemName );
			editorHTML = editorHTML.replace( '[Store Name]', iItemStoreName );
			editorHTML = editorHTML.replace( '[Store Logo]', '<img src="' + storeURL + '/custom/uploads/images/' + iItemStoreLogo + '" alt="" />' );
			editorHTML = editorHTML.replace( '[Category]', iItemCategory );
			editorHTML = editorHTML.replace( '[Brand]', iItemBrand );
			editorHTML = editorHTML.replace( '[Product Description]', iItemProductDescription );
			editorHTML = editorHTML.replace( '[Product Specs]', iItemSpecs );
			editorHTML = editorHTML.replace( '[SKU]', iItemSKU );
			
			var photos = new Array;
			photos = document.getElementsByClassName( 'hiddenImage' );
			
			var photoHTML = '', index = 0;
			
			if ( photos.length ) {
				while ( editorHTML.indexOf( "[Photo]" ) >= 0 ) {
					if ( index >= photos.length ) index = 0;
					
					photoHTML = '<img src="' + photos[index]['src'] + '" />';
					editorHTML = editorHTML.replace( "[Photo]", photoHTML );
					index++;
				}
			}
			
			$('#tCraigslistPublishTitle').val( $("#hTemplateTitle").val() );
			$("#taCraigslistPublish").html( htmlToText( editorHTML ) );
			
			$("#dGenerateHTML").show();
		} else {
			$("#iPublishConfirm").attr("value", "1");
			$("#dCreateAd, #dPreviewAd").hide();
			
			refreshPreview();
			var content = $("#dCraigslistCustomPreview").html();
			if( '' == content ){
				alert( "You haven't created ad text!" );
				$("#dCreateAd, #dPreviewAd").show();
				return false;
			}
			
			// Set the text area, so it submits properly
			$("#hCraigslistAdDescription").val( $("#taDescription").html() );
			
			$('#tCraigslistPublishTitle').val( $("#hTemplateTitle").val() );
			$("#taCraigslistPublish").html( htmlToText( content ) );
			
			$("#dGenerateHTML").show();
		}
	});
	
	$("#aCreateAd").click( function() {
		$("#dPreviewTemplate").hide();
		$("#iTemplateId").val( '-1' );
		openEditorAndPreview();
	});
	
	$("#aRefresh").click( refreshPreview );
	
	checkAdStatus();	
});

/**
 * If there is an ad in the titlebar, load it using AJAX.
 */
function checkAdStatus(){
	craigslistAdId = parseInt( $('#hCraigslistAdId').val() );
	if( craigslistAdId ) {
		$.post(  '/ajax/craigslist/get/', { '_nonce' : $('#_nonce').val(), 'craigslist_ad_id' : craigslistAdId },
		   function( result ) {
				if( result['success'] ) {
					selectProduct();
				} else {
				}
			}, 
			'json' );
	}
}

// Needs to be done at the end of the previous function
$.fn.determineTemplate = function() {
	if( '1' == $("#hPublishType").val() ) {
		openEditorAndPreview();
	} else {
		getFirstTemplate();
	}
}

/**
 * Gets the number of templates for this ad, and loads the first one.
 */
function getFirstTemplate() {
	var categoryID = $("#hProductCategoryID").val();
	
	if ( !categoryID ) 
		return;
	
	$.post( '/ajax/craigslist/get-category-template-count/', { '_nonce' : $('#_ajax_get_category_template_count').val(), 'cid' : categoryID }, ajaxResponse, 'json' );
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
 * Opens the editor and template area
 */
function openTemplateSelector() {
	$("#dAdPaging").html( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );
	$("#dNarrowSearch").hide();
	$("#dPreviewTemplate").show();
	
	loadTemplatePreview(1);
}

$.fn.openTemplateSelector = openTemplateSelector;

/**
 * Loads the current template into the preview window, using the current product data 
 */
function loadTemplatePreview( direction ) {
	$.post( '/ajax/craigslist/get-template/', { '_nonce' : $('#_ajax_get_template').val(), 'cid' : $("#hProductCategoryID").val(), 'tid' : $('#hTemplateIndex').val(), 'd' : direction, 'pid' : $('#hProductID').val() }, ajaxResponse, 'json' );
}

/**
 * Gets the next product in the lineup for sampling purposes
 */
function refreshPreview() {
	var productID = parseInt( $("#hProductID").val() );
	var product_name = $("#hProductName").val();
	var store_name = $("#hStoreName").val();
	var store_logo = $("#hStoreLogo").val();
	var storeURL = $('#hStoreURL').val();
	var category = $("#hProductCategoryName").val();
	var brand = $("#hProductBrandName").val();
	var product_description = $("#hProductDescription").html();
	var product_specs = ""; // $("").val();
	
	var sku = $("#hProductSKU").val();
	

	//get the contents of the tinyMCE editor and replace tags with actual stuff.
	var newContent = CKEDITOR.instances.taDescription.getData();
	newContent = newContent.replace( /\[Brand\]/gi, brand );
	newContent = newContent.replace( /\[Product\ Name\]/gi, product_name );
	newContent = newContent.replace( /\[Product\ Specs\]/g, product_specs );
	newContent = newContent.replace( /\[Category\]/gi, category );
	newContent = newContent.replace( /\[Store\ Name\]/gi, store_name );
	newContent = newContent.replace( /\[Store\ Logo\]/gi, '<img src="' + storeURL + '/custom/uploads/images/' + store_logo + '" alt="" />' );
	newContent = newContent.replace( /\[Product\ Description\]/gi, product_description );
	newContent = newContent.replace( /\[SKU\]/gi, sku );
	newContent = newContent.replace( /\[Photo\]/gi, '[Photo]' );
	
	var photos = new Array;
	photos = document.getElementsByClassName( 'hiddenImage' );
	var photoHTML = '', index = 0;
	
	if ( photos.length ) {
		while ( newContent.indexOf( '[Photo]' ) >= 0 ) {
			if ( index >= photos.length ) 
				index = 0;
			
			photoHTML = '<img src="' + photos[index]['src'] + '" />';
			newContent = newContent.replace( "[Photo]", photoHTML );
			index++;
		}
	}
	
	$("#dCraigslistCustomPreview").html( newContent );
}

/*
 * Replace all instances of < and > with htmlspecialchars, making HTML viewable as plain text.
 */
function htmlToText( html ) {
	html = html.replace( '<', '&lt;');
	html = html.replace( '>', '&gt;');
	return html;
}

