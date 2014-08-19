var CategoryList = {

    template: null

    , init: function() {
        CategoryList.template = $('#category-template').clone().removeClass('hidden').removeAttr('id');
        $('#category-template').remove();

        $('#category').change( CategoryList.add );
        $('#category-list').on( 'click', '.remove', CategoryList.remove );

        // Sortable
        $( '#category-list' ).sortable({
            items		: '.category',
            cancel		: 'a',
            placeholder	: 'category-placeholder',
            forcePlaceholderSize : true,
            update: CategoryList.updateSequence
        });
    }

    , remove: function() {
        if ( !confirm('Are you sure do you want to remove this Category?') )
            return;

        $(this).parents('.category').remove();
        CategoryList.updateSequence();
    }

    , add: function() {
        var select = $(this);
        var name = select.find(':selected').text();
        var image = select.find(':selected').data('img');

        CategoryList.template.clone()
            .data( 'category-id', select.val() )
            .find('img').attr( 'src', image ).end()
            .find('h4').text( name ).end()
            .appendTo( '#category-list' );
        CategoryList.updateSequence();
    }

    , updateSequence: function() {
        var sequence = [];
        $( '#category-list .category' ).each( function(){
            sequence.push($( this ).data('category-id') );
        })

        $.post(
            '/products/update-top-category-sequence/'
            , { s: sequence.join('|'), _nonce: $('#_update_top_category_sequence' ).val() }
            , GSR.defaultAjaxResponse
        );
    }

}

jQuery( CategoryList.init );
