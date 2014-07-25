jQuery(function(){
    // Add Autoprice row
    $('#add').click( function() {
        var brand = $('#brand'), brandId = brand.val(), category = $('#category'), categoryId = category.val(), alternatePrice = $('#alternate_price'), alternatePriceValue = alternatePrice.val(), price = $('#price'), priceValue = price.val(), salePrice = $('#sale_price'), salePriceValue = salePrice.val(), ending = $('#ending'), endingValue = ending.val();

        $.post('/products/add-auto-price/', { _nonce : $('#_add_auto_price').val(), bid : brandId, cid : categoryId, alternate_price : alternatePriceValue, price : priceValue, sale_price : salePriceValue, ending : endingValue }, function( response ) {
            if ( response.success ) {
                var html = '<tr id="ap_' + brandId + '_' + categoryId + '">';
                html += '<td>' + brand.find('option:selected').text() + '</td>';
                html += '<td>' + category.find('option:selected').text() + '</td>';
                html += '<td><input type="text" class="tb" name="auto-price[' + brandId + '][' + categoryId + '][alternate_price]" value="' + alternatePriceValue + '"></td>';
                html += '<td><input type="text" class="tb" name="auto-price[' + brandId + '][' + categoryId + '][price]" value="' + priceValue + '"></td>';
                html += '<td><input type="text" class="tb" name="auto-price[' + brandId + '][' + categoryId + '][sale_price]" value="' + salePriceValue + '"></td>';
                html += '<td><input type="text" class="tb" name="auto-price[' + brandId + '][' + categoryId + '][ending]" value="' + endingValue + '"></td>';
                html += '<td>';
                html += '<a href="/products/run-auto-prices/?bid=' + brandId + '&cid=' + categoryId + '&_nonce=' + $('#_run_auto_prices').val() + '" ajax="1" confirm=\'Make sure you have pressed "Save" before continuing.\'>Run</a> | ';
                html +='<a href="/products/remove-auto-price/?bid=' + brandId + '&cid=' + categoryId + '&_nonce=' + $('#_remove_auto_price').val() + '" ajax="1" confirm="Are you sure you want to remove these prices? This cannot be undone.">Remove Prices From All Products</a> | ';
                html +='<a href="/products/delete-auto-price/?bid=' + brandId + '&cid=' + categoryId + '&_nonce=' + $('#_delete_auto_price').val() + '" ajax="1" confirm="Are you sure you want to delete this row? This cannot be undone">Delete</a>';
                html += '</td>';
                html += '</tr>';

                $('#auto-prices tr:last').before(html);
                $('#ap_' + brandId + '_' + categoryId).sparrow();

                brand.find('option:first').attr('selected', true);
                category.find('option:first').attr('selected', true);
                alternatePrice.val('');
                price.val('');
                salePrice.val('');
                ending.val('');
            } else {
                alert(response.error);
            }
        }, 'json' );
    });

    // Update all rows
    $('#update').click( function() {
        // Get Values
        var exampleSalePrice = $("#example-sale-price")
            , wholeSalePrice = parseFloat( exampleSalePrice.attr('data-original-price') )
            , msrp = parseFloat( $('#example_alternate_price').val()), price = parseFloat( $('#example_price').val() )
            , salePrice = parseFloat( $('#example_sale_price').val())
            , ending = parseFloat( $('#example_ending').val() ).toFixed(2);

        // MSRP
        var newMsrp = '', newPrice = '', newSalePrice = '';

        if ( msrp > 0 )
            newMsrp = '(MSRP $' + number_format( Math.ceilEnding( wholeSalePrice * ( msrp + 1 ) , ending), 2 ).replace( '.00', '' ) + ')';

        $('#example-msrp').text(newMsrp);

        // Regular Price
        if ( price > 0 )
            newPrice = '$' + number_format( Math.ceilEnding( wholeSalePrice * ( price + 1 ) , ending), 2 );

        $('#example-regular-price').text(newPrice);

        // Sale Price
        if ( salePrice > 0 ) {
            newSalePrice = '$' + number_format( Math.ceilEnding( wholeSalePrice * ( salePrice + 1 ) , ending), 2 );
        } else {
            // If there is no sale price, but there is a normal price
            if ( price > 0 ) {
                $('#example-regular-price').text('');
                newSalePrice = newPrice;
            }
        }

        $('#example-sale-price').text(newSalePrice);
    });
});

// Round up to a decimal place or ending
Math.ceilEnding = function( number, ending ) {
    var numberEnding = number.toFixed( 2 ).substr( -parseFloat( ending ).toFixed(2).length ), difference = numberEnding - ending, placeValue = Math.pow( 10, Math.floor( ending ).toString().length );

    if ( difference > 0 ) {
        return Math.round( ( number + placeValue - difference ) * 100 ) / 100;
    } else if ( difference < 0 ) {
        return Math.round( ( number - difference ) * 100 ) / 100;
    }

    return number;
};

// Format numbers nicely
function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'

    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}