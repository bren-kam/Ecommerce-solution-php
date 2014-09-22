AttributeEditor = {

    template: null

    , init: function() {

        // Template
        AttributeEditor.template = $('#attribute-item-template').clone();
        AttributeEditor.template.removeClass('hidden').removeAttr('id');
        $('#attribute-item-template').remove();

        // Events
        $('#add-item').click( AttributeEditor.add );
        $('body').on( 'click', '.delete-attribute-item', AttributeEditor.remove );

        // Make attributes sortable
        $("#attribute-items-list").sortable({
            forcePlaceholderSize : true
            , placeholder: 'list-item-placeholder'
            , handle : '.move'
        });
    }

    , add: function(e) {
        if (e) e.preventDefault();

        var item = $('#tItem');
        var itemNames = item.val().split(',');
        var itemsList = $('#attribute-items-list');

        for ( var i in itemNames ) {
            var itemName = itemNames[i];

            if ( !itemName.length )
                return;

            AttributeEditor.template
                .clone()
                .find( 'input:first' ).val( itemName ).end()
                .appendTo( itemsList );
        }

        item.val('').trigger('blur');

        $("#attribute-items-list" ).sortable('refresh');
    }

    , remove: function(e) {
        if (e) e.preventDefault();

        $(this).parents('.attribute-item:first').remove();
    }
}

jQuery( AttributeEditor.init );