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

        $('#post-at').bootstrapDatepicker({
            todayHighlight: true
            , format: 'm/d/yyyy'
            , startDate: 'today'
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
        $('#post-list').on( 'click', '.edit', PostList.showEditPost );
        $('#post-list').on( 'click', '.remove', PostList.removePost );
        $('#edit-post-modal').on( 'submit', PostList.editPost );
        $('#post-list').on( 'click', '.show-details', PostList.showDetails );
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

    , showEditPost: function() {
        var post_id = $(this).parents('[data-post-id]').data('post-id');

        $.get( '/sm/post/get/', {id: post_id}, function( response ) {
            var post = response.post;
            var day = parseInt(post.post_at.substr(5, 2)) + '/' + parseInt(post.post_at.substr(8, 2)) + '/' + parseInt(post.post_at.substr(0, 4));
            var time = post.post_at.substr(11, 5);

            $('#edit-post-at-day')
                .val( day )
                .bootstrapDatepicker({
                    todayHighlight: true
                    , format: 'm/d/yyyy'
                    , startDate: 'today'
                });
            $('#edit-post-at-time').val(time);
            $('#edit-post-id').val(post_id);

            $('#edit-post-modal').modal();
        });

    }

    , editPost: function() {
        $('#edit-post-modal').modal('hide');
        setTimeout( PostList.list, 1000 );
    }

    , removePost: function(e) {
        e.preventDefault();

        var post_id = $(this).parents('[data-post-id]').data('post-id');

        if ( !confirm( 'Do you want to remove this Post? Can not be undone' ) )
            return;

        $.get(
            $(this).attr('href'),
            function( response ) {
                GSR.defaultAjaxResponse( response );
                PostList.list();
            }
        );
    }

    , showDetails: function() {
        $(this).parents('[data-post-id]').find('.hidden').removeClass('hidden');
        $(this).remove();
    }

};

jQuery( PostForm.init );
jQuery( PostList.init );
