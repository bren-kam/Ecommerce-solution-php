var Analytics = {

    init: function () {
        Analytics.setupDates();

        Analytics.drawCharts();
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

    , drawCharts: function () {

        Chart.defaults.global = {
            // Boolean - Whether to animate the chart
            animation: true,

            // Number - Number of animation steps
            animationSteps: 60,

            // String - Animation easing effect
            animationEasing: "easeOutQuart",

            // Boolean - If we should show the scale at all
            showScale: true,

            // Boolean - If we want to override with a hard coded scale
            scaleOverride: false,

            // ** Required if scaleOverride is true **
            // Number - The number of steps in a hard coded scale
            scaleSteps: null,
            // Number - The value jump in the hard coded scale
            scaleStepWidth: null,
            // Number - The scale starting value
            scaleStartValue: null,

            // String - Colour of the scale line
            scaleLineColor: "rgba(0,0,0,.1)",

            // Number - Pixel width of the scale line
            scaleLineWidth: 1,

            // Boolean - Whether to show labels on the scale
            scaleShowLabels: true,

            // Interpolated JS string - can access value
            scaleLabel: "<%=value%>",

            // Boolean - Whether the scale should stick to integers, not floats even if drawing space is there
            scaleIntegersOnly: true,

            // Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero: false,

            // String - Scale label font declaration for the scale label
            scaleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

            // Number - Scale label font size in pixels
            scaleFontSize: 12,

            // String - Scale label font weight style
            scaleFontStyle: "normal",

            // String - Scale label font colour
            scaleFontColor: "#666",

            // Boolean - whether or not the chart should be responsive and resize when the browser does.
            responsive: true,

            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: true,

            // Boolean - Determines whether to draw tooltips on the canvas or not
            showTooltips: true,

            // Function - Determines whether to execute the customTooltips function instead of drawing the built in tooltips (See [Advanced - External Tooltips](#advanced-usage-custom-tooltips))
            customTooltips: false,

            // Array - Array of string names to attach tooltip events
            tooltipEvents: ["mousemove", "touchstart", "touchmove"],

            // String - Tooltip background colour
            tooltipFillColor: "rgba(0,0,0,0.8)",

            // String - Tooltip label font declaration for the scale label
            tooltipFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

            // Number - Tooltip label font size in pixels
            tooltipFontSize: 14,

            // String - Tooltip font weight style
            tooltipFontStyle: "normal",

            // String - Tooltip label font colour
            tooltipFontColor: "#fff",

            // String - Tooltip title font declaration for the scale label
            tooltipTitleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

            // Number - Tooltip title font size in pixels
            tooltipTitleFontSize: 14,

            // String - Tooltip title font weight style
            tooltipTitleFontStyle: "bold",

            // String - Tooltip title font colour
            tooltipTitleFontColor: "#fff",

            // Number - pixel width of padding around tooltip text
            tooltipYPadding: 6,

            // Number - pixel width of padding around tooltip text
            tooltipXPadding: 6,

            // Number - Size of the caret on the tooltip
            tooltipCaretSize: 8,

            // Number - Pixel radius of the tooltip border
            tooltipCornerRadius: 6,

            // Number - Pixel offset from point x to tooltip edge
            tooltipXOffset: 10,

            // String - Template string for single tooltips
            tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",

            // String - Template string for multiple tooltips
            multiTooltipTemplate: "<%= value %>",

            // Function - Will fire on animation progression.
            onAnimationProgress: function(){},

            // Function - Will fire on animation completion.
            onAnimationComplete: function(){}
        }

        var visitors_data = {
            labels: AnalyticsSettings.visitors_keys,
            datasets: [
                {
                    label: "Visits",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: AnalyticsSettings.visitors_values
                }
            ]
        };

        var visitors = document.getElementById('visitors-graph').getContext("2d");
        var visitorsChart = new Chart(visitors).Line(visitors_data, {
            bezierCurve: true,
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

        });

        var signups_data = {
            labels: AnalyticsSettings.signups_keys,
            datasets: [
                {
                    label: "Email Signups",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: AnalyticsSettings.signups_values
                }
            ]
        };

        var signups = document.getElementById('signups-graph').getContext("2d");
        var signupsChart = new Chart(signups).Bar(signups_data, {
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
        });


    }

}

jQuery(Analytics.init);


