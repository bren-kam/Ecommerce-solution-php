var Services = {
    basePrice: null,
    calculateBasePrice : function() {
        if ( Services.basePrice != null )
            return;

        var basePrice = $('#original-subscription').val();

        $('#services input[type=checkbox]').each(function() {
            if ( $(this).is(':checked') )
                basePrice -= $(this).data('price')
        });

        Services.basePrice = basePrice;
    },
    calculatePrice : function() {
        var originalPrice = $('#original-subscription').val(), newPrice = Services.basePrice;

        $('#services input[type=checkbox]').each(function() {
            if ( $(this).is(':checked') )
                newPrice += $(this).data('price') * 1
        });

        if ( Services.updateChangedServices() ) {
            $('.new-price').text(newPrice);

            var priceDifference = newPrice - originalPrice;

            if ( priceDifference > 0 ) {
                $('.price-difference').text( '(+$' + priceDifference.toLocaleString() + ')' );
            } else if (priceDifference < 0) {
                $('.price-difference').text( '(-$' + (priceDifference * - 1).toLocaleString() + ')' );
            } else {
                $('.price-difference').text('');
            }

            $('#service-change').show();
        } else {
            $('#service-change').hide();
            $('.new-price').text(newPrice.toLocaleString());
            $('#new-price').val(newPrice);
            $('.price-difference').text('');
        }
    },
    init: function() {
        Services.calculateBasePrice();

        // Upload file trigger
        $('#services input[type=checkbox]').click( Services.calculatePrice );

        $("#form-services").submit( function() {
            if ( !$('#verify').is(':checked') ) {
                alert( 'You must verify the service changes before going on.')
                $('#verify').focus();
                return false;
            }
        });
    },
    updateChangedServices: function() {
        var changed = false, newServices = [], oldServices = [];

        $('#services input[type=checkbox]').each( function() {
            if ( 'checked' != $(this).data('default') && $(this).is(':checked') ) {
                changed = true;
                newServices.push($(this).data('group') + ' - ' + $(this).parent().text().replace(/\(/, '(+'));
            } else if ( 'checked' == $(this).data('default') && !$(this).is(':checked') ) {
                changed = true;
                oldServices.push($(this).data('group') + ' - ' + $(this).parent().text().replace(/\(/, '(-'));
            }

        });

        if ( newServices.length ) {
            $('#new-services').html( '<strong>Add Services</strong><br>' + newServices.join('<br>')).show();
        } else {
            $('#new-services').hide();
        }

        if ( oldServices.length ) {
            $('#old-services').html( '<strong>Disable Services</strong><br>' + oldServices.join('<br>')).show();
        } else {
            $('#old-services').hide();
        }

        return changed;
    }
}

jQuery( Services.init );
