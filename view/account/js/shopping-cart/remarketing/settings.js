var SettingsForm = {

    uploader: null

    , init: function() {
        // Setup File Uploader
        SettingsForm.uploader = new qq.FileUploader({
            action: '/shopping-cart/remarketing/upload-coupon/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: SettingsForm.submit
            , onComplete: SettingsForm.complete
        });

        // Upload file trigger
        $('#upload').click( SettingsForm.open );

    }

    , submit: function( id, fileName ) {
        SettingsForm.uploader.setParams({
            _nonce : $('#_upload_coupon').val()
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            $('#coupon' ).html( '<img src="' + response.url + '" />' );
            $('#coupon-path' ).val( response.url );
        }
    }

    , open: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

};

jQuery(SettingsForm.init);