var Settings = {
    init: function () {

    }
    , removeAmazon: function () {
        var check = confirm('Are you sure you want to erase your Amazon Access Keys?');
        if (check) {
            $.get('/shopping-cart/shipping/remove-amazon', {}, function (response) {
                location.reload();
            });
        }
    }
};

jQuery(Settings.init);