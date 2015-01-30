var Passwords = {

    init: function() {
        $('body').on( 'click', '.delete-password', Passwords.deletePassword );
        Passwords.setupAddEditModal();
    }

    , setupAddEditModal : function(){
        $('body').on( 'click', '[data-modal]', function(e) {
            e.preventDefault();

            var modal = $('#modal');
            if (modal.size() == 0 ) {
                modal = $('<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" />');
                modal.appendTo('body');
            }
            modal.load( $(this).attr('href'), function() {
                Passwords.loadPasswordTriggers();
                modal.modal();
            });
        });

        // This will allow remove based modals to get content on every click by disabling local cache.
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');
        });
    }

    , refreshData : function(){
        $('.dt').dataTable().fnDraw();
    }

    , loadPasswordTriggers : function(){
        $('#generate-password').pGenerator({
            onPasswordGenerated: function (generatedPassword) {
                $("#tPassword").trigger("input");
                $("#tPassword").trigger("keyup");
            },
            passwordElement: '#tPassword'
        });

        $('#tPassword').pStrength({
            backgrounds: [
                ['#FFFFFF', '#FFF'],
                ['#cc0000', '#FFF'],
                ['#cc3333', '#FFF'],
                ['#cc6666', '#FFF'],
                ['#cc9966', '#FFF'],
                ['#e0941c', '#FFF'],
                ['#e8a53a', '#FFF'],
                ['#eab259', '#FFF'],
                ['#66cc66', '#FFF'],
                ['#339933', '#FFF'],
                ['#006600', '#FFF'],
                ['#105610', '#FFF'],
                ['#105610', '#FFF']
            ]
        });

        $('#fAddEditPassword').bootstrapValidator({
            fields: {
                sTitle: {
                    validators: {
                        notEmpty: {
                            message: 'A title is required'
                        }
                    }
                }
                , tUsername: {
                    validators: {
                        notEmpty: {
                            message: 'A username is required'
                        }
                    }
                }
                , tPassword: {
                    validators: {
                        notEmpty: {
                            message: 'A password is required'
                        }
                    }
                }
            }
        }).on( 'success.form.bv', Passwords.submitAddEdit );
    }

    , submitAddEdit: function(e) {
        e.preventDefault();

        var form = $(this);
        var nonce_update = $('#_add_edit').val();

        $.post(
            form.attr('action')
            , form.serialize() + '&_nonce=' + nonce_update
            , function( response ) {
                form.parents('.modal:first').modal('hide');
                Passwords.refreshData();
            }
        );
    }

    , deletePassword: function(e) {
        e.preventDefault();

        var nonce_delete = $('#_delete').val();

        if ( confirm( 'Do you want to delete this Password?' ) ) {
            var form = $(this);
            $.get( form.attr('href'),{
                _nonce: nonce_delete
            }, function(response) {
                GSR.defaultAjaxResponse( response );
                if ( response.success ) {
                    form.parents('.modal:first').modal('hide');
                    Passwords.refreshData();
                }
            } );
        }
    }

}

jQuery(Passwords.init);