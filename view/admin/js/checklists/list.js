// When the page has loaded
jQuery(function($) {
    $('#sCompleted').change( function() {
        $.post( '/checklists/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'checklists', 'completed' ], value : $(this).val() }, function( response ) {
            if ( response.success )
                $('.dt:first').dataTable().fnDraw();
        } );
    })
});