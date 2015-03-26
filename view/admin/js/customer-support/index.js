var InboxNavigation = {

    template: null
    , container: null

    , init: function() {
        InboxNavigation.container = $('#inbox-nav');
        InboxNavigation.template = $('#inbox-nav-template').clone().removeClass('hidden').removeAttr('id');
        $('#inbox-nav-template').remove();

        $('#search').keyup( InboxNavigation.getTickets );
        InboxNavigation.getTickets();

        $('#filter-status').change(InboxNavigation.getTickets);
        $('#filter-assigned-to').change(InboxNavigation.getTickets);

        // Event when selecting a Ticket
        InboxNavigation.container.on('click', '.show-ticket', function() {
            var ticketId = $(this).parents('.inbox-nav-item:first').data('ticket-id');
            if ( ticketId ) {
                Ticket.show(ticketId);
            }
        });

        $('#compose').click(function(){
            NewTicketForm.reset();
            NewTicketForm.container.removeClass('hidden').show();
            Ticket.container.hide();
        });
    }

    , getTickets: function() {
        InboxNavigation.container.css('opacity', '0.6');
        $.get(
            '/customer-support/list-all/'
            , {
                _nonce: $('#list-all-nonce').val()
                , search: $('#search').val()
                , status: $('#filter-status').val()
                , "assigned-to": $('#filter-assigned-to').val()
            }
            , InboxNavigation.loadTickets
        );
    }

    , loadTickets: function(response) {
        GSR.defaultAjaxResponse( response );
        InboxNavigation.container.css('opacity', '1');
        if (response.success) {
            InboxNavigation.container.empty();
            for (i in response.tickets) {
                var ticket = response.tickets[i];
                var item = InboxNavigation.template.clone();
                item.data('ticket-id', ticket.id);
                item.find('.checkbox').val(ticket.id);
                item.find('.email-name').text(ticket.user_name);
                item.find('.email-address').text('<' + ticket.user_email + '>');
                item.find('.email-subject').text(ticket.summary);
                item.find('.email-preview').text(ticket.intro_text);
                item.find('.email-date').text(ticket.date_created);
                if ( ticket.priority == 2 ) {  // Urgent
                    item.find('.email-date').append(' <i class="fa fa-circle ticket-urgent"></i > ');
                } else if ( ticket.priority == 1 ) {  // High Priority
                    item.find('.email-date').append(' <i class="fa fa-circle ticket-high"></i> ');
                } else if ( ticket.priority == 0 ) {  // Low Priority
                    item.find('.email-date').append(' <i class="fa fa-circle ticket-open"></i> ');
                }

                InboxNavigation.container.append(item);
            }
        }
    }

};

var Ticket = {

    container: null
    , commentsContainer: null
    , commentTemplate: null

    , init: function() {
        Ticket.container = $('#ticket-container');
        Ticket.commentsContainer = Ticket.container.find('#ticket-comments');
        Ticket.commentTemplate = Ticket.container.find('#ticket-comment-template').clone().removeClass('hidden').removeAttr('id');

        // $('#assign-to').change(Ticket.assignTo);
        $(Ticket.container).on('click', '.selectpicker li a', Ticket.assignTo);
    }

    , show: function(ticketId) {
        Ticket.container.css('opacity', '0.6');
        NewTicketForm.container.hide();
        Ticket.container.removeClass('hidden').show();
        $.get(
            '/customer-support/get/'
            , {
                _nonce: $('#get-nonce').val()
                , id: ticketId
            }
            , Ticket.load
        );
    }

    , reload: function() {
        Ticket.show(
            Ticket.container.data('ticket-id')
        );
    }

    , load: function(response) {
        GSR.defaultAjaxResponse(response);
        Ticket.container.css('opacity', '1');

        if (response.success) {
            var currentTicket = response.ticket;

            // Ticket --
            var statusText = 'Open';
            if (currentTicket.status == 1) {
                statusText = 'Closed';
            }

            var priorityText = '';
            if (currentTicket.priority == 2) {
                priorityText = 'Urgent';
            } else if (currentTicket.priority == 1) {
                priorityText = 'High Priority'
            } else if (currentTicket.priority == 0) {
                priorityText = 'Low'
            }

            Ticket.container.data('ticket-id', currentTicket.id);
            Ticket.container.find('#ticket-id').val(currentTicket.id);  // For Comment Form
            Ticket.container.find('#assign-to').selectpicker('val', currentTicket.assigned_to_user_id);
            Ticket.container.find('.ticket-status').text(statusText + (priorityText ? ' - ' + priorityText : ''));
            if ( currentTicket.priority == 2 ) {  // Urgent
                Ticket.container.find('.ticket-status').prepend('<i class="fa fa-circle ticket-urgent"></i> ');
            } else if ( currentTicket.priority == 1 ) {  // High Priority
                Ticket.container.find('.ticket-status').prepend('<i class="fa fa-circle ticket-high"></i> ');
            } else if ( currentTicket.status == 0 ) {  // Open
                Ticket.container.find('.ticket-status').prepend('<i class="fa fa-circle ticket-open"></i> ');
            }

            Ticket.container.find('.ticket-summary').text(currentTicket.summary);
            Ticket.container.find('.ticket-user-name').text(currentTicket.name);
            if ( currentTicket.website ) {
                Ticket.container.find('.ticket-user-name').append(' - ' + currentTicket.website);
            }
            Ticket.container.find('.ticket-user-email').text('<' + currentTicket.email + '>');
            Ticket.container.find('.ticket-updated').text(currentTicket.updated_ago);
            Ticket.container.find('.ticket-created').text(currentTicket.created_ago);
            Ticket.container.find('.ticket-account').text(currentTicket.website);
            if ( currentTicket.website ) {
                Ticket.container.find('.edit-account').attr('href', '/accounts/edit/?aid=' + currentTicket.website_id);
                Ticket.container.find('.control-account').attr('href', '/accounts/control/?aid=' + currentTicket.website_id);
                Ticket.container.find('.edit-account').parents('li').show();
            } else {
                Ticket.container.find('.edit-account').parents('li').hide();
            }

            Ticket.container.find('.ticket-message').html(currentTicket.message);

            // Ticket Attachments --
            if ( currentTicket.uploads ) {
                for ( i in currentTicket.uploads ) {
                    var upload = currentTicket.uploads[i];
                    $('#ticket-attachments').append(
                        $('<li />').append(
                            $('<a />')
                                .attr('href', upload.link)
                                .attr('target', '_blank')
                                .text(upload.name)
                        )
                    );
                }
            }

            // Comments --
            Ticket.commentsContainer.empty();
            var comments = response.comments;
            for ( i in comments ) {
                var comment = comments[i];
                var item = Ticket.commentTemplate.clone();
                item.data('ticket-comment-id', comment.id);
                item.find('.comment-user-name').text(comment.name);
                if ( comment.private == 1 ) {
                    item.find('.comment-user-name').prepend('<i class="fa fa-lock" title="This is a Note/Private Comment!"></i> ');
                }
                item.find('.comment-user-email').text('<' + comment.email + '>');
                item.find('.comment-created-ago').text(comment.created_ago);
                item.find('.comment-message').html(comment.comment);

                // Comment Attachments --
                if ( comment.uploads ) {
                    for ( i in comment.uploads ) {
                        var upload = comment.uploads[i];
                        item.find('.comment-attachments').append(
                            $('<li />').append(
                                $('<a />')
                                    .attr('href', upload.link)
                                    .attr('target', '_blank')
                                    .text(upload.name)
                            )
                        );
                    }
                }

                Ticket.commentsContainer.append(item);
            }

        }
    }

    , assignTo: function() {
        // First Attempt: <select> value
        var userId = $('#assign-to').val();

        $.post(
            '/customer-support/update-assigned-to/'
            , {
                _nonce : $('#update-assigned-to-nonce').val()
                , tid : Ticket.container.data('ticket-id')
                , auid : userId
            }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    InboxNavigation.getTickets();
                }
            }
        );

    }


};

var TicketCommentForm = {

    uploader: null

    , init: function() {
        // Add Comment
        $('#send-comment-form').submit( TicketCommentForm.add );

        // // Delete Comment Events
        // $('#ticket-comments').on( 'click', '.delete-comment', TicketCommentForm.delete );

        // Comment Attachments Uploader
        TicketCommentForm.uploader = new qq.FileUploader({
            action: '/customer-support/upload-to-comment/'
            , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt']
            , element: $('#upload-files')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: TicketCommentForm.uploadSubmit
            , onComplete: TicketCommentForm.uploadComplete
        });

        // Upload file trigger
        $('#upload').click( TicketCommentForm.selectFile );
        $('#ticket-container').on('click', '.delete-file', TicketCommentForm.deleteFile );
    }

    , add: function() {
        var form = $('#add-comment-form');
        $.post(
            '/customer-support/add-comment/'
            , form.serialize()
            , TicketCommentForm.addComplete
        )
        return false;
    }

    , addComplete: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            Ticket.reload();
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
            _nonce : $( '#upload-to-comment-nonce' ).val()
            , tid : $( '#ticket-id').val()
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
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

};

var NewTicketForm = {

    container: null
    , uploader: null

    , init: function() {
        // Add Comment
        $('#new-ticket-form').submit( NewTicketForm.add );

        // Comment Attachments Uploader
        NewTicketForm.uploader = new qq.FileUploader({
            action: '/customer-support/upload-to-ticket/'
            , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt']
            , element: $('#new-ticket-upload-files')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: NewTicketForm.uploadSubmit
            , onComplete: NewTicketForm.uploadComplete
        });

        // Upload file trigger
        $('#new-ticket-upload').click( NewTicketForm.selectFile );
        $('#create-ticket').on('click', '.delete-file', NewTicketForm.deleteFile );

        // Autocomplete
        NewTicketForm.setupAutocomplete();

        NewTicketForm.container = $('#create-ticket');
    }

    , add: function(e) {
        var form = $('#new-ticket-form');
        $.post(
            '/customer-support/create-ticket/'
            , form.serialize()
            , NewTicketForm.addComplete
        )
        e.preventDefault();
    }

    , addComplete: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            InboxNavigation.getTickets();  // update ticket list
            if ( response.id ) {
                Ticket.show( response.id );  // load ticket
            }
        }
    }
    , uploadSubmit: function() {
        NewTicketForm.uploader.setParams({
            _nonce : $( '#upload-to-ticket-nonce' ).val()
            , tid : $( '#new-ticket-id').val()
        });

        $('#new-ticket-upload').hide();
        $('#new-ticket-upload-loader').removeClass('hidden').show();
    }

    , selectFile: function() {
        if ( $.support.cors ) {
            $('#new-ticket-upload-files input:first').click();
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

            if ( response.ticket_id ) {
                $('#new-ticket-id').val( response.ticket_id );
            }

            fileItem.appendTo( '#new-ticket-file-list' );
        }

        $('#new-ticket-upload').show();
        $('#new-ticket-upload-loader').hide();
    }

    , deleteFile: function() {
        if ( !confirm( 'Do you really want to remove this file from the comment?' ) )
            return;

        $(this).parents('li:first').remove();
    }

    , setupAutocomplete: function() {

        var nonce = $('#get-emails-autocomplete').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/customer-support/get-emails/?_nonce=' + nonce + '&term=%QUERY'
                , filter: function( list ) {
                    return list.objects
                }
            }
        });

        autocomplete.initialize();
        $("#to")
            .typeahead('destroy')
            .typeahead(null, {
                source: autocomplete.ttAdapter()
                , displayKey: "email"
                , templates: {
                    empty: [
                        '<div class="empty-message">',
                        'Unable to find any User with that email',
                        '</div>'
                    ].join('\n'),
                    suggestion: Handlebars.compile('<p><strong>{{contact_name}}</strong> &lt;{{email}}&gt; - {{main_website}}</p>')
                }
            })
            .unbind('typeahead:selected')
            .on('typeahead:selected', NewTicketForm.selectUser );
    }

    , reset: function() {
        var form = $('#new-ticket-form');
        form[0].reset();
        form.find('#new-ticket-id').val('');
    }


}


jQuery(InboxNavigation.init);
jQuery(Ticket.init);
jQuery(TicketCommentForm.init);
jQuery(NewTicketForm.init);