/**
 * Product Options - Add/Edit Page
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
	// Make it have a temporary value
	$('#tListItemValue').tmpVal( '#929292', '#000000' );
	
	/********** Product Options Section **********/
	$('.choices').click( function(){
		var optionType = $(this).attr('id').replace( /aChoose(?:Button)?/, '' );
		
		// Add breadcrumb
		optionBreadcrumbAdd( $(this).parents('.page').attr('id'), $(this).attr('title') );

		// Hide current page
		$('#dAddOptionStep1').fadeOut('fast');

		setTimeout( function() {
			// Show new page
			$('#dOption_' + optionType).fadeIn();
		}, 200 );
	});
	
	// Make the breadcrumbs show the right page
	$('.option-breadcrumb').live( 'click', function() {
		optionBreadcrumbSelect( $(this).attr('id').replace( 'aOptionPage_', '' ), $(this).text() );
	});
	
	// The 'Add List Item' link
	$('#aAddListItem').click( function() {
		var itemName = $('#tListItemValue').val();
		
		// If they entered nothing, do nothing
		if ( '' == itemName || 'Item Name' == itemName )
			return;
		
		// Append new div
		$('#dDropDownItemsList').append( '<div extra="' + itemName.slug() + '" id="dListItem_' + itemName.slug() + '" style="display:none;" class="list-item-container"><div class="list-item" id="dLI0"><span class="list-item-name">' + itemName + '</span><div style="display:inline;float:right"><a href="javascript:;" title=\'Edit "' + itemName + '" List Item\' class="edit-list-item"><img src="/images/icons/edit.png" alt=\'Edit "' + itemName + '" List Item\' width="15" height="17" /></a><a href="javascript:;" class="delete-list-item" title=\'Delete "' + itemName + '" List Item\'><img src="/images/icons/x.png" /></a></div></div></div>' );
		
		// Slide it down
		$('#dListItem_' + itemName.slug()).slideDown();

		// Reset to default values
		$('#tListItemValue').val('Item Name').css('color', '#929292');
		$('#tListItemPrice').val('');
		$('#tListItemPriceType').attr( 'selectedIndex', 0 );

		// Update list items
		updateListItems();
	});
	
	// Edit the List Items
	$('.edit-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first');
		
		parent.find('span, div, a').fadeOut('fast');
		
		setTimeout( function() {
			parent.append('<input type="text" class="tb edit-list-item-value" style="width:95%;margin-left:2%; margin-right:3%;display:none" value="' + parent.find('span:first').text() + '" maxlength="100" /><p style="float:right"><a href="javascript:;" class="save-list-item button" title="Save" style="display:none">Save</a> <a href="javascript:;" class="cancel-list-item" title="Cancel" style="display:none;">Cancel</a></p>');
			parent.find('input[type=text], a.save-list-item, a.cancel-list-item').fadeIn();
		}, 200 );
		
		parent.css( 'borderColor', '#FF0000' ).animate( { height: '40px' }, 200 );
	});
	
	// Stop editing a list item
	$('.cancel-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first');

		parent.css( 'borderColor', '' ).animate( { height: '17px' }, 200 );
		parent.find('input[type=text], p').remove();
		parent.find('span, a, div').fadeIn();
	});
	
	// Save a profile link
	$('.save-list-item').live( 'click', function() {
		var parent = $(this).parents('div.list-item:first'), listItemField = parent.find('.edit-list-item-value'), listItem = listItemField.val();
		
		// Make sure it's not an empty profile_name
		if ( '' == listItem ) {
			alert( 'Please enter a list item name' );
			listItemField.focus();
			return;
		}
		
		// Make sure it's within 15 characters
		if ( listItem.length > 100 ) {
			alert( 'The attribute name may only have up to 100 characters' );
			listItemField.focus();
			return;
		}
		
		parent.find('.list-item-name').text( listItem );
		
		updateListItems();
		
		parent.css( 'borderColor', '' ).animate( { height: '17px' }, 200 );
		parent.find('input[type=text], p').remove();
		parent.find('span, a, div').fadeIn();
	});
	
	// Delete Drop Down List Items
	$('.delete-list-item').live( 'click', function() {
		if ( !confirm( 'Are you sure you want to delete this list item? You will not be able to access any analytics using this item.' ) )
			return false;
			
		$(this).parents('.list-item-container:first').remove();
		updateListItems();
	});
	
	// Make the items sortable
	$('#dDropDownItemsList').sortable( {
		forcePlaceholderSize : true,
		update: function(event, ui) {
			updateListItems();
		}, 
		placeholder: 'list-item-placeholder'
	});
}

/**
 * Adds something to the end of the products -> add option -> breadcrumb
 */
function optionBreadcrumbAdd( objectID, breadcrumbText ) {
	var oldText = $('#sOptionCurrentPage').text();

	// Make sure it's being shown
	$('#pOptionBreadCrumb span').fadeIn();

	// Replace current option
	$('#sOptionCurrentPage').replaceWith( '<a href="javascript:;" id="aOptionPage_' + objectID + '" title="' + oldText + '" class="option-breadcrumb">' + oldText + '</a> &gt; ' );

	// Add new option
	$('#pOptionBreadCrumb').append( '<span id="sOptionCurrentPage">' + breadcrumbText + '</span>' );
}

/**
 * Selects one of the items in the products -> add option -> breadcrumb
 */
function optionBreadcrumbSelect( objectID, breadcrumbText ) {
	// Hide all current pages
	$('.page').fadeOut('fast');

	// Fade in the other pages
	setTimeout( function() {
		$('#' + objectID).fadeIn();
	}, 200 );

	// Remove the extra breadcrumb
	var breadcrumbPattern = new RegExp('(.*)<a\\s[^>]*id="aOptionPage_' + objectID + '"[^>]+>.+'), pOptionBreadcrumb = $('#pOptionBreadCrumb');

	// Change the breadcrumb itself
	pOptionBreadcrumb.html( pOptionBreadcrumb.html().replace( breadcrumbPattern, '$1' ) ).append( '<span id="sOptionCurrentPage">' + breadcrumbText + '</span>' );
	
	// Hide it if you're on the home page
	if ( -1 == $('#pOptionBreadCrumb').html().search( /<a/ ) )
		$('#pOptionBreadCrumb span').fadeOut('fast');
};

//"Insert in alphabetical order by ID" plugin
// version 0.2
// Copyright (C) 2008 Ricardo Tomasi 
// ricardobeat at gmail.com
// Licensed under the WTFPL (http://sam.zoy.org/wtfpl/)
// http://ff6600.org/j/jquery.insertInOrder.js

jQuery.fn.insertInOrder = function(container,attr){
    if (!attr) attr = 'id';
	return this.each(function(){
		var $c = $(container), $elms = $c.children(),
			ln = $elms.length, $t = $(this), ta = $t.attr(attr),
			ids = [ta];
		while(ln--){ ids.push($elms.eq(ln).attr(attr)) };
		ids.sort();
		if (ids[0] == ta) {
			$c.prepend(this);
			return;
		} else if (ids[ids.length-1] == ta) {
			$c.append(this);
			return;
		} else {
			while ( ids.pop() != ta );
			$elms.filter('['+attr+'='+ids[ids.length-1]+']').after(this);
		};
	});
};

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

/**
 * Updates a hidden field with the list of items in a new drop down
 */
function updateListItems() {
	var hListItems = $('#hListItems');
	
	// Reset
	hListItems.val('');

	// Update the hidden input
	$('#dDropDownItemsList .list-item').each( function() {
		hListItems.val( hListItems.val() + $(this).attr('id').replace( /^dLI/, '' ) + ':' + $(this).find('.list-item-name').text() + '|' );
	});
}