jQuery(function() {
    $('#save-less').click( function(e) {
        e.preventDefault();
        console.log(editor.getValue());
        $.post( $(this).attr('href'), { _nonce : $('#_nonce').val(), less: editor.getValue() }, ajaxResponse, 'json' );
    });
});