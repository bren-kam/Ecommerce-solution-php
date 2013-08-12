// When the page has loaded
jQuery(function($) {
   	$('#sSection').change( function() {
        $.post( '/knowledge-base/articles/get-categories/', { _nonce : $('#_get_categories').val(), s : $(this).val() }, function( JSON ) {
            ajaxResponse( JSON );
            $('#sCategory').trigger('change');
        });
    });

    $('#fAddEditArticle').on( 'change', '#sCategory', function() {
        $.post( '/knowledge-base/articles/get-pages/', { _nonce : $('#_get_pages').val(), kbcid : $(this).val(), kbpid : $('#sPage').val() }, ajaxResponse );
    });

    $('#sCategory').trigger('change');

    // Trigger the check to make sure the slug is available
    $('#tTitle').change( function() {
        // Get slugs
        var tSlug = $('#tSlug');

        // Change slug
        if ( '' == tSlug.val() )
            tSlug.val( $(this).val().slug() );
    });

    /**
     * Make sure it also contains a proper slug
     */
    $('#tSlug').change( function() {
        $(this).val( $(this).val().slug() );
    });

    // This makes it so that clicking on the link selects the whole thing
    $('#tCurrentLink').click( function() {
      $(this).select();
    });


    // Show the current link
    $('body').on( 'click', 'a.file', function(e) {
        e.preventDefault();

        $('#file-list .selected').removeClass('selected');
        $(this).addClass('selected');

        var url = $(this).attr('href').substring(1);

        $('#tCurrentLink').val( url );
        $('#tdDate').text( $(this).attr('rel') );

        if ( $(this).hasClass('img') ) {
            var img = new Image();
            img.src = url;
            img.onload = function() {
                $('#tdSize').text( img.width + 'x' + img.height );
            };
        } else {
            $('#tdSize').text('');
        }

        $('#dCurrentLink').show();
    });

    // Make it possible to insert into post
    $('#insert-into-post').click( function() {
        var url = $('#tCurrentLink').val(), extension = url.substr( ( url.lastIndexOf('.') +1 )), fileName = url.substr( ( url.lastIndexOf('/') +1 )).replace( '.' + extension, '' ), html = '';

        switch ( extension ) {
            case 'jpeg':
            case 'jpg':
            case 'png':
            case 'gif':
                html += '<img src="' + url + '" alt="' + fileName + '" />';
            break;

            default:
                html += '<a href="' + url + '">' + fileName + '.' + extension + '</a>';
            break;
        }

        $('#taContent').redactor( 'insertHtml', html );
    });

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/knowledge-base/articles/upload-file/'
        , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml']
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
        if ( $.support.cors ) {
            $('#upload-file input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    });
});

// Turns text into a slug
String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); };