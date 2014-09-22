var ProductSearch = {

    init: function() {
        // Autocomplete
        ProductSearch.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( ProductSearch.setupAutocomplete );

        // Search button
        $('#product-search').submit( ProductSearch.search );

        $('#product-search-results').addClass('dt').dataTable({
            aaSorting: [[0,'asc']],
            bAutoWidth: false,
            bProcessing : 1,
            bServerSide : 1,
            iDisplayLength : 20,
            sAjaxSource : '/products/list-add-products/',
            sDom : '<"top"lr>t<"bottom"pi>',
            fnServerData: function ( sSource, aoData, fnCallback ) {
                aoData.push({ name : 's', value : $('#tAutoComplete').val() });
                aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });
                aoData.push({ name : 'c', value : $('#sCategory').val() });

                // Get the data
                $.get( sSource, aoData, fnCallback );
            }
        });

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
                    return list.suggestions
                }
            }
        });

        autocomplete.initialize();
        $("#tAutoComplete")
            .typeahead('destroy')
            .typeahead(null, {
                displayKey: 'name'
                , source: autocomplete.ttAdapter()
            })
            .unbind('typeahead:selected')
            .on('typeahead:selected', ProductSearch.search );
    }

    , search: function( event ) {
        if ( event )
            event.preventDefault();

        $('#product-search-results').dataTable().fnDraw();
    }

};

var ProductAdd = {

    template: null

    , init: function() {
        ProductAdd.template = $('#add-product-template').clone().removeClass('hidden').removeAttr('id');
        $('#add-product-template').remove();

        $('#product-search-results').on( 'click', '.add-product', ProductAdd.add );
        $('#product-list').on( 'click', '.remove', ProductAdd.remove );
    }

    , add: function() {
        ProductAdd.template.clone()
            .prepend( $(this).data('name') )
            .find('input').val( $(this).data('id') ).end()
            .appendTo('#product-list');

        $('#add-product-form :submit').prop('disabled', false);
    }

    , remove: function() {
        $(this).parents('li').remove();

        if ( $('#product-list li').size() == 0 )
            $('#add-product-form :submit').prop('disabled', true);
    }
}

var ProductRequest = {

    template: null

    , init: function() {
        ProductRequest.template = $('#request-template').clone().removeClass('hidden').removeAttr('id');
        $('#request-template').remove();

        $('#add-request').click( ProductRequest.checkSKU );

        $('body').on( 'submit', '#fRequestAProduct', ProductRequest.submit );

        $('body').on( 'click', '#fRequestAProduct .remove', ProductRequest.remove );
    }

    , checkSKU: function() {
        var tRequestSKU = $('#tRequestSKU');
        var sRequestBrand = $('#sRequestBrand');

        // Check if the product already exists
        $.post(
            '/products/sku-exists/'
            , { _nonce: $('#_sku_exists').val(), sku : tRequestSKU.val(), brand_id: sRequestBrand.val() }
            , ProductRequest.add
        );
    }

    , add: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var sRequestBrand = $('#sRequestBrand')
                , tRequestSKU = $('#tRequestSKU')
                , tCollection = $('#tCollection');

            ProductRequest.template.clone()
                .find('.title').text( sRequestBrand.find(':selected').text() + " | " + tRequestSKU.val() + " | " + tCollection.val() ).end()
                .find('input').val( sRequestBrand.find(':selected').text() + "|" + tRequestSKU.val() + "|" + tCollection.val() ).end()
                .appendTo('#request-list');

            sRequestBrand.val('');
            tRequestSKU.val('');
            tCollection.val('');
        }
    }

    , submit: function(e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.post(
            url
            , data
            , function( response ){
                GSR.defaultAjaxResponse( response );
                $('#request-list').empty();
            }
        );
    }

    , remove: function() {
        $(this).parents('.request:first').remove();
    }

}

jQuery(ProductSearch.init);
jQuery(ProductAdd.init);
jQuery(ProductRequest.init);