head.js( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
	// Date Picker
	$('#tStartDate, #tEndDate').datepicker({
		minDate: 0,
		dateFormat: 'mm/dd/yy'
	});
	
	// Time Picker
    var timePickers = $('#tStartTime, #tEndTime');
	timePickers.timepicker({
	  	step: 60
		, show24Hours: false
        , timeFormat: 'g:i a'
	}).timepicker('show');

    // Fix for offset
    timePickers.timepicker('hide');
});