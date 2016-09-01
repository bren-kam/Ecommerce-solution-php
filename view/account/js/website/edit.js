var ProductForm = {
    
    template: null  
    
    , init: function() {

        // Autocomplete
        ProductForm.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#sAutoComplete').change( ProductForm.setupAutocomplete );

        // Search button
        $('#bSearch').click( ProductForm.refreshTable );

        // Setup DataTable
        $('#tAddProducts').dataTable({
            aaSorting: [[0,'asc']],
            bAutoWidth: false,
            bProcessing : 1,
            bServerSide : 1,
            iDisplayLength : 5,
            sAjaxSource : '/website/list-products/',
            sDom : '<"top"lr>t<"bottom"pi>',
            oLanguage: {
                sLengthMenu: 'Rows: <select><option value="5">5</option><option value="10">10</option><option value="20">20</option></select>'
                , sInfo: "_START_ - _END_ of _TOTAL_"
                , oPaginate: {
                    sNext : ''
                    , sPrevious : ''
                }
            },
            fnServerData: function ( sSource, aoData, fnCallback ) {
                aoData.push({ name : 's', value : $('#tAutoComplete').val() });
                aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });

                // Get the data
                $.get( sSource, aoData, fnCallback );
            }
        });

        // Add Product & Product Template
        $( '#show-product-form' ).click( ProductForm.showProductForm );
        $( '#tAddProducts' ).on( 'click', '.add-product', ProductForm.addProduct );
        ProductForm.template = $( '#product-template' ).clone().removeClass('hidden').removeAttr('id');
        $( '#product-template' ).remove();

        // Remove Product
        $( '#product-list' ).on( 'click', '.remove', ProductForm.removeProduct );

        // Page's products sortable
        $("#product-list").sortable( {
            scroll: true,
            cancel: "h4,p",
            placeholder: 'product-placeholder'
        });
    }
    , setupAutocomplete: function() {

        var searchType = $("#sAutoComplete").val();
        var nonce = $('#_autocomplete').val();
/*
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
            .on('typeahead:selected', ProductForm.search );
*/

    }
    , refreshTable: function ( ) {
        console.log(5);
        $('#tAddProducts').dataTable().fnDraw();
    }

    , showProductForm: function() {
        $( '#product-form' ).removeClass( 'hidden' );
    }

    , addProduct: function(e) {
        var anchor = $(this);
        if (e) e.preventDefault();
        $.get(
            anchor.attr( 'href' )
            , ProductForm.addProductResponse
        )
    }

    , addProductResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            ProductForm.template.clone()
                .find( 'h4' ).text( response.product.name ).end()
                .find( 'img' ).attr( 'src', response.product.image_url ).end()
                .find( '.brand' ).text( response.product.brand ).end()
                .find( 'input' ).val( response.product.id ).end()
                .appendTo( '#product-list' )
        }
    }

    , removeProduct: function() {
        $(this).parents( '.product:first' ).remove();
    }
};

var CurrentOfferForm = {

    uploader: null

    , init: function() {
        // Not a Current Offer page?
        if ( $( '#current-offer-settings' ).size() == 0 )
            return;

        // Setup File Uploader
        CurrentOfferForm.uploader = new qq.FileUploader({
            action: '/website/upload-image/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: CurrentOfferForm.submit
            , onComplete: CurrentOfferForm.complete
        });

        // Upload file trigger
        $('#upload').click( CurrentOfferForm.open );

    }

    , submit: function( id, fileName ) {
        CurrentOfferForm.uploader.setParams({
            _nonce : $('#_upload_image').val()
            , apid : $('#fEditPage').data( 'account-page-id' )
            , fn   : 'coupon'
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            $('#current-coupon' ).html( '<img src="' + response.url + '" />' );
        }
    }

    , open: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

};


var FinancingForm = {

    uploader: null

    , init: function() {
        // Not a Financing page?
        if ( $( '#financing-settings' ).size() == 0 )
            return;

        // Setup File Uploader
        FinancingForm.uploader = new qq.FileUploader({
            action: '/website/upload-image/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: FinancingForm.submit
            , onComplete: FinancingForm.complete
        });

        // Upload file trigger
        $('#upload').click( FinancingForm.open );

    }

    , submit: function( id, fileName ) {
        FinancingForm.uploader.setParams({
            _nonce : $('#_upload_image').val()
            , apid : $('#fEditPage').data( 'account-page-id' )
            , fn   : 'financing'
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , complete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            $( '#apply-now-button' ).html( '<img src="' + response.url + '" />' );
        }
    }

    , open: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

};

var ContactUsForm = {

    uploader: null

    , template: null

    , init: function() {
        // Not a Contact Us page?
        if ( $( '#contact-us-settings' ).size() == 0 )
            return;

        ContactUsForm.template = $( '#location-template' ).clone().removeClass('hidden');
        $( '#location-template' ).remove();

        // Place this at the end and wrap this content in a form
        // We create form here because browsers don't allow a form within a form
        var form = $( '<form />', { method: 'post', action: '/website/add-edit-location/', role: 'form' } );
        $( '#locationModal' ).appendTo('body');
        $( '#locationModal' ).wrapInner( form );

        $( '#add-location' ).click( ContactUsForm.showLocationForm );

        // Setup File Uploader
        ContactUsForm.uploader = new qq.FileUploader({
            action: '/website/upload-file/'
            , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
            , element: $('#uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: ContactUsForm.uploaderSubmit
            , onComplete: ContactUsForm.uploaderComplete
        });

        // Upload file trigger
        $('#upload').click( ContactUsForm.uploaderOpen );
        $('#remove-store-image' ).click( ContactUsForm.removeStoreImage );

        // Form Submit
        $( '#locationModal form' ).submit( ContactUsForm.saveLocation );

        // Edit Location
        $( '#location-list' ).on( 'click', '.edit', ContactUsForm.editLocation );

        // Remove Location
        $( '#location-list' ).on( 'click', '.remove', ContactUsForm.removeLocation );

        // Sortable
        $( '#location-list' ).sortable({
            items		: '.location',
            cancel		: 'a',
            placeholder	: 'location-placeholder',
            forcePlaceholderSize : true,
            update: ContactUsForm.updateLocationSequence
        });

        // Multiple Location Map toggle
        $('#cbMultipleLocationMap').click( function() {
            $.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'mlm', v : $(this).is(':checked') ? true : false, apid : $('#fEditPage').data('account-page-id') }, GSR.defaultAjaxResponse );
        });

        // Hide All Maps toggle
        $('#cbHideAllMaps').click( function() {
            $.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'ham', v : $(this).is(':checked') ? true : false, apid : $('#fEditPage').data('account-page-id') }, GSR.defaultAjaxResponse );
        });

        //Hide Contact Form toggle
        $('#cbHideContactForm').click( function() {
            $.post( '/website/set-pagemeta/', { _nonce: $('#_set_pagemeta').val(), k : 'hcf', v : $(this).is(':checked') ? true : false, apid : $('#fEditPage').data('account-page-id') }, GSR.defaultAjaxResponse );
        });
    }

    , showLocationForm: function( e ) {

        $( '#locationModal form' ).get( 0 ).reset();
        $( '#wlid' ).remove();

        // its an edit?
        if ( e && e.location ) {
            var location = e.location;
            $( '#name' ).val( location.name );
            $( '#phone' ).val( location.phone );
            $( '#fax' ).val( location.fax );
            $( '#email' ).val( location.email );
            $( '#store-hours' ).val( location.store_hours );
            $( '#address' ).val( location.address );
            $( '#city' ).val( location.city );
            $( '#state' ).val( location.state );
            $( '#zip' ).val( location.zip );
            $( '#website' ).val( location.website );
            $( '<input />', { type: 'hidden', name: 'wlid', id: 'wlid' } ).val( location.id ).appendTo( '#locationModal form' );
        }

        $( '#locationModal' ).modal();
    }

    , editLocation: function() {
        var location_id = $( this ).parents( '.location' ).data( 'location-id' );
        $.get(
            '/website/get-location/'
            , { wlid: location_id, _nonce: $('#_get_location' ).val() }
            , ContactUsForm.showLocationForm
        )
        $( '#locationModal' ).modal();
    }

    , saveLocation: function(e) {
        var form = $(this);

        if (e) e.preventDefault();

        $.post(
            form.attr( 'action' )
            , form.serialize()
            , ContactUsForm.saveLocationResponse
        )
    }

    , saveLocationResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var location = response.location;

            var container = $('#location-list [data-location-id='+ location.id +']');
            if ( container.size() == 0 ) {
                container = ContactUsForm.template.clone();
                container.appendTo( '#location-list' );
            }

            container
                .attr( 'data-location-id', location.id )
                .find( 'h3' ).text( location.name ).end()
                .find( 'p:first span:first' ).html( location.address + '<br>' + location.city + ', ' + location.state + ' ' + location.zip ).end()
                .find( 'p:first span:last' ).html( location.phone + '<br>' + location.fax ).end()
                .find( 'p:nth-child(2)' ).html( location.email + '<br>' + location.website ).end()
                .find( 'p.store-hours' ).html( '<strong>Store Hours:</strong> <br />' + location.store_hours ).end();
        }
        $( '#locationModal' ).modal( 'hide' );
    }

    , removeLocation: function() {
        var location = $( this ).parents( '.location' );
        var location_id = location.data( 'location-id' );
        $.get(
            '/website/delete-location/'
            , { wlid: location_id, _nonce: $('#_delete_location' ).val() }
            , function( r ) {
                if ( r.success ) {
                    location.remove();
                }
            }
        )
    }

    , uploaderSubmit: function( id, fileName ) {
        ContactUsForm.uploader.setParams({
            _nonce : $('#_upload_file').val()
            , fn: 'location-image-' + (new Date().getTime().toString())
        });

        $('#upload').hide();
        $('#upload-loader').removeClass('hidden').show();
    }

    , uploaderComplete: function( id, fileName, response ) {
        $('#upload-loader').hide();
        $('#upload').show();

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {
            $( '#store-image' ).removeClass('hidden' ).show();;
            $( '#store-image input' ).val( response.url );
            $( '#store-image img' ).remove();
            $( '#store-image' ).append( '<img src="' + response.url + '" />' );
        }
    }

    , uploaderOpen: function(e) {
        if ( e )
            e.preventDefault();

        if ( $.support.cors ) {
            $('#uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

    , removeStoreImage: function() {
        $( '#store-image input' ).val( '' );
        $( '#store-image img' ).remove();
        $( '#store-image' ).hide();
    }

    , updateLocationSequence: function() {
        var sequence = [];
        $( '#location-list .location' ).each( function(){
            sequence.push($( this ).data('location-id') );
        })

        $.post(
            '/website/update-location-sequence/'
            , { s: sequence.join('|'), _nonce: $('#_update_location_sequence' ).val() }
            , GSR.defaultAjaxResponse
        );
    }

}

var PageForm = {

    init: function() {

    }

};

jQuery( PageForm.init );
jQuery( ProductForm.init );
jQuery( CurrentOfferForm.init );
jQuery( FinancingForm.init );
jQuery( ContactUsForm.init );
