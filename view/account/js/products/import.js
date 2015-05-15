/**
 * Object for Product Import Uploader
 */
var ProductImport = {

    /**
     * The File Uploader
     */
    uploader: null

    /**
     * Setup events
     */
    , init: function() {
        // Setup File Uploader
        ProductImport.uploader = new qq.FileUploader({
            action: '/products/prepare_import/'
            , allowedExtensions: ['csv', 'xls']
            , element: $('#import-products')[0]
            , sizeLimit: 26214400 // 25 mb's
            , onSubmit: ProductImport.submit
            , onComplete: ProductImport.complete
        });

        // Upload file trigger
        $('#aUpload').click( ProductImport.open );
    }

    /**
     * Submit - triggered when a file is selected to upload
     *
     * @param id
     * @param fileName
     */
    , submit: function( id, fileName ) {
        ProductImport.uploader.setParams({
            _nonce : $('#_prepare_import').val()
            , brand_id: $('#brand').val()
        });

        $('#aUpload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    /**
     * Complete - handles upload response
     * @param id
     * @param fileName
     * @param responseJSON
     */
    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#aUpload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.error )
            return;

        // Upload Overview
        $('#tUploadOverview')
            .append('<tr><td>Total rows read:</td><td><span class="badge bg-info">' + response.count + '</span></td></tr>')
            .append('<tr><td>Errors found:</td><td><span class="badge bg-important">' + response.count_skipped + '</span></td></tr>')
            .append('<tr><td>Total products to insert/update:</td><td><span class="badge bg-success">' + response.count_to_import + '</span><span class="badge bg-warning">' + response.count_to_update + '</span></td></tr>');


        // Skipped Rows details
        if ( response.skipped_rows[0] ) {
            var tSkippedRows = $('#dSkippedRows table:first');
            tSkippedRows.empty();
            for ( i in response.skipped_rows ) {
                var product = response.skipped_rows[i];

                var row = $('<tr />');
                $('<td />').text( product.sku ).appendTo( row );
                $('<td />').text( product.name ).appendTo( row );
                $('<td />').text( product.description ).appendTo( row );
                $('<td />').text( product.industry ).appendTo( row );
                $('<td />').text( product.category ).appendTo( row );
                $('<td />').html( '<a href="'+ product.image +'" target="_blank">Image</a>' ).appendTo( row );
                $('<td />').text( product.price_wholesale ).appendTo( row );
                $('<td />').text( product.price_map ).appendTo( row );
                $('<td />').text( product.status ).appendTo( row );
                $('<td />').text( product.reason ).appendTo( row );

                tSkippedRows.append(row);
            }

            $('#dSkippedRows').removeClass('hidden');
        }

        $('#dForm').hide();
        $('#dConfirm').removeClass('hidden');

    }

    /**
     * Opens uploader
     * @param e
     */
    , open: function(e) {
        if ( e ) e.preventDefault();

        if ( $.support.cors ) {
            $('#import-products input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

}

jQuery( ProductImport.init );