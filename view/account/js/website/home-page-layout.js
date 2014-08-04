var Layout = {

    init: function() {
        $('#layout-list').on( 'switchChange.bootstrapSwitch', ':checkbox', Layout.changeStatus );

        $('#layout-list').sortable({
            items		: '.layout',
            cancel		: 'input, button, a',
            placeholder	: 'banner-placeholder',
            revert		: true,
            forcePlaceholderSize : true
        });


    }

    , changeStatus: function( event, state ) {
        var element = $(this).parents('.layout');
        var input = element.find('.layout-value');

        var search = state ? '|1' : '|0';
        var replace = state ? '|0' : '|1';
        input.val( input.val().replace( search, replace ) );

        if ( state )
            element.removeClass('disabled');
        else
            element.addClass('disabled');
    }

}

jQuery( Layout.init );
