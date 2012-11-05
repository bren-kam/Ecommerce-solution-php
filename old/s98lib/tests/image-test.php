<?php
/**
 * PHP Unit Test for image class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class imageTest extends PHPUnit_Framework_TestCase {
	/**
     * Make sure the image is within proportions
     */
	public function testAssignKeyA() {
        // Define constraints
        $original_width = 1343;
        $original_height = 1879;
        $width_constraint = 800;
        $height_constraint = 600;

        // Get new image proprtions
        list( $width, $height ) = image::proportions( $original_width, $original_height, $width_constraint, $height_constraint );

        // The ratio should be the same
        $this->assertTrue( $width <= $original_width && $height <= $original_height );
    }

    /**
     * Make sure it calculates the right proportions
     */
	public function testAssignKeyB() {
        // Define constraints
        $original_width = 1343;
        $original_height = 1879;
        $width_constraint = 800;
        $height_constraint = 600;

        // Get new image proprtions
        list( $width, $height ) = image::proportions( $original_width, $original_height, $width_constraint, $height_constraint );

        // The ratio should be the same
        $this->assertEquals( ceil( $width / $height ), ceil( $original_width / $original_height ) );
    }
}