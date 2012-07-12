<?php
/**
 * PHP Unit Test for ar (array) class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class arTest extends PHPUnit_Framework_TestCase {
	/**
     * Make sure we can assign keys properly
     */
	public function testAssignKeyA() {
        // Define standard array
		$array = array(
            array(
                'color' => 'blue'
                , 'fruit' => 'blueberry'
            )
            , array(
                'color' => 'orange'
                , 'fruit' => 'orange'
            )
            , array(
                'color' => 'green'
                , 'fruit' => 'apple'
            )
        );

        // Assign the key to the array
        $new_array = ar::assign_key( $array, 'color' );

        // Create the array it should look like
        $proper_array = array(
            'blue' => array(
                'color' => 'blue'
                , 'fruit' => 'blueberry'
            )
            , 'orange' => array(
                'color' => 'orange'
                , 'fruit' => 'orange'
            )
            , 'green' => array(
                'color' => 'green'
                , 'fruit' => 'apple'
            )
        );

        // Make sure they're equal
        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Make sure we can assign keys properly and remove the key
     */
	public function testAssignKeyB() {
		// Define standard array
		$array = array(
            array(
                'color' => 'blue'
                , 'fruit' => 'blueberry'
            )
            , array(
                'color' => 'orange'
                , 'fruit' => 'orange'
            )
            , array(
                'color' => 'green'
                , 'fruit' => 'apple'
            )
        );

        // Assign the key to the array Remove the key from the original
        $new_array = ar::assign_key( $array, 'color', true );

        // Create the array it should look like
        $proper_array = array(
            'blue' => 'blueberry'
            , 'orange' => 'orange'
            , 'green' => 'apple'
        );

        // Make sure they're equal
        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Make sure we can assign keys properly and remove the key
     */
	public function testAssignKeyC() {
		// Define standard array
		$array = array(
            array(
                'color' => 'blue'
                , 'fruit' => 'blueberry'
                , 'environment' => 'sky'
            )
            , array(
                'color' => 'orange'
                , 'fruit' => 'orange'
                , 'environment' => 'fire'
            )
            , array(
                'color' => 'green'
                , 'fruit' => 'apple'
                , 'environment' => 'grass'
            )
        );

        // Assign the key to the array Remove the key from the original
        $new_array = ar::assign_key( $array, 'color', true );

        // Create the array it should look like
        $proper_array = array(
            'blue' => array(
                'fruit' => 'blueberry'
                , 'environment' => 'sky'
            )
            , 'orange' => array(
                'fruit' => 'orange'
                , 'environment' => 'fire'
            )
            , 'green' => array(
                'fruit' => 'apple'
                , 'environment' => 'grass'
            )
        );

        // Make sure they're equal
        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Test comparing arrays
     */
    public function testCompare() {
        // Create the two arrays for comparison
        $array = array(
            'color' => 'blue'
            , 'fruit' => 'blueberry'
            , 'environment' => 'sky'
        );

        $other_array = array(
            'color' => 'green'
            , 'fruit' => 'blueberry'
            , 'environment' => 'sky'
        );

        // Compare the 2 arrays
        $result_array = ar::compare( $array, $other_array );

        // Should show the items that are differentiating of each array
        $proper_array = array (
            array( 'color' => 'blue' )
            , array( 'color' => 'green' )
        );

        $this->assertEquals( $result_array, $proper_array );
    }

    /**
     * Tests
     */
    public function testCompareDate() {
        // Create base array
        $array = array(
            array(
                'color' => 'blue'
                , 'started' => '2012-07-03'
                , 'environment' => 'sky'
            )
            , array(
                'color' => 'green'
                , 'started' => '2012-07-01'
                , 'environment' => 'grass'
            )
            , array(
                'color' => 'orange'
                , 'started' => '2012-07-02'
                , 'environment' => 'fire'
            )
        );

        // Sorted array by date
        $sorted_array = ar::sort_by_date( $array, 'started' );

        // Define what the array should be
        $correct_array = array(
            array(
                'color' => 'blue'
                , 'started' => '2012-07-03'
                , 'environment' => 'sky'
            )
            , array(
                'color' => 'orange'
                , 'started' => '2012-07-02'
                , 'environment' => 'fire'
            )
            , array(
                'color' => 'green'
                , 'started' => '2012-07-01'
                , 'environment' => 'grass'
            )
        );

        $this->assertEquals( $sorted_array, $correct_array );
    }
}