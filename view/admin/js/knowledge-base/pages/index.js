var PageList = {

    init: function() {

        $('body').on( 'click', '.delete-page', PageList.deletePage );

    }

    , deletePage: function(e) {
        if ( e ) e.preventDefault();

        var anchor = $(this);

        if ( !confirm( 'Are you sure you want to delete this Page?' ) )
            return;

        $.get(
            anchor.attr( 'href' )
            , function() {
                $('.dt:first').dataTable().fnDraw();
            }
        )
    }

}

jQuery( PageList.init );