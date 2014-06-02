// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', '/resources/js_single/?f=jquery.nestedSortable', function() {
    $('#navigation-menu-list').nestedSortable({
        listType: 'ul',
        handle: 'div',
        items: 'li',
        placeholder: 'placeholder',
        isTree: true,
        maxLevels: 2
    }).on( 'click', 'a.delete-item', function(e) {
        e.preventDefault();
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
            .find('div').append('<input type="hidden" name="navigation[]" value="' + url + '|' + menuItemName.val() + '">').end()
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

    $('#fNavigation').submit( function() {
        var tree = $('#navigation-menu-list').nestedSortable('toHierarchy');
        $('<input />', {
            type: 'hidden'
            , name: 'tree'
            , value: JSON.stringify( tree )
        }).appendTo(this);
    });
});