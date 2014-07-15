var ChecklistManager = {

    section_template: null
    , section_sortable_config: {
        items: '.section'
        , cancel: 'input'
        , cursor: 'move'
        , placeholder: 'section-placeholder'
        , forceHelperSize: true
        , forcePlaceholderSize: true
        , handle: 'a.handle'
    }

    , item_template: null
    , item_sortable_config: {
        items: '.item'
        , cancel: 'input'
        , cursor: 'move'
        , placeholder: 'item-placeholder'
        , forcePlaceholderSize: true
        , handle: 'a.handle'
    }

    , init: function() {

        $('#checklist-sections').sortable( ChecklistManager.section_sortable_config );

        $('#add-section').click( ChecklistManager.addSection );
        $('#checklist-sections').on( 'click', '.remove-section', ChecklistManager.removeSection );

        ChecklistManager.section_template = $('#section-template').clone().removeClass('hidden');
        $('#section-template').remove();

        $('.section').sortable( ChecklistManager.item_sortable_config );

        $('#checklist-sections').on( 'click', '.add-item', ChecklistManager.addItem );
        $('#checklist-sections').on( 'click', '.remove-item', ChecklistManager.removeItem );

        ChecklistManager.item_template = $('#item-template').clone().removeClass('hidden');
        $('#item-template').remove();
    }

    , addSection: function() {

        $.post(
            '/checklists/add-section/?_nonce=' + $('#_add_section').val()
            , ChecklistManager.addSectionResponse
        )
    }

    , addSectionResponse: function( response ) {

        if ( response.success ) {
            ChecklistManager.section_template.clone()
                .attr( 'data-section-id', response.section_id )  // we can't use data here, check addItem()
                .find( 'input:first' )
                .attr( 'name', 'sections[' + response.section_id + ']' )
                .end()
                .find( '.add-item' )
                .data( 'section-id', response.section_id )
                .end()
                .sortable( ChecklistManager.item_sortable_config )
                .appendTo( '#checklist-sections' )
        }

    }

    , removeSection: function() {
        if ( !confirm( 'Do you really want to remove this Section?' ) )
            return;

        $(this).parents('.section:first').remove();
    }

    , addItem: function() {

        $.post(
            '/checklists/add-item/?_nonce=' + $('#_add_item').val() + '&csid=' + $(this).data('section-id')
            , ChecklistManager.addItemResponse
        )
    }

    , addItemResponse: function( response ) {

        if ( response.success ) {
            ChecklistManager.item_template.clone()
                .find('input:first')
                .attr( 'name', 'items[' + response.section_id + '][' + response.id + '][name]' )
                .next()
                .attr( 'name', 'items[' + response.section_id + '][' + response.id + '][assigned_to]' )
                .end().end()  // first end() for next(); second end() for find()
                .appendTo( '[data-section-id=' + response.section_id + '] .section-items' );
        }

    }

    , removeItem: function() {
        if ( !confirm( 'Do you really want to remove this Item' ) )
            return;

        $(this).parents('.item').remove();
    }

}

jQuery( ChecklistManager.init );