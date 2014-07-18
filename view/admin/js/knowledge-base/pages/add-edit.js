var PageForm = {

    init: function() {
        $('#sSection').change( PageForm.getCategories );
    }

    , getCategories: function() {
        $.post(
            '/knowledge-base/pages/get-categories/'
            , { _nonce : $('#_get_categories').val(), s : $(this).val() }
            , PageForm.getCategoriesComplete
        );
    }

    , getCategoriesComplete: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {

            $('#sCategory').empty();

            for ( i in response.categories ) {
                var category = response.categories[i];
                $('<option />')
                    .val( i )
                    .html( category )
                    .appendTo('#sCategory');
            }

        }
    }

}

jQuery( PageForm.init );