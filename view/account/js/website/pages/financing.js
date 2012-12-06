jQuery(function(){
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/website/upload-image/'
        , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
        , element: $('#upload-image')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload_image').val()
                , aid : $('#hAccountId').val()
                , apid : $('#hAccountPageId').val()
                , fn : 'financing'
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadImage').click( function() {
        $('#upload-image input:first').click();
    });
});