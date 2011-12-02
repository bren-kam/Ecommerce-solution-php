 /* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=jquery.form', function() {
	// On load put the dividers in
	updateDividers();
	
	// Make the elements sortable
	$('#dContactBoxes').sortable({
		items		: '.contact-box',
		cancel		: 'object, input, textarea',
		placeholder	: 'box-placeholder',
		revert		: true,
		forcePlaceholderSize : true,
		stop		: updateDividers,
		update		: updateElementOrder
	});
	
	// Make the upload image icon work with uploadify
	$('#fNewImage').uploadify({
		auto      	: true,
		displayData	: 'speed',
		debug		: true,
		buttonImg 	: 'http://account.imagineretailer.com/images/buttons/add-image.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.jpg;*.gif;*.png',
		fileDesc	: 'Web Image Files', // @Fix needs to be put in PHP
		scriptData	: { _nonce : $('#_ajax_new_image').val(), wid : $('#hWebsiteID').val(), wpid : $('#hWebsitePageID').val() },
		onComplete	: function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		sizeLimit	: 6291456,// (6mb) In bytes? Really?
		script    	: '/ajax/website/sidebar/new-image/',
		uploader  	: '/media/flash/uploadify.swf',
		width		: 147,
		height		: 35
	});
	
	$('#fUploadVideo').uploadify({
		auto      	: true,
		displayData	: 'speed',
		debug		: true,
		buttonImg 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.swf;*.flv;*.mp4;*.f4v',
		fileDesc	: 'Video Files', // @Fix needs to be put in PHP
		scriptData	: { _nonce : $('#_ajax_upload_video').val(), wid : $('#hWebsiteID').val(), wpid : $('#hWebsitePageID').val() },
		onComplete	: function( e, queueID, fileObj, response ) {
            ajaxResponse( $.parseJSON( response ) );
		},
		sizeLimit	: 26214400,// (25mb) In bytes? Really?
		script    	: '/ajax/website/sidebar/upload-video/',
		uploader  	: '/media/flash/uploadify.swf'
	});
});

// Update the element order
function updateElementOrder() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123. 
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#dContactBoxes').sortable('serialize');

	$.post( '/ajax/website/sidebar/update-sequence/', { _nonce : $('#_ajax_update_sequence').val(), s : idList }, ajaxResponse, 'json' );
	
	return this;
}

// Make it a jquery Plugin as well
$.fn.updateElementOrder = updateElementOrder;

// Update dividers
function updateDividers() {
	$('#dContactBoxes').find('.box-divider').remove().end().find('.contact-box:not(:last)').after('<div class="box-divider"></div>');
	
	return this;
}

// Make it a jquery Plugin as well
$.fn.updateDividers = updateDividers;