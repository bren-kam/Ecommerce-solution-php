 /* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', '/resources/js_single/?f=jquery.form', function() {
	// On load put the dividers in
	updateDividers();
	
	// Make the elements sortable
	$('#dElementBoxes').sortable({
		items		: '.element-box',
		cancel		: 'object, input, textarea',
		placeholder	: 'box-placeholder',
		revert		: true,
		forcePlaceholderSize : true,
		stop		: updateDividers,
		update		: updateElementOrder
	});

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/website/upload_sidebar_image/'
        , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
        , element: $('#upload-sidebar-image')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload_sidebar_image').val()
                , apid : $('#hAccountPageId').val()
            });

            $('#aUploadSidebarImage').hide();
            $('#upload-sidebar-image-loader').show();
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadSidebarImage').click( function() {
        $('#upload-sidebar-image input:first').click();
    });

    // Setup File Uploader
    var videoUploader = new qq.FileUploader({
        action: '/website/upload_sidebar_video/'
        , allowedExtensions: ['swf', 'flv', 'mp4', 'f4v']
        , element: $('#upload-sidebar-video')[0]
        , sizeLimit: 26214400 // (25mb) In bytes? Really?
        , onSubmit: function( id, fileName ) {
            videoUploader.setParams({
                _nonce : $('#_upload_sidebar_video').val()
                , apid : $('#hAccountPageId').val()
            });

            $('#aUploadSidebarVideo').hide();
            $('#upload-sidebar-video-loader').show();
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadSidebarVideo').click( function() {
        $('#upload-sidebar-video input:first').click();
    });
});

// Update the element order
function updateElementOrder() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123. 
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#dElementBoxes').sortable('serialize');

	$.post( '/website/update-attachment-sequence/', { _nonce : $('#_update_attachment_sequence').val(), s : idList }, ajaxResponse, 'json' );
	
	return this;
}

// Make it a jquery Plugin as well
$.fn.updateElementOrder = updateElementOrder;

// Update dividers
function updateDividers() {
	$('#dElementBoxes').find('.box-divider').remove().end().find('.element-box:not(:last)').after('<div class="box-divider"></div>');
	
	return this;
}

// Make it a jquery Plugin as well
$.fn.updateDividers = updateDividers;