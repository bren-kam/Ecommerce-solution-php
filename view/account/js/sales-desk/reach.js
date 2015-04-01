var ReachCommentForm = {

    template: null

    , init: function() {
        // Add Comment Form Events
        $('#comment').click( ReachCommentForm.showForm );
        if ( $('#comment').is( ':focus' ) )
            ReachCommentForm.showForm();

        // Elements we will hide on blur
        $('#add-comment-form .hidden')
            .hide()
            .removeClass('hidden')
            .filter(':not(#upload-loader)')
            .addClass('hide-on-blur');
        $('#comment').blur( ReachCommentForm.hideForm );

        // Add Comment
        $('#add-comment-form').submit( ReachCommentForm.add );
        ReachCommentForm.template = $('#comment-template').clone().removeClass('hidden').removeAttr('id');
        $('#comment-template').remove();

        // Delete Comment Events
        $('#comments').on( 'click', '.delete-comment', ReachCommentForm.delete );
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
            '/sales-desk/add-comment/'
            , form.serialize()
            , ReachCommentForm.addComplete
        )
        return false;
    }

    , addComplete: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var comment = ReachCommentForm.template.clone();
            comment.find('.template-contact-name').text( response.contact_name );
            comment.find('[data-assign-to]').attr( 'data-assign-to', response.user_id );
            comment.find('[data-comment-id]').attr( 'data-comment-id', response.id );
            comment.find('.comment-text').text( response.comment );

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

            comment.prependTo( '#comments' );

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
            '/sales-desk/delete-comment/'
            , { _nonce : $('#_delete_comment').val(), wrcid : commentId }
            , function( response ) {
                GSR.defaultAjaxResponse( response );

                if ( response.success )
                    anchor.parents('.msg-time-chat:first').remove();
            }
        );
    }

}

var ReachView = {

    init: function() {

        // Reach Settings Events
        $('#priority').change( ReachView.updatePriority );

        $('#status').change( ReachView.updateStatus );

        $('#assigned-to').change( ReachView.assignTo );
        $('#comments').on( 'click', '[data-assign-to]', ReachView.assignTo );

    }

    , updatePriority: function() {
        var reachPriority = $(this).val();
        $.post(
            '/sales-desk/update-priority/'
            , { _nonce : $('#_update_priority').val(), wrid : $('#hReachId').val(), priority : reachPriority }
            , function ( response ) {
                GSR.defaultAjaxResponse( response );

                // Add Priority label to Title
                $('#reach-title span').remove();
                if ( reachPriority == '1' ) {
                    $('#reach-title').append( '<span class="label label-warning">High priority</span>' );
                } else if ( reachPriority == '2' ) {
                    $('#reach-title').append( '<span class="label label-danger">URGENT</span>' );
                }
            }
        );
    }

    , updateStatus: function() {
        $.post(
            '/sales-desk/update-status/'
            , { _nonce : $('#_update_status').val(), wrid : $('#hReachId').val(), status : $(this).val() }
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
            '/sales-desk/update-assigned-to/'
            , { _nonce : $('#_update_assigned_to').val(), wrid : $('#hReachId').val(), auid : userId }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success )
                    $('#sAssignedTo').val( userId );
            }
        );
    }

}

jQuery( function() {
    ReachView.init();
    ReachCommentForm.init();
});