var GSR = {

    /**
     * Make DataTable
     * @param context
     */
    datatable: function(context) {

        var tables = $('table[ajax],table.dt:not(.manual)', context);

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
                    , a = $(this).attr('ajax');

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
                    aoColumns: columns
                };

                // If it's AJAX
                if( a )
                    settings.bProcessing = 1, settings.bServerSide = 1, settings.sAjaxSource = a;

                // Make the dataTable
                $(this).dataTable(settings);
            });

    }

    /**
     * Create checkbox with Switch layout
     */
    , setupSwitches: function() {
        var switches = $("[data-toggle='switch']");
        if ( switches.size() > 0 )
            switches.wrap('<div class="switch" />').parent().bootstrapSwitch();
    }

    /**
     * Setup anchors with confirm attribute
     */
    , setupConfirmAttr: function() {
        $(document).on('click', 'a[confirm]:not([ajax])', function(e){
            e.preventDefault();
            return confirm( $(this).attr('confirm') );
        });
    }

    /**
     * Setup anchors with ajax attribute
     */
    , setupAjaxAttr: function() {
        $(document).on('click', 'a[ajax]', function(e){
            e.preventDefault();

            confirmQuestion = $(this).attr('confirm');
            if( confirmQuestion && !confirm( confirmQuestion ) )
                return;

            $.get( $(this).attr('href'), GSR.defaultAjaxResponse );
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
                    CKEDITOR.replace( $(this).attr('id'), {
                        allowedContent : true
                        , autoGrow_minHeight : 100
                        , resize_minHeight: 100
                        , height: 100
                        , toolbar : [
                            ['Bold', 'Italic', 'Underline']
                            , ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
                            , ['NumberedList','BulletedList', 'Table']
                            , ['Format']
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

    , setupTicketForm: function() {
        $('#fCreateTicket').bootstrapValidator({
            fields: {
                tSummary: {
                    validators: {
                        notEmpty: {
                            message: 'A Ticket "Summary" is required'
                        }
                    }
                }
                , tMessage: {
                    validators: {
                        notEmpty: {
                            message: 'A Ticket "Message" is required'
                        }
                    }
                }
            }
        }).on( 'success.form.bv', GSR.createTicket );
    }

    , createTicket: function(e) {
        e.preventDefault();

        var form = $( e.target );
        $.post(
            form.attr( 'action' )
            , form.serialize()
            , GSR.createTicketResponse
        )
    }

    , createTicketResponse: function( response ) {
        GSR.defaultAjaxResponse( response );
        if ( response.success ) {
            $('#support-modal').modal( 'hide' );
        }
    }

};

jQuery(function(){
    GSR.datatable();
    GSR.setupSwitches();
    GSR.setupConfirmAttr();
    GSR.setupAjaxAttr();
    GSR.setupRTE();
    GSR.setupModal();
    GSR.setupTicketForm();
});

String.prototype.slug = function() { return this.replace(/^\s+|\s+$/g,"").replace( /[^-a-zA-Z0-9\s]/g, '' ).replace( /[\s]/g, '-' ).toLowerCase(); }

String.prototype.toCamel = function(){ return this.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');}); };