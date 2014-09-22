ProductOptionEditor = {

    template: null

    , init: function() {

        // Template
        ProductOptionEditor.template = $('#product-option-item-template').clone();
        ProductOptionEditor.template.removeClass('hidden').removeAttr('id');
        $('#product-option-item-template').remove();

        // Events For Product Option Dropdown Items
        $('#add-item').click( ProductOptionEditor.add );
        $('body').on( 'click', '.delete-product-option-item', ProductOptionEditor.remove );

        $('.switch-form').click( ProductOptionEditor.show );
        $('.switchable-form.hidden').removeClass('hidden').hide();
    }

    , show: function(e) {
        e.preventDefault();

        var target = $(this).attr('href');
        var title = '- ' + target.replace( /-/g, ' ' ).substring( 1 );

        $('.switchable-form').hide();
        $( target ).show();

        $('#form-type').text( title );
    }

    , add: function(e) {
        if (e) e.preventDefault();

        var item = $('#tItem');
        var itemsList = $('#product-option-item-list');

        if ( item.val() == '')
            return;

        ProductOptionEditor.template
            .clone()
            .find( 'input:first' ).val( item.val() ).end()
            .appendTo( itemsList );

        item.val('').trigger('blur');
    }

    , remove: function(e) {
        if (e) e.preventDefault();

        $(this).parents('.product-option-item:first').remove();
    }
}

jQuery( ProductOptionEditor.init );