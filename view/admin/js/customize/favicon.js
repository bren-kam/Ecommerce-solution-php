jQuery(function() {
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/accounts/customize/upload-favicon/'
        , allowedExtensions: ['ico']
        , element: $('#upload-favicon')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function(id, fileName) {
            uploader.setParams({
                _nonce: $('#_upload_favicon').val(),
                aid: $('#aid').val()
            })
        }
        , onComplete: function(id, fileName, responseJSON) {
            ajaxResponse(responseJSON);
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadFavicon').click(function() {
        if ($.support.cors) {
            $('#upload-favicon input:first').click();
        } else {
            alert($('#err-support-cors').text());
        }
    });
});