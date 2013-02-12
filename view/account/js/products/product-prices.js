/**
 * @page Product Prices
 */
jQuery(function($) {
    // Grab the products for each brand
    $('#sBrand').change( function() {
        $('#tProductPrices').dataTable().fnDraw();
    });

	// Save the data
	$('#bSave, #bSave2').click( function() {
		var brandID = $('#sBrand').val(), values = {}, extra = $(this).attr('id').replace( 'bSave', '' );

        // We don't want to do anything if they haven't selected a brand yet
        if ( '' == brandID || 0 == brandID )
            return;

		// Create the values
		$('#tProductPrices input').each( function() {
			var value = $(this).val(), inputID = $(this).attr('id').replace( /[^0-9]+/, '' );
			
			// Make sure we don't add empty values
			if ( !value.length )
				return;

            if ( 'undefined' == typeof( values[inputID] ) )
                values[inputID] = {};
           
			// Add the rest of the values
			values[inputID][$(this).attr('class')] = value;
		});

		$.post( '/products/set-product-prices/', { _nonce : $('#_set_product_prices').val(), v : values }, ajaxResponse, 'json' );
	});

    $('#tProductPrices').addClass('dt').dataTable({
        aaSorting: [[0,'asc']],
        bAutoWidth: false,
        bProcessing : 1,
        bServerSide : 1,
        iDisplayLength : 20,
        sAjaxSource : '/products/list-product-prices/',
        sDom : '<"top"lr>t<"bottom"pi>',
        oLanguage: {
                sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>',
                sInfo: "_START_ - _END_ of _TOTAL_"
        },
        fnDrawCallback : function() {
            // Run Sparrow on new content and add the class last to the last row
            sparrow( $(this).find('tr:last').addClass('last').end() );
        },
        fnServerData: function ( sSource, aoData, fnCallback ) {
            aoData.push({ name : 'b', value : $('#sBrand').val() });

            // Get the data
            $.ajax({
                url: sSource,
                dataType: 'json',
                data: aoData,
                success: fnCallback
            });
        }
    });
});