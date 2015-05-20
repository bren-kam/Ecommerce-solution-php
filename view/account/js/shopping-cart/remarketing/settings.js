var PopupForm = {

    /**
     * Init
     */
    init: function() {
        $('#submit-color').colpick({
            onChange: PopupForm.changeSubmitColor
        });

        MediaManager.submit = PopupForm.setImage;

        $('#coupon-image').mouseleave(PopupForm.hideDeleteCoupon);
        $('#coupon-image').mouseenter(PopupForm.showDeleteCoupon);
        $('#delete-coupon').click(PopupForm.deleteCoupon);

        autosize($('#popup-title'));
        autosize($('#popup-text'));

        $('.popover-container').popover();
    }

    /**
     * Change Submit Color
     */
    , changeSubmitColor: function(hsb, hex, rgb, e, bySetColor) {
        color = "#" + hex;
        $('#popup-submit-color').val(color);
        $('#selected-color').text(color);
        $('#submit-color').css('background-color', color);
    }

    /**
     * Set Image - Overwrites MediaManager submit function to add images
     */
    , setImage: function() {
        var file = MediaManager.view.find( '.mm-file.selected:first').parents( 'li:first').data();

        if ( file && MediaManager.isImage( file ) ) {
            $( MediaManager.targetOptions.imageTarget )
                .find('img:first').attr('src', file.url).end()
                .find('input').val(file.url).end();
        }
    }

    , deleteCoupon: function() {
        $('#coupon-image').find('img').attr('src', '//placehold.it/700x200/eee/a1a1a1&text=upload+coupon');
        $('#coupon-image').find('input').val('');
    }

    , hideDeleteCoupon: function() {
        $('#delete-coupon').hide();
    }

    , showDeleteCoupon: function() {
        if ( $('#coupon-image').find('input').val() ) {
            $('#delete-coupon').show();
        }
    }
};

jQuery(PopupForm.init);
