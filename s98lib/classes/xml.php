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

    /**
	 * XML Encode - Encodes array into XML
	 *
	 * XML Encode is json_encode counterpart, does the same thing for xml.
	 *
	 * @param mixed $mixed
	 * @param object $domElement (optional) the dom element object
	 * @param object $DOMDocument (optional) the dom document object
	 * @return string the XML
	 */
	public static function encode( $mixed, $domElement = NULL, $DOMDocument = NULL ){
		if ( is_null( $DOMDocument ) ) {
			$DOMDocument = new DOMDocument;
			$DOMDocument->formatOutput = true;
			self::encode( array( 'xml' => $mixed ), $DOMDocument, $DOMDocument );
			header('Content-type: text/xml');
			echo $DOMDocument->saveXML();
		} else {
			if ( is_array( $mixed ) ) {
				foreach ( $mixed as $index => $mixedElement ) {
					if ( is_int( $index ) ) {
						if ( 0 == $index ) {
							$node = $domElement;
						} else {
							$node = $DOMDocument->createElement( $domElement->tagName );
							$domElement->parentNode->appendChild( $node );
						}
					} else {
						$plural = $DOMDocument->createElement( $index );
						$domElement->appendChild( $plural );
						$node = $plural;

						if ( rtrim( $index, 's' ) !== $index ) {
							$singular = $DOMDocument->createElement( rtrim( $index, 's' ) );
							$plural->appendChild( $singular );
							$node = $singular;
						}
					}
					self::xml_encode( $mixedElement, $node, $DOMDocument );
				}
			} else{
				$domElement->appendChild( $DOMDocument->createTextNode( $mixed ) );
			}
		}
	}
}