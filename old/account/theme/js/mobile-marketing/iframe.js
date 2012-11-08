// When the page has loaded
jQuery(function($) {
	var iframe = $('#iframe'), iframeContents = iframe.contents();

    var id = $('#id', iframeContents);

    if ( id.is('input') ) {
        id.val( $('#trumpia-username').val() );
        $('#password', iframeContents).val( $('#trumpia-password').val() );
        $('#loginForm').submit();
    }
});