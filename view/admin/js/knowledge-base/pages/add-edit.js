// When the page has loaded
jQuery(function($) {
   	$('#sSection').change( function() {
        $.post( '/knowledge-base/pages/get-categories/', { _nonce : $('#_get_categories').val(), s : $(this).val() }, ajaxResponse );
    });
});