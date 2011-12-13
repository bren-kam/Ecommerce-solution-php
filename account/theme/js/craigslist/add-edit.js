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
		// Get the template numbers
        var templateNumber = parseInt( $('#hTemplateIndex').val() ), numberOfTemplates = parseInt( $("#hTemplateCount").val() );

        // If they've gone to far, do nothing
        if ( templateNumber >= numberOfTemplates)
            return;

        // Assign new number
		$('#hTemplateIndex').val( templateNumber + 1 );
		$("#dAdPaging").text( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );

        // Load the template preview
        loadTemplatePreview(1);
	});
	
	$("#aPrevTemplate").live( 'click', function() {
        // Get the template numbers
		var templateNumber = parseInt( $('#hTemplateIndex').val() );

         // If they've gone to far, do nothing
		if ( templateNumber <= 1 )
            return;

         // Assign new number
        $('#hTemplateIndex').val( templateNumber - 1 );
		$("#dAdPaging").text( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );

        // Load the template preview
        loadTemplatePreview(-1);
	});

    // Go to publish screen
	$('#aPublish, #aSelectTemplate').click( function() {
        // Hide the narrow your search and show that they are publishing
		$("#dNarrowSearch").hide();
		$("#hPublishConfirm").val(1);

        // Seeif there is a template
		var template = $("#dPreviewTemplate").is(':visible');
		
		if ( template ) {
			// We don't want to see the preview anymore
            $("#dPreviewTemplate").hide();
			
			var editorHTML = $("#hTemplateDescription").val(), iItemName = $("#hProductName").val(), iItemStoreName = $("#hStoreName").val(), iItemStoreLogo = $("#hStoreLogo").val(), iItemCategory = $("#hProductCategoryName").val();
			var iItemBrand = $("#hProductBrandName").val(), iItemProductDescription = $("#hProductDescription").val(), iItemSpecs = '', iItemSKU = $("#hProductSKU").val();
			var storeURL = $('#hStoreURL').val();
			
			// Set the text area, so it submits properly
			$("#hCraigslistAdDescription").val( editorHTML );

            // Get the new content
			editorHTML = editorHTML.replace( '[Product Name]', iItemName );
			editorHTML = editorHTML.replace( '[Store Name]', iItemStoreName );
			editorHTML = editorHTML.replace( '[Store Logo]', '<img src="' + storeURL + '/custom/uploads/images/' + iItemStoreLogo + '" alt="" />' );
			editorHTML = editorHTML.replace( '[Category]', iItemCategory );
			editorHTML = editorHTML.replace( '[Brand]', iItemBrand );
			editorHTML = editorHTML.replace( '[Product Description]', iItemProductDescription );
			editorHTML = editorHTML.replace( '[SKU]', iItemSKU );

            // Create photos
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

            // Update the title
			$('#tCraigslistPublishTitle').val( $("#hTemplateTitle").val() );

            // Get the textare filled with the right content
			$("#taCraigslistPublish").html( htmlToText( editorHTML ) );

            // Show the new HTML
			$("#dGenerateHTML").show();
		} else {
            // Show that we are confirming
			$("#hPublishConfirm").val(1);

            // Hide the initial section
            $("#dCreateAd, #dPreviewAd").hide();

            // Refresh the preview
			refreshPreview();

            // Get the content
			var content = $("#dCraigslistCustomPreview").html();

            // Make sure they entered something in
			if( '' == content ) {
				alert( "You haven't created ad text!" );
				$("#dCreateAd, #dPreviewAd").show();
				return false;
			}
			
			// Set the hidden value, so it submits properly
			$("#hCraigslistAdDescription").val( $("#taDescription").val() );

            // Change the title
			$('#tCraigslistPublishTitle').val( $("#tTitle").val() );

            // Change the textarea
			$("#taCraigslistPublish").html( htmlToText( content ) );

            // Show the new HTML
			$("#dGenerateHTML").show();
		}
	});

    // If they click create an ad, what do we do?
	$("#aCreateAd").click( function() {
        // Hide the preview template
		$("#dPreviewTemplate").hide();
		$("#hTemplateID").val('-1');

		openEditorAndPreview();
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

// Needs to be done at the end of the previous function
$.fn.determineTemplate = function() {
	if( '1' == $("#hPublishType").val() ) {
		openEditorAndPreview();
	} else {
		getFirstTemplate();
        openEditorAndPreview();
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
	$("#dAdPaging").text( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );
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
	var productName = $("#hProductName").val(), storeName = $("#hStoreName").val(), storeLogo = $("#hStoreLogo").val(), sku = $("#hProductSKU").val();
	var storeURL = $('#hStoreURL').val(), category = $("#hProductCategoryName").val(), brand = $("#hProductBrandName").val(), productDescription = $("#hProductDescription").val();

	//get the contents of the tinyMCE editor and replace tags with actual stuff.
	var newContent = CKEDITOR.instances.taDescription.getData();

    newContent = newContent.replace( '[Product Name]', productName );
    newContent = newContent.replace( '[Store Name]', storeName );
    newContent = newContent.replace( '[Store Logo]', '<img src="' + storeURL + '/custom/uploads/images/' + storeLogo + '" alt="" />' );
    newContent = newContent.replace( '[Category]', category );
    newContent = newContent.replace( '[Brand]', brand );
    newContent = newContent.replace( '[Product Description]', productDescription );
    newContent = newContent.replace( '[SKU]', sku );

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