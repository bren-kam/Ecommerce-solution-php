var TaxEditor = {

    template: null

    , init: function() {
        TaxEditor.template = $('#tax-template').clone().removeClass('hidden').removeAttr('id');
        $('#tax-template').remove();

        $('#tax-list').on( 'click', '.delete', TaxEditor.deleteTax );
        $('#tax-list').on( 'click', '.toggle-zip-codes', TaxEditor.toggleZipCodes );
        $('.add').click( TaxEditor.addTax );

        $('textarea').each( function(){
            $(this).val( $.trim($(this).val()) );
        });
    }

    , deleteTax: function() {
        $(this).parents('.tax').remove();
    }

    , toggleZipCodes: function() {
        var textarea = $(this).parents('.tax').find('textarea');
        if ( textarea.is(':visible') ) {
            textarea.hide();
        } else {
            textarea.removeClass('hidden').show();
        }
    }

    , addTax: function() {
        var row = TaxEditor.template.clone();
        var code = $('#state').val();
        var state = $('#state').find(':selected').text();
        var tax = $('#tax').val();

        row.html(
            row.html()
                .replace( /STATE_CODE/g, code )
                .replace( /STATE_NAME/g, state )
                .replace( /TAX/g, tax )
        );

        row.appendTo('#tax-list');
    }

}

jQuery( TaxEditor.init );