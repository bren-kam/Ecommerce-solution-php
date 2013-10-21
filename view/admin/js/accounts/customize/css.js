jQuery(function() {
    $('#save-css').click( function(e) {
        e.preventDefault();
        $.post( $(this).attr('href'), { _nonce : $('#_nonce').val(), css: editor.getValue() }, ajaxResponse, 'json' );
    });
});