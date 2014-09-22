var CategoryList = {

    init: function() {
        $('#sParentCategoryID').change( function() {
            $.post(
                '/website/store-session/'
                , { _nonce : $('#_store_session').val()
                , keys : [ 'categories', 'pcid' ], value : $(this).val() }
                , function( response ) {
                    if ( response.success )
                        $('.dt').dataTable().fnDraw();
                }
            );
        });
    }

};

jQuery( CategoryList.init );