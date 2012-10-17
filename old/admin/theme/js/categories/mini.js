/**
 * Categories Mini
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
	$('#frAdd, #frEdit').ajaxForm({
		dataType : 'json',
		success : function( response ){
			// Handle any errors
			if ( !response['result'] ) {
				alert( response['error'] );
				return;
			}
			
			refreshAllCategories();
			dEditCategory.dialog('close');
		}
	});

	/**
	 * When they're typing in the name, it will update the Category Slug
	 */
	$('#tName').keyup( function() {
		var newSlug = '/' + $(this).val().slug() + '/';
		
		// We don't want any empty slashes, so do a check
		if ( '//' == newSlug )
			newSlug = '';
		
		$('#tSlug').val( newSlug );
	});

	$('#tSlug').change( function() {
		var newSlug = '/' + $(this).val().slug() + '/'; 

		// We don't want any empty slashes, so do a check
		if ( '//' == newSlug )
			newSlug = '';
		
		$(this).val( newSlug );
	});
	
	// Add attributes
	$('#aAddAttribute').click( function() {
		var attribute = $('#sAttributes option:selected'), attributeTitle = attribute.text(), attributeID = attribute.val();
		
		if ( '' == attributeID ) 
			return;
			
		// Create new div
		var new_list_item = '<div extra="' + attributeTitle.slug() + '" id="dAttribute' + attributeID + '" style="display:none;" class="attribute-container"><div class="attribute"><span class="attribute-name">' + attributeTitle + '</span>';

		// Add 'X'
		new_list_item += '<div style="display:inline;float:right"><a href="javascript:;" class="delete-attribute" title=\'Delete "' + attributeTitle + '" Attribute\'><img class="delete-attribute" src="/images/icons/x.png" width="15" height="17" /></a></div></div></div>';

		// Append it
		$( new_list_item ).insertInOrder( '#dAttributeList', 'extra' );
		
		// Slide it down
		$('#dAttribute' + attributeID).slideDown();
		
		// Disable that option in the drop down
		attribute.attr('disabled', true);
		
		$('#sAttributes option:first').attr( 'selected', true );
		
		// Update attributes
		updateAttributes();
	});
	
	// Deletes an attribute
	$('.delete-attribute').live( 'click', function() {
		var parent = $(this).parents('div.attribute-container:first');
		
		// Enable the drop down
		$('#sAttributes option[value=' + parent.attr('id').replace( 'dAttribute', '' ) + ']').attr( 'disabled', false );
		
		// Remove the parent
		parent.remove();

		// Update attributes
		updateAttributes();
	});
	
	// Make the attributes sortable
	$('#dAttributeList').sortable( {
		forcePlaceholderSize : true,
		update: function() {
			updateAttributes();
		}, 
		placeholder: 'attribute-placeholder'
	});
}

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

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

/**
 * Updates a hidden field with the list of attributes
 */
function updateAttributes() {
	$('#hAttributes').val('');
	$('#dAttributeList .attribute-container').each( function() {
		var hiddenField = $('#hAttributes');
		hiddenField.val( hiddenField.val() + $(this).attr('id').replace('dAttribute', '') + '|' );
	});
}