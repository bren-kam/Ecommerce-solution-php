head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    // Add a category
    $('#add-category').click( function(e) {
        e.preventDefault();

        var sCategoryId = $('#sCategoryId'), category = sCategoryId.find('option:selected'), img = category.attr('data-img')
            , name = category.text().trim();

        if ( !img.length )
            img = 'http://placehold.it/200x200&text=' + name;

        // AJAX call to get the offer box
        $('#category-template')
            .clone()
                .show()
                .attr( 'id', 'dTopCategory_' + category.val() )
                .appendTo('#top-categories')
                    .find('img:last').attr( 'src', img )
                    .next() // H4
                        .text( name );

        updateCategorySequence();

        // Reset
        sCategoryId.val('');
    });

    // make them sortable
	$('#top-categories').sortable({
		items		: '.top-category',
		cancel		: 'a',
		placeholder	: 'top-category-placeholder',
		revert		: true,
		forcePlaceholderSize : true,
		update		: updateCategorySequence
	}).on( 'click', '.remove-category', function(e) { // Make them removable
        e.preventDefault();

        if ( !confirm( $(this).attr('data-confirm') ) )
            return;

        $(this).parents('.top-category:first').remove();
        updateCategorySequence();
    });
});

function updateCategorySequence() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123.
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#top-categories').sortable('serialize');

	// Use Sidebar's -- it's the same thing
	$.post( '/products/update-top-category-sequence/', { _nonce : $('#_update_top_category_sequence').val(), 's' : idList }, ajaxResponse, 'json' );
}