/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=jquery.form', function() {
	// On load put the dividers in
	updateDividers();
	
	// Make the elements sortable
	$('#dContactBoxes').sortable({
		'items'			: '.contact-box',
		'placeholder'	: 'box-placeholder',
		'revert'		: true,
		'forcePlaceholderSize' : true,
		'stop'			: updateDividers,
		'update'		: updateElementOrder
	});
	
	// Change the width of all the items
	$(".contact-box, .box-divider").css("width", $('#hBannerWidth').val() + "px");

	// Make the upload image icon work with uploadify
	$('#fUploadBanner').uploadify({
		auto      	: true,
		displayData	: 'speed',
		debug		: true,
		buttonImg 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.jpg;*.gif;*.png',
		fileDesc	: 'Web Image Files', // @Fix needs to be put in PHP
		scriptData	: { _nonce : $('#_ajax_upload_banner').val(), wid : $('#hWebsiteID').val(), wpid : $('#hWebsitePageID').val() },
		onComplete	: function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		sizeLimit	: 6291456,// (6mb) In bytes? Really?
		script    	: '/ajax/website/banners/upload-banner/',
		uploader  	: '/media/flash/uploadify.swf'
	});
});

// Update the banner_order
function updateElementOrder() {
	/**
	 * Because numbers are invalid HTML ID attributes, we can't use .sortable('toArray'), which gives something like dAttachment_123. 
	 * This means we would have to loop through the array on the serverside to determine everything.
	 * When it is serialized like a string, it means that we can use the PHP explode function to determine the right IDs, very easily.
	 */
	var idList = $('#dContactBoxes').sortable('serialize');
	
	// Use Sidebar's -- it's the same thing
	$.post( '/ajax/website/sidebar/update-sequence/', { _nonce : $('#_ajax_update_sequence').val(), 's' : idList }, ajaxResponse, 'json' ); 
	
	return this;
}

$.fn.updateElementOrder = updateElementOrder;

// Update dividers
function updateDividers() {
	$('#dContactBoxes').find('.box-divider').remove().end().find('.contact-box:not(:last)').after('<div class="box-divider"></div>');
	
	return this;
}

$.fn.updateDividers = updateDividers;