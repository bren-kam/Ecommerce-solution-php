var ReachesList = {

    init: function() {
        $('#status').change( function(){
            $.post(
                '/products/reaches/store-session/'
                , { _nonce : $('#_store_session').val(), keys : [ 'reaches', 'status' ], value : $(this).val() }
                , ReachesList.refreshTable
            );
        } );
    }

    , refreshTable: function() {
        $('.dt').dataTable().fnDraw();
    }

}

jQuery( ReachesList.init );