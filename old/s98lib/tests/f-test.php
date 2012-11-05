<?php
/**
 * PHP Unit Test for f (file) class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class fTest extends PHPUnit_Framework_TestCase {
    /**
     * Test Reading a file
     */
	public function testRead() {
        // Create temp file
        $temp_file = tempnam( "/tmp", "foo" );

        // Write to it
        $handle = fopen( $temp_file, "w" );
        fwrite( $handle, "Hello World!" );
        fclose( $handle );

        // Read it
        $file_content = f::read( $temp_file );

        $this->assertEquals( $file_content, "Hello World!" );
	}

    /**
     * Test Reading a directory
     */
    public function testReadDir() {
        // Get the files
        $files = f::read_dir( "." );

        // Only thing we can guarantee is . and ..
        $this->assertTrue( is_array( $files ) );
    }

    /**
     * Test Getting a file name
     */
    public function testName() {
        // Define path
        $path = '/home/tefdfd/public_html/includes/downloads/test.pdf';

        // Get name
        $file_name = f::name( $path );

        $this->assertEquals( 'test.pdf', $file_name );
    }

    /**
     * Test Getting a file extension
     */
    public function testExtension() {
        // Define path
        $path = '/home/tefdfd/public_html/includes/downloads/test.pdf';

        // Get extension
        $extension = f::extension( $path );

        $this->assertEquals( 'pdf', $extension );
    }

    /**
     * Test stripping an extension from a file name
     *
     * @depends testExtension
     */
    public function testStripExtension() {
        // Define filename
        $file_name = 'test.pdf';

        // File without extension
        $new_file_name = f::strip_extension( $file_name );

        $this->assertEquals( 'test', $new_file_name );
    }
}