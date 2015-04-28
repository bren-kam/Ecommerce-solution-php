var CSS = {

    _editor: null

    , init: function() {
            if ( $('#core').size() > 0 )  {
            core = ace.edit("core");
            core.setReadOnly(true);
            core.setTheme("ace/theme/chrome");
            core.getSession().setMode("ace/mode/less");
        }

        CSS._editor = ace.edit("editor");
        CSS._editor.setTheme("ace/theme/chrome");
        CSS._editor.getSession().setMode("ace/mode/less");

        $('#save-less').click( CSS.saveLess );
    }

    , saveLess: function(e) {
        e.preventDefault();
        $.post(
            $(this).attr('href')
            , { _nonce : $('#_nonce').val(), less: CSS._editor.getValue().replace( "\\f", "\\\\f" ) }
            , GSR.defaultAjaxResponse
        );
    }
}

jQuery( CSS.init );