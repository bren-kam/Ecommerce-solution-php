// When the page has loaded
jQuery(function($) {

    $('#sCoupon').change( function() {
        $.post( '/shopping-cart/coupons/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'coupons', 'wcid' ], value : $(this).val() }, endStoreSession );
    }).change();

});

/**
 * The function to end store session AJAX call
 * @param response
 */
function endStoreSession( response ) {
    if ( response.success )
        $('.dt:first').dataTable().fnDraw();
}