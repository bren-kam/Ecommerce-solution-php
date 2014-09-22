var PasswordRecovery = {

    init: function() {
        $( '#forgot-password' ).submit( PasswordRecovery.submit );
    }

    , submit: function(e) {
        var form = $( '#forgot-password' );
        e.preventDefault();
        $.post(
            form.attr('action')
            , form.serialize()
            , PasswordRecovery.submitResponse
        )
    }

    , submitResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        $('.modal').modal('hide');
    }

}

jQuery( PasswordRecovery.init );
