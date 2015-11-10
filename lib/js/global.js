var GSR = {

    /**
     * Make DataTable
     * @param context
     */
    datatable: function(context, options) {

        var tables = typeof context !== 'undefined' ? context : $('table[ajax],table.dt:not(.manual)');

        // If there are tables, load datatables plugin and load the content
        if( tables.length )
            // Make each table
            tables.addClass('dt').each( function() {
                // Define variables and add on image to th's
                var aPerPage = $(this).attr('perPage').split(',')
                    , opts = ''
                    , ths = $(this).find('thead th')
                    , sorting = new Array()
                    , columns = new Array()
                    , s = ''
                    , c = ''
                    , a = $(this).attr('ajax')
                    , hideFilter = $(this).data('hide-filter');

                // Form options
                for( var i in aPerPage ) {
                    opts += '<option value="' + aPerPage[i] + '">' + aPerPage[i] + '</option>';
                }

                if( ths.length ) {
                    // Create sorting array
                    for( var i = 0; i < ths.length; i++ ) {
                        if( s = $(ths[i]).attr('sort') ) {
                            var direction = ( -1 == s.search('desc') ) ? 'asc' : 'desc';
                            sorting[s.replace( ' ' + direction, '' ) - 1] = [i, direction];
                        }

                        if( c = $(ths[i]).attr('column') ) {
                            columns.push( { 'sType' : c } );
                        } else {
                            columns.push( null );
                        }
                    }
                } else {
                    // If they don't choose anything, do the first one
                    sorting = [[0,'asc']];
                }

                var settings = {
                    bAutoWidth: false,
                    iDisplayLength : parseInt( aPerPage[0] ),
                    aaSorting: sorting,
                    aoColumns: columns,
                    "sDom":
                        "<'row'<'col-xs-8 col-md-6'f><'col-xs-4 col-md-6'l>r>"+
                        "t"+
                        "<'row'<'col-xs-6'i><'col-xs-6'p>>",
                    oLanguage: {
                        sSearch: '<span class="hidden-xs">Search:</span>',
                        sLengthMenu: '_MENU_ <span class="hidden-xs">items per page</span>'
                    }
                };

                // If it's AJAX
                if( a )
                    settings.bProcessing = 1, settings.bServerSide = 1, settings.sAjaxSource = a;

                if ( hideFilter ) {
                    settings.bFilter = 0;
                }

                // Override with custom settings
                settings = $.extend( settings, options );

                // Make the dataTable
                $(this).dataTable(settings);
                $('.dataTables_filter input').attr('placeholder', 'Search...');

            });

    }

    /**
     * Create checkbox with Switch layout
     */
    , setupSwitches: function() {
        if ( $("[data-toggle='switch']").size() > 0 )
            $("[data-toggle='switch']").bootstrapSwitch();
    }

    /**
     * Setup anchors with confirm attribute
     */
    , setupConfirmAttr: function() {
        $(document).on('click', 'a[confirm]:not([ajax])', function(e){
            return confirm( $(this).attr('confirm') );
        });
    }

    /**
     * Setup anchors with ajax attribute
     */
    , setupAjaxAttr: function() {
        $(document).on('click', 'a[ajax]', function(e){
            e.preventDefault();

            var confirmQuestion = $(this).attr('confirm');
            if( confirmQuestion && !confirm( confirmQuestion ) )
                return;

            $.get( $(this).attr('href'), GSR.defaultAjaxResponse );
        });
        $(document).on('submit', 'form[ajax]', function(e){
            e.preventDefault();

            var confirmQuestion = $(this).attr('confirm');
            if( confirmQuestion && !confirm( confirmQuestion ) )
                return;

            var url = $(this).attr('action');
            var data = $(this).serialize();

            if ( $(this).attr('method') == 'post' )
                $.post( url, data, GSR.defaultAjaxResponse );
            else
                $.get( url, data, GSR.defaultAjaxResponse );
        });
    }

    /**
     * Default Ajax Response Handler
     * @param object response
     */
    , defaultAjaxResponse: function(response) {
        if ( response.notification ) {
            $.gritter.add({
                title: response.notification.success ? 'Success' : 'Notification'
                , text: response.notification.message
            });
        }

        if ( response.error ) {
            $.gritter.add({
                title: 'Error'
                , text: response.error
            });
        }

        if ( response.reload_datatable ) {
            $('.dt').dataTable().fnDraw();
        }

        if ( response.refresh ) {
            window.location = window.location;
        }

        if ( response.close_modal ) {
            $('.modal').modal('hide');
        }
    }

    /**
     * Setup all Ritch Text Editor (CKEditor)
     * @param context
     */
    , setupRTE: function(context) {
        var RTEs = $('textarea[rte]', context);

        // If there are RTEs
        if ( RTEs.length ) {

            head.load( '/ckeditor/ckeditor.js', function() {
                RTEs.each ( function() {
		    CKEDITOR.dtd.$removeEmpty['span'] = false;
                    CKEDITOR.replace( $(this).attr('id'), {
                        allowedContent : true
                        , autoGrow_minHeight : 100
                        , resize_minHeight: 100
                        , height: 100
			, extraPlugins: 'autolink,fontawesome,justify'
			, contentsCss: '/ckeditor/plugins/fontawesome/font-awesome/css/font-awesome.min.css'
                        , toolbar : [
                            ['Bold', 'Italic', 'Underline']
                            , ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
                            , ['NumberedList','BulletedList', 'Table']
                            , ['Format']
			    , ['FontAwesome']
                            , ['Link','Unlink']
                            , ['Source']
                        ]
                    });
                });
            });

        }

    }

    , setupModal: function() {

        $('body').on( 'click', '[data-modal]', function(e) {
            e.preventDefault();
            var modal = $('#modal');
            if (modal.size() == 0 ) {
                modal = $('<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" />');
                modal.appendTo('body');
            }
            modal.load( $(this).attr('href'), function() {
                modal.modal();
            });
        });

        // This will allow remove based modals to get content on every click by disabling local cache.
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
        });
    }

    , setupDropdowns: function() {
        $(document).on('click', '.dropdown-menu a', function (e) {
            $(this).hasClass('keep-open') && e.stopPropagation();
        });
    }

    /**
     * Fadeout for request level notifications
     */
    , setupNotifications: function() {
        $( '#container > .alert' ).delay( 10000 ).fadeOut( 'fast' );
    }

};

var TicketForm = {

    uploader: null

    , init: function() {
        if ( $('#fCreateTicket').size() == 0 )
            return;

        $('#fCreateTicket select#tTicketTopic').on('change', function (e) {
            var messageInput = $('#fCreateTicket textarea#taTicketMessage');
            var placeholderVal = 'Please include as much relevant information as possible';

            switch (this.value) {
                case 'design':
                    placeholderVal = 'a) Logo: Please Attach \nb) Colors: Indicate 3 \nc) Theme: Paste theme here';
                    break;
                case 'development':
                case 'bug':
                    placeholderVal =
                        'Please include as much relevant information as possible to help our engineers ensure a quick and efficient resolution. For example:\n\n'
                        + ' i) a clear description of the issues you are seeing.\n'
                        + ' ii) The steps that we can follow to reproduce the issue.\n'
                        + ' iii) A link to where the issue can be seen and any relevant screenshots.\n\n'
                        + ' Thank You,\n'
                        + ' Technical Team\n\n';
                    break;
                default:
                    break;
            }

            messageInput.attr( 'placeholder', placeholderVal);
        });

        $('#fCreateTicket').bootstrapValidator({
            fields: {
                tTicketSummary: {
                    validators: {
                        notEmpty: {
                            message: 'A "Summary" is required'
                        }
                    }
                }
                , taTicketMessage: {
                    validators: {
                        notEmpty: {
                            message: 'A "Message" is required'
                        }
                    }
                }
            }
        }).on( 'success.form.bv', TicketForm.create );

        // Comment Attachments Uploader
        TicketForm.uploader = new qq.FileUploader({
            action: '/tickets/upload-to-ticket/'
            , allowedExtensions: ['pdf', 'mov', 'wmv', 'flv', 'swf', 'f4v', 'mp4', 'avi', 'mp3', 'aif', 'wma', 'wav', 'csv', 'doc', 'docx', 'rtf', 'xls', 'xlsx', 'wpd', 'txt', 'wps', 'pps', 'ppt', 'wks', 'bmp', 'gif', 'jpg', 'jpeg', 'png', 'psd', 'ai', 'tif', 'zip', '7z', 'rar', 'zipx', 'aiff', 'odt', 'eml']
            , element: $('#ticket-uploader')[0]
            , sizeLimit: 10485760 // 10 mb's
            , onSubmit: TicketForm.uploadSubmit
            , onComplete: TicketForm.uploadComplete
        });
        // Upload file trigger
        $('#ticket-upload').click( TicketForm.selectFile );
        $('#ticket-attachments').on('click', '.delete-file', TicketForm.deleteFile );

    }

    , create: function(e) {
        e.preventDefault();

        var form = $( e.target );
        $.post(
            form.attr( 'action' )
            , form.serialize()
            , TicketForm.createResponse
        )
    }

    , createResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            $('#support-modal').modal( 'hide' );

            $( '#fCreateTicket' ).get( 0 ).reset();
            $( '#fCreateTicket :submit' ).prop( 'disabled', false );
            $( '#ticket-attachments' ).empty();
            $( '#ticket-attachments' ).empty();
        }
    }


    , uploadSubmit: function() {
        TicketForm.uploader.setParams({
            _nonce : $( '#_upload_to_ticket' ).val()
            , tid : ''
        });

        $('#ticket-upload').hide();
        $('#ticket-upload-loader').show();
    }

    , selectFile: function() {
        if ( $.support.cors ) {
            $('#ticket-uploader input:first').click();
        } else {
            alert( $('#err-support-cors').text() );
        }
    }

    , uploadComplete: function( id, filename, response) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            var fileItem = $('<li/>');

            $('<a />')
                .attr( 'href', response.url )
                .attr( 'target', '_blank' )
                .text( filename )
                .appendTo( fileItem );

            $('<a />')
                .addClass( 'delete-file' )
                .attr( 'href', 'javascript:;' )
                .attr( 'title', 'Delete this file' )
                .html('&nbsp;<i class="fa fa-trash-o"></i>')
                .appendTo( fileItem );

            $('<input />')
                .attr( 'type', 'hidden' )
                .attr( 'name', 'uploads[]' )
                .val( response.id )
                .appendTo( fileItem );

            fileItem.appendTo( '#ticket-attachments' );
        }

        $('#ticket-upload').show();
        $('#ticket-upload-loader').hide();
    }

    , deleteFile: function() {
        if ( !confirm( 'Do you really want to remove this file from the comment?' ) )
            return;

        $(this).parents('li:first').remove();
    }
};

jQuery(function(){
    GSR.datatable();
    GSR.setupSwitches();
    GSR.setupConfirmAttr();
    GSR.setupAjaxAttr();
    GSR.setupRTE();
    GSR.setupModal();
    GSR.setupDropdowns();
    GSR.setupNotifications();
    TicketForm.init();
});

String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

String.prototype.toCamel = function(){ return this.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');}); };

Math.ceilEnding = function(e,t){var n=e.toFixed(2).substr(-parseFloat(t).toFixed(2).length),r=n-t,i=Math.pow(10,Math.floor(t).toString().length);if(r>0){return Math.round((e+i-r)*100)/100}else if(r<0){return Math.round((e-r)*100)/100}return e}

Number.prototype.numberFormat = function(e,t,n){var r=(this+"").replace(/[^0-9+\-Ee.]/g,"");var i=!isFinite(+r)?0:+r,s=!isFinite(+e)?0:Math.abs(e),o=typeof n==="undefined"?",":n,u=typeof t==="undefined"?".":t,a="",f=function(e,t){var n=Math.pow(10,t);return""+(Math.round(e*n)/n).toFixed(t)};a=(s?f(i,s):""+Math.round(i)).split(".");if(a[0].length>3){a[0]=a[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,o)}if((a[1]||"").length<s){a[1]=a[1]||"";a[1]+=(new Array(s-a[1].length+1)).join("0")}return a.join(u)}
