var Reports = {

    init: function() {
        $('#services').change( Reports.addService );

        $('#criteria-list').on( 'click', '.delete-criteria', Reports.deleteCriteria );

        // Autocomplete
        Reports.setupAutocomplete();
        // Autcomplete - When change search type, we must reconfigure
        $('#type').change( Reports.setupAutocomplete );

        $('#report-form').submit( Reports.run );
    }

    , addService: function() {
        var option = $(this).find( 'option:selected' );

        if ( option.val() == '' )
            return;

        $( '<li />' )
            .text( 'Service - ' + option.text() + ' ' )
            .append(
                $( '<input />' )
                    .attr( 'type', 'hidden' )
                    .attr( 'name', 'c[services][' + option.val() + ']')
                    .val( '1' )
            )
            .append(
                $( '<a />' )
                    .addClass( 'delete-criteria' )
                    .attr( 'href', 'javascript:; ')
                    .attr( 'title', 'Delete ' + option.text() )
                    .html( '<i class="fa fa-trash-o"></i>' )
            ).appendTo( '#criteria-list' );

        option.prop( 'disabled', true );
    }

    , addCriteria: function( event, item ) {
        var typeItem = $('#type option:selected');
        var type = typeItem.val();

        $( '<li />' )
            .text( typeItem.text() + ' - ' + item[type] + ' ' )
            .append(
                $( '<input />' )
                    .attr( 'type', 'hidden' )
                    .attr( 'name', 'c[' + type +'][' + item.object_id + ']')
                    .val( '1' )
            )
            .append(
                $('<a />')
                    .addClass( 'delete-criteria' )
                    .attr( 'href', 'javascript:; ')
                    .attr( 'title', 'Delete ' + item[type] )
                    .html( '<i class="fa fa-trash-o"></i>' )
            ).appendTo( '#criteria-list' );

        $('#tAutoComplete').val( '' );
    }

    , deleteCriteria: function() {
        var criteriaItem = $(this).parents( 'li:first' );
        var criteria = criteriaItem.data( 'criteria' );

        // If it's a Service, enable it again from the dropdown
        if ( criteria.service ) {
            $('#services [value=' + criteria.service + ']').prop( 'disabled', false );
        }

        criteriaItem.remove();
    }

    , setupAutocomplete: function() {
        var searchType = $("#type").val();
        var nonce = $('#_autocomplete').val();

        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , remote: {
                url: '/reports/autocomplete/?_nonce=' + nonce + '&type=' + searchType + '&term=%QUERY'
                , filter: function( list ) {
                    return list.objects
                }
            }
        });

        autocomplete.initialize();
        $("#tAutoComplete")
            .typeahead('destroy')
            .typeahead(null, {
                displayKey: searchType
                , source: autocomplete.ttAdapter()
            })
            .unbind('typeahead:selected')
            .on('typeahead:selected', Reports.addCriteria );
    }

    , run: function(e) {
        console.log('here');
        e.preventDefault();

        $('#report-form').find(':submit').text('Running...');

        $.post(
            '/reports/search/'
            , $(this).serialize()
            , Reports.runComplete
        )
    }

    , runComplete: function( response ) {
        $('#report-form').find(':submit').text('Run Report');
        $('#report').html( response );
    }

}

jQuery( Reports.init );