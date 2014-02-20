jQuery(function(){
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/products/prepare_import/'
        , allowedExtensions: ['csv', 'xls']
        , element: $('#import-products')[0]
        , sizeLimit: 26214400 // 25 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_prepare_import').val()
                , brand_id: $('#brand').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aImportProducts').click( function(e) {
        e.preventDefault();
        if ( $.support.cors ) {
            $('#import-products input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    });
});