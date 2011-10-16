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

if( !is_object( $s98_cache ) ) {
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
	 * @return nothing|array
	 */
	public function states( $display = true, $select_value = '' ) {
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
		
		
		if( $display ) {
			foreach($states as $st => $state) {
				$selected = ( $select_value == $st ) ? ' selected="selected"' : '';
				$state_options .= "<option value='$st'$selected>$state</option>\n";
			}
	
			echo $state_options;
		} else {
			return $states;
		}
	}
	
	/**
	 * Returns or displays credit cards
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param string $gateway_list which list to grab the credit cards from
	 * @return nothing|array
	 */
	public function credit_cards( $display = true, $select_value = '', $gateway_list = 'AIM' ) {
		// Authorize.net AIM gateway_list
		$cc['AIM'] = array(
			'Visa' => 'Visa'
			, 'MasterCard' => 'MasterCard'
			, 'Discover' => 'Discover'
			, 'Amex' => 'American Express'
		);
		
		if( $display ) {
			foreach( $cc[$gateway_list] as $k => $v ) {
				$selected = ( $select_value == $k ) ? ' selected="selected"' : '';
				$cc_options .= "<option value='$k'$selected>$v</option>\n";
			}
	
			echo $cc_options;
		} else {
			return $cc[$gateway_list];
		}
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
	 * @return nothing|array
	 */
	public function months( $display = true, $select_value = '', $key_format = 'abbr', $value_format = 'full' ) {
		$months['num'] = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
		$months['abbr'] = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
		$months['full'] = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
		
		$a_months = array_combine( $months[$key_format], $months[$value_format] );
		
		if( $display ) {
			foreach( $a_months as $k => $v ) {
				$selected = ( $select_value == $k ) ? ' selected="selected"' : '';
				$months_options .= "<option value='$k'$selected>$v</option>\n";
			}
	
			echo $months_options;
		} else {
			return $a_months;
		}
	}
	
	/**
	 * Returns or displays years
	 * 
	 * @since 1.0
	 *
	 * @param bool $display whether or not to return or echo the values
	 * @param string $select_value if value matches key in state array, will select that option
	 * @param int $amount the amount of years from the present
	 * @return nothing|array
	 */
	public function years( $display = true, $select_value = '', $amount = 10 ) {
		global $s98_cache;
		$year = $s98_cache->get( 'year' );
		
		if( empty( $year ) ) {
			$year = date('Y');
			
			$s98_cache->add( 'year', $year );
		}
		
		$ending_year = $year + $amount;
		
		if( $display ) {
			for( $i = $year; $i <= $ending_year; $i++ ) {
				$selected = ( $select_value == $i ) ? ' selected="selected"' : '';
				$year_options .= "<option value='$i'$selected>$i</option>\n";
			}
	
			echo $year_options;
		} else {
			return range( $year, $ending_year );
		}
	}
}