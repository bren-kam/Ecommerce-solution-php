head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js', function() {
	// Date Picker
	$('#tStartDate, #tEndDate').datepicker({
		minDate: 0,
		dateFormat: 'mm/dd/yy'
	})
	
	// Time Picker
	$('#tStartTime, #tEndTime').timePicker({
	  	step: 60,
		show24Hours: false
	});
});