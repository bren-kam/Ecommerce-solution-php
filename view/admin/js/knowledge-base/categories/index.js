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
        $('body').on( 'submit', '#fAddEditCategory', CategoryEditor.submitAddEdit );

    }

    , getCategory: function() {

        var category_id = $(this).data('category-id') ? $(this).data('category-id') : 0;

        $('#categories-list').fadeTo( 'fast', 0.4 );

        $.get(
            '/knowledge-base/categories/get/'
            , { _nonce: $('#_get_categories').val() ,kbcid: category_id, s: $('#hSection').val() }
            , CategoryEditor.loadCategories
        );
    }

    , loadCategories: function( response ) {

        GSR.defaultAjaxResponse( response );

        if ( response.success ) {

            if ( response.category ) {
                var nonce_delete = $('#_delete').val();
                $('#current-category span').text( response.category.name );
                $('#current-category .edit-category').attr( 'href', '/knowledge-base/categories/add-edit/?kbcid=' + response.category.id + '&s=' + $('#hSection').val() );
                $('#current-category .delete-category').attr( 'href', '/knowledge-base/categories/delete/?kbcid=' + response.category.id + '&_nonce=' + nonce_delete );
                $('#current-category small').show();
            } else {
                $('#current-category span').text( 'Main Category' );
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

            var category = ( response.category && response.category.id ) ? response.category : { id: 0, name: 'Parent Category' };

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
                    .attr( 'href', '/knowledge-base/categories/add-edit/?kbcid=' + child_category.id + '&s=' + $('#hSection').val() )
                    .attr( 'data-modal', '' )
                    .data( 'category-id', child_category.id )
                element.find('.delete-category')
                    .attr( 'href', '/knowledge-base/categories/delete/?kbcid=' + child_category.id + '&_nonce=' + nonce_delete )
                element.find('.url-preview')
                    .text( 'http://www.mysite.com/category/' + child_category.slug );

                element.appendTo('#categories-list');
            }
        } else {
            $('#categories-list').html( '<p>No sub categories have been created for this category. <a href="/knowledge-base/categories/add-edit/" data-modal>Add a category now</a>.</p>' );
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

}



jQuery( CategoryEditor.init );