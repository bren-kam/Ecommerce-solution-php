// When the page has loaded
jQuery(function($) {
	// Make the Meta Data expandable
	$('#aMetaData').click( function() {
		var text = $(this).html();
		
		if ( text.search( /\+/ ) > 0 ) {
			$(this).html( text.replace( '+', '&ndash;' ) );
			
			// Show
			$('#dMetaData').show();
		} else {
			$(this).text( text.replace( /\[[^\]]+\]/, '[ + ]' ) );
			
			// Hide
			$('#dMetaData').hide();
		}
	});
	
	// This makes it so that clicking on the link selects the whole thing
	$('#tCurrentLink').click( function() {
		$(this).select();
	});
	
	// Show the current link
	$('a.file').live( 'click', function(e) {
		e.preventDefault();
		
		$(this).parents('ul:first').find('.file.bold').removeClass('bold');
		$(this).addClass('bold');
		
		$('#tCurrentLink').val( $(this).attr('href') );
		$('#dCurrentLink').show();
	});
	
	// Make the upload image icon work with uploadify
	$('#fUploadFile').uploadify({
		auto      	: true,
		displayData	: 'speed',
		buttonImg 	: '/images/buttons/products/upload-images.png',
		cancelImg 	: '/images/icons/cancel.png',
		fileExt		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps;*.ppt;*.wks;*.bmp;*.gif;*.jpg;*.jpeg;*.png;*.psd;*.tif;*.zip;*.7z;*.rar;*.zipx;',
		fileDesc	: 'Valid File Formats', // @Fix needs to be put in PHP
		scriptData	: { '_nonce' : $('#_ajax_upload_file').val(), 'wid' : $('#hWebsiteID').val() },
		onComplete	: function( e, queueID, fileObj, response ) {
			ajaxResponse( $.parseJSON( response ) );
		},
		onSelect	: function() {
			$('#fUploadFile').uploadifySettings( 'scriptData', { '_nonce' : $('#_ajax_upload_file').val(), 'wid' : $('#hWebsiteID').val(), 'fn' : $('#tFileName').val() } );
			return true;
		},
		sizeLimit	: 6291456,// (6mb) In bytes? Really?
		script    	: '/ajax/website/page/upload-file/',
		uploader  	: '/media/flash/uploadify.swf'
	});
	
	/**
	 * Current Offer
	 */
	 // Make the upload image icon work with uploadify
	$('input[uploadify]').each( function() {
		$(this).uploadify({
			auto      	: true,
			displayData	: 'speed',
			buttonImg 	: '/images/buttons/products/upload-images.png',
			cancelImg 	: '/images/icons/cancel.png',
			fileExt		: '*.jpg;*.gif;*.png',
			fileDesc	: 'Web Image Files', // @Fix needs to be put in PHP
			scriptData	: { '_nonce' : $('#_ajax_upload_image').val(), 'wid' : $('#hWebsiteID').val(), 'wpid' : $('#hWebsitePageID').val() },
			onComplete	: function( e, queueID, fileObj, response ) {
				ajaxResponse( $.parseJSON( response ) );
			},
			sizeLimit	: 6291456,// (6mb) In bytes? Really?
			script    	: '/ajax/website/page/upload-image/',
			uploader  	: '/media/flash/uploadify.swf'
		});
	});

    /********** Page Link  **********/
	// Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
		if ( $(this).attr('tmpval') == $(this).val() || '' == $(this).val().replace(/\s/g, '') ) {
			$('#dPageSlug, #pPageSlugError').hide();
			return;
		}

		// Get slugs
		var pageSlug = $(this).val().slug(), sPageSlug = $('#sPageSlug');

		// Makes sure it only changes the name when you first write the title
		if ( '' == sPageSlug.text() ) {
			// Assign the slugs
			sPageSlug.text( pageSlug );
			$('#tPageSlug').val( pageSlug );
		}

		// Show the text
		$('#dPageSlug').show();
	});

	// The "Edit" slug button
	$('#aEditPageSlug').click( function() {
		// Hide the slug
		$('#sPageSlug, #aEditPageSlug').hide();

		// Show the other buttons
		$('#tPageSlug, #aSavePageSlug, #aCancelPageSlug').show();
	});

	// The "Save" slug button
	$('#aSavePageSlug').click( function() {
		var productSlug = $('#tPageSlug').val().slug();

		// Assign the slugs
		$('#sPageSlug').text( productSlug );
		$('#tPageSlug').val( productSlug );

		// Hide the buttons
		$('#tPageSlug, #aSavePageSlug, #aCancelPageSlug').hide();

		// Show the slug
		$('#sPageSlug, #aEditPageSlug').show();
	});

	// The "Cancel" slug link
	$('#aCancelPageSlug').click( function() {
		// Assign the slugs
		$('#tPageSlug').val( $('#sPageSlug').text() );

		// Hide the buttons
		$('#tPageSlug, #aSavePageSlug, #aCancelPageSlug').hide();

		// Show the slug
		$('#sPageSlug, #aEditPageSlug').show();
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
