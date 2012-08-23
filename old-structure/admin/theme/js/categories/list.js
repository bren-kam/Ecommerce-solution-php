/**
 * Categories List Page
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
	// Load initial categories
	$.post( '/ajax/categories/get/', { '_nonce' : $('#_ajax_get_categories').val(), 'cid': 0 }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		// Get the categories
		var categories = response['categories'], html = '';
		
		// Get html
		for ( var i = 0; i < categories.length; i++) {
			var c = categories[i];
			html += categoryRow( c['category_id'], c['name'], 'Parent Category', c['image'], c['slug'], c['page_title'] );
		}
		
		// Set new HTML
		$("#dCategoriesList").html( html );
	}, 'json' );

	// Make Edit Category Dialog
	$('#aEditCategory').live( 'click', function() {
		loadEditCategory( $('#hCurrentCategoryID').val() );
	});

	// Make categories sortable
	$("#dCategoriesList").sortable({
		// When they move something, update data in the database
		update: function() {
			var categoriesList = $("#dCategoriesList").sortable('serialize'), parentCategoryID = $('#hCurrentCategoryID').val();
			
			$.post( '/ajax/categories/update-sequence/', { '_nonce' : $('#_ajax_update_category_sequence').val(), 'pcid': parentCategoryID, 'sequence' : categoriesList }, function( response ) {
				// Handle any errors
				if ( !response['result'] ) {
					alert( response['error'] );
					return;
				}
				
				refreshAllCategories();
			}, 'json' );
		},
		placeholder: 'category-placeholder'
	});
	
	// Make the added categories show/hide the edit/delete buttons
	$('#dAddCategory').live('click', function() {
		loadAddCategory( $("#hCurrentCategoryID").val() );
	});
		
	$('.category-actions a').live( 'click', function() {
		var categoryID = $(this).parent().parent().attr('id').replace( /cat_?/, '' );
		
		if ( 'Edit Category' == $(this).attr( 'title' ) ) {
			loadEditCategory( categoryID );
		} else if ( 'Delete Category' == $(this).attr( 'title') ) {
			if ( confirm( 'Are you sure you want to delete this category?' ) ) {
				var categoryID = $(this).parent().parent().attr( 'id' ).replace( /cat_?/, '' );
				deleteCategory( categoryID );
			}
		}
			
	});

	/**
	 * Creates the ability to load sub categories
	 */
	$('a.parent-category').live( 'click', function() {
		// Hide any 'No sub categories' message
		$('#pNoSubCategories').hide();
		$("#dCategoriesList").fadeOut('fast');

		// Get the category ID and name
		var categoryID = $(this).attr('id').replace( /ai?/, '' ), categoryName = $(this).text();

		loadCategories( categoryID, categoryName );
	});

	/**
	 * Creates the ability to load categories from bread crumb
	 */
	$('#dCategoryBreadCrumb a').live( 'click', function() {
		// Hide any 'No sub categories' message
		$('#pNoSubCategories').hide();
		$("#dCategoriesList").fadeOut();

		// Get the category ID and name
		var categoryID = $(this).attr('id').replace( /brd?/, '' ), categoryName = $(this).text();
		
		loadCategories( categoryID, categoryName );
	});
}

/**
 * Build Category Row
 *
 * Build a single HTML row of category from the given values
 *
 */
function categoryRow( categoryID, name, parentName, image, slug ) {
	var rootURL = $("#hRootURL").val();

	return '<div id="cat_' + categoryID + '" class="category"><h4><a href="javascript:;" title="' + name + '" id="a' + categoryID + '" class="parent-category">' + name + '</a> <span class="gray-small">(' + parentName + ')</span></h4><p class="category-actions"> <a href="javascript:;" title="Edit Category">Edit</a> | <a href="javascript:;"title="Delete Category">Delete</a></p><a target="_blank" id="aURL' + categoryID + '" class="url" target="_blank" title="View ' + name + '" href="http://www.' + rootURL + "/" + slug + '/">http://www.' + rootURL + '/' + slug + '</a></div>';
}

/**
 * Load Categories
 *
 * Load the categories from the given categoryID and categoryName
 *
 */
function loadCategories( categoryID, categoryName ) {
	// Show the edit category as long as it's not the Main Categories
	if ( 0 == categoryID ) {
		$('#smEditDeleteCategory').fadeOut();
	} else {
		$('#smEditDeleteCategory').fadeIn();
	}

	// Change the current selected category name
	$('#hCurrentCategory span').text( categoryName );
	$('#hCurrentCategoryID').val( categoryID );

	// Fade out the image and link
	$('#iCurrentCategory, #pCurrentURL').fadeOut( 500, function() {
		// Fade In the image and link
		$('#iCurrentCategory, #pCurrentURL').fadeIn();
	});
	
	var aURL = $('#aURL' + categoryID);
	if ( aURL.length ) {
		$('#pCurrentURL a').attr( 'href' , aURL.attr('href') ).html( aURL.html() );
	} else {
		$('#pCurrentURL a').attr( 'href' , 'javascript:;' ).html('');
	}
	
	// Make sure the Edit/Delete Category has the right category value
	$('#aEditCategory').attr( 'href', $('#aEditCategory').attr('href').replace( /hCategoryID=[^&]*/, 'hCategoryID=' + categoryID ) );
	$('#aDeleteCategory').attr( 'href', $('#aDeleteCategory').attr('href').replace( /hCategoryID=[^&]*/, 'hCategoryID=' + categoryID ) );

	// Do AJAX Call to get new categories
	$.post( '/ajax/categories/get/', { '_nonce' : $('#_ajax_get_categories').val(), 'cid': categoryID }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		// Load Parent Category
		var parentCategory = response['parent_category'], parentName = '';
		
		if ( null == parentCategory ) {
			$('#iCurrentCategory').attr( 'src', '/media/images/categories/categories-big.gif' );
			parentName = 'Parent Category';
		} else {
			$('#iCurrentCategory').attr( 'src', '/media/images/categories/' + parentCategory['category_id'] + '/' + parentCategory['image'] );
			parentName = parentCategory['name'];
		}
		
		// Get the Root Slug Value
		var breadCrumb = response['breadcrumb'], rootSlug = '';
		for (i = 0; i < breadCrumb.length - 1 ; i++) {
			rootSlug = breadCrumb[i]['slug'] + '/' + rootSlug;
		}

		// Load the Categories
		var categories = response['categories'], dCategoryBreadCrumb = $('#dCategoryBreadCrumb'), html = '';
		
		// Get html
		for ( var i = 0; i < categories.length; i++) {
			var c = categories[i];
			html += categoryRow( c['category_id'], c['name'], parentName, c['image'], rootSlug + '/' + c['slug'] );
		}
		
		$("#dCategoriesList").html( html ).fadeIn();
		
		if ( 0 == i )
			$('#pNoSubCategories').show();
		
		dCategoryBreadCrumb.empty();
		
		for ( i = 0; i < breadCrumb.length; i++ ) {
			if ( 0 != i ) {
				dCategoryBreadCrumb.prepend( '<a href="javascript:;" id="' + breadCrumb[i]['category_id'] + '">' + breadCrumb[i]['category_name'] + '</a>' );
			} else {
				dCategoryBreadCrumb.prepend( '<span>' + breadCrumb[i]['category_name'] + '</span>' );
			}
			
			if ( i != breadCrumb.length - 1 ) {
				dCategoryBreadCrumb.prepend( '<span> &raquo; </span>' );
			}
		}
		
		if ( null != parentCategory ) {
			var rootURL = $("#hRootURL").val();
			$('#hCurrentCategory span').text( parentCategory['name'] );
			$('#hCurrentCategoryID').val ( parentCategory['category_id'] );
			$('#pCurrentURL a').attr( 'href', "http://www." + rootURL + "/" + rootSlug ).html( "http://www." + $("#hRootURL").val() + "/" + rootSlug );
		}
	}, 'json' );
}

/**
 * Load Add Category
 */
function loadAddCategory( categoryID ) {
	dEditCategory = $("#dEditCategory");
	dEditCategory.empty().attr( 'title' , 'Add Category' );
	
	dEditCategory.dialog({
		modal: true,
		autoOpen: false,
		width: 400,
		draggable: false,
		resizable: false,
		cache: false
	});

	dEditCategory.load( '/ajax/categories/add/?cid=' + categoryID, function() {
		dEditCategory.dialog( 'open' );
	});
}

/**
 * Load Edit Category
 */
function loadEditCategory( categoryID ) {
	dEditCategory = $("#dEditCategory");

	dEditCategory.empty().attr( 'title' , 'Edit Category' );
	dEditCategory.dialog({
		modal: true,
		autoOpen: false,
		width: 400,
		draggable: false,
		resizable: false,
		cache: false
	});

	dEditCategory.load( '/ajax/categories/edit/?cid=' + categoryID, function() {
		dEditCategory.dialog('open');
	});
}

/**
 * Delete a category
 */
function deleteCategory( categoryID ) {
	$.post( '/ajax/categories/delete/', { '_nonce' : $('#_ajax_delete_category').val(), 'cid': categoryID }, function( response ) {
		// Handle any errors
		if ( !response['result'] ) {
			alert( response['error'] );
			return;
		}
		
		loadCategories( $("#hCurrentCategoryID").val(), $("#hCurrentCategory span").text() );
	}, 'json' );
}

/**
 * Refresh all categories
 */
function refreshAllCategories() {
	loadCategories( $("#hCurrentCategoryID").val(), $("#hCurrentCategory span").text() );
}