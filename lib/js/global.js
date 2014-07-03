var GSR = {

    /**
     * Make DataTable
     * @param context
     */
    datatable: function(context) {

        var tables = $('table[ajax],table.dt:not(.manual)', context);

        // If there are tables, load datatables plugin and load the content
        if( tables.length )
            head.load(
                '//cdn.datatables.net/1.10.0/js/jquery.dataTables.js'
                , '//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.js'
                , function() {
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
                        aoColumns: columns,
                        fnDrawCallback : function() {
                            // Run Sparrow on new content and add the class last to the last row
                            GSR.datatable( $(this).find('tr:last').addClass('last').end() );
                        }
                    };

                    // If it's AJAX
                    if( a )
                        settings.bProcessing = 1, settings.bServerSide = 1, settings.sAjaxSource = a;

                    // Make the dataTable
                    $(this).dataTable(settings);
                });
            });

    }

    , setupSwitches: function() {
        var switches = $("[data-toggle='switch']");
        if ( switches.size() > 0 )
            switches.wrap('<div class="switch" />').parent().bootstrapSwitch();
    }

    , setupConfirmAttr: function() {
        $(document).on('click', 'a[confirm]:not([ajax])', function(e){
            e.preventDefault();
            return confirm( $(this).attr('confirm') );
        });
    }

    , setupAjaxAttr: function() {
        $(document).on('click', 'a[ajax]', function(e){
            e.preventDefault();

            confirmQuestion = $(this).attr('confirm');
            if( confirmQuestion && !confirm( confirmQuestion ) )
                return;

            $.get( $(this).attr('href'), GSR.defaultAjaxResponse );
        });
    }

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
    }

};

jQuery(function(){
    GSR.datatable();
    GSR.setupSwitches();
    GSR.setupConfirmAttr();
    GSR.setupAjaxAttr();
});