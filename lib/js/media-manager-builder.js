var MediaManager = {

    defaultView: {
        container:
            '<div class="modal fade" id="mm-modal" tabindex="-1" role="dialog" aria-labelledby="mm-label" aria-hidden="true">' +
            '<div class="modal-dialog modal-lg">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
            '<h4 class="modal-title" id="mm-label">Media Manager</h4>' +
            '</div>' +
            '<div class="modal-body" />' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" class="btn btn-primary" id="mm-submit" data-dismiss="modal">Use This Image</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'

        , uploader:
            '<div class="row">'+
            '<div class="col-lg-9">'+
            '<input type="text" class="form-control" id="mm-file-name" placeholder="Please set a file name before uploading a new file..." />' +
            '</div>' +
            '<div class="col-lg-3">'+
            '<button type"button" class="btn btn-default" id="mm-select-file" disabled>Upload a File</button>' +
            '</div>' +
            '</div>' +
            '<div class="progress progress-sm hidden" id="mm-upload-progress">' +
            '<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">' +
            '<span class="sr-only">Loading...</span>' +
            '</div>' +
            '</div>' +
            '<div id="mm-uploader" />'

        , search: '<div class="row"><div class="col-lg-12"><input type="text" class="form-control" id="mm-search" placeholder="Narrow your search..." /></div></div>'

        , filesContainer: '<div class="row"><div class="col-lg-12"><ul class="list-inline" id="mm-file-container" /></div></div>'

        , fileInfo: '<div class="row"><div class="col-lg-12"><ul id="mm-file-info"></ul></div></div>'

        , fileTemplate: '<li><a href="javascript:;" class="mm-delete-file"><i class="fa fa-trash-o"></i></a></li>'
    }

    , view: null

    , uploader: null

    , searchUrl: null

    , searchTerm: null

    , searchPage: 0

    , deleteUrl: null

    , preventScroll: false

    , options: {}

    , targetOptions: {}

    , init: function() {
        // Global Event, open Media Manager
        $('body').on( 'click', '[data-media-manager]', MediaManager.open );
    }

    , configure: function( options ) {
        // Our View
        MediaManager.view = $( MediaManager.defaultView.container );
        MediaManager.view.find( '.modal-body:first' )
            .append(MediaManager.defaultView.uploader)
            .append(MediaManager.defaultView.search)
            .append(MediaManager.defaultView.filesContainer)
            .append(MediaManager.defaultView.fileInfo);

        // Set up Uploader
        MediaManager.uploader = new qq.FileUploader({
            action: options.uploadUrl
            , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'tif', 'zip', '7z', 'rar', 'zipx', 'xml']
            , element: MediaManager.view.find('#mm-uploader')[0]
            , sizeLimit: 10485760  // 10 MBs
            , onSubmit: MediaManager.uploaderSubmit
            , onComplete: MediaManager.uploaderComplete
        });

        // Uploader Trigger
        MediaManager.view.find( '#mm-select-file' ).click( MediaManager.uploaderOpen );

        // Name for the file to be Uploaded
        MediaManager.view.find( '#mm-file-name' ).keyup( MediaManager.validateFileName )
            .change( MediaManager.validateFileName );

        // We will use this for file filtering
        if ( options.searchUrl )
            MediaManager.searchUrl = options.searchUrl;
        MediaManager.view.find( '#mm-search' ).keyup( MediaManager.search );

        // Scrolling and Pagination
        MediaManager.view.find( '#mm-file-container').scroll( MediaManager.scroll );

        MediaManager.view.on( 'click', '.mm-file', MediaManager.selectFile );

        // Delete File Settings
        if ( options.deleteUrl )
            MediaManager.deleteUrl = options.deleteUrl;
        MediaManager.view.on( 'click', '.mm-delete-file', MediaManager.deleteFile );

        // Media Manager submit
        MediaManager.view.find( '#mm-submit' ).click( MediaManager.submit );

        // Locales
        if ( options.submitText )
            MediaManager.view.find( '#mm-submit').text( options.submitText );

        // Store Options
        MediaManager.options = options;

    }

    , open: function( options ) {
        if ( !MediaManager.view ) {
            options = $.extend( {}, options, $(this).data() );
            MediaManager.configure( options );
        }

        MediaManager.targetOptions = $(this).data();

        // Show window
        MediaManager.view
            .appendTo('body')
            .modal();

        // Trigger first search
        MediaManager.search();
    }

    , uploaderOpen: function() {
        MediaManager.view
            .find( '#mm-uploader input:first' )
            .click();
    }

    , uploaderSubmit: function() {
        MediaManager.uploader.setParams({
            fn: MediaManager.view.find( '#mm-file-name').val()
        });
        MediaManager.view.find( '#mm-select-file').hide();
        MediaManager.view.find( '#mm-file-name').hide();
        MediaManager.view.find( '#mm-upload-progress').removeClass( 'hidden').show();
    }

    , uploaderComplete: function( id, filename, response ) {
        MediaManager.view.find( '#mm-file-name').val('');
        MediaManager.view.find( '#mm-select-file').show();
        MediaManager.view.find( '#mm-file-name').show();
        MediaManager.view.find( '#mm-upload-progress').hide();

        if ( response.success ) {
            response.date = 'just now';
            MediaManager.appendFile( response, "first" );
        }
    }

    , validateFileName: function() {
        var input = MediaManager.view.find( '#mm-file-name');
        if ( input.val() == '' ) {
            MediaManager.view.find( '#mm-select-file' ).prop( 'disabled', true );
        } else {
            MediaManager.view.find( '#mm-select-file' ).prop( 'disabled', false );
        }
    }

    , search: function() {
        if ( MediaManager.searchTerm !== MediaManager.view.find( '#mm-search').val() ) {
            MediaManager.searchTerm = MediaManager.view.find( '#mm-search').val();
            MediaManager.searchPage = 0;
            MediaManager.view.find('#mm-file-container').text('Please wait while we are fetching files...');
            MediaManager.loadPage();
        }
    }

    , loadPage: function() {
        if ( MediaManager.searchRequest && MediaManager.searchRequest.abort ) {
            MediaManager.searchRequest.abort();
        }

        MediaManager.searchRequest = $.get(
            MediaManager.searchUrl
            , { pattern: MediaManager.searchTerm, page: MediaManager.searchPage }
            , MediaManager.showFiles
        );
    }

    , showFiles: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {

            if ( MediaManager.view.find('#mm-file-container .mm-file' ).size() == 0 )
                MediaManager.view.find('#mm-file-container' ).empty();

            for ( i in response.files ) {
                var file = response.files[i];
                MediaManager.appendFile( file );
            }
            MediaManager.preventScroll = false;
        }
    }

    , appendFile: function( file, position ) {
        var item = $(MediaManager.defaultView.fileTemplate);
        var anchor = $( '<a />', { href: 'javascript:;' , title: file.name, class: 'mm-file' } );
        var fileTag = MediaManager.isImage( file ) ?
                        $( '<img />', { src: file.url } ) :
                        $( '<img />', { src: "http://placehold.it/120x120&text=" + file.name }) ;

        fileTag.appendTo(anchor);
        anchor.appendTo(item)
        if ( position == "first" ) {
            item.prependTo( MediaManager.view.find('#mm-file-container') );
        } else {
            item.appendTo( MediaManager.view.find('#mm-file-container') );
        }

        item.data( file );
    }

    , selectFile: function() {
        var anchor = $(this);
        var file = anchor.parents( 'li:first').data();

        // Used to get image width and height
        var image = new Image();
        image.src = file.url;

        MediaManager.view.find('#mm-file-info').empty()
            .append( $( '<li />').html( '<strong>File: </strong>' + file.url ) )
            .append( $( '<li />').html( '<strong>Date: </strong>' + file.date ) )
            .append( $( '<li />').html( '<strong>Size: </strong>' + image.width + 'x' + image.height ) );

        // Switch to selected file
        MediaManager.view.find('.mm-file.selected').removeClass('selected');
        anchor.addClass('selected');
    }

    , submit: function() {
        var file = MediaManager.view.find( '.mm-file.selected:first').parents( 'li:first').data();

        if ( file ) {
            var html = ( MediaManager.isImage( file ) ) ?
                '<img src="' + file.url + '" alt="' + file.url + '" class="img-responsive" />' :
                '<a href="' +  file.url+ '" title=' +  file.url + '>' + file.name + '</a>';
	    
	    $("#imageURL").val(file.url);
        }
    }

    , deleteFile: function() {
        var anchor = $(this);
        var file = anchor.parents( 'li:first' ).data();

        if ( !confirm( 'Ar you sure you want to delete this file?' ) )
            return;

        var params = { key: file.name };
        if ( file.id )
            params.id = file.id;

        $.get(
            MediaManager.deleteUrl
            , params
            , function( r ) {
                if ( r.success )
                    anchor.parents( 'li:first' ).remove();
            }
        )
    }

    , isImage: function( file ) {
        var url = file.url.toLowerCase();
        if ( url.indexOf( '.jpg' ) > 0 )
            return true;
        if ( url.indexOf( '.png' ) > 0 )
            return true;
        if ( url.indexOf( '.jpeg' ) > 0 )
            return true;
        if ( url.indexOf( '.bmp' ) > 0 )
            return true;
        if ( url.indexOf( '.gif' ) > 0 )
            return true;

        return false;
    }

    , scroll: function() {
        if ( MediaManager.preventScroll )
            return;

        if( $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight ) {
            MediaManager.preventScroll = true;
            MediaManager.searchPage++;
            MediaManager.loadPage();
        }
    }

}

jQuery( MediaManager.init );
