// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    $('#navigation-menu-list').sortable({
        update: function() {
            //var categoriesList = $("#categories-list").sortable('serialize'), parentCategoryID = $('#current-category span:first').attr('rel');
            //$.post( '/products/categories/update-sequence/', { _nonce : $('#_update_sequence').val(), pcid: parentCategoryID, sequence : categoriesList }, ajaxResponse, 'json' );
        },
        placeholder: 'menu-item-placeholder'
    }).on( 'click', 'a.delete-item', function() {
        if( confirm( $(this).attr('data-confirm') ) )
            $(this).parents('.menu-item:first').remove();
    });

    $('#add-menu-item').click( function(e) {
        e.preventDefault();

        var navigationBox = $('#dAddEditNavigation'), menuItemName = $('#menu-item-name'), clone = $('#dMenuItem').clone(), url = $('#' + navigationBox.find('input[name="menu-link"]:checked').val()).val();

        displayUrl = ( -1 == url.indexOf('http') ) ? '/' + url + '/' : url;

        clone
            .find('h4:first').text( menuItemName.val() ).end()
            .find('a.url:first').attr( 'href', displayUrl ).text( url).end()
            .removeAttr('id')
            .append('<input type="hidden" name="navigation[]" value="' + url + '|' + menuItemName.val() + '">')
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