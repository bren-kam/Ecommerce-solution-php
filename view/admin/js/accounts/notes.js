var Notes = {

    init: function() {
        $('.delete-note').click( Notes.delete );
    }

    , delete: function(e) {
        e.preventDefault();
        if ( confirm( 'Do you want to delete this Note?' ) ) {
            var a = $(this);
            $.get( a.attr('href'), function(e) {
                if ( e.success ) {
                    a.parents('.panel:first').remove();
                }
            } );
        }
    }

}

jQuery(function(){
    Notes.init();
});