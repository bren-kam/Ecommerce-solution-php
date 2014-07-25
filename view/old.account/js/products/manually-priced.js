head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {

    $('.manually-priced-remove').click( function(e) {
        e.preventDefault();
        var anchor = $(this);
        if ( confirm('Do you want to remove this product from the list and allow Auto Price tool to edit this Product.') ) {
            $.get(
                $(this).attr('href')
                , { _nonce: $('#_nonce').val() }
                , function( response )  {
                    ajaxResponse( response );
                    anchor.parents( 'tr' ).fadeOut();
                }
            );
        }
    });

    $("#remove-all").click( function() {
        return confirm("Are you sure you want to remove all products from this list? This will allow Auto Price tool to modify their values.");
    });

    $("#lock-all-products").click( function() {
        return confirm("Are you sure to want to lock all Product prices? They won't be updated by the Auto Price tool.");
    });

});
