var AnalyticsOAuth2 = {

    init: function() {
        $('#login-popup-link').click( AnalyticsOAuth2.openPopup );
    }

    , openPopup: function(e) {
        if ( e ) e.preventDefault();
        window.open( $(this).attr('href'), 'login-popup', "width=640,height=480,scrollbars=no" );
    }

}

jQuery( AnalyticsOAuth2.init );