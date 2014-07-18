var ArticleForm = {

    init: function() {
        $('#sSection').change( ArticleForm.getCategories );
        $('#sCategory').change( ArticleForm.getPages );
        $('#tTitle').change( ArticleForm.setSlug );
    }

    , getCategories: function() {
        $.post(
            '/knowledge-base/articles/get-categories/'
            , { _nonce : $('#_get_categories').val(), s : $(this).val() }
            , ArticleForm.getCategoriesResponse
        );
    }

    , getCategoriesResponse: function( response ) {
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

    , getPages: function() {
        $.post(
            '/knowledge-base/articles/get-pages/'
            , { _nonce : $('#_get_pages').val(), kbcid : $(this).val() }
            , ArticleForm.getPagesResponse
        );
    }

    , getPagesResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {

            $('#sPage').empty();

            for ( i in response.pages ) {
                var page = response.pages[i];
                $('<option />')
                    .val( i )
                    .html( page )
                    .appendTo('#sPage');
            }
        }
    }

    , setSlug: function() {
        var slugInput = $('#tSlug');
        if ( slugInput.val() == '' ) {
            slugInput.val($('#tTitle').val().slug());
        }
    }

}

jQuery( ArticleForm.init );