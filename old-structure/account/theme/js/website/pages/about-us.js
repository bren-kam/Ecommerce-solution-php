jQuery(function(){
	// Make the upload image icon work with uploadify
	$('#fAboutUs').uploadify({
		auto      	: true,
		displayData	: 'speed',
		buttonImg 	: '/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps,*.ppt,*.wks,*.bmp,*.gif;*.jpg;*.jpeg;*.png,*.psd;*.tif;*.zip;*.7z;*.rar;*.zipx;',
		fileDesc	: 'Valid File Formats', // @Fix needs to be put in PHP
		scriptData	: { '_nonce' : $('#_ajax_upload_file').val(), 'wid' : $('#hWebsiteID').val() },
		onComplete	: function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		onSelect	: function() {
			// $('#fCoupon').uploadifySettings( 'scriptData', { '_nonce' : $('#_ajax_upload_image').val(), 'wid' : $('#hWebsiteID').val() } );
			$('#fAboutUs').uploadifySettings( 'scriptData', { '_nonce' : $('#_ajax_upload_image' ).val(), 'wid' : $('#hWebsiteID').val(), 'wpid' : $('#hWebsitePageID').val(), 'fn' : 'about-us' } );
			return true;
		},
		sizeLimit	: 6291456,// (6mb) In bytes? Really?
		script    	: '/ajax/website/page/upload-image/',
		uploader  	: '/media/flash/uploadify.swf'
	});	
});