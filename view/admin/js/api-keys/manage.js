var ManageBrands = {

    init: function() {
        ManageBrands.setupAutocomplete();
        $('#brand-list').on( 'click', '.remove', ManageBrands.remove );
    }

    , setupAutocomplete: function() {
        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , local: Brands
        });

        autocomplete.initialize();
        $("#brand-autocomplete")
            .typeahead(
                {
                    hint: true
                    , highlight: true
                }
                , {
                    displayKey: 'name'
                    , source: autocomplete.ttAdapter()
                }
            )
            .unbind('typeahead:selected')
            .on('typeahead:selected', ManageBrands.add );
    }

    , add: function( event, item ) {
        $('#brand-list').append(
            '<li>' + item.name + ' <input type="hidden" name="brands[]" value="' + item.id + '" /> <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a></li>'
        );
    }

    , remove: function() {
        $(this).parents('li').remove();
    }

};

var ManageAshleyAccounts = {

    init: function() {
        ManageAshleyAccounts.setupAutocomplete();
        $('#ashley-account-list').on( 'click', '.remove', ManageAshleyAccounts.remove );
    }

    , setupAutocomplete: function() {
        var autocomplete = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name')
            , queryTokenizer: Bloodhound.tokenizers.whitespace
            , local: AshleyAccounts
        });

        autocomplete.initialize();
        $("#ashley-account-autocomplete")
            .typeahead(
            {
                hint: true
                , highlight: true
                }
            , {
                displayKey: 'name'
                , source: autocomplete.ttAdapter()
            }
        )
            .unbind('typeahead:selected')
            .on('typeahead:selected', ManageAshleyAccounts.add );
    }

    , add: function( event, item ) {
        $('#ashley-account-list').append(
            '<li>' + item.name + ' <input type="hidden" name="ashley-accounts[]" value="' + item.id + '" /> <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a></li>'
        );
    }

    , remove: function() {
        $(this).parents('li').remove();
    }

}


jQuery( ManageBrands.init );
jQuery( ManageAshleyAccounts.init );