// When the page has loaded
jQuery(function($) {
    $('#sParentCategoryID').change( function() {
        $.post( '/website/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'categories', 'pcid' ], value : $(this).val() }, function( response ) {
            if ( response.success )
                $('.dt:first').dataTable().fnDraw();
        } );
    })
});