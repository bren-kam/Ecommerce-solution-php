var ProductSearch = {
    init: function() {

        // Autocomplete
        ProductSearch.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( ProductSearch.setupAutocomplete );

        // Search button
        $('#aSearch').click( ProductSearch.search );

        // State Change
        $('#visibility, #user-option, #user, #cid').change( function() {
            $.post( '/accounts/store-session/', { '_nonce' : $('#_store_session').val(), keys : [ 'products', $(this).attr('id') ], value : $(this).val() }, ProductSearch.refreshTable );
        } );

    }
    , setupAutocomplete: function() {

        var searchType = $("#sAutoComplete").val();
        var nonce = $('#_autocomplete').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/products/autocomplete/?_nonce=' + nonce + '&type=' + searchType + '&term=%QUERY'
                , filter: function( list ) {
                    return list.objects
                }
            }
        });

        autocomplete.initialize();
        $("#tAutoComplete")
            .typeahead('destroy')
            .typeahead(null, {
                displayKey: searchType
                , source: autocomplete.ttAdapter()
            })
            .unbind('typeahead:selected')
            .on('typeahead:selected', ProductSearch.search );

        // Switch search type
        $.post( '/accounts/store-session/', { '_nonce' : $('#_store_session').val(), keys : [ 'products', 'type' ], value : $('#sAutoComplete').val() }, ProductSearch.refreshTable );

    }
    , refreshTable: function ( response ) {

        if ( response.success )
            $('.dt:first').dataTable().fnDraw();

    }
    , search: function( event ) {
        if ( event )
            event.preventDefault();

        $.post(
            '/accounts/store-session/'
            , {
                _nonce : $('#_store_session').val()
                , keys : [ 'products', 'search' ]
                , value : $('#tAutoComplete').val()
            }
            , ProductSearch.refreshTable
        );

    }

};

jQuery(ProductSearch.init);

