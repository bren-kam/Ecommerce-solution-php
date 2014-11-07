var BrandList = {

    template: null

    , init: function() {
        $('#brand-list').on( 'click', '.remove', BrandList.remove );
        BrandList.setupAutocomplete();
        BrandList.template = $('#brand-template').clone().removeClass('hidden').removeAttr('id');
        $('#brand-template').remove();
        // Sortable
        $( '#brand-list' ).sortable({
            items		: '.brand',
            cancel		: 'a',
            placeholder	: 'brand-placeholder',
            forcePlaceholderSize : true,
            update: BrandList.updateSequence
        });

        $('#brand-link').change( BrandList.setBrandLinkSetting );
    }

    , setupAutocomplete: function() {

        var nonce = $('#_autocomplete').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/products/autocomplete/?_nonce=' + nonce + '&type=brand&term=%QUERY'
                , filter: function( list ) {
                    return list.suggestions
                }
            }
        });

        autocomplete.initialize();
        $("#autocomplete")
            .typeahead('destroy')
            .typeahead(null, {
                displayKey: 'name'
                , source: autocomplete.ttAdapter()
            })
            .unbind('typeahead:selected')
            .on('typeahead:selected', BrandList.add );

    }

    , remove: function() {
        if ( !confirm('Are you sure do you want to remove this Brand?') )
            return;

        var brand = $(this).parents('.brand');
        var brand_id = brand.data('brand-id');

        $.get(
            '/products/remove-brand/'
            , { _nonce: $('#_remove_brand').val(), bid: brand_id }
            , function ( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success )
                    brand.remove();
            }
        )
    }

    , add: function(e, i) {
        $.post(
            '/products/add-brand/'
            , { _nonce: $('#_add_brand').val(), bid: i.value, s: $('.brand').size() }
            , BrandList.addResponse
        )
    }

    , addResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var brand = response.brand;

            BrandList.template.clone()
                .data( 'brand-id', brand.id )
                .find('img').attr( 'src', brand.image ).end()
                .find('h4').text( brand.name ).end()
                .find('.brand-url a').attr( 'href', 'http://' + brand.link ).text( brand.link ).end()
                .appendTo( '#brand-list' );
        }
    }

    , updateSequence: function() {
        var sequence = [];
        $( '#brand-list .brand' ).each( function(){
            sequence.push($( this ).data('brand-id') );
        })

        $.post(
            '/products/update-brand-sequence/'
            , { s: sequence.join('|'), _nonce: $('#_update_brand_sequence' ).val() }
            , GSR.defaultAjaxResponse
        );
    }

    , setBrandLinkSetting: function() {
        $.post(
            '/products/set-brand-link/', { _nonce: $('#_set_brand_link').val(), 'checked' : ( $('#brand-link').is(':checked') ) ? 1 : 0 }
            , GSR.defaultAjaxResponse
        );
    }

}

jQuery( BrandList.init );
