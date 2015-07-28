ProductOptionEditor = {

    template: null
    , groupTemplate: null

    , init: function() {
        // Template
        ProductOptionEditor.template = $('#product-option-item-template').clone();
        ProductOptionEditor.template.removeClass('hidden').removeAttr('id');
        $('#product-option-item-template').remove();

        // Events For Product Option Dropdown Items
        $('body').on('click', '.add-item', ProductOptionEditor.add )    ;
        $('body').on( 'click', '.delete-product-option-item', ProductOptionEditor.remove );

        $('.switch-form').click( ProductOptionEditor.show );
        $('.switchable-form.hidden').removeClass('hidden').hide();

        ProductOptionEditor.groupTemplate = $('#product-option-group-template').clone();
        ProductOptionEditor.groupTemplate.removeClass('hidden').removeAttr('id');
        $('#product-option-group-template').remove();

        $('#add-product-option-group').click(ProductOptionEditor.addGroup);
        $('body').on( 'click', '.delete-product-option-group', ProductOptionEditor.removeGroup );

        if ( $('#product-option-group-container div').size() == 0 ) {
            ProductOptionEditor.addGroup();
        }
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

        var productOptionGroup = $(this).parents('.product-option-group');
        var item = productOptionGroup.find('.add-item-text');
        var itemsList = productOptionGroup.find('.product-option-item-list');

        if ( item.val() == '')
            return;

        ProductOptionEditor.template
            .clone()
            .find( 'input:first' )
                .attr('name', 'list-items['+ productOptionGroup.data('group-id') +'][]')
                .val( item.val() )
                .end()
            .appendTo( itemsList );

        item.val('').focus();
    }

    , remove: function(e) {
        if (e) e.preventDefault();

        var productOptionGroup = $(this).parents('.product-option-group');
        if ( !productOptionGroup.has('input.action') || productOptionGroup.has('input.action') && confirm('By clicking "OK" you will also delete all Product Permutations relating to this item.') )
            $(this).parents('.product-option-item:first').remove();
    }

    , addGroup: function() {
        var optionGroup = ProductOptionEditor.groupTemplate.clone();
        var id = (new Date).getTime();

        optionGroup.find('input:first')
            .attr('name', 'option-name[n' + id + ']');

        optionGroup.data('group-id', id);

        $('#product-option-group-container').append(optionGroup);
    }

    , removeGroup: function() {
        var productOptionGroup = $(this).parents('.product-option-group');

        if ( !productOptionGroup.has('input.action') || productOptionGroup.has('input.action') && confirm('By clicking "OK" you will also delete all Product Permutations in this product option.') )
            productOptionGroup.remove();
    }
}

jQuery( ProductOptionEditor.init );