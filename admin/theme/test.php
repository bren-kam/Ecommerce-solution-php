<?php
/*library('ashley-api/ashley-api');
$a = new Ashley_API();
$packages = $a->get_packages();

exit;

// Load the library
library( 'craigslist-api' );
$b = new Base_Class();
// Create API object
$craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
$markets = $b->db->get_results( 'SELECT `website_id`, `market_id` FROM `craigslist_market_links` WHERE `cl_category_id` = 0', ARRAY_A );

foreach ( $markets as $m ) {
    $cl_categories = $craigslist_api->get_cl_market_categories( $m['market_id'] );

    if ( is_array( $cl_categories ) )
    foreach ( $cl_categories as $clc ) {
        if ( 'furniture' == $clc->name ) {
            $b->db->update( 'craigslist_market_links', array( 'cl_category_id' => $clc->cl_category_id ), array( 'website_id' => $m['website_id'], 'market_id' => $m['market_id'] ), 'i', 'ii' );
            break;
        }
    }
}
*/

// Unique Ads
$ads = array(
	array( 'unique_id' => 1, 'sku' => '1001' )
	, array( 'unique_id' => 3, 'sku' => '1002' )
	, array( 'unique_id' => 2, 'sku' => '1001' )
	, array( 'unique_id' => 4, 'sku' => '1003' )
	, array( 'unique_id' => 5, 'sku' => '1001' )
);

// Hold new ads
fn::info( array_unique_sort( $ads, 'sku' ) );

/**
 * Organize the ads so unique products are not next to each other
 *
 * @param array $array
 * @param string $key [optional]
 * @return array( $unique_array, $duplicate_array )
 */
function array_unique_sort( array $array, $key = NULL ) {
	// Initialize variables
	$last_value = '';
	$identical_elements = $padded_elements = array();

    // Form the first array
	foreach ( $array as $k => $element ) {
        // Get the value that we are comparing
        $value = ( is_null( $key ) ) ? $element : $element[$key];

        // Insert any of elements that have been waiting to get inserted (if there are any)
        if ( 0 != count( $identical_elements ) )
        foreach ( $identical_elements as $ik => $identical_element ) {
            // Form the identical value
            $identical_value = ( is_null( $key ) ) ? $identical_element : $identical_element[$key];

            // If it is identical, check the next item
            if ( $last_value == $identical_value )
                continue;

            // We only get here if they are not matching, so add it onto the next spot in the array
            $padded_elements[] = $identical_element;

            // Set the last value as we are now continuing in the loop
            $last_value = $identical_value;

            // Remove it from our list of identical elements
            unset( $identical_elements[$ik] );
        }

        // Check to see if we need to put the next item in the identical elements list
		if ( $last_value == $value ) {
			$identical_elements[] = $element;
			continue;
		}

        // Form new array "padded" elements
		$padded_elements[] = $element;

        // Set the last value so we know what we did
		$last_value = $value;
	}

    $remaining_element_count = count( $identical_elements );

    if ( 0 != $remaining_element_count ) {
        $last_count = 0;

        while ( $remaining_element_count != $last_count ) {
            $last_count = $remaining_element_count;
            $last_value = '';
            $new_elements = array();

            foreach ( $padded_elements as $padded_element ) {
                $padded_value = ( is_null( $key ) ) ? $padded_element : $padded_element[$key];

                // Insert any of elements that have been waiting to get inserted (if there are any)
                foreach ( $identical_elements as $ik => $identical_element ) {
                    // Form the identical
                    $identical_value = ( is_null( $key ) ) ? $identical_element : $identical_element[$key];

                    if ( empty( $last_value ) ) {
                        if ( $identical_value != $padded_value ) {
                            $new_elements[] = $identical_element;
                            $last_value = $identical_value;
                            unset( $identical_elements[$ik] );
                            continue;
                        } else {
                            break;
                        }
                    }

                    if ( $last_value == $identical_value )
                        continue;

                    $new_elements[] = $identical_element;
                    $last_value = $identical_value;
                    unset( $identical_elements[$ik] );
                }

                // Check to see if we need to put the next item in the identical elements list
                if ( $last_value == $padded_value ) {
                    $identical_elements[] = $padded_element;
                    continue;
                }

                $new_elements[] = $padded_element;
                $last_value = $padded_value;
            }

            $padded_elements = $new_elements;
            $remaining_element_count = count( $identical_elements );
        }
    }

    return array( $padded_elements, $identical_elements );
}