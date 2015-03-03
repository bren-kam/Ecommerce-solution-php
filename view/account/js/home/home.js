var Analytics = {

    init: function () {

        Analytics.loadVisitorsGraph();
        Analytics.loadSignupsGraph();

        $('#visitors-graph').bind( 'plothover', Analytics.showTooltip );
        $('#signups-graph').bind( 'plothover', Analytics.showTooltip );

        Analytics.setupDates();
    }

    , setupDates: function () {
        var date_start_visitors = $('#date-start-visitors').datepicker({
            format: 'm/d/yyyy', onRender: function (date) {
                var max_date = typeof date_end_visitors == 'undefined' ? new Date() : date_end_visitors.data('datepicker').date;
                return date.valueOf() >= max_date.valueOf() ? 'disabled' : '';
            }
        });

        var date_end_visitors = $('#date-end-visitors').datepicker({
            format: 'm/d/yyyy', onRender: function (date) {
                return date.valueOf() <= date_start_visitors.data('datepicker').date.valueOf() ? 'disabled' : '';
            }
        });

        $('#date-start-visitors, #date-end-visitors').on('changeDate', function (e) {
            var qs = location.search ? location.search.substring(1) : '';
            qs = qs.replace(/&dsv(=[^&]*)?|^dsv(=[^&]*)?&?/, '').replace(/&dev(=[^&]*)?|^dev(=[^&]*)?&?/, '');
            qs += qs ? '&' : '?';
            qs += 'dsv=' + date_start_visitors.data('datepicker').date.toISOString().slice(0, 10);
            qs += '&dev=' + date_end_visitors.data('datepicker').date.toISOString().slice(0, 10);
            location.search = qs;
        });
        
        var date_start_signups = $('#date-start-signups').datepicker({
            format: 'm/d/yyyy', onRender: function (date) {
                var max_date = typeof date_end_signups == 'undefined' ? new Date() : date_end_signups.data('datepicker').date;
                return date.valueOf() >= max_date.valueOf() ? 'disabled' : '';
            }
        });

        var date_end_signups = $('#date-end-signups').datepicker({
            format: 'm/d/yyyy', onRender: function (date) {
                return date.valueOf() <= date_start_signups.data('datepicker').date.valueOf() ? 'disabled' : '';
            }
        });

        $('#date-start-signups, #date-end-signups').on('changeDate', function (e) {
            var qs = location.search ? location.search.substring(1) : '';
            qs = qs.replace(/&dss(=[^&]*)?|^dss(=[^&]*)?&?/, '').replace(/&des(=[^&]*)?|^dev(=[^&]*)?&?/, '');
            qs += qs ? '&' : '?';
            qs += 'dss=' + date_start_signups.data('datepicker').date.toISOString().slice(0, 10);
            qs += '&des=' + date_end_signups.data('datepicker').date.toISOString().slice(0, 10);
            location.search = qs;
        });
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
                $( '.graph-tooltip').remove();
            }
        }

    , loadVisitorsGraph: function() {
            $.plot(
                $('#visitors-graph')
                , [{ label: "Visits", data: AnalyticsSettings.visitors, color: '#97BBCD' }]
                , {
                    lines: { show: true, fill: true }
                    , points: { show: true }
                    , selection: { mode: 'x' }
                    , grid: { hoverable: true, clickable: true }
                    , legend: { position: 'se' }
                    , xaxis: { mode: 'time' }
                    , yaxis: { min: 0 }
//                    series: { curvedLines: { apply: true, active: true, monotonicFit: true  } }
                }
            );
        }

    , loadSignupsGraph: function() {
            $.plot(
                $('#signups-graph')
                , [{ label: "Email Signups", data: AnalyticsSettings.signups, color: '#97BBCD' }]
                , {
                    lines: { show: true, fill: true }
                    , points: { show: true }
                    , selection: { mode: 'x' }
                    , grid: { hoverable: true, clickable: true }
                    , legend: { position: 'se' }
                    , xaxis: { mode: 'time' }
                    , yaxis: { min: 0 }
//                    series: { curvedLines: { apply: true, active: true, monotonicFit: true  } }
                }
            );
        }
}

jQuery(Analytics.init);


