var Sidebar = {

    init: function() {
        $('#sidebar-list').on( 'switchChange.bootstrapSwitch', ':checkbox', Sidebar.changeStatus );

        $('#sidebar-list').sortable({
            items		: '.sidebar-element',
            cancel		: 'input, textarea, button, a, #sidebar-video',
            placeholder	: 'sidebar-element-placeholder',
            revert		: true,
            forcePlaceholderSize : true,
            update		: Sidebar.reorder
        });

        $('#sidebar-list').on( 'click', '.remove', Sidebar.remove );

        // Overwrite MediaManager submit to Create Sidebar Element
        MediaManager.submit = Sidebar.create;

        Sidebar.template = $('#sidebar-image-template').clone().removeClass('hidden').removeAttr('id');
        $('#sidebar-image-template').remove();

        $('#sidebar-list').on( 'click', '.show-date-range', Sidebar.showDateRange );
        Sidebar.bindDateRange();
    }

    , changeStatus: function( event, state ) {
        var element = $(this).parents('.sidebar-element');

        if ( state )
            element.removeClass('disabled');
        else
            element.addClass('disabled');

        $.get(
            '/website/update-attachment-status/'
            , { _nonce: $('#_update_attachment_status').val(), apaid: element.data('attachment-id'), s: state ? 1 : 0 }
            , Sidebar.changeStatusResponse
        );
    }

    , changeStatusResponse: function( response ) {
        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            var element = $('.sidebar-element[data-attachment-id=' + response.id + ']');
            element.find('input, a, button, textarea').prop( 'disabled', element.hasClass('disabled') );
            Sidebar.reorder();
        }
    }

    , reorder: function() {
        var sequence = [];

        $('.sidebar-element').each(function(){
            sequence.push( $(this).data( 'attachment-id' ) );
        });

        $.post(
            '/website/update-attachment-sequence/'
            , { _nonce: $('#_update_attachment_sequence').val(), s: sequence.join( '|' ) }
            , GSR.defaultAjaxResponse
        );
    }

    , remove: function() {
        if ( !confirm( 'Do you want to delete this Sidebar Element? It cannot be undone' ) )
            return;

        var element = $(this).parents('.sidebar-element');

        $.get(
            '/website/remove-attachment/'
            , { _nonce: $('#_remove_attachment').val(), apaid: element.data('attachment-id') }
            , function ( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success )
                    element.remove();
            }
        )
    }

    , create: function() {
        var file = MediaManager.view.find('.mm-file.selected:first').parents('li:first').data();

        if ( file && MediaManager.isImage( file ) ) {

            $('#new-element-loader').removeClass('hidden').show();

            $.post(
                '/website/create-sidebar-image/'
                , { _nonce: $('#_create_sidebar_image').val(), fn: file.url, apid: $('#page-id').val() }
                , function( response ) {
                    var element = Sidebar.template.clone();
                    element.attr('data-attachment-id', response.id)
                        .find('img').attr('src', response.url).end()
                        .find('[name=hAccountPageAttachmentId]').val( response.id ).end()
                        .find('input[type=checkbox][data-toggle=switch]').bootstrapSwitch().end()
                        .prependTo('#sidebar-list');

                    $('#new-element-loader').hide()
                        .prependTo('#sidebar-list');

                    Sidebar.bindDateRange( element );

                    Sidebar.reorder();
                }
            );

        }
    }

    , bindDateRange: function(context) {
        if ( context ) {
            context.find('.input-daterange').datepicker({});
        } else {
            $('#sidebar-list .sidebar-element').each( function(k, v) {
                $(v).find('.input-daterange').datepicker({});
            });
        }
    }

    , showDateRange: function() {
        var daterange = $(this).parents('.sidebar-element').find('.input-daterange');
        if ( $(this).is(':checked') ) {
            daterange.removeClass('hidden').show();
        } else {
            daterange.hide();
        }
    }

}

var SidebarVideo = {

    uploader: null

    , init: function() {

        // Setup File Uploader
        SidebarVideo.uploader = new qq.FileUploader({
            action: '/website/upload-sidebar-video/'
            , allowedExtensions: ['mp4']
            , element: $('#video-uploader')[0]
            , sizeLimit: 25*1024*1024 // 25 mb's
            , onSubmit: SidebarVideo.submit
            , onComplete: SidebarVideo.complete
        });

        // Upload file trigger
        $('#video-upload').click( SidebarVideo.open );
    }

    , submit: function( id, fileName ) {
        SidebarVideo.uploader.setParams({
            _nonce : $('#_upload_sidebar_video').val()
            , apid : $('#page-id').val()
        });

        $('#video-upload').hide();
        $('#video-upload-loader').removeClass('hidden').show();
    }

    , complete: function( id, fileName, response ) {
        $('#video-upload-loader').hide();
        $('#video-upload').show();

        GSR.defaultAjaxResponse( response );
    }

    , open: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#video-uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

};

jQuery( Sidebar.init );
jQuery( SidebarVideo.init );