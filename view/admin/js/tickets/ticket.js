var TicketCommentForm = {

    uploader: null

    , template: null

    , init: function() {
        // Add Comment Form Events
        $('#comment').click( TicketCommentForm.showForm );
        if ( $('#comment').is( ':focus' ) )
            TicketCommentForm.showForm();

        // Elements we will hide on blur
        $('#add-comment-form .hidden')
            .hide()
            .removeClass('hidden')
            .filter(':not(#upload-loader)')
            .addClass('hide-on-blur');
        $('#comment').blur( TicketCommentForm.hideForm );

        // Add Comment
        $('#add-comment-form').submit( TicketCommentForm.add );
        TicketCommentForm.template = $('#comment-template').clone().removeClass('hidden');
        $('#comment-template').remove();

        // Delete Comment Events
        $('#ticket-comments').on( 'click', '.delete-comment', TicketCommentForm.delete );

        // Comment Attachments Uploader
        TicketCommentForm.uploader = new qq.FileUploader({
            action: '/tickets/upload-to-comment/'
            , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt']
            , element: $('#upload-files')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: TicketCommentForm.uploadSubmit
            , onComplete: TicketCommentForm.uploadComplete
        });
        // Upload file trigger
        $('#upload').click( TicketCommentForm.selectFile );
        $('body').on('click', '.delete-file', TicketCommentForm.deleteFile );
    }

    , showForm: function() {
        var textarea = $('#comment');

        if ( textarea.val() == '' ) {
            textarea.attr( 'rows', '3' );
            $('#add-comment-form .hide-on-blur').show();
        }
    }

    , hideForm: function() {
        var textarea = $('#comment');

        if ( textarea.val() == '' ) {
            textarea.attr( 'rows', '1' );
            $('#add-comment-form .hide-on-blur').hide();
        }
    }

    , add: function() {
        var form = $('#add-comment-form');
        $.post(
            '/tickets/add-comment/'
            , form.serialize()
            , TicketCommentForm.addComplete
        )
        return false;
    }

    , addComplete: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var comment = TicketCommentForm.template.clone();
            comment.find('.template-contact-name').text( response.contact_name );
            comment.find('[data-assign-to]').attr( 'data-assign-to', response.user_id );
            comment.find('[data-comment-id]').attr( 'data-comment-id', response.id );
            comment.find('.comment-text').html( response.comment );

            if ( response.private == 0 )
                comment.find('.template-private-comment').remove();

            if ( response.uploads ) {
                var uploads_container = comment.find('.comment-attachments');
                uploads_container.removeClass('hidden');
                for ( i in response.uploads ) {
                    var upload = response.uploads[i];
                    $('<li />').append(
                        $('<a />')
                            .attr( 'href', upload.link )
                            .text( upload.name )
                    ).appendTo( uploads_container );
                }
            }

            comment.prependTo( '#ticket-comments' );

            // Reset Form
            $('#add-comment-form')
                .find('input:text, textarea').val('').blur().end()
                .find('input:checkbox').prop( 'checked', false).end()
                .find('#file-list').empty();
        }
    }

    , delete: function () {
        var anchor = $(this);
        var commentId = anchor.data('comment-id');

        if ( !commentId )
            return;

        if ( !confirm( 'Do you really want to delete this comment?' ) )
            return;

        $.post(
            '/tickets/delete-comment/'
            , { _nonce : $('#_delete_comment').val(), tcid : commentId }
            , function( response ) {
                GSR.defaultAjaxResponse( response );

                if ( response.success )
                    anchor.parents('.msg-time-chat:first').remove();
            }
        );
    }

    , uploadSubmit: function() {
        TicketCommentForm.uploader.setParams({
            _nonce : $( '#_upload_to_comment' ).val()
            , tid : $( '#hTicketId').val()
        });

        $('#upload').hide();
        $('#upload-loader').show();
    }

    , selectFile: function() {
        if ( $.support.cors ) {
            $('#upload-files input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

    , uploadComplete: function( id, filename, response) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var fileItem = $('<li/>');

            $('<a />')
                .attr( 'href', response.url )
                .attr( 'target', '_blank' )
                .text( filename )
                .appendTo( fileItem );

            $('<a />')
                .addClass( 'delete-file' )
                .attr( 'href', 'javascript:;' )
                .attr( 'title', 'Delete this file' )
                .html('&nbsp;<i class="fa fa-trash-o"></i>')
                .appendTo( fileItem );

            $('<input />')
                .attr( 'type', 'hidden' )
                .attr( 'name', 'uploads[]' )
                .val( response.id )
                .appendTo( fileItem );

            fileItem.appendTo( '#file-list' );
        }

        $('#upload').show();
        $('#upload-loader').hide();
    }

    , deleteFile: function() {
        if ( !confirm( 'Do you really want to remove this file from the comment?' ) )
            return;

        $(this).parents('li:first').remove();
    }

}

var TicketView = {

    init: function() {

        // Ticket Settings Events
        $('#sPriority').change( TicketView.updatePriority );

        $('#sStatus').change( TicketView.updateStatus );

        $('#sAssignedTo').change( TicketView.assignTo );
        $('body').on( 'click', '[data-assign-to]', TicketView.assignTo );

    }

    , updatePriority: function() {
        var ticketPriority = $(this).val();
        $.post(
            '/tickets/update-priority/'
            , { _nonce : $('#_update_priority').val(), tid : $('#hTicketId').val(), priority : ticketPriority }
            , function ( response ) {
                GSR.defaultAjaxResponse( response );

                // Add Priority label to Title
                $('#ticket-title span').remove();
                if ( ticketPriority == '1' ) {
                    $('#ticket-title').append( '<span class="label label-warning">High priority</span>' );
                } else if ( ticketPriority == '2' ) {
                    $('#ticket-title').append( '<span class="label label-danger">URGENT TICKET</span>' );
                }
            }
        );
    }

    , updateStatus: function() {
        $.post(
            '/tickets/update-status/'
            , { _nonce : $('#_update_status').val(), tid : $('#hTicketId').val(), status : $(this).val() }
            , GSR.defaultAjaxResponse
        );
    }

    , assignTo: function() {
        // First Attempt: <select> value
        var userId = $(this).val();
        // Second Attempt: <a> data-assign-to attribute
        if ( !userId )
            userId = $(this).data('assign-to');

        if ( !userId )
            return;

        $.post(
            '/tickets/update-assigned-to/'
            , { _nonce : $('#_update_assigned_to').val(), tid : $('#hTicketId').val(), auid : userId }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success )
                    $('#sAssignedTo').val( userId );
            }
        );
    }

}

jQuery( function() {
    TicketView.init();
    TicketCommentForm.init();
});