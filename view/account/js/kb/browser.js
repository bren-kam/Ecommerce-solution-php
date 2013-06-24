// When the page has loaded
jQuery(function($) {
	$('a.tab').click( function() {
        var container = $(this).parent().parent(), tabId = $(this).attr('href').replace( '#', '');
        $('ol:not(' + tabId + ')', container).hide();
        $('#' + tabId).show();

        $(this).parent().find('a.tab.selected').removeClass('selected');
        $(this).addClass('selected');
    });
});