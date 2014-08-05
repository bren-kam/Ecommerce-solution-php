var Navigation = {

    template: null

    , init: function() {
        $('#navigation').nestable( {
            maxDepth: 1
        }).on( 'change', Navigation.updateTree ).change();

        $('#save-menu-item').click( Navigation.create );
        $('#navigation').on( 'click', '.delete', Navigation.deleteItem );
        $('#link-select').change( Navigation.setLink );
        $('#link').keyup( Navigation.resetLinkSelect );

        Navigation.template = $('#item-template').clone().removeClass('hidden').removeAttr('id');
        $('#item-template').parents('ul').remove();
    }

    , create: function() {

        Navigation.template.clone()
            .find('.dd3-content').prepend( $('#name').val() ).end()
            .find('.page-url').prepend( $('#link').val() ).end()
            .find('input').attr( 'name', 'footer-navigation[' + (new Date()).getTime() + ']').val( $('#name').val() + '|' + $('#link').val() ).end()
            .appendTo('#navigation > .dd-list');

        $('#name').val( '' );
        $('#link').val( '' );
        $('#link-select').val( '' );
        $('#add-menu-item').modal('hide');
    }

    , deleteItem: function() {
        $(this).parents('.dd-item:first').remove();
        $('#navigation').change();
    }

    , setLink: function() {
        if ( $(this).val() != '' ) {
            $('#link').val( $(this).val() );
        }
    }

    , resetLinkSelect: function() {
        if ( $(this).val() != '' ) {
            $('#link-select').val( '' );
        }
    }

    , updateTree: function(e){
        $('#tree').val( JSON.stringify( $(e.target).nestable('serialize') ) );
    }

}

jQuery( Navigation.init );