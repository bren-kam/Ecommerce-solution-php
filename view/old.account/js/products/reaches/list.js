jQuery(function() {

    $('#sStatus').change( function() {
        $.post( '/products/reaches/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'reaches', 'status' ], value : $(this).val() }, endStoreSession );
    });

});

/**
 * The function to end store session AJAX call
 * @param response
 */
function endStoreSession( response ) {
    if ( response.success )
        $('.dt:first').dataTable().fnDraw();
}
