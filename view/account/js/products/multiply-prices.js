jQuery(function(){
    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/products/multiply-prices/'
        , allowedExtensions: ['csv', 'xls']
        , element: $('#multiply-prices')[0]
        , sizeLimit: 26214400 // 25 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_multiply_prices').val()
                , price : $('#price').val()
                , sale_price : $('#sale-price').val()
                , alternate_price : $('#alternate-price').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aMultiplyPrices').click( function() {
        $('#multiply-prices input:first').click();
    });
});