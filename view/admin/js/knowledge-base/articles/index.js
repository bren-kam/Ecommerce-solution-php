var ArticlesList = {

    init: function() {

        $('body').on( 'click', '.delete-article', ArticlesList.deleteArticle );

    }

    , deleteArticle: function(e) {
        if ( e ) e.preventDefault();

        var anchor = $(this);

        if ( !confirm( 'Are you sure you want to delete this Article?' ) )
            return;

        $.get(
            anchor.attr( 'href' )
            , function() {
                $('.dt:first').dataTable().fnDraw();
            }
        )
    }

}

jQuery( ArticlesList.init );