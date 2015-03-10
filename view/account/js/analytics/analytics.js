var Analytics = {

    init: function(){
        Analytics.loadMainGraph();

        if ( AnalyticsSettings.show_pie_chart )
            Analytics.loadPieChart();

        $('#large-graph').bind( 'plothover', Analytics.showTooltip );

        $('[data-report]').click( Analytics.loadReport );

        Analytics.setupDates();
    }

    , loadMainGraph: function() {
        $.plot(
            $('#large-graph')
            , [{ label: AnalyticsSettings.plotting_label, data: AnalyticsSettings.plotting_data, color: '#97BBCD' }]
            , {
                lines: { show: true, fill: true },
                points: { show: true },
                selection: { mode: 'x' },
                grid: { hoverable: true, clickable: true },
                legend: { position: 'se' },
                xaxis: { mode: 'time', minTickSize: [1, 'day'] },
                yaxis: { min: 0 }
            }
        );
    }

    , loadPieChart: function() {
        swfobject.embedSWF(
            '/media/flash/open-flash-chart.swf'
            , 'traffic-sources'
            , '200'
            , '200'
            , '9.0.0'
            , ''
            , null
            , { wmode: 'transparent' }
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
                left: item.pageX - 180,
                opacity: 0.80
            }).appendTo("body").fadeIn( 200 );
        } else {
            $('.graph-tooltip').remove();
        }
    }

    , loadReport: function() {
        var name = $(this).data('title')
        var filter = $('#hFilter').length > 0 ? $('#hFilter').val() : null;
        var date_start = $('#date-start').data('datepicker').date.toISOString().slice(0, 10);
        var date_end = $('#date-end').data('datepicker').date.toISOString().slice(0, 10);

        $.post(
            '/analytics/get-graph/'
            , { _nonce: $("#_get_graph").val(), metric: $(this).data('report'), f : filter, ds : date_start, de : date_end }
            , function( plots ) {

                active_graph = name;
                graphOptions = {
                    lines: { show: true, fill: true },
                    points: { show: true },
                    selection: { mode: "x" },
                    grid: { hoverable: true, clickable: true },
                    legend: { position: "se" },
                    xaxis: { mode: "time", minTickSize: [1, 'day'] },
                    yaxis: { min: 0 }
                }

                switch ( name ) {
                    case 'New Visits':
                    case 'Bounce Rate':
                    case 'Exit Rate':
                        percent = '%';
                        time = false;
                        graphOptions = {
                            lines: { show: true, fill: true },
                            points: { show: true },
                            selection: { mode: "x" },
                            grid: { hoverable: true, clickable: true },
                            legend: { position: "se" },
                            xaxis: { mode: "time", minTickSize: [1, 'day'] },
                            yaxis: {
                                min: 0,
                                max: 100,
                                tickFormatter: function (val, axis) {
                                    return val + '%';
                                }
                            }
                        }

                        break;

                    case 'Pages/Visits':
                        time = false;
                        percent = '';
                        decimal = true;
                        break;

                    case 'Time On Site':
                    case 'Time On Page':
                    case 'Avg. Time On Site':
                        percent = '';
                        time = true;
                        decimal = false;
                        graphOptions = {
                            lines: { show: true, fill: true },
                            points: { show: true },
                            selection: { mode: "x" },
                            grid: { hoverable: true, clickable: true },
                            legend: { position: "se" },
                            xaxis: { mode: "time", minTickSize: [1, 'day'] },
                            yaxis: { mode: "time", min: 0 }
                        }
                        break;

                    case 'Direct Traffic':
                    case 'Referring Sites':
                    case 'Search Engines':
                    default:
                        percent = '';
                        time = false;
                        decimal = false;
                        break;
                }

                plotData = [{
                    label: name,
                    data: plots['plotting_array'],
                    color: '#97BBCD'
                }];

                $.plot( $('#large-graph'), plotData, graphOptions );
            }
        );
    }

    , setupDates: function() {
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

        $('#date-start, #date-end').on('changeDate', function(e) {
            var qs = location.search ? location.search.substring(1) : '';
            qs = qs.replace( /&ds(=[^&]*)?|^ds(=[^&]*)?&?/, '' ).replace( /&de(=[^&]*)?|^de(=[^&]*)?&?/, '' );
            qs += qs ? '&' : '?';
            qs += 'ds=' + date_start.data('datepicker').date.toISOString().slice(0, 10);
            qs += '&de=' + date_end.data('datepicker').date.toISOString().slice(0, 10);
            location.search = qs;
        });
    }

}

/**
 * Used for Pie Chart
 * @returns Object
 */
function open_flash_chart_data() {
    return AnalyticsSettings.pie_chart;
}

jQuery( Analytics.init );