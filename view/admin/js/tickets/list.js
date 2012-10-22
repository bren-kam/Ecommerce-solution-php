// When the page has loaded
jQuery(function($) {
    $('#sStatus').change( function() {
        $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'status' ], value : $(this).val() }, endStoreSession );
    });

    $('#sAssignedTo').change( function() {
        $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'assigned-to' ], value : $(this).val() }, endStoreSession );
    });

    // Refresh the page every 5 minutes
    setInterval( function() {
        $('.dt:first').dataTable().fnDraw();
    }, 5000 );
});

/**
 * The functin to end store session AJAX call
 * @param response
 */
function endStoreSession( response ) {
    if ( response.success )
        $('.dt:first').dataTable().fnDraw();
}