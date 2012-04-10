head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', '/js2/?f=charCount', function() {
	// Date Picker
	$('#tDate').datepicker({
		minDate: 0,
		dateFormat: 'mm/dd/yy'
	})
	
	// Time Picker
	$('#tTime').timePicker({
	  	step: 60,
		show24Hours: false
	});
	
	$('#taMessage').charCount({
        css : 'counter bold'
        , cssExceeded : 'error'
        , counterText : 'Characters Left: '
        , allowed : 131
    });

    // Date Picker
	$('#tDate').datepicker({
		minDate: 0,
		dateFormat: 'mm/dd/yy'
	});
});