<?php
/**
 * Data class, has functions for random lists
 *
 * Functions:
 * |array states( [ $display = true [, $select_value = '' ]] ) - shows the states
 * |array credit_cards( [ $display = true [, $select_value = '' [, $gateway_list = 'AIM' ]]] ) - shows credit cards
 * |array months( [ $display = true [, $select_value = '' [, $key_format = 'abbr' [, $value_format = 'full' ]]]] ) - shows all the months
 * |array years( [ $display = true [, $select_value = '' [, $amount = 10 ]]] ) - shows the years (determined by $amount)
 *
 * @package Studio98 Framework
 * @since 1.0
 */

if ( !isset( $s98_cache ) ) {
	global $s98_cache;
	$s98_cache = new Base_Cache();
}

class data extends Base_Class {
	/**
	 * Returns or displays states
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @return array
	 */
	public static function states( $display = true, $select_value = '' ) {
		$states = array(
			'AK' => 'Alaska'
			, 'AL' => 'Alabama'
			, 'AR' => 'Arkansas'
			, 'AZ' => 'Arizona'
			, 'CA' => 'California'
			, 'CO' => 'Colorado'
			, 'CT' => 'Connecticut'
			, 'DC' => 'District of Columbia'
			, 'DE' => 'Delaware'
			, 'FL' => 'Florida'
			, 'GA' => 'Georgia'
			, 'HI' => 'Hawaii'
			, 'IA' => 'Iowa'
			, 'ID' => 'Idaho'
			, 'IL' => 'Illinois'
			, 'IN' => 'Indiana'
			, 'KS' => 'Kansas'
			, 'KY' => 'Kentucky'
			, 'LA' => 'Louisiana'
			, 'MA' => 'Massachusetts'
			, 'MD' => 'Maryland'
			, 'ME' => 'Maine'
			, 'MI' => 'Michigan'
			, 'MN' => 'Minnesota'
			, 'MO' => 'Missouri'
			, 'MS' => 'Mississippi'
			, 'MT' => 'Montana'
			, 'NC' => 'North Carolina'
			, 'ND' => 'North Dakota'
			, 'NE' => 'Nebraska'
			, 'NH' => 'New Hampshire'
			, 'NJ' => 'New Jersey'
			, 'NM' => 'New Mexico'
			, 'NV' => 'Nevada'
			, 'NY' => 'New York'
			, 'OH' => 'Ohio'
			, 'OK' => 'Oklahoma'
			, 'OR' => 'Oregon'
			, 'PA' => 'Pennsylvania'
			, 'RI' => 'Rhode Island'
			, 'SC' => 'South Carolina'
			, 'SD' => 'South Dakota'
			, 'TN' => 'Tennessee'
			, 'TX' => 'Texas'
			, 'UT' => 'Utah'
			, 'VA' => 'Virginia'
			, 'VT' => 'Vermont'
			, 'WA' => 'Washington'
			, 'WI' => 'Wisconsin'
			, 'WV' => 'West Virginia'
			, 'WY' => 'Wyoming'
		);
		
		if ( $display ) {
			// Initialize variable
			$state_options = '';
			
			foreach ( $states as $st => $state ) {
				$selected = ( $select_value == $st ) ? ' selected="selected"' : '';
				$state_options .= "<option value='$st'$selected>$state</option>\n";
			}
	
			echo $state_options;
		}

        return $states;
	}
	
	/**
	 * Returns or displays countries
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @return array
	 */
	public static function countries( $display = true, $select_value = '' ) {
		$countries = array(
			'AF' => 'Afghanistan'
			, 'AL' => 'Albania'
			, 'DZ' => 'Algeria'
			, 'AS' => 'American Samoa'
			, 'AD' => 'Andorra'
			, 'AO' => 'Angola'
			, 'AI' => 'Anguilla'
			, 'AQ' => 'Antarctica'
			, 'AG' => 'Antigua and Barbuda'
			, 'AR' => 'Argentina'
			, 'AM' => 'Armenia'
			, 'AW' => 'Aruba'
			, 'AU' => 'Australia'
			, 'AT' => 'Austria'
			, 'AZ' => 'Azerbaijan'
			, 'BS' => 'Bahamas'
			, 'BH' => 'Bahrain'
			, 'BD' => 'Bangladesh'
			, 'BB' => 'Barbados'
			, 'BY' => 'Belarus'
			, 'BE' => 'Belgium'
			, 'BZ' => 'Belize'
			, 'BJ' => 'Benin'
			, 'BM' => 'Bermuda'
			, 'BT' => 'Bhutan'
			, 'BO' => 'Bolivia'
			, 'BA' => 'Bosnia and Herzegowina'
			, 'BW' => 'Botswana'
			, 'BV' => 'Bouvet Island'
			, 'BR' => 'Brazil'
			, 'IO' => 'British Indian Ocean Territory'
			, 'BN' => 'Brunei Darussalam'
			, 'BG' => 'Bulgaria'
			, 'BF' => 'Burkina Faso'
			, 'BI' => 'Burundi'
			, 'KH' => 'Cambodia'
			, 'CM' => 'Cameroon'
			, 'CA' => 'Canada'
			, 'CV' => 'Cape Verde'
			, 'KY' => 'Cayman Islands'
			, 'CF' => 'Central African Republic'
			, 'TD' => 'Chad'
			, 'CL' => 'Chile'
			, 'CN' => 'China'
			, 'CX' => 'Christmas Island'
			, 'CC' => 'Cocos (Keeling) Islands'
			, 'CO' => 'Colombia'
			, 'KM' => 'Comoros'
			, 'CG' => 'Congo'
			, 'CD' => 'Congo, the Democratic Republic of the'
			, 'CK' => 'Cook Islands'
			, 'CR' => 'Costa Rica'
			, 'CI' => "Cote d'Ivoire"
			, 'HR' => 'Croatia (Hrvatska)'
			, 'CU' => 'Cuba'
			, 'CY' => 'Cyprus'
			, 'CZ' => 'Czech Republic'
			, 'DK' => 'Denmark'
			, 'DJ' => 'Djibouti'
			, 'DM' => 'Dominica'
			, 'DO' => 'Dominican Republic'
			, 'TP' => 'East Timor'
			, 'EC' => 'Ecuador'
			, 'EG' => 'Egypt'
			, 'SV' => 'El Salvador'
			, 'GQ' => 'Equatorial Guinea'
			, 'ER' => 'Eritrea'
			, 'EE' => 'Estonia'
			, 'ET' => 'Ethiopia'
			, 'FK' => 'Falkland Islands (Malvinas)'
			, 'FO' => 'Faroe Islands'
			, 'FJ' => 'Fiji'
			, 'FI' => 'Finland'
			, 'FR' => 'France'
			, 'FX' => 'France, Metropolitan'
			, 'GF' => 'French Guiana'
			, 'PF' => 'French Polynesia'
			, 'TF' => 'French Southern Territories'
			, 'GA' => 'Gabon'
			, 'GM' => 'Gambia'
			, 'GE' => 'Georgia'
			, 'DE' => 'Germany'
			, 'GH' => 'Ghana'
			, 'GI' => 'Gibraltar'
			, 'GR' => 'Greece'
			, 'GL' => 'Greenland'
			, 'GD' => 'Grenada'
			, 'GP' => 'Guadeloupe'
			, 'GU' => 'Guam'
			, 'GT' => 'Guatemala'
			, 'GN' => 'Guinea'
			, 'GW' => 'Guinea-Bissau'
			, 'GY' => 'Guyana'
			, 'HT' => 'Haiti'
			, 'HM' => 'Heard and Mc Donald Islands'
			, 'VA' => 'Holy See (Vatican City State)'
			, 'HN' => 'Honduras'
			, 'HK' => 'Hong Kong'
			, 'HU' => 'Hungary'
			, 'IS' => 'Iceland'
			, 'IN' => 'India'
			, 'ID' => 'Indonesia'
			, 'IR' => 'Iran (Islamic Republic of)'
			, 'IQ' => 'Iraq'
			, 'IE' => 'Ireland'
			, 'IL' => 'Israel'
			, 'IT' => 'Italy'
			, 'JM' => 'Jamaica'
			, 'JP' => 'Japan'
			, 'JO' => 'Jordan'
			, 'KZ' => 'Kazakhstan'
			, 'KE' => 'Kenya'
			, 'KI' => 'Kiribati'
			, 'KP' => "Korea, Democratic People's Republic of"
			, 'KR' => 'Korea, Republic of'
			, 'KW' => 'Kuwait'
			, 'KG' => 'Kyrgyzstan'
			, 'LA' => "Lao People's Democratic Republic"
			, 'LV' => 'Latvia'
			, 'LB' => 'Lebanon'
			, 'LS' => 'Lesotho'
			, 'LR' => 'Liberia'
			, 'LY' => 'Libyan Arab Jamahiriya'
			, 'LI' => 'Liechtenstein'
			, 'LT' => 'Lithuania'
			, 'LU' => 'Luxembourg'
			, 'MO' => 'Macau'
			, 'MK' => 'Macedonia, The Former Yugoslav Republic of'
			, 'MG' => 'Madagascar'
			, 'MW' => 'Malawi'
			, 'MY' => 'Malaysia'
			, 'MV' => 'Maldives'
			, 'ML' => 'Mali'
			, 'MT' => 'Malta'
			, 'MH' => 'Marshall Islands'
			, 'MQ' => 'Martinique'
			, 'MR' => 'Mauritania'
			, 'MU' => 'Mauritius'
			, 'YT' => 'Mayotte'
			, 'MX' => 'Mexico'
			, 'FM' => 'Micronesia, Federated States of'
			, 'MD' => 'Moldova, Republic of'
			, 'MC' => 'Monaco'
			, 'MN' => 'Mongolia'
			, 'MS' => 'Montserrat'
			, 'MA' => 'Morocco'
			, 'MZ' => 'Mozambique'
			, 'MM' => 'Myanmar'
			, 'NA' => 'Namibia'
			, 'NR' => 'Nauru'
			, 'NP' => 'Nepal'
			, 'NL' => 'Netherlands'
			, 'AN' => 'Netherlands Antilles'
			, 'NC' => 'New Caledonia'
			, 'NZ' => 'New Zealand'
			, 'NI' => 'Nicaragua'
			, 'NE' => 'Niger'
			, 'NG' => 'Nigeria'
			, 'NU' => 'Niue'
			, 'NF' => 'Norfolk Island'
			, 'MP' => 'Northern Mariana Islands'
			, 'NO' => 'Norway'
			, 'OM' => 'Oman'
			, 'PK' => 'Pakistan'
			, 'PW' => 'Palau'
			, 'PA' => 'Panama'
			, 'PG' => 'Papua New Guinea'
			, 'PY' => 'Paraguay'
			, 'PE' => 'Peru'
			, 'PH' => 'Philippines'
			, 'PN' => 'Pitcairn'
			, 'PL' => 'Poland'
			, 'PT' => 'Portugal'
			, 'PR' => 'Puerto Rico'
			, 'QA' => 'Qatar'
			, 'RE' => 'Reunion'
			, 'RO' => 'Romania'
			, 'RU' => 'Russian Federation'
			, 'RW' => 'Rwanda'
			, 'KN' => 'Saint Kitts and Nevis' 
			, 'LC' => 'Saint LUCIA'
			, 'VC' => 'Saint Vincent and the Grenadines'
			, 'WS' => 'Samoa'
			, 'SM' => 'San Marino'
			, 'ST' => 'Sao Tome and Principe' 
			, 'SA' => 'Saudi Arabia'
			, 'SN' => 'Senegal'
			, 'SC' => 'Seychelles'
			, 'SL' => 'Sierra Leone'
			, 'SG' => 'Singapore'
			, 'SK' => 'Slovakia (Slovak Republic)'
			, 'SI' => 'Slovenia'
			, 'SB' => 'Solomon Islands'
			, 'SO' => 'Somalia'
			, 'ZA' => 'South Africa'
			, 'GS' => 'South Georgia and the South Sandwich Islands'
			, 'ES' => 'Spain'
			, 'LK' => 'Sri Lanka'
			, 'SH' => 'St. Helena'
			, 'PM' => 'St. Pierre and Miquelon'
			, 'SD' => 'Sudan'
			, 'SR' => 'Suriname'
			, 'SJ' => 'Svalbard and Jan Mayen Islands'
			, 'SZ' => 'Swaziland'
			, 'SE' => 'Sweden'
			, 'CH' => 'Switzerland'
			, 'SY' => 'Syrian Arab Republic'
			, 'TW' => 'Taiwan, Province of China'
			, 'TJ' => 'Tajikistan'
			, 'TZ' => 'Tanzania, United Republic of'
			, 'TH' => 'Thailand'
			, 'TG' => 'Togo'
			, 'TK' => 'Tokelau'
			, 'TO' => 'Tonga'
			, 'TT' => 'Trinidad and Tobago'
			, 'TN' => 'Tunisia'
			, 'TR' => 'Turkey'
			, 'TM' => 'Turkmenistan'
			, 'TC' => 'Turks and Caicos Islands'
			, 'TV' => 'Tuvalu'
			, 'UG' => 'Uganda'
			, 'UA' => 'Ukraine'
			, 'AE' => 'United Arab Emirates'
			, 'GB' => 'United Kingdom'
			, 'US' => 'United States'
			, 'UM' => 'United States Minor Outlying Islands'
			, 'UY' => 'Uruguay'
			, 'UZ' => 'Uzbekistan'
			, 'VU' => 'Vanuatu'
			, 'VE' => 'Venezuela'
			, 'VN' => 'Viet Nam'
			, 'VG' => 'Virgin Islands (British)'
			, 'VI' => 'Virgin Islands (U.S.)'
			, 'WF' => 'Wallis and Futuna Islands'
			, 'EH' => 'Western Sahara'
			, 'YE' => 'Yemen'
			, 'YU' => 'Yugoslavia'
			, 'ZM' => 'Zambia'
			, 'ZW' => 'Zimbabwe'
		);
		
		if ( $display ) {
			// Initialize variable
			$country_options = '';
			
			foreach ( $countries as $c => $country ) {
				$selected = ( $select_value == $c ) ? ' selected="selected"' : '';
				$country_options .= "<option value='$c'$selected>$country</option>\n";
			}
	
			echo $country_options;
		}

		return $countries;
	}
	
	/**
	 * Returns or displays credit cards
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param string $gateway_list which list to grab the credit cards from
	 * @return array
	 */
	public static function credit_cards( $display = true, $select_value = '', $gateway_list = 'AIM' ) {
		// Authorize.net AIM gateway_list
		$cc['AIM'] = array(
			'Visa' => 'Visa'
			, 'MasterCard' => 'MasterCard'
			, 'Discover' => 'Discover'
			, 'Amex' => 'American Express'
		);
		
		if ( $display ) {
            $cc_options = '';

			foreach ( $cc[$gateway_list] as $k => $v ) {
				$selected = ( $select_value == $k ) ? ' selected="selected"' : '';
				$cc_options .= "<option value='$k'$selected>$v</option>\n";
			}
	
			echo $cc_options;
		}

		return $cc[$gateway_list];
	}
	
	/**
	 * Returns or displays days
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param string $key_format the format of the months ( 'num', 'abbr', 'full' )
	 * @param string $value_format the format of the months ( 'num', 'abbr', 'full' )
	 * @return array
	 */
	public static function days( $display = true, $select_value = '', $key_format = 'abbr', $value_format = 'full' ) {
		$days['num'] = array( '1', '2', '3', '4', '5', '6', '7' );
		$days['abbr'] = array( 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' );
		$days['full'] = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
		
		$a_days = array_combine( $days[$key_format], $days[$value_format] );
		
		if ( $display ) {
            $days_options = '';

			foreach ( $a_days as $k => $v ) {
				$selected = ( $select_value == $k ) ? ' selected="selected"' : '';
				$days_options .= "<option value='$k'$selected>$v</option>\n";
			}
	
			echo $days_options;
		}

        return $a_days;
	}
	
	/**
	 * Returns or displays months
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param string $key_format the format of the months ( 'num', 'abbr', 'full' )
	 * @param string $value_format the format of the months ( 'num', 'abbr', 'full' )
	 * @return array
	 */
	public static function months( $display = true, $select_value = '', $key_format = 'abbr', $value_format = 'full' ) {
		$months['num'] = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
		$months['abbr'] = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
		$months['full'] = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
		
		$a_months = array_combine( $months[$key_format], $months[$value_format] );
		
		if ( $display ) {
            $months_options = '';

			foreach ( $a_months as $k => $v ) {
				$selected = ( $select_value == $k ) ? ' selected="selected"' : '';
				$months_options .= "<option value='$k'$selected>$v</option>\n";
			}
	
			echo $months_options;
		}

		return $a_months;
	}
	
	/**
	 * Returns or displays years
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param int $amount the amount of years from the present
	 * @return array
	 */
	public static function years( $display = true, $select_value = '', $amount = 10 ) {
		global $s98_cache;
		$year = $s98_cache->get( 'year' );
		
		if ( empty( $year ) ) {
			$year = date('Y');
			
			$s98_cache->add( 'year', $year );
		}
		
		$ending_year = $year + $amount;
		
		if ( $display ) {
            $year_options = '';

			for ( $i = $year; $i <= $ending_year; $i++ ) {
				$selected = ( $select_value == $i ) ? ' selected="selected"' : '';
				$year_options .= "<option value='$i'$selected>$i</option>\n";
			}
	
			echo $year_options;
		}

        return range( $year, $ending_year );
	}
	
	/**
	 * Returns or displays timezones
	 *
	 * @param bool $echo [optional]
	 * @param string $select_value [optional]
	 * @return array
	 */
	public static function timezones( $echo = true, $select_value = '' ) {
		$timezones = array(
			'-12.0'		=> '(GMT -12:00) Eniwetok, Kwajalein'
			, '-11.0'	=> '(GMT -11:00) Midway Island, Samoa'
			, '-10.0'	=> '(GMT -10:00) Hawaii'
			, '-9.0'	=> '(GMT -9:00) Alaska'
			, '-8.0'	=> '(GMT -8:00) Pacific Time (US &amp; Canada)'
			, '-7.0'	=> '(GMT -7:00) Mountain Time (US &amp; Canada)'
			, '-6.0'	=> '(GMT -6:00) Central Time (US &amp; Canada), Mexico City'
			, '-5.0'	=> '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima'
			, '-4.0'	=> '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz'
			, '-3.5'	=> '(GMT -3:30) Newfoundland'
			, '-3.0'	=> '(GMT -3:00) Brazil, Buenos Aires, Georgetown'
			, '-2.0'	=> '(GMT -2:00) Mid-Atlantic'
			, '-1.0'	=> '(GMT -1:00 hour) Azores, Cape Verde Islands'
			, '0.0'		=> '(GMT) Western Europe Time, London, Lisbon, Casablanca'
			, '1.0'		=> '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris'
			, '2.0'		=> '(GMT +2:00) Kaliningrad, South Africa'
			, '3.0'		=> '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg'
			, '3.5'		=> '(GMT +3:30) Tehran'
			, '4.0'		=> '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi'
			, '4.5'		=> '(GMT +4:30) Kabul'
			, '5.0'		=> '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent'
			, '5.5'		=> '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi'
			, '5.75'	=> '(GMT +5:45) Kathmandu'
			, '6.0'		=> '(GMT +6:00) Almaty, Dhaka, Colombo'
			, '7.0'		=> '(GMT +7:00) Bangkok, Hanoi, Jakarta'
			, '8.0'		=> '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong'
			, '9.0'		=> '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk'
			, '9.5'		=> '(GMT +9:30) Adelaide, Darwin'
			, '10.0'	=> '(GMT +10:00) Eastern Australia, Guam, Vladivostok'
			, '11.0'	=> '(GMT +11:00) Magadan, Solomon Islands, New Caledonia'
			, '12.0'	=> '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka'
		);
		
		if ( $echo ) {
			// Initialize variable
			$timezone_options = '';
			
			foreach ( $timezones as $tz => $timezone ) {
				$selected = ( $select_value == $tz ) ? ' selected="selected"' : '';
				$timezone_options .= "<option value='$tz'$selected>$timezone</option>\n";
			}
	
			echo $timezone_options;
		}
		
		return $timezones;
	}

    /**
	 * Returns or displays php timezones using geoIP
	 *
	 * @param bool $echo [optional]
	 * @param string $select_value [optional]
	 * @return array
	 */
	public static function php_timezones( $echo = true, $select_value = '' ) {
        $regions = array(
            //'Africa' => DateTimeZone::AFRICA
            'America' => DateTimeZone::AMERICA
            //, 'Antarctica' => DateTimeZone::ANTARCTICA
            //, 'Asia' => DateTimeZone::ASIA
            //, 'Atlantic' => DateTimeZone::ATLANTIC
            //, 'Europe' => DateTimeZone::EUROPE
            //, 'Indian' => DateTimeZone::INDIAN
            //, 'Pacific' => DateTimeZone::PACIFIC
        );

        foreach ( $regions as $name => $mask ) {
            $timezones[$name] = DateTimeZone::listIdentifiers( $mask );
        }

		if ( $echo ) {
			// Initialize variable
			$timezone_options = '';

			foreach ( $timezones as $tz => $timezone ) {
				$selected = ( $select_value == $tz ) ? ' selected="selected"' : '';
				$timezone_options .= "<option value='$tz'$selected>$timezone</option>\n";
			}

			echo $timezone_options;
		}

		return $timezones;
	}
}