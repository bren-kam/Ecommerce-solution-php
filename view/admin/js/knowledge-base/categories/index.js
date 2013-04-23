// When the page has loaded
jQuery(function($) {
    // Load initial categories
	$.post( '/knowledge-base/categories/get/', { _nonce : $('#_get_categories').val(), cid: 0 }, ajaxResponse, 'json' );

	/**
	 * Creates the ability to load sub categories
	 */
	$('#categories-list').on( 'click', 'a.parent-category', loadCategories );
	$('#breadcrumb').on( 'click', 'a', loadCategories );
});

function loadCategories() {
    // Get the category ID and name
    var kbCategoryID = $(this).attr('id').replace( /[^0-9]+/, '' );

    $.post( '/knowledge-base/categories/get/', { _nonce : $('#_get_categories').val(), kbcid: kbCategoryID }, ajaxResponse, 'json' );
}