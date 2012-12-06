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
		fileExt		: '*.pdf;*.mov;*.wmv;*.flv;*.swf;*.f4v;*mp4;*.avi;*.mp3;*.aif;*.wma;*.wav;*.csv;*.doc;*.docx;*.rtf;*.xls;*.xlsx;*.wpd;*.txt;*.wps;*.pps;*.ppt;*.wks;*.bmp;*.gif;*.jpg;*.jpeg;*.png;*.psd;*.tif;*.zip;*.7z;*.rar;*.zipx;*.xml;',
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

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/products/upload-image/'
        , allowedExtensions: ['gif', 'jpg', 'jpeg', 'png']
        , element: $('#upload-image')[0]
        , sizeLimit: 10485760 // 10 mb's
        , onSubmit: function( id, fileName ) {
            uploader.setParams({
                _nonce : $('#_upload_image').val()
                , iid : $('#sIndustry').val()
                , pid : $('#hProductId').val()
            })
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUpload').click( function() {
        $('#upload-image input:first').click();
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
        $('#tPageSlug').val( $(this).val().slug() );
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
