// When the page has loaded
jQuery(function($) {
	 // Make the upload image icon work with uploadify
	$('#fLogo').each( function() {
		$(this).uploadify({
			auto      	: true,
			displayData	: 'speed',
			buttonImg 	: 'http://admin2.imagineretailer.com/images/buttons/products/upload-images.png',
			cancelImg 	: '/images/icons/cancel.png',
			fileExt		: '*.jpg;*.gif;*.png',
			fileDesc	: 'Web Image Files', // @Fix needs to be put in PHP
			scriptData	: { '_nonce' : $('#_ajax_upload_logo').val(), 'wid' : $('#hWebsiteID').val() },
			onComplete	: function( e, queueID, fileObj, response ) {
				ajaxResponse( $.parseJSON( response ) );
			},
			sizeLimit	: 6291456,// (6mb) In bytes? Really?
			script    	: '/ajax/website/upload-logo/',
			uploader  	: '/media/flash/uploadify.swf'
		});
	});
});