// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    // Load initial categories
	$.post( '/products/categories/get/', { _nonce : $('#_get_categories').val(), cid: 0 }, ajaxResponse, 'json' );

	// Make categories sortable
	$("#categories-list").sortable({
		// When they move something, update data in the database
		update: function() {
			var categoriesList = $("#categories-list").sortable('serialize'), parentCategoryID = $('#current-category span:first').attr('rel');
			$.post( '/products/categories/update-sequence/', { _nonce : $('#_update_sequence').val(), pcid: parentCategoryID, sequence : categoriesList }, ajaxResponse, 'json' );
		},
		placeholder: 'category-placeholder'
	});

	/**
	 * Creates the ability to load sub categories
	 */
	$('#categories-list').on( 'click', 'a.parent-category', loadCategories );
	$('#breadcrumb').on( 'click', 'a', loadCategories );

    /**
     * Popup controls
     */
    $('body').on( 'keyup', '#tName', function() {
        $('#tSlug').val( $(this).val().slug() );
    }).on( 'change', '#sAttributes', function() {
        var attribute = $(this).find('option:selected'), attributeTitle = attribute.text(), attributeID = $(this).val();

		if ( '' == attributeID )
			return;

		// Create new div
		var newListItem = '<div extra="' + attributeTitle.slug() + '" id="dAttribute' + attributeID + '" class="attribute-container"><div class="attribute"><span class="attribute-name">' + attributeTitle + '</span>';

		// Add 'X'
		newListItem += '<a href="#" class="delete-attribute" title="Delete"><img src="/images/icons/x.png" width="15" height="17" alt="" /></a></div></div></div>';

		// Append it
		$(newListItem).insertInOrder( '#attributes-list', 'extra' );

		// Disable that option in the drop down
		attribute.attr('disabled', true);

		// Update attributes
		updateAttributes();
    }).on( 'click', '#attributes-list a.delete-attribute', function() {
        var parent = $(this).parents('div.attribute-container:first');

        // Enable the drop down
        $('#sAttributes option[value=' + parent.attr('id').replace( 'dAttribute', '' ) + ']').attr( 'disabled', false );

        // Remove the parent
        parent.remove();

        // Update attributes
        updateAttributes();
    });
});

function loadCategories() {
    // Get the category ID and name
    var categoryID = $(this).attr('id').replace( /[^0-9]+/, '' );

    $.post( '/products/categories/get/', { _nonce : $('#_get_categories').val(), cid: categoryID }, ajaxResponse, 'json' );
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
    var values = [];

	$('#attributes-list .attribute-container').each( function() {
		values.push( $(this).attr('id').replace('dAttribute', '') );
	});

    $('#hAttributes').val( values.join('|') );
}