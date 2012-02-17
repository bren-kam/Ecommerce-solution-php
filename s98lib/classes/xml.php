<?php
/**
 * XML class -- construct an XML page
 *
 * @package Studio98 Library
 * @since 1.0
 */

class xml extends Base_Class {
    /**
     * Variables
     */
    public $dom;

    /**
     * Construct to setup DOM document
     *
     * @return mixed
     */
    public function __construct() {
        // Create XML document
        $this->dom = new DOMDocument('1.0');

        // Set hte header
        header::type( 'xml' );

        return $this->dom;
    }

    /**
     * Parse an array and create elements
     *
     * @param array $array
     * @param string $element_name
     * @param mixed $root
     * @return mixed
     */
    public function parse( $array, $element_name = NULL, $root = NULL ) {
        if ( NULL == $root )
            $root = $this->dom;

        foreach ( $array as $k => $v ) {
            $e = NULL;
            $use_root = true;

            if ( !is_int( $k ) ) {
                if ( is_array( $v ) && isset( $v[0] ) )
                    $use_root = false;

                $e = ( $use_root ) ? $this->dom->createElement( $k ) : $root;
            } elseif( NULL != $element_name ) {
                $e = $this->dom->createElement( $element_name );
            }

            if ( is_array( $v ) ) {
                $children = $this->parse( $v, $k, $e );

                if ( is_object( $children ) )
                    $e->appendChild( $children );
            } else {
                $e->appendChild( $this->dom->createTextNode( $v ) );
            }

            if ( $use_root )
                $root->appendChild( $e );
        }

        if ( !$root && !is_int( $k ) )
            return $e;
    }

	/**
	 * Saves as XML
	 *
	 * @return string
	 */
	public function output() {
		return $this->dom->saveXML();
	}
}