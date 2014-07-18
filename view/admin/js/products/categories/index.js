var CategoryEditor = {

    template: null

    , init: function() {

        // Category Template
        CategoryEditor.template = $('#category-template').clone();
        CategoryEditor.template.removeAttr('id').removeClass('hidden');
        $('#category-template').remove();

        // Events for Category Browser
        $('body').on( 'click', '.get-category', CategoryEditor.getCategory );
        $('body').on( 'click', '.delete-category', CategoryEditor.deleteCategory );
        CategoryEditor.getCategory();

        // Events for Category Form Modal
        $('body').on( 'change', '#tName', CategoryEditor.setSlug );
        $('body').on( 'change', '#sAttributes', CategoryEditor.addAttribute );
        $('body').on( 'click', '.delete-attribute', CategoryEditor.deleteAttribute );
        $('body').on( 'submit', '#fAddEditCategory', CategoryEditor.submitAddEdit );

    }

    , getCategory: function() {

        var category_id = $(this).data('category-id') ? $(this).data('category-id') : 0;

        $('#categories-list').fadeTo( 'fast', 0.4 );

        $.get(
            '/products/categories/get/'
            , { _nonce: $('#_get_categories').val() , cid: category_id }
            , CategoryEditor.loadCategories
        );
    }

    , loadCategories: function( response ) {

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {

            if ( response.category ) {
                var nonce_delete = $('#_delete').val();
                $('#current-category .edit-category').attr( 'href', '/products/categories/add-edit/?cid=' + response.category.id + '&pcid=' + response.category.id );
                $('#current-category .delete-category').attr( 'href', '/products/categories/delete/?cid=' + response.category.id + '&_nonce=' + nonce_delete );
                $('#current-category small').show();
            } else {
                $('#current-category small').hide();
            }

            CategoryEditor._loadCategoriesList( response );
            CategoryEditor._loadBreadcrums( response );
        }

        $('#categories-list').fadeTo( 'fast', 1 );

    }

    , _loadCategoriesList: function( response ){

        var nonce_delete = $('#_delete').val();

        $('#categories-list').empty();

        // Load Category List
        if ( response.categories ) {

            var category = response.category ? response.category : { id: 0, name: 'Parent Category' };

            for ( i in response.categories ) {
                var child_category = response.categories[i];
                var element = CategoryEditor.template.clone();

                element.data( 'category-id', child_category.id );

                element.find('.get-category')
                    .data( 'category-id', child_category.id )
                    .text( child_category.name );
                element.find('h4 small')
                    .text( '(' + category.name + ')' );
                element.find('.edit')
                    .attr( 'href', '/products/categories/add-edit/?cid=' + child_category.id + '&pcid=' + category.id )
                    .attr( 'data-modal', '' )
                    .data( 'category-id', child_category.id )
                element.find('.delete-category')
                    .attr( 'href', '/products/categories/delete/?cid=' + child_category.id + '&_nonce=' + nonce_delete )
                element.find('.url-preview')
                    .text( 'http://www.mysite.com/category/' + child_category.slug );

                element.appendTo('#categories-list');
            }
        } else {
            $('#categories-list').html( '<p>No sub categories have been created for this category. <a href="/products/categories/add-edit/" data-modal>Add a category now</a>.</p>' );
        }

    }

    , _loadBreadcrums: function ( response ) {

        // We start only with our Main category
        $('.breadcrumb li:not(:first)').remove();

        // Add parents
        if ( response.parent_categories ) {
            for ( i in response.parent_categories ) {
                var parent_category = response.parent_categories[i];

                var anchor = $('<a />')
                    .data( 'category-id', parent_category.id )
                    .attr( 'href', 'javascript:;' )
                    .addClass( 'get-category' )
                    .text( parent_category.name );

                $('<li />').append( anchor ).appendTo( '.breadcrumb' );

            }
        }

        // Add current category
        if ( response.category ) {
            var anchor = $('<a />')
                .data( 'category-id', response.category.id )
                .attr( 'href', 'javascript:;' )
                .text( response.category.name );

            $('<li />').append( anchor ).appendTo( '.breadcrumb' );
        }

    }

    , deleteCategory: function(e) {
        e.preventDefault();

        var anchor = $(this);

        if ( !confirm('Are you sure you want to delete this Category? Can not be undone') )
            return;

        $.get(
            anchor.attr( 'href' )
            , function( response ) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    anchor.parents('.category:first').remove();
                }
            }
        )
    }

    , addAttribute: function() {

        var attributeId = $(this).val();
        var attribute = $(this).find('option:selected')
        var title = attribute.text()

        // Create and add attribute to list
        var p = $('<p />')
            .addClass('attribute')
            .addClass('clearfix')
            .data( 'attribute-id', attributeId )
            .text( title );
        $('<a />')
            .addClass( 'delete-attribute' )
            .addClass( 'pull-right' )
            .attr( 'href', 'javascript:;' )
            .attr( 'title', 'Delete')
            .html('<i class="fa fa-trash-o"></i>')
            .appendTo(p);
        $('<input />')
            .attr( 'type', 'hidden' )
            .attr( 'name', 'hAttributes[]' )
            .val( attributeId )
            .appendTo(p);

        p.appendTo('#attributes-list');

        // Can't be added again
        attribute.attr('disabled', true);

    }

    , deleteAttribute: function() {

        var parent = $(this).parents('p.attribute:first');

        // Enable attribute in the drop down
        $('#sAttributes option[value=' + parent.data('attribute-id') + ']').attr( 'disabled', false );

        // Remove the parent
        parent.remove();

    }

    , submitAddEdit: function(e) {
        e.preventDefault();

        var form = $(this);
        var nonce_update = $('#_add_edit').val();

        $.post(
            form.attr('action')
            , form.serialize() + '&_nonce=' + nonce_update
            , function( response ) {
                CategoryEditor.loadCategories( response );
                form.parents('.modal:first').modal('hide');
            }
        );
    }

    , setSlug: function() {
        var slugInput = $('#tSlug');
        if ( slugInput.val() == '' ) {
            slugInput.val($('#tName').val().slug());
        }
    }

}



jQuery( CategoryEditor.init );