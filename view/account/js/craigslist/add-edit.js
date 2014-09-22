var CraigslistForm = {

    init: function() {
        // Autocomplete
        CraigslistForm.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( CraigslistForm.setupAutocomplete );

        $('#fAddCraigslistTemplate .hidden').removeClass('hidden').hide();

        $('#show-preview').click( CraigslistForm.preview );
        $('#post-ad').click( CraigslistForm.postAD );

        if ( $('#hProductID').val() ) {
            CraigslistForm.load();
        }
    }

    , load: function() {
        if ( typeof CKEDITOR === 'undefined')
            return setTimeout( CraigslistForm.load, 1000 );

        $.post(
            '/craigslist/load-product/'
            , { _nonce : $('#_load_product').val(), pid : $('#hProductID').val() }
            , function( response ) {
                CraigslistForm.loadProductResponse( response );
                CraigslistForm.preview();
            }
        );
    }

    , setupAutocomplete: function() {
        var searchType = $("#sAutoComplete").val();
        var nonce = $('#_autocomplete_owned').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/products/autocomplete-owned/?_nonce=' + nonce + '&type=' + searchType + '&owned=1&term=%QUERY'
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
            .on('typeahead:selected', CraigslistForm.loadProduct );
    }

    , loadProduct: function( event, item ) {
        $.post(
            '/craigslist/load-product/'
            , { _nonce : $('#_load_product').val(), pid : item.value }
            , CraigslistForm.loadProductResponse
        );
    }

    , loadProductResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var product = response.product;
            
            $('#hProductDescription').val( product.description );
            $('#hProductName').val( product.name );
            $('#hProductCategoryID').val( product.category_id );
            $('#hProductID').val( product.product_id );
            $('#hProductCategoryName').val( product.category );
            $('#hProductSKU').val( product.sku );
            $('#hProductBrandName').val( product.brand );
            $('#hProductSpecifications').val( product.specifications );
            $('#tPrice[val=""]').val( product.price );

            $('#dProductPhotos').html( product.image );
            $('#dPreviewTemplate').hide();
            $('#dCreateAd, #dPreviewAd').show();
        }
    }

    , preview: function() {

        var productName = $("#hProductName").val(), storeName = $("#hStoreName").val(), storeLogo = $("#hStoreLogo").val(), sku = $("#hProductSKU").val();
        var storeURL = $('#hStoreURL').val(), category = $("#hProductCategoryName").val(), brand = $("#hProductBrandName").val(), productDescription = $("#hProductDescription").val(), productSpecifications = $("#hProductSpecifications").val();

        storeLogo = ( storeLogo.search( /http:/i ) > -1 ) ? storeLogo : storeURL + '/custom/uploads/images/' + storeLogo;

        //get the contents of the tinyMCE editor and replace tags with actual stuff.
        var newContent = CKEDITOR.instances.taDescription.getData();

        newContent = newContent.replace( '[Product Name]', productName );
        newContent = newContent.replace( '[Store Name]', storeName );
        newContent = newContent.replace( '[Store Logo]', '<img src="' + storeLogo + '" alt="" />' );
        newContent = newContent.replace( '[Category]', category );
        newContent = newContent.replace( '[Brand]', brand );
        newContent = newContent.replace( '[Product Description]', productDescription );
        newContent = newContent.replace( '[SKU]', sku );
        newContent = newContent.replace( '[Product Specifications]', productSpecifications );

        var photos = document.getElementsByClassName( 'hiddenImage' ), photoHTML = '', index = 0;

        if ( photos.length ) {
            while ( newContent.indexOf( '[Photo]' ) >= 0 ) {
                if ( index >= photos.length )
                    index = 0;

                photoHTML = '<img src="' + photos[index]['src'] + '" />';
                newContent = newContent.replace( "[Photo]", photoHTML );
                index++;
            }
        }

        $("#preview").html( newContent );
        $('#hCraigslistPost').val( newContent );
    }

    , postAD: function() {
        // Say we want to post it
        $('#hPostAd').val('1');

        // Submit the form
        $('#fAddCraigslistTemplate').submit();
    }

}

jQuery( CraigslistForm.init );