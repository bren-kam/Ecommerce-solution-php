var OrderView = {

    init: function() {
        $('#status').change( OrderView.updateStatus );
        $('.toggle-options').click( OrderView.toggleOptions );
    }

    , updateStatus: function() {
        $.post(
            '/shopping-cart/orders/update-status/'
            , { _nonce : $('#_nonce').val(), s : $(this).val(), woid : $(this).data('order-id') }
            , GSR.defaultAjaxResponse
        );
    }

    , toggleOptions: function(e) {
        var anchor = $(this);

        e.preventDefault();

        if ( anchor.hasClass('open') ) {
            $( anchor.attr('href') ).hide();
            anchor.find('span').text('[ + ]');
        } else {
            $( anchor.attr('href')).removeClass('hidden').show();
            anchor.find('span').text('[ - ]');
        }
        anchor.toggleClass('open');
    }

};

jQuery( OrderView.init );