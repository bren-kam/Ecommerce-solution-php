// When the page has loaded
jQuery(function($) {
    $('#sStatus').change( function() {
        $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'status' ], value : $(this).val() }, endStoreSession );
    });

    $('#sAssignedTo').change( function() {
        $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'assigned-to' ], value : $(this).val() }, endStoreSession );
    });
});

/**
 * The functin to end store session AJAX call
 * @param response
 */
function endStoreSession( response ) {
    if ( response.success )
        $('.dt:first').dataTable().fnDraw();
}