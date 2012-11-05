/**
 * Craigslist - Add/Edit Page
 */

// var editorHTML = CKEDITOR.instances.taDescription.getData();

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
		
	// Create autocomplete
	ac = $('#tAutoComplete').autocomplete( {
		serviceUrl: '/ajax/craigslist/autocomplete',
		width: 300,
		params: {
			_nonce: $('#_nonce').val(),
			type: 'sku'
		}
	} );
	
	$('#sAutoComplete').change( function() {
		switch( $(this).val() ) {
			case 'sku':
				ac.setOptions( {
					params: {
						_nonce: $('#_nonce').val(),
						type: 'sku'
					}
				} );
				var newType = 'sku';
				var newVal = 'Enter SKU...';
				break;
			
			case 'products':
				ac.setOptions( {
					params: {
						_nonce: $('#_nonce').val(),
						type: 'products'
					}
				} );
				var newType = 'productName';
				var newVal = 'Enter Product Name...';
				break;
				
			default:
				break;
		}
		$('#hSearchType').attr( 'value', newType );
		$('#tAutoComplete').attr( 'tmpval', newVal ).val( newVal );
	});
	
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

	// Create the product selection functionality
	$('#aSelect').click( selectProduct );
	
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
				
	$('#aPublish, #aSelectTemplate').click( publishProduct );
	
	$("#aCreateAd").click( function() {
		$("#dPreviewTemplate").css('display', 'none');
		$("#iTemplateId").val( '-1' );
		openEditorAndPreview();
	});
	
	$("#aRefresh").click( refreshPreview );
	
	checkAdStatus();	
}

/**
 * If there is an ad in the titlebar, load it using AJAX.
 */
function checkAdStatus(){
	craigslistAdId = parseInt( $('#hCraigslistAdId').attr('value') );
	if( craigslistAdId ){
		$.post(  '/ajax/craigslist/get/', 
		   { 
		   'nonce' : $('#_nonce').val(),
		   'craigslist_ad_id' : craigslistAdId
		   },
		   function( result ) {
				if( result['success'] ) {
					selectProduct();
				} else {
				}
			}, 
			'json' );
	}		
}

/**
 *  Verify that the product selected in the search bar is valid, and load it for use in the ad.
 */
function selectProduct(){
	var queryType = $('#hSearchType').attr('value'), query = '', searchBy = '';
	
	switch( queryType ){
		case 'sku':
			query = $("#tAutoComplete").attr( 'value' );
			searchBy = 'sku';
			break;
		case 'productName':
			query = $("#tAutoComplete").attr('value');
			searchBy = 'product_name';
			break;
		default:
			return;
			break;
	}
	$.post( '/ajax/craigslist/set-product/',
	   {
			'nonce' : $('#_nonce').val(),
			'search_by' : searchBy,
			'query': query
	   },
	   function( result ) {
		  if( result['success'] ){
			  $("#hProductDescription").html( result['product_description'] );
			  $("#hProductName").attr("value", result['product_name'] );	// Product Name
			  $("#hProductCategoryId").attr( "value", result['category_id'] );	// Category ID
			  $("#hProductId").attr( "value", result['product_id'] );
			  $("#hProductCategoryName").attr("value", result['category_name'] );	// Category Name
			  $("#hProductSKU").attr("value", result['sku'] );
			  $("#hProductSpecs").attr("value", result['product_specs'] );
			  $("#hProductBrandName").attr( "value", result['brand'] );
			  $("#hStoreName").attr( "value", result['store_name'] ); 
			  $("#hStoreLogo").attr( "value", result['store_logo'] );
			  $("#hStoreURL").attr( "value", result['store_url'] );
			  
			  var imageHTML = "";
			  for( image in result['images']){
				  var url = result['images'][image];
				  imageHTML += "<img class='hiddenImage' name='hiddenImage' src='" + url + "'/>"
			  }
			  $("#dProductPhotos").html( imageHTML );
			  
			  if( '1' == $("#hPublishType").val() ) {
				  openEditorAndPreview();
			  } else {
				  getFirstTemplate();
			  }
		  } else {
			 alert( result['message'] );
		  }
	   },
	   'json' 
	);
}

/**
 * Gets the number of templates for this ad, and loads the first one.
 */
function getFirstTemplate( ) {
	var categoryID = $("#hProductCategoryId").attr( "value" );
	if( !categoryID ) return;
	
	$.post( '/ajax/craigslist/get-category-template-count/',
		   {
				'nonce' : $('#_nonce').val(),
				'category_id' : categoryID
		   },
		   function( result ){
				if( result['noresults'] ) {
					alert( 'There are no templates in this category.  Please create your own.');
					openEditorAndPreview();
				} else {
					$('#hTemplateCount').val( result['result'] );
					openTemplateSelector();
				}
		   },
		   'json' );
}

/**
 *  Opens the editor area.
 */
function openEditorAndPreview(){
	$("#dPreviewTemplate").css("display", "none");
	$("#dCreateAd, #dPreviewAd").css("display", "block");
}

/**
 * Opens the editor and template area
 */
function openTemplateSelector(){
	$("#dAdPaging").html( $('#hTemplateIndex').val() + ' / ' + $("#hTemplateCount").val() );
	$("#dNarrowSearch").css("display", "none");
	$("#dPreviewTemplate").css("display", "block");
	
	loadTemplatePreview(1);
}

/**
 * Loads the current template into the preview window, using the current product data 
 */
function loadTemplatePreview( direction ){
	// Get product values from the document.
	var iItemName = $("#hProductName").val();
	var iItemStoreName = $("#hStoreName").val();
	var iItemStoreLogo = $("#hStoreLogo").val();
	var iItemCategory = $("#hProductCategoryName").val();
	var iItemBrand = $("#hProductBrandName").val();
	var iItemProductDescription = $("#hProductDescription").html();
	var iItemSpecs = ''; //$("#iItemSpecs").val();
	//var iAttributes = ;
	var iItemSKU = $("#hProductSKU").val();
	
	// Load the template HTML
	var templateNumber = $('#hTemplateIndex').val();
	var templateID = $("#hTemplateID").val();
	var categoryID = $("#hProductCategoryId").val();
	
	$.post( '/ajax/craigslist/get-template/',
		   {
				'nonce' : $('#_nonce').val(),
				'category_id' : categoryID,
				'template_id' : templateID,
				'direction' : direction
		   },
		   function( result ){
				if( !result['results'] ) {
				} else {
					var titleHTML = result["results"]["title"];
					var editorHTML = result["results"]["description"];
					titleHTML = "<h2><b>" + titleHTML + "</b></h2><hr/>Date: 2011-4-25, 11:35 CST<br/>Reply to: <a href='mailto:test@test.com'>sale-rgf3-2123432@craigslist.org</a><hr/>";
					editorHTML = titleHTML + editorHTML;
					editorHTML = editorHTML.replace( '[Product Name]', iItemName );
					editorHTML = editorHTML.replace( '[Store Name]', iItemStoreName );
					editorHTML = editorHTML.replace( '[Store Logo]', iItemStoreLogo );
					editorHTML = editorHTML.replace( '[Category]', iItemCategory );
					editorHTML = editorHTML.replace( '[Brand]', iItemBrand );
					editorHTML = editorHTML.replace( '[Product Description]', iItemProductDescription );
					editorHTML = editorHTML.replace( '[Product Specs]', iItemSpecs );
					editorHTML = editorHTML.replace( '[SKU]', iItemSKU );
					
					var photos = new Array;
					photos = document.getElementsByClassName( 'hiddenImage' );
					var photoHTML = "";
					var index = 0;
					if( photos.length ){
						while( editorHTML.indexOf( "[Photo]" ) >= 0 ){
							if( index >= photos.length ) index = 0;
							photoHTML = "<img src='" + photos[ index ].src + "'/>";
							editorHTML = editorHTML.replace( "[Photo]", photoHTML );
							index++;
						}
					}
					$("#dCraigslistPreview").html( editorHTML );
					$("#hTemplateID").val( result['results']['craigslist_template_id'] );
					$("#hTemplateTitle").val( result['results']['title'] );
					$("#hTemplateDescription").val( result['results']['description'] );
					$("#tTitle").attr( 'value', $("#hTemplateTitle").val() );
				}
		   },
		   'json' 
	);
}

/**
 * Gets the next product in the lineup for sampling purposes
 */
function refreshPreview() {
	var productID = parseInt( $("#hProductId").attr('value') );
	var product_name = $("#hProductName").attr("value");
	var store_name = $("#hStoreName").attr("value");
	var store_logo = $("#hStoreLogo").val();
	var category = $("#hProductCategoryName").attr("value");
	var brand = $("#hProductBrandName").attr("value");
	var product_description = $("#hProductDescription").html();
	var product_specs = ""; // $("").attr("value");
	//var attributes = $("").attr("value");
	var sku = $("#hProductSKU").attr("value");
	
	//get the contents of the tinyMCE editor and replace tags with actual stuff.
	var newContent = CKEDITOR.instances.taDescription.getData();
	newContent = newContent.replace( /\[Brand\]/gi, brand );
	newContent = newContent.replace( /\[Product\ Name\]/gi, product_name );
	newContent = newContent.replace( /\[Product\ Specs\]/g, product_specs );
	newContent = newContent.replace( /\[Category\]/gi, category );
	newContent = newContent.replace( /\[Store\ Name\]/gi, store_name );
	newContent = newContent.replace( /\[Store\ Logo\]/gi, store_logo );
	newContent = newContent.replace( /\[Product\ Description\]/gi, product_description );
	newContent = newContent.replace( /\[SKU\]/gi, sku );
	newContent = newContent.replace( /\[Photo\]/gi, '[Photo]' );
	
	var photos = new Array;
	photos = document.getElementsByClassName( 'hiddenImage' );
	var photoHTML = "";
	var index = 0;
	if( photos.length ){
		while( newContent.indexOf( "[Photo]" ) >= 0 ){
			if( index >= photos.length ) index = 0;
			photoHTML = "<img src='" + photos[ index ].src + "'/>";
			newContent = newContent.replace( "[Photo]", photoHTML );
			index++;
		}
	}
	$("#dCraigslistCustomPreview").html( newContent );
}

/*
 * Hide everythign and copy the HTML to the "paste this into craigslist" area.
 */
function publishProduct()
{
	$("#dNarrowSearch").css("display", "none");
	$("#hPublishConfirm").val( 1 );	
	var template = ( $("#dPreviewTemplate").css('display') == "block" ) ? true : false;
	
	if( template ){
		$("#tTitle").html( $("#hTemplateTitle").val() );
		$("#dPreviewTemplate").css("display", "none");
		var editorHTML = $("#hTemplateDescription").val();
		var iItemName = $("#hProductName").val();
		var iItemStoreName = $("#hStoreName").val();
		var iItemStoreLogo = $("#hStoreLogo").val();
		var iItemCategory = $("#hProductCategoryName").val();
		var iItemBrand = $("#hProductBrandName").val();
		var iItemProductDescription = $("#hProductDescription").html();
		var iItemSpecs = ''; //$("#iItemSpecs").val();
		//var iAttributes = ;
		var iItemSKU = $("#hProductSKU").val();
		
		// Set the text area, so it submits properly
		$("#hCraigslistAdDescription").val( editorHTML );
		
		editorHTML = editorHTML.replace( '[Product Name]', iItemName );
		editorHTML = editorHTML.replace( '[Store Name]', iItemStoreName );
		editorHTML = editorHTML.replace( '[Store Logo]', iItemStoreLogo );
		editorHTML = editorHTML.replace( '[Category]', iItemCategory );
		editorHTML = editorHTML.replace( '[Brand]', iItemBrand );
		editorHTML = editorHTML.replace( '[Product Description]', iItemProductDescription );
		editorHTML = editorHTML.replace( '[Product Specs]', iItemSpecs );
		editorHTML = editorHTML.replace( '[SKU]', iItemSKU );
		
		var photos = new Array;
		photos = document.getElementsByClassName( 'hiddenImage' );
		var photoHTML = "";
		var index = 0;
		if( photos.length ){
			while( editorHTML.indexOf( "[Photo]" ) >= 0 ){
				if( index >= photos.length ) index = 0;
				photoHTML = "<img src='" + photos[ index ].src + "'/>";
				editorHTML = editorHTML.replace( "[Photo]", photoHTML );
				index++;
			}
		}
		$("#dCraigslistPublish").html( htmlToText( editorHTML ) );
		$("#dGenerateHTML").css( "display", "block" );
	} else {
		$("#iPublishConfirm").attr("value", "1");
		$("#dCreateAd, #dPreviewAd").css( "display", "none" );
		refreshPreview();
		var content = $("#dCraigslistCustomPreview").html();
		if( '' == content ){
			alert( "You haven't created ad text!" );
			$("#dCreateAd, #dPreviewAd").css( "display", "block" );
			return false;
		}
		// Set the text area, so it submits properly
		$("#hCraigslistAdDescription").val( $("#taDescription").html() );
		
		$("#dCraigslistPublish").html( htmlToText( content ) );
		$("#dGenerateHTML").css( "display", "block" );
	}
}

/*
 * Replace all instances of < and > with htmlspecialchars, making HTML viewable as plain text.
 */
function htmlToText( html ) {
	html = html.replace( '<', '&lt;');
	html = html.replace( '>', '&gt;');
	return html;
}

/*
 * Prepare the title text by replacing attributes.
 */
function prepareTitle( object ) {
	var iItemName = $("#hProductName").val();
	var iItemStoreName = $("#hStoreName").val();
	var iItemStoreLogo = $("#hStoreLogo").val();
	var iItemCategory = $("#hProductCategoryName").val();
	var iItemBrand = $("#hProductBrandName").val();
	var iItemProductDescription = $("#hProductDescription").html();
	var iItemSpecs = ''; //$("#iItemSpecs").val();
	var iItemSKU = $("#hProductSKU").val();	
	
	var editorHTML = $( object ).val();	
	editorHTML = editorHTML.replace( '[Product Name]', iItemName );
	editorHTML = editorHTML.replace( '[Store Name]', iItemStoreName );
	editorHTML = editorHTML.replace( '[Store Logo]', iItemStoreLogo );
	editorHTML = editorHTML.replace( '[Category]', iItemCategory );
	editorHTML = editorHTML.replace( '[Brand]', iItemBrand );
	editorHTML = editorHTML.replace( '[Product Description]', iItemProductDescription );
	editorHTML = editorHTML.replace( '[Product Specs]', iItemSpecs );
	editorHTML = editorHTML.replace( '[SKU]', iItemSKU );
	//return editorHTML;
}

