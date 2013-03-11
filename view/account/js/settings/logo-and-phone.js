jQuery(function(){
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/settings/upload-logo/'
        , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
        , element: $('#upload-logo')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload_logo').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadLogo').click( function() {
        $('#upload-logo input:first').click();
    });
});