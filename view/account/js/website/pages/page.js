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
	$('body').on( 'click', 'a.file', function(e) {
        e.preventDefault();

		$(this).parents('ul:first').find('.file.bold').removeClass('bold');
		$(this).addClass('bold');
		
		$('#tCurrentLink').val( $(this).attr('href') );
		$('#dCurrentLink').show();
	});

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/website/upload-file/'
        , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v;*mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml']
        , element: $('#upload-file')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            var tFileName = $('#tFileName');

            if ( tFileName.val() == tFileName.attr('tmpval') ) {
                alert( tFileName.attr('error') );
                return false;
            }

            uploader.setParams({
                _nonce : $('#_upload_file').val()
                , fn : $('#tFileName').val()
            });

            $('#aUploadFile').hide();
            $('#upload-file-loader').show();
        }
        , onComplete: function( id, fileName, responseJSON ) {
            ajaxResponse( responseJSON );
        }
    });

    /**
     * Make the uploader work
     */
    $('#aUploadFile').click( function() {
        $('#upload-file input:first').click();
    });

    /********** Page Link  **********/
	// Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
        var tPageSlug = $('#tPageSlug');

        if ( tPageSlug.is('input') )
            tPageSlug.val( $(this).val().slug() );
	});
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }
