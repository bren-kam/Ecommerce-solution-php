// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
    // Load initial categories
	$.post( '/products/categories/get/', { _nonce : $('#_get_categories').val(), cid: 0 }, ajaxResponse, 'json' );

	// Make categories sortable
	$("#categories-list").sortable({
		// When they move something, update data in the database
		update: function() {
			var categoriesList = $("#categories-list").sortable('serialize'), parentCategoryID = $('#current-category-id').val();

			$.post( '/products/categories/update-sequence/', { _nonce : $('#_update_sequence').val(), pcid: parentCategoryID, sequence : categoriesList }, ajaxResponse, 'json' );
		},
		placeholder: 'category-placeholder'
	});

	/**
	 * Creates the ability to load sub categories
	 */
	$('#categories-list').on( 'click', 'a.parent-category', loadCategories );
	$('#breadcrumb').on( 'click', 'a', loadCategories );
});

function loadCategories() {
    // Hide any 'No sub categories' message and the list
    $('#no-sub-categories').hide();

    // Get the category ID and name
    var categoryID = $(this).attr('id').replace( /[^0-9]+/, '' );

    // Show the edit category as long as it's not the Main Categories
    if ( 0 == categoryID ) {
        $('#edit-delete-category').hide();
    } else {
        $('#edit-delete-category').show();
    }

    // Change the current selected category name
    $('#current-category span:first').text( $(this).text() );
    $('#current-category-id').val( categoryID );

    $.post( '/products/categories/get/', { _nonce : $('#_get_categories').val(), cid: categoryID }, ajaxResponse, 'json' );
}