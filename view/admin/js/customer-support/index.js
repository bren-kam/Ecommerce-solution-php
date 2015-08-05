var InboxNavigation = {

    template: null
    , container: null

    , init: function() {
        InboxNavigation.container = $('#inbox-nav');
        InboxNavigation.template = $('#inbox-nav-template').clone().removeClass('hidden').removeAttr('id');
        $('#inbox-nav-template').remove();

        $('#search').keyup( InboxNavigation.getTickets );
        $('#filter-status').change(InboxNavigation.getTickets);
        $('#filter-assigned-to').change(InboxNavigation.getTickets);
        $('#filter-account').change(InboxNavigation.getTickets);

        InboxNavigation.getTickets();

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

            // Adapt Ticket list height
            var height = $('.lg-side:visible .inbox-head').height() + $('.lg-side:visible .inbox-body').height();
            $('.inbox-nav').height(height > 700 ? height : 700);
        });

        $('#refresh').click(InboxNavigation.getTickets);

        setInterval(InboxNavigation.getTickets, 30000);

        var hashTicketId = window.location.hash;
        if (hashTicketId.indexOf('!tid=') >= 0 ) {
            ticketId = hashTicketId.substr(6);
            Ticket.show(ticketId);
        }
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
                , account: $('#filter-account').val()
            }
            , InboxNavigation.loadTickets
        );
    }

    , loadTickets: function(response) {
        GSR.defaultAjaxResponse( response );
        InboxNavigation.container.css('opacity', '1');
        if (response.success) {
            var currentUserId = $('#filter-assigned-to').val();
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

                if ( ticket.assigned_to_user_id == currentUserId) {
                    if ( ticket.status == 0 ) { // Open
                        item.find('.email-status').text('Open');
                    } else if ( ticket.status == 1 ) { // Closed
                        item.find('.email-status').text('Closed');
                    } else if ( ticket.status == 2 ) { // In Progress
                        item.find('.email-status').text('In Progress');
                    }
                } else {
                    if ( ticket.status == 0 ) { // Open
                        item.find('.email-status').text('Awaiting Response');
                    } else if ( ticket.status == 1 ) { // Closed
                        item.find('.email-status').text('Closed');
                    } else if ( ticket.status == 2 ) { // In Progress
                        item.find('.email-status').text('Awaiting Response');
                    }

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
    , currentTicket: null

    , init: function() {
        Ticket.container = $('#ticket-container');
        Ticket.commentsContainer = Ticket.container.find('#ticket-comments');
        Ticket.commentTemplate = Ticket.container.find('#ticket-comment-template').clone().removeClass('hidden').removeAttr('id');

        // $('#assign-to').change(Ticket.assignTo);
        $(Ticket.container.find('.assign-to-container')).on('click', '.selectpicker li a', Ticket.assignTo);
        $(Ticket.container.find('.assign-to-user')).on('click', Ticket.assignTo);
        $(Ticket.container.find('.change-status-container')).on('click', '.selectpicker li a', Ticket.changeStatus);
        $(Ticket.container.find('.change-priority-container')).on('click', '.selectpicker li a', Ticket.changePriority);
        $(Ticket.container.find('.attach-to-account-container')).on('click', '.selectpicker li a', Ticket.attachUserToAccount);

        $('#to-address').tooltip({'trigger':'focus', 'title': 'Changing this will update the Ticket Primary Contact'});

        Ticket.container.find('#ticket-comments').on('click', '.comment-assign-to', Ticket.assignTo);

        Ticket.container.find('#ticket-summary').blur(Ticket.updateSummary);
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
            Ticket.currentTicket = currentTicket;

            window.location.hash = '!tid=' + currentTicket.id;

            // Ticket --
            var statusText = 'Open';
            if (currentTicket.status == 1) {
                statusText = 'Closed';
            }

            Ticket.container.data('ticket-id', currentTicket.id);
            Ticket.container.find('#ticket-id').val(currentTicket.id);  // For Comment Form
            if ( currentTicket.priority == 2 ) {  // Urgent
                Ticket.container.find('.ticket-priority').html('<i class="fa fa-circle ticket-urgent" title="Urgent"></i> ');
            } else if ( currentTicket.priority == 1 ) {  // High Priority
                Ticket.container.find('.ticket-priority').html('<i class="fa fa-circle ticket-high" title="High Priority"></i> ');
            } else if ( currentTicket.priority == 0 ) {  // Open
                Ticket.container.find('.ticket-priority').html('<i class="fa fa-circle ticket-open" title="Low Priority"></i> ');
            }

            // we need to reset it as loading from hashbang fails
            Ticket.container.find('#assign-to').selectpicker('val', 0);
            Ticket.container.find('#change-status').selectpicker('val', 0);
            Ticket.container.find('#change-priority').selectpicker('val', 0);
            Ticket.container.find('#assign-to').selectpicker('val', currentTicket.assigned_to_user_id);
            Ticket.container.find('#change-status').selectpicker('val', currentTicket.status);
            Ticket.container.find('#change-priority').selectpicker('val', currentTicket.priority);
            if ( currentTicket.user_has_account ) {
                Ticket.container.find('.attach-to-account-container').hide();
            } else {
                Ticket.container.find('.attach-to-account-container').show();
                Ticket.container.find('#attach-to-account').selectpicker('val', '');
            }


            Ticket.container.find('#ticket-summary').val(currentTicket.summary);
            Ticket.container.find('.ticket-user-name').text(currentTicket.name);
            if ( currentTicket.website && currentTicket.user_role < 7 ) {
                Ticket.container.find('.assign-to-user').data('assign-to', null).css('color', '#333');
                Ticket.container.find('.ticket-user-name').append(' - ' + currentTicket.website);
            } else {
                Ticket.container.find('.assign-to-user').data('assign-to', currentTicket.user_id).removeAttr('style');
            }
            Ticket.container.find('.ticket-user-email').text('<' + currentTicket.email + '>');
            Ticket.container.find('.ticket-user-edit').attr('href', '/users/add-edit/?uid=' + currentTicket.user_id);
            Ticket.container.find('.ticket-id').text(currentTicket.id);
            Ticket.container.find('.ticket-updated').text(currentTicket.updated_ago);
            Ticket.container.find('.ticket-created').text(currentTicket.created_ago);
            Ticket.container.find('.ticket-creator').text(currentTicket.creator_name);
            Ticket.container.find('.ticket-account').text(currentTicket.website);
            Ticket.container.find('.ticket-account-domain').attr('href', "//" + currentTicket.domain);
            if ( currentTicket.website ) {
                Ticket.container.find('.edit-account').attr('href', '/accounts/edit/?aid=' + currentTicket.website_id);
                Ticket.container.find('.control-account').attr('href', '/accounts/control/?aid=' + currentTicket.website_id);
                Ticket.container.find('.edit-account').parents('li').show();
                Ticket.container.find('.ticket-online-specialist').text(currentTicket.os_user_name);
                Ticket.container.find('.ticket-online-specialist').parents('li').show();
            } else {
                Ticket.container.find('.edit-account').parents('li').hide();
                Ticket.container.find('.ticket-online-specialist').parents('li').hide();
            }

            Ticket.container.find('.ticket-message').html(currentTicket.message);

            Ticket.container.find('#to-address').val(currentTicket.email);

            // Ticket Attachments --
            $('#ticket-attachments').empty().hide();
            if ( response.uploads ) {
                for ( i in response.uploads ) {
                    var upload = response.uploads[i];
                    $('#ticket-attachments').append(
                        $('<li />').append(
                            $('<a />')
                                .attr('href', upload.link)
                                .attr('target', '_blank')
                                .text(upload.name)
                        )
                    );
                    $('#ticket-attachments').show();
                }
            }

            // Comments --
            Ticket.commentsContainer.empty();
			var comments = [];
			
			for ( i in response.comments ) {
				comments[i] = response.comments[i];
			}
			
            comments = comments.sort(function(a, b){return b.id-a.id});
			
            for ( i in comments ) {
                var comment = comments[i];
                var item = Ticket.commentTemplate.clone();
                item.data('ticket-comment-id', comment.id);
                item.find('.comment-user-name').text(comment.name);
                if ( comment.private == 1 ) {
                    item.find('.comment-user-name').prepend('<i class="fa fa-lock" title="This is a Note/Private Comment!"></i> ');
                    item.find('.comment-to-address').parents('li:first').hide();
                }
                item.find('.comment-user-email').text('<' + comment.email + '>');
                var toAddress = '';
                if ( comment.to_address ) {
                    toAddress += comment.to_address;
                }
                if ( comment.cc_address ) {
                    toAddress += '; cc: ' + comment.cc_address;
                }
                if ( comment.bcc_address ) {
                    toAddress += '; bcc: ' + comment.bcc_address;
                }
                if ( toAddress ) {
                    item.find('.comment-to-address').text(toAddress);
                } else {
                    item.find('.comment-to-address').parents('li:first').hide();
                }

                item.find('.comment-created-ago').text(comment.created_ago);
                item.find('.comment-message').html(comment.comment);
                item.find('.comment-assign-to').data('assign-to', comment.user_id);

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

            TicketCommentForm.reset();

            // Adapt Ticket list height
            var height = $('.lg-side:visible .inbox-head').height() + $('.lg-side:visible .inbox-body').height();
            $('.inbox-nav').height(height > 700 ? height : 700);
        }
    }

    , assignTo: function() {
        var $this = $(this);
        var userId = $this.data('assign-to');

        if ( userId ) {
            // assignTo comes from a data, lets see if it's an assignable user
            if ( $('#assign-to [value='+userId+']').size() > 0 ) {
                $('#assign-to').selectpicker('val', userId);
            }
        } else {
            userId = $('#assign-to').val();
        }

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

    , changeStatus: function() {
        var status = $('#change-status').val();

        $.post(
            '/customer-support/update-status/'
            , {
                _nonce : $('#update-status-nonce').val()
                , tid : Ticket.container.data('ticket-id')
                , status : status
            }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    InboxNavigation.getTickets();
                }
            }
        );
    }

    , changePriority: function() {
        var priority = $('#change-priority').val();

        $.post(
            '/customer-support/update-priority/'
            , {
                _nonce : $('#update-priority-nonce').val()
                , tid : Ticket.container.data('ticket-id')
                , priority : priority
            }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    InboxNavigation.getTickets();
                }
            }
        );
    }

    , attachUserToAccount: function() {
        var account = $('#attach-to-account').val();

        $.post(
            '/customer-support/attach-user-to-account/'
            , {
                _nonce : $('#attach-user-to-account-nonce').val()
                , tid : Ticket.container.data('ticket-id')
                , account_id : account
            }
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    InboxNavigation.getTickets();
                    Ticket.reload();
                }
            }
        );
    }

    , updateSummary: function() {
        var summary = $('#ticket-summary').val();

        $.post(
            '/customer-support/update-summary/'
            , {
                _nonce : $('#update-summary-nonce').val()
                , tid : Ticket.container.data('ticket-id')
                , "summary" : summary
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

        $('#cc-address').parent('.hidden:first').removeClass('hidden').hide();
        $('#show-cc').click(function(){
            $('#cc-address').parent().show();
            $('#cc-address').focus();
            $(this).hide();
        });
        $('#bcc-address').parent('.hidden:first').removeClass('hidden').hide();
        $('#show-bcc').click(function(){
            $('#bcc-address').parent().show();
            $('#bcc-address').focus();
            $(this).hide();
        });

    }

    , add: function() {
        var form = $('#send-comment-form');
        for (var i in CKEDITOR.instances) {
            CKEDITOR.instances[i].updateElement();
        }
        form.find(':submit').prop('disabled', true);
        $.post(
            '/customer-support/add-comment/'
            , form.serialize()
            , TicketCommentForm.addComplete
        )
        return false;
    }

    , addComplete: function( response ) {
        $('#send-comment-form').find(':submit').prop('disabled', false);
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            Ticket.reload();
        }
    }

    , reset: function() {
        // Reset Comment Form
        $('#cc-address, #bcc-address').val('');
        $('#file-list').empty();
        for (var i in CKEDITOR.instances) {
            CKEDITOR.instances[i].setData('');
        }
        $('#cc-address').parent().hide();
        $('#show-cc').show();
        $('#bcc-address').parent().hide();
        $('#show-bcc').show();
        $('input[name=private]').prop('checked', false);
        $('input[name=include-whole-thread]').prop('checked', false);
    }

    , delete: function () {
        var anchor = $(this);
        var commentId = anchor.data('comment-id');

        if ( !commentId )
            return;

        if ( !confirm( 'Do you really want to delete this comment?' ) )
            return;

        $.post(
            '/customer-support/delete-comment/'
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

            $('<input />')
                .attr( 'type', 'hidden' )
                .attr( 'name', 'upload-names['+ response.id +'][name]' )
                .val( filename )
                .appendTo( fileItem );

            $('<input />')
                .attr( 'type', 'hidden' )
                .attr( 'name', 'upload-names['+ response.id +'][url]' )
                .val( response.url )
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
                InboxNavigation.getTickets();  // Update list
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
        $('#new-ticket-file-list').empty();
    }


}


jQuery(function() {
    Ticket.init();
    TicketCommentForm.init();
    NewTicketForm.init();
    InboxNavigation.init();
});
