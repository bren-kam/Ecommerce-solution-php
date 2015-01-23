PostForm = {

    /**
     * Init
     */
    init: function() {
        PostForm.setupValidation();
        MediaManager.submit = PostForm.setImage;

        // Date Picker - No Conflict with jQueryUI
        var datepicker = $.fn.datepicker.noConflict();
        $.fn.bootstrapDatepicker = datepicker;

        // Inline DIV
        $('#post-at').bootstrapDatepicker({
            todayHighlight: true
            , dateFormat: 'yyyy-mm-dd'
        });

    }

    /**
     * Setup Validation
     */
    , setupValidation: function() {
        $("#post-form").bootstrapValidator({"fields":{
            "content":{"validators":{"notEmpty":{"message":"A Post is Required"}}}
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

};

PostList = {

    init: function() {
        $('#show-posted').change( PostList.list );
        $('#show-account').change( PostList.list );
        PostList.list();
    }

    , list: function() {
        var url = "/sm/post/list-all/?" + Math.random();

        if ( $('#show-posted').val() ) {
            url += '&posted=' + $('#show-posted').val();
        }

        if ( $('#show-account').val() ) {
            url += '&website_sm_account_id=' + $('#show-account').val();
        }

        $('#post-list').find('*').css('opacity', '0.8');
        $('#post-list').load( url );
    }

};

jQuery( PostForm.init );
jQuery( PostList.init );
