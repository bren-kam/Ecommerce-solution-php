// When the page has loaded
head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
    // Make the sections sortable
	$('#checklist-sections').sortable({
		items		: '.section'
		, cancel		: 'input'
        , cursor      : 'move'
		, placeholder	: 'section-placeholder'
        , forceHelperSize : true
		, forcePlaceholderSize : true
        , handle : '.handle'
	});

    // Make the section items sortable
    $('.section').sortable({
		items		: '.item'
		, cancel		: 'input'
        , cursor      : 'move'
		, placeholder	: 'item-placeholder'
		, forcePlaceholderSize : true
        , handle : '.handle'
	});

    // Remove items
    $('.remove-item').live( 'click', function() {
        if ( !confirm( $(this).attr('confirm') ) )
            return false;

        $(this).parent().remove();
    });

    // Remove Section
    $('.remove-section').live( 'click', function() {
        if ( $(this).parent().find('div.section-items:first .item:first').is('div') ) {
            alert( $(this).attr('err') );
            return;
        }

        if ( !confirm( $(this).attr('confirm') ) )
            return false;

        $(this).parent().remove();
    });
});