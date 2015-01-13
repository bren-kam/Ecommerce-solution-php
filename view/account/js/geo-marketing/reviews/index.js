var Listings = {

    init: function() {
        $('#location-id').change( Listings.changeLocation );
        $('#site-id').change( Listings.changeSite );
    }

    , changeLocation: function() {
        var locationId = $(this).val();
        $.post(
            '/geo-marketing/listings/store-session/'
            , { _nonce : $('#_store_session').val(), keys : [ 'reviews', 'location-id' ], value : locationId }
            , function() {
                GSR.defaultAjaxResponse( { 'reload_datatable': 'reload_datatable' } );
            }
        );
    }

    , changeSite: function() {
        var siteId = $(this).val();
        $.post(
            '/geo-marketing/listings/store-session/'
            , { _nonce : $('#_store_session').val(), keys : [ 'reviews', 'site-id' ], value : siteId }
            , function() {
                GSR.defaultAjaxResponse( { 'reload_datatable': 'reload_datatable' } );
            }
        );
    }
};

jQuery( Listings.init );