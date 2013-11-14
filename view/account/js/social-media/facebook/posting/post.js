head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
	// Date Picker
	$('#tDate').datepicker({
		minDate: 0,
		dateFormat: 'mm/dd/yy'
	});

	// Time Picker
    var tTime = $('#tTime');
    tTime.timepicker({
        step: 60
        , show24Hours: false
        , timeFormat: 'g:i a'
    }).timepicker('show');

    // Fix for offset
    tTime.timepicker('hide');

    // Stop double postings
    $('#fFBPost').submit( function() {
        var validated = document.fFBPost.trigger('validate');

        if ( validated )
            $('#sSubmit').attr( 'disabled', true );
    });
});