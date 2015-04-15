var ReachesList = {

    init: function() {
        $('#status').change( function(){
            $.post(
                '/sales-desk/store-session/'
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