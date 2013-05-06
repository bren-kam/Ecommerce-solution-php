// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', 'http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js', function() {
    // Cache
    cache = { 'offer-box' : {}, 'sku' : {}, 'product' : {}, 'brand' : {} };

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

    var tAddProducts = $('#tAddProducts');

    if ( tAddProducts.is('table') ) {
        tAddProducts.dataTable({
            aaSorting: [[0,'asc']],
            bAutoWidth: false,
            bProcessing : 1,
            bServerSide : 1,
            iDisplayLength : 20,
            sAjaxSource : '/email-marketing/emails/list-products/',
            sDom : '<"top"lr>t<"bottom"pi>',
            oLanguage: {
                    sLengthMenu: 'Rows: <select><option value="20">20</option><option value="50">50</option><option value="100">100</option></select>',
                    sInfo: "_START_ - _END_ of _TOTAL_"
            },
            fnDrawCallback : function() {
                // Run Sparrow on new content and add the class last to the last row
                sparrow( $(this).find('tr:last').addClass('last').end() );
            },
            fnServerData: function ( sSource, aoData, fnCallback ) {
                aoData.push({ name : 's', value : $('#tAutoComplete').val() });
                aoData.push({ name : 'sType', value : $('#sAutoComplete').val() });

                // Get the data
                $.ajax({
                    url: sSource,
                    dataType: 'json',
                    data: aoData,
                    success: fnCallback
                });
            }
        });
    }

	// Make the Add Products expandable
    $('#aAddProducts').click( function() {
        var text = $(this).html();

        if ( text.search( /\+/ ) > 0 ) {
            $(this).html( text.replace( '+', '&ndash;' ) );
            // Show
            $('#dAddProducts').show();
        } else {
            $(this).text( text.replace( /\[[^\]]+\]/, '[ + ]' ) );

            // Hide
            $('#dAddProducts').hide();
        }
    });

    // Change the text
    $('#sAutoComplete').change( function() {
        var tAutoComplete = $('#tAutoComplete');

        tAutoComplete.attr( 'tmpval', tAutoComplete.attr('tmpval').replace( /\s([\w\s]+).../, ' ' + $(this).find('option:selected').text() + '...' ) ).val('').blur();
    });

    $('#tAutoComplete').autocomplete({
        minLength: 1,
        source: function( request, response ) {
            // Get the cache type
            var cacheType = $('#sAutoComplete').val();

            // Find out if they are already cached so we don't have to do another ajax called
            if ( request['term'] in cache[cacheType] ) {
                response( $.map( cache[cacheType][request['term']], function( item ) {
                    return {
                        'label' : item['name'],
                        'value' : item['name']
                    }
                }) );

                // If it was cached, return now
                return;
            }

            // It was not cached, get data
            $.post( '/products/autocomplete-owned/', { '_nonce' : $('#_autocomplete_owned').val(), 'type' : cacheType, 'term' : request['term'], owned : 1 }, function( autocompleteResponse ) {
                // Assign global cache the response data
                cache[cacheType][request['term']] = autocompleteResponse['suggestions'];

                // Return the response data
                response( $.map( autocompleteResponse['suggestions'], function( item ) {
                    return {
                        'label' : item['name'],
                        'value' : item['name']
                    }
                }));
            }, 'json' );
        }
    });

    // Create the search functionality
    $('#aSearch').click( function() {
        tAddProducts.dataTable().fnDraw();
    });

    // Remove product
    $('#subcontent').on( 'click', '.remove-product', function() {
        $(this).parents('.product').remove();
    });

    // Make the list sortable
    $("#dSelectedProducts").sortable( {
        scroll:true,
        placeholder:'product-placeholder'
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
