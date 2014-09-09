var LESS = {

    _editor: null

    , init: function() {
        LESS._editor = ace.edit("editor");
        LESS._editor.setTheme("ace/theme/chrome");
        LESS._editor.getSession().setMode("ace/mode/less");

        $('#save-less').click( LESS.saveLess );
    }

    , saveLess: function(e) {
        e.preventDefault();
        $.post(
            $(this).attr('href')
            , { _nonce : $('#_nonce').val(), less: LESS._editor.getValue() }
            , GSR.defaultAjaxResponse
        );
    }
}

jQuery( LESS.init );
