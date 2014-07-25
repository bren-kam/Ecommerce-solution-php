 /* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
	// Make the elements sortable
	$('#dElementBoxes').sortable({
		items		            : '.element-box'
		, placeholder	        : 'box-placeholder'
		, forcePlaceholderSize  : true
        , update                : saveLayout
	}).on( 'click', 'a.enable-disable', function() {
        var parent = $(this).parent();

        // Handle stuff
        if ( $(this).hasClass('disabled') ) {
            $(this).removeClass('disabled');
            parent.removeClass('disabled');
            parent.find('input:first').val( parent.find('h2:first').text() + '|0');
        } else {
            $(this).addClass('disabled');
            parent.addClass('disabled');
            parent.find('input:first').val( parent.find('h2:first').text() + '|1');
        }

        saveLayout();
    });
});

function saveLayout() {
    $.post( '/website/save-layout/', { _nonce: $('#_save_layout').val(), layout : $('#fHomePageLayout').serialize() }, ajaxResponse );
}