var PageForm = {

    init: function() {

        $('#tTitle').change( function() {
            if ( $('#tSlug').val() == '' ) {
                $('#tSlug').val( $('#tTitle').val().slug() );
            }
        } );

    }

};
jQuery( PageForm.init );


