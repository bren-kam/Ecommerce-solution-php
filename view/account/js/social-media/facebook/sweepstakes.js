var Sweepstakes = {

    init: function() {
        $('#tStartTime, #tEndTime').timepicker({
            step: 60
            , show24Hours: false
            , timeFormat: 'g:i a'
        });

        $('#tStartDate, #tEndDate').datepicker({
            todayHighlight: true
            , format: 'yyyy-mm-dd'
        })
    }

}

jQuery( Sweepstakes.init );