var Listings = {

    init: function() {
        $('#location-id').change( Listings.changeLocation );
    }

    , changeLocation: function() {
        var locationId = $(this).val();
        $.post(
            '/geo-marketing/listings/store-session/'
            , { _nonce : $('#_store_session').val(), keys : [ 'listings', 'location-id' ], value : locationId }
            , function() {
                GSR.defaultAjaxResponse( { 'reload_datatable': 'reload_datatable' } );
            }
        );
    }

};

jQuery( Listings.init );