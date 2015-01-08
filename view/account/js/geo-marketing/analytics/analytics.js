var Analytics = {

    init: function(){
        Analytics.loadMainGraph();

        $('.report').bind( 'plothover', Analytics.showTooltip );

        Analytics.setupFilters();
    }

    , loadMainGraph: function() {
        var plotOptions = {
            lines: { show: true, fill: true },
            points: { show: true },
            selection: { mode: 'x' },
            grid: { hoverable: true, clickable: true },
            legend: { position: 'se' },
            xaxis: { mode: 'time' },
            yaxis: { min: 0 }
        };

        $.plot(
            $('#searches')
            , [{ label: 'Searches', data: AnalyticsSettings.reports.searches, color: '#FFA900' }]
            , plotOptions
        );

        $.plot(
            $('#profile-views')
            , [{ label: 'Profile Views', data: AnalyticsSettings.reports.profile_views, color: '#FFA900' }]
            , plotOptions
        );

        $.plot(
            $('#special-offer-clicks')
            , [{ label: 'Special Offer Clicks', data: AnalyticsSettings.reports.special_offer_clicks, color: '#FFA900' }]
            , plotOptions
        );

        $.plot(
            $('#foursquare-checkins')
            , [{ label: 'Foursquare Checkins', data: AnalyticsSettings.reports.foursquare_checkins, color: '#FFA900' }]
            , plotOptions
        );

    }

    , showTooltip: function( event, pos, item ) {
        if ( item ) {
            var contents = item.datapoint[1];

            var date = new Date();
            date.setTime( item.datapoint[0] );
            var date_str = (date.getMonth()+1) + '/' + date.getDate() + '/' + date.getFullYear();

            $('<div class="graph-tooltip"><span class="tooltip-text">' + date_str + '</span><span class="tooltip-value">' + contents + ' ' + item.series.label + '</span></div>').css( {
                position: 'absolute',
                display: 'none',
                top: item.pageY - 25,
                left: item.pageX + 10,
                opacity: 0.80
            }).appendTo("body").fadeIn( 200 );
        } else {
            $('.graph-tooltip').remove();
        }
    }

   , setupFilters: function() {
        var date_start = $('#date-start').datepicker({
            format: 'm/d/yyyy'
            , onRender: function(date) {
                var max_date = typeof date_end == 'undefined' ? new Date() : date_end.data('datepicker').date;
                return date.valueOf() >= max_date.valueOf() ? 'disabled' : '';
            }
        });

        var date_end = $('#date-end').datepicker({
            format: 'm/d/yyyy'
            , onRender: function(date) {
                return date.valueOf() <= date_start.data('datepicker').date.valueOf() ? 'disabled' : '';
            }
        });

        var location_id = $('#location-id');

        var updateFilters = function() {
            console.log(location_id.val());
            var qs = location.search ? location.search.substring(1) : '';
            qs = qs.replace( /&ds(=[^&]*)?|^ds(=[^&]*)?&?/, '' ).replace( /&de(=[^&]*)?|^de(=[^&]*)?&?/, '' ).replace( /&location_id(=[^&]*)?|^location_id(=[^&]*)?&?/, '' );
            qs += qs ? '&' : '?';
            qs += 'ds=' + date_start.data('datepicker').date.toISOString().slice(0, 10);
            qs += '&de=' + date_end.data('datepicker').date.toISOString().slice(0, 10);
            qs += '&location_id=' + location_id.val();
            location.search = qs;
        };

        $('#date-start, #date-end').on('changeDate', updateFilters);
        location_id.change(updateFilters);
    }

};

jQuery( Analytics.init );