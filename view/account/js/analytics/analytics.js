// When the page has loaded
jQuery(function($) {
	decimal = false;

    $.plot($('#dLargeGraph'),[
            { label: plotting_label, data: plotting_data, color: '#FFA900' }
        ],{
            lines: { show: true, fill: true },
            points: { show: true },
            selection: { mode: 'x' },
            grid: { hoverable: true, clickable: true },
            legend: { position: 'se' },
            xaxis: { mode: 'time' },
            yaxis: { min: 0 }
    });

    active_graph = 'Visits', percent = '', time = false;

    if ( 'undefined' != typeof show_piechart )
        swfobject.embedSWF('/media/flash/open-flash-chart.swf', 'dTrafficSources', '200', '200', '9.0.0', '', null, { wmode:'transparent' } );

    // Load the datepicker
    head.load( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js', function() {
        // Date Picker
        $('#tDateStart').datepicker({
            maxDate: -3
            , dateFormat: 'M d, yy'
            , altFormat: 'yy-mm-dd'
            , onSelect: function( dateText, dp ) {
                var url = insertParam( 'ds', $('#tDateStart').val() );
                document.location.search =  insertParam( 'de', $('#tDateEnd').val(), url ).replace( /^&/, '' );
            }
            , onClose: function( selectedDate ) {
                var minDate = $( "#tDateStart").datepicker("getDate");
                minDate.setDate( minDate.getDate() + 1 );
                $( "#tDateEnd" ).datepicker( "option", "minDate", minDate );
            }
        });
        $('#tDateEnd').datepicker({
            maxDate: -1
            , dateFormat: 'M d, yy'
            , altFormat: 'yy-mm-dd'
            , onSelect: function( dateText, dp ) {
                var url = insertParam( 'ds', $('#tDateStart').val() );
                document.location.search =  insertParam( 'de', $('#tDateEnd').val(), url ).replace( /^&/, '' );
            }
            , onClose: function( selectedDate ) {
                var maxDate = $( "#tDateEnd").datepicker("getDate");
                maxDate.setDate( maxDate.getDate() - 1 );
                $( "#tDateStart" ).datepicker( "option", "maxDate", maxDate );
            }
        });
    });

	// Put the tooltip there
	$('#dLargeGraph').bind("plothover", function( event, pos, item ) {
		if ( item ) {
			// Changed 1/9/12 - Raffy said the tooltips were off by a day
			var x = date( 'F j, Y', item.datapoint[0] / 1000 ), y = item.datapoint[1];
			showTooltip(item.pageX, item.pageY, x, y, "tooltipHover");
		} else {
			$(".tooltipHover").remove();
		}
	});
	
	$('a.sparkline').click( function(e) {
		e.preventDefault();
		var name = $(this).attr('title').replace( ' Sparkline', '' ), filter = $('#hFilter');

        if ( 'undefined' != filter )
            filter = filter.val();

		$.post( '/analytics/get-graph/', { _nonce: $("#_get_graph").val(), metric: $(this).attr('href').replace('#',''), f : filter, ds : $('#tDateStart').val(), de : $('#tDateEnd').val() }, function( plots ) {

			active_graph = name;
			graphOptions = {
				lines: { show: true, fill: true },
				points: { show: true },
				selection: { mode: "x" },
				grid: { hoverable: true, clickable: true },
				legend: { position: "se" },
				xaxis: { mode: "time" },
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
						xaxis: { mode: "time" },
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
						xaxis: { mode: "time" },
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
					color: '#FFA900'
				}];
			
			$.plot($('#dLargeGraph'), plotData, graphOptions );
		}, 'json' );
	});
	
	
});

function showTooltip( x, y, date_number, contents, type ) {
	if ( time ) {
		var data =  format_time( contents );
	} else {
		var data = ( '%' == percent || decimal ) ? number_format( contents, 2 ) : number_format( contents );
	}
	
	$('<div class="' + type + '" id="tooltip_' + x + '"><span class="date">' + date_number + '</span><span class="label">' + active_graph + ': <strong>' + data + percent + '</strong></span></div>').css( {
		display: 'none',
		top: y - 25,
		left: x + 10,
		opacity: 0.80
	}).appendTo("body").fadeIn( 200 );
}

function date(format, timestamp) {
    // http://kevin.vanzonneveld.net
    // +   original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    // +      parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard
    // +   improved by: Tim Wiel
    // +   improved by: Bryan Elliott
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: David Randall
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +  derived from: gettimeofday
    // +      input by: majak
    // +   bugfixed by: majak
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Alex
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +   improved by: Thomas Beaucourt  (http://www.webapp.fr)
    // +   improved by: JT
    // +   improved by: Theriault
    // %        note 1: Uses global: php_js to store the default timezone
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000); 
    // *     example 4: (x+'').length == 10 // 2009 01 09
    // *     returns 4: true
    // *     example 5: date('W', 1104534000);
    // *     returns 5: '53'
    // *     example 6: date('B t', 1104534000);
    // *     returns 6: '999 31'
    // *     example 7: date('W', 1293750000); // 2010-12-31
    // *     returns 7: '52'
    // *     example 8: date('W', 1293836400); // 2011-01-01
    // *     returns 8: '52'
    // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
    // *     returns 9: '52 2011-01-02'
    var that = this,
        jsdate, f, formatChr = /\\?([a-z])/gi, formatChrCb,
        // Keep this here (works, but for code commented-out
        // below for file size reasons)
        //, tal= [],
        _pad = function (n, c) {
            if ((n = n + "").length < c) {
                return new Array((++c) - n.length).join("0") + n;
            } else {
                return n;
            }
        },
        txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur",
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"],
        txt_ordin = {
            1: "st",
            2: "nd",
            3: "rd",
            21: "st", 
            22: "nd",
            23: "rd",
            31: "st"
        };
    formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s;
    };
    f = {
    // Day
        d: function () { // Day of month w/leading 0; 01..31
            return _pad(f.j(), 2);
        },
        D: function () { // Shorthand day name; Mon...Sun
            return f.l().slice(0, 3);
        },
        j: function () { // Day of month; 1..31
            return jsdate.getDate();
        },
        l: function () { // Full day name; Monday...Sunday
            return txt_words[f.w()] + 'day';
        },
        N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
            return f.w() || 7;
        },
        S: function () { // Ordinal suffix for day of month; st, nd, rd, th
            return txt_ordin[f.j()] || 'th';
        },
        w: function () { // Day of week; 0[Sun]..6[Sat]
            return jsdate.getDay();
        },
        z: function () { // Day of year; 0..365
            var a = new Date(f.Y(), f.n() - 1, f.j()),
                b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 864e5) + 1;
        },

    // Week
        W: function () { // ISO-8601 week number
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                b = new Date(a.getFullYear(), 0, 4);
            return 1 + Math.round((a - b) / 864e5 / 7);
        },

    // Month
        F: function () { // Full month name; January...December
            return txt_words[6 + f.n()];
        },
        m: function () { // Month w/leading 0; 01...12
            return _pad(f.n(), 2);
        },
        M: function () { // Shorthand month name; Jan...Dec
            return f.F().slice(0, 3);
        },
        n: function () { // Month; 1...12
            return jsdate.getMonth() + 1;
        },
        t: function () { // Days in month; 28...31
            return (new Date(f.Y(), f.n(), 0)).getDate();
        },

    // Year
        L: function () { // Is leap year?; 0 or 1
            var y = f.Y(), a = y & 3, b = y % 4e2, c = y % 1e2;
            return 0 + (!a && (c || !b));
        },
        o: function () { // ISO-8601 year
            var n = f.n(), W = f.W(), Y = f.Y();
            return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
        },
        Y: function () { // Full year; e.g. 1980...2010
            return jsdate.getFullYear();
        },
        y: function () { // Last two digits of year; 00...99
            return (f.Y() + "").slice(-2);
        },

    // Time
        a: function () { // am or pm
            return jsdate.getHours() > 11 ? "pm" : "am";
        },
        A: function () { // AM or PM
            return f.a().toUpperCase();
        },
        B: function () { // Swatch Internet time; 000..999
            var H = jsdate.getUTCHours() * 36e2, // Hours
                i = jsdate.getUTCMinutes() * 60, // Minutes
                s = jsdate.getUTCSeconds(); // Seconds
            return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
        },
        g: function () { // 12-Hours; 1..12
            return f.G() % 12 || 12;
        },
        G: function () { // 24-Hours; 0..23
            return jsdate.getHours();
        },
        h: function () { // 12-Hours w/leading 0; 01..12
            return _pad(f.g(), 2);
        },
        H: function () { // 24-Hours w/leading 0; 00..23
            return _pad(f.G(), 2);
        },
        i: function () { // Minutes w/leading 0; 00..59
            return _pad(jsdate.getMinutes(), 2);
        },
        s: function () { // Seconds w/leading 0; 00..59
            return _pad(jsdate.getSeconds(), 2);
        },
        u: function () { // Microseconds; 000000-999000
            return _pad(jsdate.getMilliseconds() * 1000, 6);
        },

    // Timezone
        e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
// The following works, but requires inclusion of the very large
// timezone_abbreviations_list() function.
/*              var abbr = '', i = 0, os = 0;
            if (that.php_js && that.php_js.default_timezone) {
                return that.php_js.default_timezone;
            }
            if (!tal.length) {
                tal = that.timezone_abbreviations_list();
            }
            for (abbr in tal) {
                for (i = 0; i < tal[abbr].length; i++) {
                    os = -jsdate.getTimezoneOffset() * 60;
                    if (tal[abbr][i].offset === os) {
                        return tal[abbr][i].timezone_id;
                    }
                }
            }
*/
            return 'UTC';
        },
        I: function () { // DST observed?; 0 or 1
            // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
            // If they are not equal, then DST is observed.
            var a = new Date(f.Y(), 0), // Jan 1
                c = Date.UTC(f.Y(), 0), // Jan 1 UTC
                b = new Date(f.Y(), 6), // Jul 1
                d = Date.UTC(f.Y(), 6); // Jul 1 UTC
            return 0 + ((a - c) !== (b - d));
        },
        O: function () { // Difference to GMT in hour format; e.g. +0200
            var a = jsdate.getTimezoneOffset();
            return (a > 0 ? "-" : "+") + _pad(Math.abs(a / 60 * 100), 4);
        },
        P: function () { // Difference to GMT w/colon; e.g. +02:00
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2));
        },
        T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
// The following works, but requires inclusion of the very
// large timezone_abbreviations_list() function.
/*              var abbr = '', i = 0, os = 0, default = 0;
            if (!tal.length) {
                tal = that.timezone_abbreviations_list();
            }
            if (that.php_js && that.php_js.default_timezone) {
                default = that.php_js.default_timezone;
                for (abbr in tal) {
                    for (i=0; i < tal[abbr].length; i++) {
                        if (tal[abbr][i].timezone_id === default) {
                            return abbr.toUpperCase();
                        }
                    }
                }
            }
            for (abbr in tal) {
                for (i = 0; i < tal[abbr].length; i++) {
                    os = -jsdate.getTimezoneOffset() * 60;
                    if (tal[abbr][i].offset === os) {
                        return abbr.toUpperCase();
                    }
                }
            }
*/
            return 'UTC';
        },
        Z: function () { // Timezone offset in seconds (-43200...50400)
            return -jsdate.getTimezoneOffset() * 60;
        },

    // Full Date/Time
        c: function () { // ISO-8601 date.
            return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
        },
        r: function () { // RFC 2822
            return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
        },
        U: function () { // Seconds since UNIX epoch
            return Math.round(jsdate.getTime() / 1000);
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = (
            (typeof timestamp === 'undefined') ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
            new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
        );
        return format.replace(formatChr, formatChrCb);
    };
    return this.date(format, timestamp);
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival
    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');
    // *     returns 3: '1234.57'

    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'
    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');
    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'
    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    var n = !isFinite(+number) ? 0 : +number, 
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/**
 * Formats seconds into readable time
 */
function format_time( time_in_seconds ) {
	var tm = new Date( time_in_seconds );
		
	var hours = tm.getUTCHours();
	if ( hours < 10 )
		hours = '0' + hours;

	var minutes = tm.getUTCMinutes();
	if ( minutes < 10 )
		minutes = '0' + minutes;

	var seconds = tm.getUTCSeconds();
	if ( seconds < 10 )
		seconds = '0' + seconds;
		
	return hours + ':' + minutes + ':' + seconds;
}

/**
 * Insert a parameter into the query string
 * @param key
 * @param value
 */
function insertParam(key, value)
{
    key = escape(key); value = escape(value);

    var kvp = ( 2 == arguments.length ) ? document.location.search.substr(1).split('&') : arguments[2].split('&');

    var i=kvp.length; var x; while(i--)
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
        }
    }

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}

    //this will reload the page, it's likely better to store this until finished
    //document.location.search = kvp.join('&');
    return kvp.join('&');
}
