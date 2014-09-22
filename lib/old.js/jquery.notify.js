(function ($) {
    $.fn.extend({
        notify: function (options) {
            var settings = $.extend({ type: 'sticky', fade: 5000, speed: 500, onDemandButtonHeight: 35 }, options);
            return this.each(function () {
                var wrapper = $(this);

                // Fade out and remove
                if ( 0 != settings.fade )
                setTimeout( function() {
                    wrapper.fadeOut( 500, function() {
                        $(this).remove();
                    });
                }, settings.fade );

                var ondemandBtn = $('.ondemand-button');
                var dh = -35;
                var w = wrapper.outerWidth() - ondemandBtn.outerWidth();
                ondemandBtn.css('left', w).css('margin-top',  dh + "px" );
                var h = -wrapper.outerHeight();
                wrapper.addClass(settings.type).css('margin-top', h).show();
                if (settings.type != 'ondemand') {
                    wrapper.stop(true, false).animate({ marginTop: 0 }, settings.speed);
                }
                else {
                    ondemandBtn.stop(true, false).animate({ marginTop: 0 }, settings.speed);
                }

                var closeBtn = $('.close', wrapper);
                closeBtn.click(function () {
                    if (settings.type == 'ondemand') {
                        wrapper.stop(true, false).animate({ marginTop: h }, settings.speed, function () {
                            wrapper.hide();
                            ondemandBtn.stop(true, false).animate({ marginTop: 0 }, settings.speed);
                        });
                    }
                    else {
                        wrapper.stop(true, false).animate({ marginTop: h }, settings.speed, function () {
                            wrapper.hide();
                        });
                    }
                });
                if (settings.type == 'floated') {
                    $(document).scroll(function (e) {
                        wrapper.stop(true, false).animate({ top: $(document).scrollTop() }, settings.speed);
                    }).resize(function (e) {c
                        wrapper.stop(true, false).animate({ top: $(document).scrollTop() }, settings.speed);
                    });
                }
                else if (settings.type == 'ondemand') {
                    ondemandBtn.click(function () {
                        $(this).animate({ marginTop: dh }, settings.speed, function () {
                            wrapper.show().animate({ marginTop: 0 }, settings.speed, function () {

                            });
                        })
                    });
                }

            });

        }
    });
})(jQuery);