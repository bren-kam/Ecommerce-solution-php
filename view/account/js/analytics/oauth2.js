var AnalyticsOAuth2 = {

    init: function() {
        $('#login-popup-link').click( AnalyticsOAuth2.openPopup );

        $('#show-step-2-long').click( function() {
            $('#step-2-long').removeClass('hidden').show();
            $('#step-2-short').hide();
        } );

        $('#show-step-2-short').click( function() {
            $('#step-2-short').removeClass('hidden').show();
            $('#step-2-long').hide();
        } );
    }

    , openPopup: function(e) {
        if ( e ) e.preventDefault();
        window.open( $(this).attr('href'), 'login-popup', "width=640,height=480,scrollbars=no" );
    }

}

jQuery( AnalyticsOAuth2.init );