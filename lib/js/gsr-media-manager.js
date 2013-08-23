jQuery(function($) {
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

        CKEDITOR.instances.taContent.insertHtml( html, 'unfiltered_html' );
    });

    // Setup File Uploader
    var uploader = new qq.FileUploader({
        action: '/website/upload-file/'
        , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml']
        , element: $('#upload-file')[0]
        , sizeLimit: 6291456 // 6 mb's
        , onSubmit: function( id, fileName ) {
            var tFileName = $('#tFileName');

            if ( !tFileName.val().length ) {
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
});