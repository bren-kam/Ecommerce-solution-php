<?php
/**
 * PHP Unit Test for date-time class
 *
 * @package Studio98 Library
 * @since 1.0
 */

spl_autoload_register( function ( $class_name ) {
    s98lib_classes( $class_name );
});

class formatTest extends PHPUnit_Framework_TestCase {
	/**
     * Check to see if stripslashes_deep works (stripslashes on arrays)
     */
	public function testStripslashesDeep() {
		// Create the base array
        $array = array(
            'name' => addslashes( "Studio98's Test" )
            , 'options' => array(
                addslashes( "Joe's Slashes")
                , addslashes( "Mary's Slashes" )
            )
        );

        // Strip slashes from all the elements
        $new_array = format::stripslashes_deep( $array );

        // Create the array as it should be
        $proper_array = array(
            'name' => "Studio98's Test"
            , 'options' => array(
                "Joe's Slashes"
                , "Mary's Slashes"
            )
        );

        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Check to see if htmlspecialchars_deep works (htmlspecialchars on arrays)
     */
	public function testHTMLSpecialCharsDeep() {
		// Create the base array
        $array = array(
            'name' => "First & Dimensional"
            , 'options' => array(
                "Second & Dimensional"
                , "Second & Dimensional Too"
            )
        );

        // HTML Special chars for all the elements
        $new_array = format::htmlspecialchars_deep( $array );

        // Create the array as it should be
        $proper_array = array(
            'name' => htmlspecialchars( "First & Dimensional" )
            , 'options' => array(
                htmlspecialchars( "Second & Dimensional" )
                , htmlspecialchars( "Second & Dimensional Too" )
            )
        );

        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Check to see if urlencode_deep works (urlencode on arrays)
     */
	public function testURLEncodeDeep() {
		// Create the base array
        $array = array(
            'name' => "First Dimensional"
            , 'options' => array(
                "Second Dimensional"
                , "Second Dimensional Too"
            )
        );

        // URL encode all the elements
        $new_array = format::urlencode_deep( $array );

        // Create the array as it should be
        $proper_array = array(
            'name' => urlencode( "First Dimensional" )
            , 'options' => array(
                urlencode( "Second Dimensional" )
                , urlencode( "Second Dimensional Too" )
            )
        );

        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Check to see if trim_deep works (trim_deep on arrays)
     */
	public function testTrimDeep() {
		// Create the base array
        $array = array(
            'name' => "First Dimensional "
            , 'options' => array(
                " Second Dimensional "
                , " Second Dimensional Too"
            )
        );

        // Trim all the elements
        $new_array = format::trim_deep( $array );

        // Create the array as it should be
        $proper_array = array(
            'name' => trim( "First Dimensional " )
            , 'options' => array(
                trim( " Second Dimensional " )
                , trim( " Second Dimensional Too" )
            )
        );

        $this->assertEquals( $new_array, $proper_array );
	}

    /**
     * Tests HTMLEntities function, but allows you to not affect cerain tags
     */
    public function testHTMLEntities() {
        $string = "<h1>Lorem ipsum dolor sit amet & consectetur adipiscing elit.</h1>";

        // htmlentities everything but the ampersand
        $new_string = format::htmlentities( $string, array('&') );

        // Define the proper string
        $proper_string = "&lt;h1&gt;Lorem ipsum dolor sit amet & consectetur adipiscing elit.&lt;/h1&gt;";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Check the limit_words function
     */
    public function testLimitWords() {
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta ultrices convallis. Pellentesque semper velit vel erat auctor suscipit. Aliquam sagittis mauris sed leo accumsan sodales. Duis nec dignissim ligula. Nunc tristique venenatis luctus. Suspendisse ligula sapien, porttitor quis vestibulum sed, tristique ornare tellus. Cras quis vulputate dolor.";

        // We only want the first 10 words
        $new_string = format::limit_words( $string, 10 );

        // Define the proper string
        $proper_string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta...";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Check the limit_chars function - Preserve Words = TRUE
     */
    public function testLimitCharsA() {
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta ultrices convallis. Pellentesque semper velit vel erat auctor suscipit. Aliquam sagittis mauris sed leo accumsan sodales. Duis nec dignissim ligula. Nunc tristique venenatis luctus. Suspendisse ligula sapien, porttitor quis vestibulum sed, tristique ornare tellus. Cras quis vulputate dolor.";

        // We only want the first 100 characters -- we ARE preserving words
        $new_string = format::limit_chars( $string );

        // Define the proper string
        $proper_string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta ultrices convallis. Pellentesque...";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Check the limit_chars function - Preserve Words = FALSE
     */
    public function testLimitCharsB() {
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta ultrices convallis. Pellentesque semper velit vel erat auctor suscipit. Aliquam sagittis mauris sed leo accumsan sodales. Duis nec dignissim ligula. Nunc tristique venenatis luctus. Suspendisse ligula sapien, porttitor quis vestibulum sed, tristique ornare tellus. Cras quis vulputate dolor.";

        // We only want the first 100 characters -- we ARE NOT preserving words
        $new_string = format::limit_chars( $string, 100, NULL, false );

        // Define the proper string
        $proper_string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus porta ultrices convallis. Pellentes...";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests turning a string into nothing but entities
     */
    public function testStringToEntity() {
        $string = "<h1>Hello World!</h1>";

        // Turn a string into an html entity for every character
        $new_string = format::string_to_entity( $string );

        // Define the proper string
        $proper_string = "&#60;&#104;&#49;&#62;&#72;&#101;&#108;&#108;&#111;&#32;&#87;&#111;&#114;&#108;&#100;&#33;&#60;&#47;&#104;&#49;&#62;";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Test Preserving New Lines
     */
    public function testPreserveNewLines() {
        $string = "Hello World!\n\nHow are you doing today?\n";

        // Preserve new lines
        $new_string = format::preserve_new_lines( array( $string ) );

        // Define the proper string
        $proper_string = "Hello World!<PreserveNewline /><PreserveNewline />How are you doing today?<PreserveNewline />";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests making a paragraph out of line breaks
     *
     * @depends testPreserveNewLines
     */
    public function testAutoP() {
        $string = "Hello World!\n\nHow are you doing today?\n\nThe sun is up, the sky is blue\n";

        // Auto Paragraph
        $new_string = format::autop( $string );

        // Define the proper string
        $proper_string = "<p>Hello World!</p>\n<p>How are you doing today?</p>\n<p>The sun is up, the sky is blue</p>\n";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests making line breaks out of paragraphs
     */
    public function testUnAutoP() {
        $string = "<p>Hello World!</p><p>How are you doing today?</p><p>The sun is up, the sky is blue</p>";

        // Un Auto-Paragraph
        $new_string = format::unautop( $string );

        // Define the proper string
        $proper_string = "Hello World!\nHow are you doing today?\nThe sun is up, the sky is blue";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Makes sure we can strip only tags
     */
    public function testStripOnlyA() {
        $string = "<h1>Hello World!</h1><p>How are you doing today?</p><p>The sun is up, the sky is blue</p>";

        // Strip only the h1 tag -- but leave content
        $new_string = format::strip_only( $string, 'h1' );

        // Define the proper string
        $proper_string = "Hello World!<p>How are you doing today?</p><p>The sun is up, the sky is blue</p>";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests make line breaks out of paragaphs
     */
    public function testStripOnlyB() {
        $string = "<h1>Hello World!</h1><p>How are you doing today?</p><p>The sun is up, the sky is blue</p>";

        // Strip only the h1 tag -- but take content
        $new_string = format::strip_only( $string, 'h1', true );

        // Define the proper string
        $proper_string = "<p>How are you doing today?</p><p>The sun is up, the sky is blue</p>";

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests turning a link in a body of text into an anchor
     */
    public function testLinksToAnchors() {
        $string = "Hello and welcome to www.studio98.com!";

        // Turn www.studio98.com to a link
        $new_string = format::links_to_anchors( $string );

        // Define the proper string
        $proper_string = 'Hello and welcome to <a href="http://www.studio98.com" title="www.studio98.com">www.studio98.com</a>!';

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests text into a slug
     */
    public function testSlug() {
        $string = "Joe Smith's Library";

        // Turn www.studio98.com to a link
        $new_string = format::slug( $string );

        // Define the proper string
        $proper_string = 'joe-smiths-library';

        $this->assertEquals( $new_string, $proper_string );
    }

    /**
     * Tests turning a slug into a name
     */
    public function testSlugToName() {
        $string = "joe-smiths-library";

        // Turn www.studio98.com to a link
        $new_string = format::slug_to_name( $string );

        // Define the proper string
        $proper_string = 'Joe Smiths Library';

        $this->assertEquals( $new_string, $proper_string );
    }
}