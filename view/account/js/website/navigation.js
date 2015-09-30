var Navigation = {

    template: null

    , init: function() {
        $('#navigation').nestable( {
            maxDepth: 3,
            dropCallback: function(dropped) {
                if(dropped.destParent && dropped.destParent[0] && dropped.destParent[0].dataset && dropped.destParent[0].dataset.id){
                    var id=dropped.destParent[0].dataset.id;
                    var el= $("ol").find("[data-id='"+id+"']");
                    var total_children = $(el).find("ol").children().length;

                    if(total_children==1){
                        //Parent element turned into dropdown
                        var element_id = (new Date()).getTime().toString().substring(8);
                        var name = $(el).find(".dd3-content").first().clone().children().remove().end().text().trim();
                        var url = $(el).find(".page-url").first().text().trim();

                        //Duplicate it as first child
                        $(el).find("ol.dd-list").prepend(Navigation.template.clone()
                            .data('id', element_id )
                            .find('.dd3-content').prepend( name ).end()
                            .find('.page-url').prepend( url ).end()
                            .find('input').attr( 'name', 'navigation[' + element_id + ']').val( url + '|' + name ).end()
                        );

                        Navigation.updateTree();
                    }
                }
            }
        }).on( 'change', Navigation.updateTree ).change();

        $('#save-menu-item').click( Navigation.create );
        $('#navigation').on( 'click', '.delete', Navigation.deleteItem );
        $('#link-select').change( Navigation.setLink );
        $('#link').keyup( Navigation.resetLinkSelect );

        Navigation.template = $('#item-template').clone().removeClass('hidden').removeAttr('id');
        $('#item-template').parents('ul').remove();
    }

    , create: function() {
        var element_id = (new Date()).getTime().toString().substring(8);

        Navigation.template.clone()
            .data('id', element_id )
            .find('.dd3-content').prepend( $('#name').val() ).end()
            .find('.page-url').prepend( $('#link').val() ).end()
            .find('input').attr( 'name', 'navigation[' + element_id + ']').val( $('#link').val() + '|' + $('#name').val() ).end()
            .appendTo('#navigation > .dd-list');

        $('#name').val( '' );
        $('#link').val( '' );
        $('#link-select').val( '' );
        $('#add-menu-item').modal('hide');

        Navigation.updateTree();
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
        $('#tree').val( JSON.stringify( $('#navigation').nestable('serialize') ) );
    }

}

jQuery( Navigation.init );
