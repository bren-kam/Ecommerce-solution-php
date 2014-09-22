var ProductPrices = {

    init: function() {

        $('#product-prices').addClass('dt').dataTable({
            aaSorting: [[0,'asc']],
            bAutoWidth: false,
            bProcessing : 1,
            bServerSide : 1,
            iDisplayLength : 20,
            sAjaxSource : '/products/list-product-prices/',
            fnServerData: function ( sSource, aoData, fnCallback ) {
                aoData.push({ name : 'b', value : $('#sBrand').val() });
                aoData.push({ name : 'cid', value : $('#sCategory').val() });

                // Get the data
                $.get( sSource, aoData, fnCallback );
            }
        });

        $('#sBrand, #sCategory').change( ProductPrices.refreshTable );

        $('.save').click( ProductPrices.save );
    }

    , refreshTable: function() {
        $('#product-prices').dataTable().fnDraw();
    }

    , save: function() {
        var values = {};

        // Create the values
        $('#product-prices input').each( function() {
            var value = $(this).val()
                , name = $(this).data('name')
                , inputID = $(this).data('product-id');

            // Make sure we don't add empty values
            if ( !value.length )
                return;

            if ( 'undefined' == typeof( values[inputID] ) )
                values[inputID] = {};

            // Add the rest of the values
            values[inputID][name] = value;
        });

        $.post(
            '/products/set-product-prices/'
            , { _nonce : $('#_set_product_prices').val(), v : values }
            , GSR.defaultAjaxResponse
        );
    }



}

jQuery( ProductPrices.init );
