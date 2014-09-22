var RelatedProductsSearch = {

    template: null

    , init: function() {
        // Autocomplete
        RelatedProductsSearch.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( RelatedProductsSearch.setupAutocomplete );

        // Search button
        $('#aSearch').click( RelatedProductsSearch.search );

        $('#product-search').submit( RelatedProductsSearch.search );

        RelatedProductsSearch.template = $('#product-template').clone().removeClass('hidden').removeAttr('id');
        $('#product-template').remove();

        $('#product-list').on( 'click', '.remove', RelatedProductsSearch.remove );

        $('#tAddProducts').addClass('dt').dataTable({
            aaSorting: [[0,'asc']],
            bAutoWidth: false,
            bProcessing : 1,
            bServerSide : 1,
            iDisplayLength : 10,
            sAjaxSource : '/products/related-products/list-products/',
            fnServerData: function ( sSource, aoData, fnCallback ) {
                aoData.push({ name : 's', value : $('#tAutoComplete').val() });
                aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });
                $.get( sSource, aoData, fnCallback );
            }
        });

        $('#tAddProducts').on( 'click', '.add-product', RelatedProductsSearch.add );
    }
    , setupAutocomplete: function() {

        var searchType = $("#sAutoComplete").val();
        var nonce = $('#_autocomplete_owned').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/products/autocomplete-owned/?_nonce=' + nonce + '&type=' + searchType + '&term=%QUERY'
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
            .on('typeahead:selected', RelatedProductsSearch.search );

    }

    , search: function( event ) {
        if ( event )
            event.preventDefault();

        $('#tAddProducts').dataTable().fnDraw();
    }

    , remove: function(e) {
        if (e) e.preventDefault();
        var item = $(this).parents('.product');

        if ( !confirm('Are you sure you want to remove this product?') )
            return;

        item.remove();
    }

    , add: function(e) {
        if (e) e.preventDefault();
        var anchor = $(this);
        $.get(
            anchor.attr('href')
            , RelatedProductsSearch.addResponse
        )
    }

    , addResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var product = response.product;
            RelatedProductsSearch.template.clone()
                .find('h4').text( product.name.substring(0, 40) ).end()
                .find('img').attr( 'src', product.image_url ).end()
                .find('.brand-name').append( product.brand ).end()
                .find('input').val( product.id ).end()
                .appendTo('#product-list');
        }
    }

};


jQuery(RelatedProductsSearch.init);
