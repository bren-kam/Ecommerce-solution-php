/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', '/resources/js_single/?f=jquery.form', function() {
	// On load put the dividers in
	updateDividers();
	
	// Make the elements sortable
	$('#dElementBoxes').sortable({
		'items'			: '.element-box',
		'placeholder'	: 'box-placeholder',
		'revert'		: true,
		'forcePlaceholderSize' : true,
		'stop'			: updateDividers,
		'update'		: updateElementOrder
	});
	
	// Change the width of all the items
	$(".element-box, .box-divider").css("width", $('#hBannerWidth').val() + "px");

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/website/upload_banner/'
        , allowedExtensions: ['jpg', 'jpeg', 'gif', 'png']
        , element: $('#upload-banner')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload_banner').val()
                , apid : $('#hAccountPageId').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadBanner').click( function() {
        $('#upload-banner input:first').click();
    });
});

// Update the banner_order
function updateElementOrder() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123. 
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#dElementBoxes').sortable('serialize');
	
	// Use Sidebar's -- it's the same thing
	$.post( '/website/update_attachment_sequence/', { _nonce : $('#_update_attachment_sequence').val(), 's' : idList }, ajaxResponse, 'json' );
	
	return this;
}

$.fn.updateElementOrder = updateElementOrder;

// Update dividers
function updateDividers() {
	$('#dElementBoxes').find('.box-divider').remove().end().find('.element-box:not(:last)').after('<div class="box-divider"></div>');
	
	return this;
}

$.fn.updateDividers = updateDividers;