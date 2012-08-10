// When the page has loaded
jQuery(function($) {
    $('.on-off, #tFeatures label').click( function() {
        if ( $(this).is('a') ) {
            var onOff = $(this), checkbox = $(this).next();
        } else {
            var tr = $(this).parent().next(), onOff = $('.on-off:first', tr), checkbox = ('input:first', tr);
        }

        onOff.toggleClass('selected');
        checkbox.attr( 'checked', !checkbox.is(':checked') );
    });
});