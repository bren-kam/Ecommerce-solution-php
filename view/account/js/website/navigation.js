// When the page has loaded
head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
    $('#navigation-menu-list').sortable({
        update: function() {
            //var categoriesList = $("#categories-list").sortable('serialize'), parentCategoryID = $('#current-category span:first').attr('rel');
            //$.post( '/products/categories/update-sequence/', { _nonce : $('#_update_sequence').val(), pcid: parentCategoryID, sequence : categoriesList }, ajaxResponse, 'json' );
        },
        placeholder: 'menu-item-placeholder'
    });

    $('#add-menu-item').click( function(e) {
        e.preventDefault();

        var navigationBox = $('#dAddEditNavigation'), menuItemName = $('#menu-item-name'), clone = $('#dMenuItem').clone(), url = $('#' + navigationBox.find('input[name="menu-link"]:checked').val()).val();

        clone
            .find('h4:first').text( menuItemName.val() ).end()
            .find('a.url:first').attr( 'href', url ).text( url).end()
            .appendTo( $('#navigation-menu-list') );

        $('.close:visible').click();

        menuItemName.val('');
        navigationBox.find('input[name="menu-link"]:first').attr( 'checked', true );
        $('#menu-url').val('');
        $('#menu-page option:first').attr( 'selected', true );
    });
});