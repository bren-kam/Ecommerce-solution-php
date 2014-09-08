
var ImportSubscribers = {

    uploader: null

    , init: function() {
        // Setup File Uploader
        ImportSubscribers.uploader = new qq.FileUploader({
            action: '/email-marketing/subscribers/import-subscribers/'
            , allowedExtensions: ['csv', 'xls']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: ImportSubscribers.uploaderSubmit
            , onComplete: ImportSubscribers.uploaderComplete
        });

        // Upload file trigger
        $('#upload').click( ImportSubscribers.uploaderOpen );

        $('.email-list').change( ImportSubscribers.updateEmailListValue );
        ImportSubscribers.updateEmailListValue();
    }


    , uploaderSubmit: function( id, fileName ) {
        ImportSubscribers.uploader.setParams({
            _nonce : $('#_import_subscribers').val()
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , uploaderComplete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            var lastTenEmails = response.last_ten_emails;
            for ( i in lastTenEmails ) {
                var contact = lastTenEmails[i];

                $('#subscriber-list tbody').append( '<tr><td>' + contact.email + '</td><td>' + contact.name + '</td></tr>' );
            }

            $('#step-1').hide();
            $('#step-2').removeClass('hidden').show();
        }
    }

    , uploaderOpen: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

    , updateEmailListValue: function() {
        var emailLists = [];
        $('.email-list:checked').each(function() {
            emailLists.push( $(this).val() );
        });
        $('#hEmailLists').val( emailLists.join('|') );
    }


}

jQuery( ImportSubscribers.init );