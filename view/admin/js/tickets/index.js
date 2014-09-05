var TicketList = {

    init: function() {

        GSR.datatable(
            $('#ticket-container table')
            , {
                bProcessing: 1
                , bServerSide: 1
                , sAjaxSource: '/tickets/list-all/'
                , oLanguage: {
                    sSearch: 'Search:'
                }
            }
        )
        ajax="/tickets/list-all/"

        $('#sStatus').change( function() {
            $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'status' ], value : $(this).val() }, TicketList.reloadDataTable );
        });

        $('#sAssignedTo').change( function() {
            $.post( '/tickets/store-session/', { _nonce : $('#_store_session').val(), keys : [ 'tickets', 'assigned-to' ], value : $(this).val() }, TicketList.reloadDataTable );
        });

        // Refresh the page every 5 minutes
        setInterval( TicketList.reloadDataTable(), 300000 );
    }

    , reloadDataTable: function() {
        if ( $('.dt').dataTable )
            $('.dt').dataTable().fnDraw();
    }

}

jQuery( TicketList.init );