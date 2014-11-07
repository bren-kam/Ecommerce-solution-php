var Banner = {

    init: function() {
        $('#banner-list').on( 'switchChange.bootstrapSwitch', ':checkbox', Banner.changeStatus );

        $('#banner-list').sortable({
            items		: '.banner',
            cancel		: 'input, button, a',
            placeholder	: 'banner-placeholder',
            revert		: true,
            forcePlaceholderSize : true,
            update		: Banner.reorder
        });

        $('#banner-list').on( 'click', '.remove', Banner.remove );

        // Overwrite MediaManager submit to Create Banner Element
        MediaManager.submit = Banner.create;

        Banner.template = $('#banner-template').clone().removeClass('hidden').removeAttr('id');
        $('#banner-template').remove();

        $('#banner-list').on( 'click', '.show-date-range', Banner.showDateRange );
        Banner.bindDateRange();
    }

    , changeStatus: function( event, state ) {
        var element = $(this).parents('.banner');

        if ( state )
            element.removeClass('disabled');
        else
            element.addClass('disabled');

        $.get(
            '/website/update-attachment-status/'
            , { _nonce: $('#_update_attachment_status').val(), apaid: element.data('attachment-id'), s: state ? 1 : 0 }
            , Banner.changeStatusResponse
        );
    }

    , changeStatusResponse: function( response ) {
        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            var element = $('.banner[data-attachment-id=' + response.id + ']');
            element.find('input, a, button, textarea').prop( 'disabled', element.hasClass('disabled') );
            Banner.reorder();
        }
    }

    , reorder: function() {
        var sequence = [];

        $('.banner').each(function(){
            sequence.push( $(this).data( 'attachment-id' ) );
        });

        $.post(
            '/website/update-attachment-sequence/'
            , { _nonce: $('#_update_attachment_sequence').val(), s: sequence.join( '|' ) }
            , GSR.defaultAjaxResponse
        );
    }

    , remove: function() {
        if ( !confirm( 'Do you want to delete this Banner Element? It cannot be undone' ) )
            return;

        var element = $(this).parents('.banner');

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
                '/website/create-banner/'
                , { _nonce: $('#_create_banner').val(), fn: file.url, apid: $('#page-id').val() }
                , function( response ) {
                    var element = Banner.template.clone()
                        .attr('data-attachment-id', response.id)
                        .find('img').attr('src', response.url).end()
                        .find('[name=hAccountPageAttachmentId]').val( response.id ).end()
                        .find('input[type=checkbox]:first').bootstrapSwitch().end()
                        .prependTo('#banner-list');

                    Banner.bindDateRange( element );

                    $('#new-element-loader').hide()
                        .prependTo('#banner-list');

                    Banner.reorder();
                }
            );



        }
    }

    , bindDateRange: function(context) {
        if ( context ) {
            context.find('.input-daterange').datepicker({});
        } else {
            $('#banner-list .banner').each( function(k, v) {
                $(v).find('.input-daterange').datepicker({});
            });
        }
    }

    , showDateRange: function() {
        var daterange = $(this).parents('.banner').find('.input-daterange');
        if ( $(this).is(':checked') ) {
            daterange.removeClass('hidden').show();
        } else {
            daterange.hide();
        }
    }

}

jQuery( Banner.init );
