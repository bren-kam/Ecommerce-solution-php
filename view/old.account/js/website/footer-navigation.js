// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    $('#navigation-menu-list').sortable({
        update: function() {},
        placeholder: 'menu-item-placeholder'
    }).on( 'click', 'a.delete-item', function() {
        if( confirm( $(this).attr('data-confirm') ) )
            $(this).parents('.menu-item:first').remove();
    });

    $('#add-menu-item').click( function(e) {
        e.preventDefault();

        var navigationBox = $('#dAddEditNavigation'), menuItemName = $('#menu-item-name'), clone = $('#dMenuItem').clone(), url = $('#' + navigationBox.find('input[name="menu-link"]:checked').val()).val();

        displayUrl = ( -1 == url.indexOf('http') ) ? '/' + url + '/' : url;

        if ( '//' == displayUrl )
            displayUrl = '/';

        clone
            .find('h4:first').text( menuItemName.val() ).end()
            .find('a.url:first').text( displayUrl ).end()
            .removeAttr('id')
            .append('<input type="hidden" name="footer-navigation[]" value="' + url + '|' + menuItemName.val() + '">')
            .appendTo( $('#navigation-menu-list') );

        $('.close:visible').click();

        menuItemName.val('');
        navigationBox.find('input[name="menu-link"]:first').click();
        $('#menu-url').val('');
        $('#menu-page option:first').attr( 'selected', true );
    });

    // Make sure the right one is selected
    $('#menu-url').keyup( function() {
        $('#dAddEditNavigation input[name="menu-link"]:first').click();
    });

    $('#menu-page').change( function() {
        $('#dAddEditNavigation input[name="menu-link"]:last').click();
    });
});