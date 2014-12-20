var Services = {
    basePrice: null,
    calculateBasePrice : function() {
        if ( Services.basePrice != null )
            return;

        var basePrice = $('#original-subscription').val();

        $('#form-services input[type=checkbox]').each(function() {
            if ( $(this).is(':checked') )
                basePrice -= $(this).data('price')
        });

        Services.basePrice = basePrice;
    },
    calculatePrice : function() {
        var originalPrice = $('#original-subscription').val(), newPrice = Services.basePrice;

        $('#form-services input[type=checkbox]').each(function() {
            if ( $(this).is(':checked') )
                newPrice += $(this).data('price') * 1
        });

        if ( newPrice == originalPrice ) {
            $('#price-change').hide();
            $('.new-price').text(newPrice);
            $('.price-difference').text('');
        } else {
            $('.new-price').text(newPrice);

            var priceDifference = newPrice - originalPrice;

            if ( priceDifference > 0 ) {
                $('.price-difference').text( '+$' + priceDifference.toLocaleString() );
            } else {
                $('.price-difference').text( '-$' + (priceDifference * - 1).toLocaleString() );
            }
            console.log('made it');
            $('#price-change').show();
        }
    },
    init: function() {
        Services.calculateBasePrice();

        // Upload file trigger
        $('#form-services input[type=checkbox]').click( Services.calculatePrice );
    }
}

jQuery( Services.init );
