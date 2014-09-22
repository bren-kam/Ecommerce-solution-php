var ArticleView = {

    init: function() {
        $('#helpful a.rate').click( ArticleView.rate );
    }

    , rate: function(e) {
        var anchor = $(this);

        if (e) e.preventDefault();

        $.get(
            anchor.attr( 'href' )
            , ArticleView.rateResponse
        );
    }

    , rateResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            $('#helpful .alert' ).text('Thank you for your feedback!');
        }
    }

}

jQuery( ArticleView.init );