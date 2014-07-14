// When the page has loaded
var Checklist = {

    init: function() {

        $('.checklist').on( 'click', 'input[type=checkbox]', Checklist.updateStatus );

        $('body').on( 'click', '.delete-note', Checklist.deleteNote );
        $('body').on( 'submit', '#fNewNote', Checklist.addNote );

    }

    , updateStatus: function() {
        $.post(
            '/checklists/update-item/'
            , { _nonce: $('#_update_item').val(), cwiid : $(this).val(), checked : $(this).is(':checked') }
            , Checklist.updateStatusResponse
        );
    }

    , updateStatusResponse: function( response ) {
        GSR.defaultAjaxResponse( response );

        console.log('got' + response.success && response.cwiid );
        if ( response.success && response.cwiid ) {

            var checkbox = $('input[value=' + response.cwiid + ']');
            var itemStatus = $('input[value=' + response.cwiid + ']').parents('span:first');

            console.log( 'input[value=' + response.cwiid + ']' );
            itemStatus.removeClass( 'label-default' ).removeClass( 'label-success' );

            if ( response.checked ) {
                itemStatus.addClass( 'label-success' );
                itemStatus.text( 'Done').prepend( checkbox );
            } else {
                itemStatus.addClass( 'label-default' );
                itemStatus.text( 'Pending' ).prepend( checkbox );
            }
        }
    }

    , deleteNote: function(e) {
        if (e) e.preventDefault();

        if ( !confirm( 'Do you want to remove this Note?' ) )
            return;

        $.get(
            $(this).attr( 'href' )
            , Checklist.deleteNodeResponse
        )
    }

    , deleteNodeResponse: function(response) {
        if ( response.success && response.cwinid ){
            // Remove note
            $('[data-note-id=' + response.cwinid + ']').remove();
        }
    }

    , addNote: function(e) {
        if (e) e.preventDefault();

        var form = $(this);

        if ( $.trim(form.find('textarea').val()) == '' )
            return;

        $.post(
            form.attr('action')
            , form.serialize()
            , Checklist.addNoteResponse
        );
    }

    , addNoteResponse: function(response) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            // Increase note count by 1
            var noteStatus = $('li[data-checklist-item-id='+ response.cwiid +'] .notes');
            noteCount = parseInt( noteStatus.text().split(' ')[0] ) + 1;
            noteStatus.text( noteCount + ' notes' );

            // Hide modal
            $('.modal:first').modal('hide');
        }
    }

}
jQuery( Checklist.init );