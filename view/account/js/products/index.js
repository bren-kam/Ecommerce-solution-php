var ProductSearch = {

    template: null

    , init: function() {
        // Autocomplete
        ProductSearch.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( ProductSearch.setupAutocomplete );

        // Search button
        $('#aSearch').click( ProductSearch.search );

        $('#product-search').submit( ProductSearch.search );

        ProductSearch.template = $('#product-template').clone().removeClass('hidden').removeAttr('id');
        $('#product-template').remove();

        $('#prev-page').click( ProductSearch.prevPage );
        $('#next-page').click( ProductSearch.nextPage );

        $('#product-list').on( 'click', '.remove', ProductSearch.remove );

        // Make the list sortable
        $("#product-list").sortable( {
            items: '.product',
            update: ProductSearch.reorganize,
            scroll: true,
            placeholder: 'product-placeholder'
        });

        $('#pp').change( ProductSearch.changePerPage );
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
            .on('typeahead:selected', ProductSearch.search );

    }

    , changePerPage: function() {
        $('[name=n]').val( $(this).val() );
        ProductSearch.search();
    }

    , search: function( event ) {
        if ( event )
            event.preventDefault();

        // Reset to first page
        $('[name=p]').val('1');

        ProductSearch.loadPage();
    }

    , loadPage: function () {
        var form = $('#product-search');

        // Sortable is only enabled when we are under a category
        if( $('[name=cid]').val() ) {
            $("#product-list").sortable('enable');
        } else {
            $("#product-list").sortable('disable');
        }


        $.post(
            form.attr('action')
            , form.serialize()
            , ProductSearch.loadPageResponse
        );
    }

    , loadPageResponse: function ( response ) {
        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            var i, product;
            var remove_nonce = $('#_remove').val();
            var edit_nonce = $('#_edit').val();
            var block_nonce = $('#_block').val();
            var category_image_nonce = $('#_set_category_image').val();

            var category_id = $('[name=cid]').val();
            var is_category_selected = category_id > 0;

            $('#product-list').empty();
            for ( i in response.products ) {
                product = response.products[i];
                ProductSearch.template.clone()
                    .data( 'product-id', product.product_id )
                    .find( 'h3' ).text( product.name ).end()
                    .find( 'img' ).attr( 'src', product.image_url ).end()
                    .find( '.sku' ).text( product.sku ).end()
                    .find( '.brand' ).text( product.brand ).end()
                    .find( '.price' ).text( product.price ).end()
                    .find( '.alt-price-name' ).text( product.alternate_price_name ).end()
                    .find( '.alt-price' ).text( product.alternate_price ).end()
                    .find( '.view-product' ).attr( 'href', product.link ).end()
                    .find( '.remove' ).attr( 'href', '/products/remove/?_nonce=' + remove_nonce + '&pid=' + product.product_id ).end()
                    .find( '.edit' ).attr( 'href', '/products/edit/?_nonce=' + edit_nonce + '&pid=' + product.product_id ).end()
                    .find( '.block' ).attr( 'href', '/products/block/?_nonce=' + block_nonce + '&pid=' + product.product_id ).end()
                    .find( '.set-category-image' ).attr( 'href', '/products/set-category-image/?_nonce=' + category_image_nonce + '&cid=' + ( is_category_selected ? category_id : product.category_id ) + '&i=' + encodeURIComponent(product.image_url) + '&bid=' + product.brand_id ).end()
                    .appendTo('#product-list');
            }

            $('#product-start').text( response.product_start );
            $('#product-end').text( response.product_end );
            $('#product-count').text( response.product_count );

            if (response.product_start == 1)
                $('#prev-page').addClass('disabled');
            else
                $('#prev-page').removeClass('disabled');

            if (response.product_end >= response.product_count)
                $('#next-page').addClass('disabled');
            else
                $('#next-page').removeClass('disabled');
        }
    }

    , prevPage: function() {
        $('[name=p]').val( parseInt($('[name=p]').val()) - 1 );
        ProductSearch.loadPage();
    }

    , nextPage: function() {
        $('[name=p]').val( parseInt($('[name=p]').val()) + 1 );
        ProductSearch.loadPage();
    }

    , remove: function(e) {
        if (e) e.preventDefault();
        var item = $(this).parents('.product');

        if ( !confirm('Are you sure you want to remove this product?') )
            return;

        $.get(
            $(this).attr('href')
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                item.remove();
            }
        )
    }

    , reorganize: function() {
        var sequence = [];

        $('#product-list .product').each(function(){
            sequence.push( $(this).data('product-id') );
        });

        $.post(
            '/products/update-sequence/'
            , { _nonce : $('#_update_sequence').val(), s : sequence.join('|'), p : $('[name=p]').val(), pp :$('[name=n]').val() }
            , GSR.defaultAjaxResponse
        );
    }

};

var ProductForm = {

    coupon_template: null

    , init: function() {
        $(document).on( 'shown.bs.modal', '#modal' , ProductForm.initForm );

        $(document).on( 'click', '#add-product-option', ProductForm.addProductOption );
        $(document).on( 'click', '.remove-product-option', ProductForm.removeProductOption );

        $(document).on( 'click', '#add-coupon', ProductForm.addCoupon );
        $(document).on( 'click', '.remove-coupon', ProductForm.removeCoupon );

        $(document).on( 'submit', '#edit-product', ProductForm.save );
    }

    , initForm: function() {
        $('#sProductOptions').find('[disabled]').each(function () {
            var product_option_id = $(this).attr('value');
            $('#product-option-templates')
                .find('[data-product-option-id=' + product_option_id + ']')
                .clone()
                .appendTo('#product-option-list');
        });

        ProductForm.coupon_template = $('#coupon-template').clone().removeAttr('id');
        $('#coupon-template').parent().remove()
    }

    , addProductOption: function() {
        var product_option_id = $('#sProductOptions').val();

        $('#product-option-templates')
            .find('[data-product-option-id=' + product_option_id + ']')
            .clone()
            .appendTo('#product-option-list');

        $('#sProductOptions').find(':selected').prop( 'disabled', true );
    }

    , removeProductOption: function() {
        var product_option = $(this).parents('[data-product-option-id]');
        var product_option_id = product_option.data('product-option-id');

        $('#sProductOptions [value=' + product_option_id + ']').prop( 'disabled', false );
        product_option.remove();
    }

    , addCoupon: function() {
        var selected = $('#sCoupons').find(':selected');

        ProductForm.coupon_template.clone()
            .prepend( selected.text() )
            .find('input').val( selected.attr('value')).end()
            .appendTo('#coupon-list')

        selected.prop('disabled', true);
    }

    , removeCoupon: function() {
        var item = $(this).parents('li');
        var coupon_id = item.find('input').val();

        $('#sCoupons').find('[value='+ coupon_id +']').prop('disabled', false);
        item.remove();
    }

    , save: function(e) {
        if (e) e.preventDefault();

        var form = $('#edit-product');
        $.post(
            form.attr('action')
            , form.serialize()
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    $('#modal').modal('hide');
                }
            }
        )
    }

};

jQuery(ProductSearch.init);
jQuery(ProductForm.init);