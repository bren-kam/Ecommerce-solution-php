head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', '/js2/?f=charCount', function() {
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
	
	$('#taMessage').keyup( function() {
        $(this).val( $(this).val().replace(/\n/, '') );
    }).charCount({
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