var PriceMultiplier = {

    uplodaer: null

    , init: function() {
        PriceMultiplier.uploader = new qq.FileUploader({
            action: '/products/multiply-prices/'
            , allowedExtensions: ['csv', 'xls']
            , element: $('#uploader')[0]
            , sizeLimit: 26214400 // 25 mb's
            , onSubmit: PriceMultiplier.submit
            , onComplete: PriceMultiplier.complete
        });

        $('#upload').click( PriceMultiplier.open );
    }

    , submit: function( id, fileName ) {
        PriceMultiplier.uploader.setParams({
            _nonce : $('#_multiply_prices').val()
            , price : $('#price').val()
            , sale_price : $('#sale-price').val()
            , alternate_price : $('#alternate-price').val()
        })

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();
        GSR.defaultAjaxResponse( response );
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

jQuery( PriceMultiplier.init );