var AutoPrice = {

    template: null

    , init: function() {
        $('#add').click( AutoPrice.add );
        $('#auto-price-list').on( 'click', '.remove', AutoPrice.remove );

        AutoPrice.template = $('#auto-price-template').clone().removeClass('hidden').removeAttr('id');
        $('#auto-price-template').remove();
    }

    , add: function() {
        var brand = $('#brand-id')
            , brandId = brand.val()
            , category = $('#category-id')
            , categoryId = category.val()
            , alternatePrice = $('#alternate_price')
            , alternatePriceValue = alternatePrice.val()
            , price = $('#price')
            , priceValue = price.val()
            , salePrice = $('#sale_price')
            , salePriceValue = salePrice.val()
            , ending = $('#ending')
            , endingValue = ending.val();

        $.post(
            '/products/add-auto-price/'
            , { _nonce : $('#_add_auto_price').val(), bid : brandId, cid : categoryId, alternate_price : alternatePriceValue, price : priceValue, sale_price : salePriceValue, ending : endingValue }
            , function( response ) {
                if ( response.success ) {
                    var row = AutoPrice.template.clone();
                    row.html(
                        row.html()
                            .replace( /CATEGORY_NAME/g, category.find('option:selected').text() )
                            .replace( /CATEGORY_ID/g, categoryId )
                            .replace( /BRAND_NAME/g, brand.find('option:selected').text() )
                            .replace( /BRAND_ID/g, brandId )
                    );
                    row.find('input[name*=alternate_price]').val(alternatePriceValue);
                    row.find('input[name*=\\[price\\]]').val(priceValue);
                    row.find('input[name*=sale_price]').val(salePriceValue);
                    row.find('input[name*=ending]').val(endingValue);

                    row.appendTo('#auto-price-list tbody');
                } else GSR.defaultAjaxResponse( response );
            }
        );
    }

    , remove: function(e) {
        var anchor = $(this);

        if ( e ) e.preventDefault();

        if ( !confirm( 'Are you sure you want to remove this line? Cannot be undone.' ) )
            return;

        $.get(
            anchor.attr('href')
            , function( response ) {
                if ( response.success ) {
                    anchor.parents('tr').remove();
                } else GSR.defaultAjaxResponse( response );
            }
        );
    }

};

var AutoPriceExample = {

    init: function() {
        $('#update').click( AutoPriceExample.update );
    }

    , update: function() {
        // Get Values
        var exampleSalePrice = $("#example-sale-price")
            , wholeSalePrice = parseFloat( exampleSalePrice.attr('data-original-price') )
            , msrp = parseFloat( $('#example_alternate_price').val())
            , price = parseFloat( $('#example_price').val() )
            , salePrice = parseFloat( $('#example_sale_price').val())
            , ending = parseFloat( $('#example_ending').val() ).toFixed(2)
            , newMsrp = ''
            , newPrice = ''
            , newSalePrice = '';

        if ( msrp > 0 )
            newMsrp = '(MSRP $' + Math.ceilEnding( wholeSalePrice * ( msrp + 1 ) , ending).numberFormat( 2 ).replace( '.00', '' ) + ')';

        $('#example-msrp').text(newMsrp);

        // Regular Price
        if ( price > 0 )
            newPrice = '$' + Math.ceilEnding( wholeSalePrice * ( price + 1 ) , ending).numberFormat( 2 );

        $('#example-regular-price').text(newPrice);

        // Sale Price
        if ( salePrice > 0 ) {
            newSalePrice = '$' + Math.ceilEnding( wholeSalePrice * ( salePrice + 1 ) , ending).numberFormat( 2 );
        } else {
            // If there is no sale price, but there is a normal price
            if ( price > 0 ) {
                $('#example-regular-price').text('');
                newSalePrice = newPrice;
            }
        }

        $('#example-sale-price').text(newSalePrice);
    }

};

jQuery( AutoPrice.init );
jQuery( AutoPriceExample.init );