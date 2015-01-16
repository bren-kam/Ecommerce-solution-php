LocationForm = {

    categoryTemplate: null

    /**
     * Init
     */
    , init: function() {
        LocationForm.setupValidation();
        MediaManager.submit = LocationForm.setImage;

        LocationForm.categoryTemplate = $('#category-template').clone().removeAttr('id');

        $('#category-template').remove();
        $('#yext-categories').change( LocationForm.addCategory );
        $('#category-list').on( 'click', '.remove', LocationForm.removeCategory );
        if ( $('#category-list li').size() === 0 ) {
            $('#add-edit-location :submit').attr('disabled', 'disabled');
        }

        $('#category-list').sortable({
            items: 'li'
            , cancel: 'a'
            , cursor: 'move'
            , placeholder: 'item-placeholder'
            , forcePlaceholderSize: true
        });
    }

    /**
     * Setup Validation
     */
    , setupValidation: function() {
        $("#add-edit-location").bootstrapValidator({"fields":{
            "locationName":{"validators":{"notEmpty":{"message":"A Name is Required"}}},
            "address":{"validators":{"notEmpty":{"message":"An Address is Required"}}},
            "city":{"validators":{"notEmpty":{"message":"A City is Required"}}},
            "state":{"validators":{"notEmpty":{"message":"A State is Required"}}},
            "zip":{"validators":{"zipCode":{"message":"A Valid ZIP is Required","country":"US"}}},
            "phone":{"validators":{"notEmpty":{"message":"A Phone is Required"}}}
        }});
    }

    /**
     * Set Image - Overwrites MediaManager submit function to add images
     */
    , setImage: function() {
        var file = MediaManager.view.find( '.mm-file.selected:first').parents( 'li:first').data();

        if ( file && MediaManager.isImage( file ) ) {
            $( MediaManager.targetOptions.imageTarget )
                .find('img:first').attr('src', file.url).end()
                .find('input').val(file.url).end();
        }
    }

    /**
     * Add Category
     */
    , addCategory: function() {
        var categoryId = $(this).val();
        var categoryName = $(this).find(':selected').text();

        if ( !categoryId )
            return;

        if ( $('#category-list li').size() == 10 )
            return;

        LocationForm.categoryTemplate.clone()
            .prepend( categoryName )
            .find( 'input:hidden' ).val( categoryId ).end()
            .appendTo( '#category-list' );

        $('#add-edit-location :submit').removeAttr('disabled');
        $(this).find(':selected').attr('disabled', 'disabled');
        $(this).val('');
    }

    /**
     * Remove Category
     */
    , removeCategory: function() {
        var categoryId = $(this).parents('li:first').find( 'input:hidden').val()
        $(this).parents('li:first').remove();

        $('#yext-categories').find('[value=' + categoryId + ']').removeAttr('disabled');

        if ( $('#category-list li').size() == 0 ) {
            $('#add-edit-location :submit').attr('disabled', 'disabled');
        }
    }

};

jQuery( LocationForm.init );