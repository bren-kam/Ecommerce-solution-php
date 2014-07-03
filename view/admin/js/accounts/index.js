var AccountSearch = {
    init: function() {

        // Autocomplete
        AccountSearch.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( AccountSearch.setupAutocomplete );

        // Search button
        $('#aSearch').click( AccountSearch.search );

        // State Change
        $('#state').change( function() {
            $.post( '/accounts/store-session/', { '_nonce' : $('#_store_session').val(), keys : [ 'accounts', 'state' ], value : $(this).val() }, AccountSearch.refreshTable );
        } );

    }
    , setupAutocomplete: function() {

        var searchType = $("#sAutoComplete").val();
        var nonce = $('#_autocomplete').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/accounts/autocomplete/?_nonce=' + nonce + '&type=' + searchType + '&term=%QUERY'
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
            .on('typeahead:selected', AccountSearch.search );

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
                , keys : [ 'accounts', 'search' ]
                , value : $('#tAutoComplete').val()
            }
            , AccountSearch.refreshTable
        );

    }

};

jQuery(AccountSearch.init);

