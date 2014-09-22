var Favicon = {

    init: function() {

        var uploader = new qq.FileUploader({
            action: '/accounts/customize/upload-favicon/'
            , allowedExtensions: ['ico']
            , element: $('#upload-favicon')[0]
            , sizeLimit: 6291456
            , onSubmit: function(id, fileName) {
                uploader.setParams({
                    _nonce: $('#_upload_favicon').val(),
                    aid: $('#aid').val()
                })
            }
            , onComplete: function(id, fileName, responseJSON) {
                GSR.defaultAjaxResponse(responseJSON);
            }
        });

        $('#aUploadFavicon').click(function(e) {
            e.preventDefault();
            if ($.support.cors) {
                $('#upload-favicon input:first').click();
            } else {
                alert($('#err-support-cors').text());
            }
        });

    }

};


jQuery(Favicon.init);