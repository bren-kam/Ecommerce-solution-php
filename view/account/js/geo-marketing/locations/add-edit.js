LocationForm = {

    /**
     * Init
     */
    init: function() {
        LocationForm.setupValidation();
        MediaManager.submit = LocationForm.setImage;
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
            $( MediaManager.options.imageTarget )
                .find('img:first').attr('src', file.url).end()
                .find('input').val(file.url).end();
        }
    }

};

jQuery( LocationForm.init );