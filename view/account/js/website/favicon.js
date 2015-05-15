var Favicon = {

    init: function() {

        var uploader = new qq.FileUploader({
            action: '/website/upload-favicon/'
            , allowedExtensions: ['ico']
            , element: $('#uploader')[0]
            , sizeLimit: 6291456
            , onSubmit: function(id, fileName) {
                uploader.setParams({
                    _nonce: $('#_upload_favicon').val()
                })
            }
            , onComplete: function(id, fileName, responseJSON) {
                GSR.defaultAjaxResponse(responseJSON);
            }
        });

        $('#aUploadFavicon').click(function(e) {
            e.preventDefault();
            if ($.support.cors) {
                $('#uploader input:first').click();
            } else {
                alert($('#err-support-cors').text());
            }
        });

    }

};


jQuery(Favicon.init);