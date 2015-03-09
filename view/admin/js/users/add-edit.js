UserProfilePicture = {

    /**
     * Init
     */
    init: function() {
        MediaManager.submit = UserProfilePicture.setImage;
    }

    /**
     * Set Image - Overwrites MediaManager submit function to add images
     */
    , setImage: function() {
        var file = MediaManager.view.find( '.mm-file.selected:first').parents( 'li:first').data();

        if ( file && MediaManager.isImage( file ) ) {
            $('#profile-picture').attr( 'src', file.url );
            $.post(
                '/users/update-photo/'
                , {
                    _nonce: $('#update-photo-nonce').val(),
                    user_id: $('#update-photo-nonce').data('user-id'),
                    photo: file.url
                }
                , GSR.defaultAjaxResponse
            )
        }
    }

};

jQuery( UserProfilePicture.init );
