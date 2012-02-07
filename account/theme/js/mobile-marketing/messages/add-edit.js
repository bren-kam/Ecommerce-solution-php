head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
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
});