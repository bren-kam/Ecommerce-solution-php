
var LogoUploader = {

    uploader: null

    , init: function() {
        // Setup File Uploader
        LogoUploader.uploader = new qq.FileUploader({
            action: '/settings/upload-logo/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: LogoUploader.uploaderSubmit
            , onComplete: LogoUploader.uploaderComplete
        });

        // Upload file trigger
        $('#upload').click( LogoUploader.uploaderOpen );
    }

    , uploaderSubmit: function( id, fileName ) {
        LogoUploader.uploader.setParams({
            _nonce : $('#_upload_logo').val()
        });
        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , uploaderComplete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            $('#logo').attr( 'src', response.image );
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

}

jQuery( LogoUploader.init );
