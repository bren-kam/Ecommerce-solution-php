<?php
/**
 * CSS Compressor
 *
 * An attempt to automatically use Google's recommendation for 
 * CSS optimization, which can be seen at the following @url:
 * http://code.google.com/speed/articles/optimizing-css.html
 *
 *
 * To do:
 * 		* Convert things like font-family to font without removing other properties
 *		* Convert things like 'margin:0 0 5px 0' to 'margin-left:5px'
 *		* Convert long hex to colour names
 *		* Allow other IE hacks, over 3 characters long
 *		* Calculate the important of elements and remove unnecessary parts of selectors (# = 100, . = 10, element = 1)
 *
 * @author Kerry Jones
 * @author_url http://www.studio98.com/
 * @home_url http://cleaner.studio98.com/css/
 * @version 1.0
 */
class Compress_CSS {
	/**
	 * Whether to debug something 
	 * @var bool
	 */
	public $debug = FALSE;

	/**
	 * Whether to have perfect output, or take risks
	 * @var bool
	 */
	public $perfect = TRUE;

	/**
	 * Whether to have allow inline hacks
	 * @var bool
	 */
	public $inline_hacks = FALSE;

	/**
	 * The amount of rules
	 * @var int
	 */
	public $rules_count = 0;

	/**
	 * The amount of selectors
	 * @var int
	 */
	public $selectors_count = 0;

	/**
	 * The amount of declarations
	 * @var int
	 */
	public $declarations_count = 0;

	/**
	 * An associative array of all the selectors and their properties
	 * @var int
	 */
	private $selectors = array();
	
	/**
	 * Property orders
	 * @var int
	 */
	private $property_orders = array(
		'background' => array( 'background-color', 'background-image', 'background-repeat', 'background-attachment', 'background-position' ),
		'border' => array( 'border-width', 'border-style', 'border-color' ),
		'font' => array( 'font-style', 'font-variant', 'font-weight', 'font-size', 'line-height', 'font-family' ),
		'list-style' => array( 'list-style-type', 'list-style-position', 'list-style-image' ),
	);
	
	/**
	 * An array of valid properties
	 * @var array
	 */
	private $valid_properties = array ( 'background', 'background-attachment', 'background-color', 'background-image', 'background-position', 'background-repeat', 'border', 'border-bottom', 'border-bottom-color', 'border-bottom-style', 'border-bottom-width', 'border-color', 'border-collapse', 'border-left', 'border-left-color', 'border-left-style', 'border-left-width', 'border-right', 'border-right-color', 'border-right-style', 'border-right-width', 'border-spacing', 'border-style', 'border-top', 'border-top-color', 'border-top-style', 'border-top-width', 'border-width', 'bottom', 'caption-side', 'clear', 'clip', 'color', 'content', 'counter-increment', 'counter-reset', 'cursor', 'direction', 'display', 'empty-cells', 'filter', 'float', 'font', 'font-family', 'font-size', 'font-style', 'font-variant', 'font-weight', 'height', 'left', 'letter-spacing', 'line-height', 'list-style', 'list-style-image', 'list-style-position', 'list-style-type', 'margin', 'margin-bottom', 'margin-left', 'margin-right', 
									   	'margin-top', 'max-height', 'max-width', 'min-height', 'min-width', 'opacity', 'orphans', 'outline', 'outline-color', 'outline-style', 'outline-width', 'overflow', 'padding', 'padding-bottom', 'padding-left', 'padding-right', 'padding-top', 'page-break-after', 'page-break-before', 'page-break-inside', 'position', 'quotes', 'right', 'table-layout', 'text-align', 'text-decoration', 'text-indent', 'text-shadow', 'text-transform', 'top', 'unicode-bidi', 'vertical-align', 'visibility', 'white-space', 'widows', 'width', 'word-spacing', 'z-index');

	/**
	 * IE hack array of properties list
	 * @var array
	 */
	private $ie_hack_properties = array( '_background', '#background', '_background-attachment', '#background-attachment', '_background-color', '#background-color', '_background-image', '#background-image', '_background-position', '#background-position', '_background-repeat', '#background-repeat', '_border', '#border', '_border-bottom', '#border-bottom', '_border-bottom-color', '#border-bottom-color', '_border-bottom-style', '#border-bottom-style', '_border-bottom-width', '#border-bottom-width', '_border-color', '#border-color', '_border-collapse', '#border-collapse', '_border-left', '#border-left', '_border-left-color', '#border-left-color', '_border-left-style', '#border-left-style', '_border-left-width', '#border-left-width', '_border-right', '#border-right', '_border-right-color', '#border-right-color', '_border-right-style', '#border-right-style', '_border-right-width', '#border-right-width', '_border-spacing', '#border-spacing', '_border-style', '#border-style', '_border-top', '#border-top',
										'_border-top-color', '#border-top-color', '_border-top-style', '#border-top-style', '_border-top-width', '#border-top-width', '_border-width', '#border-width', '_bottom', '#bottom', '_caption-side', '#caption-side', '_clear', '#clear', '_clip', '#clip', '_color', '#color', '_content', '#content', '_counter-increment', '#counter-increment', '_counter-reset', '#counter-reset', '_cursor', '#cursor', '_direction', '#direction', '_display', '#display', '_empty-cells', '#empty-cells', '_float', '#float', '_font', '#font', '_font-family', '#font-family', '_font-size', '#font-size', '_font-style', '#font-style', '_font-variant', '#font-variant', '_font-weight', '#font-weight', '_height', '#height', '_left', '#left', '_letter-spacing', '#letter-spacing', '_line-height', '#line-height', '_list-style', '#list-style', '_list-style-image', '#list-style-image', '_list-style-position', '#list-style-position', '_list-style-type', '#list-style-type', '_margin', '#margin', '_margin-bottom', '#margin-bottom', 
										'_margin-left', '#margin-left', '_margin-right', '#margin-right', '_margin-top', '#margin-top', '_max-height', '#max-height', '_max-width', '#max-width', '_min-height', '#min-height', '_min-width', '#min-width', '_orphans', '#orphans', '_outline', '#outline', '_outline-color', '#outline-color', '_outline-style', '#outline-style', '_outline-width', '#outline-width', '_overflow', '#overflow', '_padding', '#padding', '_padding-bottom', '#padding-bottom', '_padding-left', '#padding-left', '_padding-right', '#padding-right', '_padding-top', '#padding-top', '_page-break-after', '#page-break-after', '_page-break-before', '#page-break-before', '_page-break-inside', '#page-break-inside', '_position', '#position', '_quotes', '#quotes', '_right', '#right', '_table-layout', '#table-layout', '_text-align', '#text-align', '_text-decoration', '#text-decoration', '_text-indent', '#text-indent', '_text-shadow', '#text-shadow', '_text-transform', '#text-transform', '_top', '#top', '_unicode-bidi', '#unicode-bidi',
										'_vertical-align', '#vertical-align', '_visibility', '#visibility', '_white-space', '#white-space', '_widows', '#widows', '_width', '#width', '_word-spacing', '#word-spacing', '_z-index', '#z-index ' );
	
	/**
	 * CSS3 properties
	 * @var array
	 */
	private $css3_properties = array( 
		'-moz-border-radius', '-webkit-border-radius', 'border-radius', '-khtml-border-radius', 
		'-moz-border-radius-topleft', '-webkit-border-top-left-radius', 'border-top-left-radius', 
		'-moz-border-radius-topright', '-webkit-border-top-right-radius', 'border-top-right-radius', 
		'-moz-border-radius-bottomleft', '-webkit-border-bottom-left-radius', 'border-bottom-left-radius', 
		'-moz-border-radius-bottomright', '-webkit-border-bottom-right-radius', 'border-bottom-right-radius',
		'-moz-box-shadow', '-webkit-box-shadow', 'box-shadow', '-o-box-shadow',
		'src'
	);

	/**
	 * If you're debugging, it tells you what are invalid properties in an array
	 * @var array( array( property, value ), ... )
	 */
	private $invalid_properties = array();

	/**
	 * Constructor
	 *
	 * @param string $contents the contents of the CSS file
	 * @returns string
	 */
	public function __construct( $contents, $perfect = true, $inline_hacks = false, $type = '' ) {
		$this->perfect = $perfect;
		$this->inline_hacks = $inline_hacks;

		// Allow inline hacks
		if ( $this->inline_hacks )
			$this->valid_properties = array_merge( $this->valid_properties, $this->ie_hack_properties, $this->css3_properties );
		
		$this->css = trim( str_replace( '[colon]', ':', $this->parse( trim( $this->compress( $contents ) ) ) ) );
		
		$this->original_length = mb_strlen( $contents );
		$this->new_length = mb_strlen( $this->css );
		
		$this->reduction = round( 100 - ( $this->new_length / $this->original_length ) * 100, 2 ) . '%';
		
		$this->original_length = number_format( $this->original_length );
		$this->new_length = number_format( $this->new_length );
	}
	
	/**
	 * CSS Compression
	 *
	 * @param string $contents the css contents
	 * @param string $type the type of compression being used
	 */
	private function compress( $contents, $type = 'in-house' ) {
		switch ( $type ) {
			case 'in-house':
			default: {
				// Remove comments
				$output = preg_replace('#/\*.*?\*/#s', '', $contents);
				// Remove whitespace
				$output = preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
				// Remove trailing whitespace at the start
				$output = preg_replace('/\s\s+(.*)/', '$1', $output);
				// Change 0px|em|% to 0
				$output = preg_replace('/([^0-9]0)(?:px|em|%)/', '$1', $output);
				// Change 6 digit color values to 3 digit (#FFFFFF to #FFF or #FFCC33 to #FC3)
				$output = preg_replace('/#([0-9a-f])\\1([0-9a-f])\\2([0-9a-f])\\3/i', '#$1$2$3', $output);
				// Remove unnecessary ;'s -- This will get handled in the parsing
				//$output = str_replace(';}', '}', $output); 
				
				// Turn some semi-colons into [colon]
				$output = preg_replace( '/\(([a-z\'"]+):/', '($1[colon]', $output );

                echo $output;exit;
				
				return $output;
			}
		}
	}
	
	/**
	 * Parses the CSS content and optimizes it
	 *
	 * @param string $contents the CSS contents 
	 * @returns string
	 */
	private function parse( $contents ) {
		$rules_pattern = '/([^{]+){([^{]+)}/'; // Matches a single CSS line/grouping
		$css_array = array();
		
		preg_match_all( $rules_pattern, $contents, $rules, PREG_SET_ORDER );

		foreach ( $rules as $r ) {
			// Grab all the selectors and declarations
			$selectors = explode( ',', $r[1] );
			$declarations = explode( ';', $r[2] );
			
			$declaration_key = 0;
			// Go through
			while( $declaration_key < count( $declarations ) ) {
				$d = $declarations[$declaration_key];
				$declaration_key++;
				
				// Get the property and value
				$d_array = explode( ':', $d );
				
				$property = $d_array[0];
				
				if ( isset( $d_array[1] ) )
					$value = $d_array[1];
				
				$compressed_declarations = $this->compress_values( strtolower( $property ), $value );
				
				// In case it creates more declarations (which will later get minimized)
				if ( 2 == count( $compressed_declarations ) ) {
					list( $property, $value ) = $compressed_declarations;
				} else {
					foreach ( $compressed_declarations as $cd ) {
						$declarations[] = $cd[0] . ':' . $cd[1];
					}
					continue;
				}
				
				// Make sure it's not empty
				if ( !empty( $property ) || !empty( $value ) ) {
					// The key is what's going into the css_array
					$key = $property . '|' . $value;
					
					foreach ( $selectors as $s ) {
						// Remove double ids
						if ( !$this->perfect && 1 == preg_match( '/([^ ]*#?[^#]+)$/', $s, $matches ) ) 
							$s = $matches[1];
						
						// Adds it to the array of css
						if ( !isset( $css_array[$key] ) || !in_array( $s, $css_array[$key] ) ) {
							// Makes sure there are no duplicate properties for selectors
							if ( isset( $this->selectors[$s] ) ) {
								// Find out if it's being duplicated
								$selector_bad_key = array_search( $property, $this->selectors[$s] );
								
								// Remove the duplicate
								if ( false != $selector_bad_key && is_array( $css_array[$selector_bad_key] ) ) {
									$bad_key = array_search( $s, $css_array[$selector_bad_key] );
									
									unset( $css_array[$selector_bad_key][$bad_key], $this->selectors[$s][$selector_bad_key] );
									
									if ( 0 == count( $css_array[$selector_bad_key] ) )
										unset( $css_array[$selector_bad_key] );

									if ( 0 == count( $this->selectors[$s] ) )
										unset( $this->selectors[$s] );
								}
							}
							
							$css_array[$key][] = $s;
						}
						
						// Adds it to the selectors list
						$this->selectors[$s][$key] = $property;
					}
				}
			}
		}
		
		// Start building next array (selector_array)
		foreach ( $css_array as $declaration => $selectors ) {
			// Break them up
			list( $property, $value ) = explode( '|', $declaration );
			$new_declaration = $property . ':' . $value;
			
			$selector_key = implode( ',', $selectors );
			
			if ( !isset( $selector_array[$selector_key] ) || !in_array( $new_declaration, $selector_array[$selector_key] ) )
				$selector_array[$selector_key][] = $new_declaration;
		}
		
		//print_r( $selector_array );
		// Minimize properties
		foreach ( $selector_array as $selector_key => $declaration_array ) {
			$selector_array[$selector_key] = $this->minimize_properties( $declaration_array );
		}
		
		// Declare variable
		$css_string = '';
		
		// Now start building the string
		foreach ( $selector_array as $selectors => $declarations ) {
			$css_string .= $selectors . '{' . implode( ';', $declarations ) . "}";
			
			// If we're debugging
			if ( $this->debug )
				$css_string .= "\n";
			
			// Calculate stats
			$this->rules_count++;
			$this->selectors_count += count( explode( ',', $selectors ) );
			$this->declarations_count += count( $declarations );
		}
		
		// Replace all ~ with spaces if not perfect (used as a work around)
		if ( !$this->perfect )
			$css_string = str_replace( '~', ' ', $css_string );
		
		return $css_string;
	}
	
	/**
	 * Compresses Values
	 *
	 * @param string $property the CSS property
	 * @param string $value the CSS property value 
	 * @returns array( $property, $value )
	 */
	private function compress_values( $property, $value ) {
		if ( in_array( $property, $this->valid_properties ) ) {
			// It's a valid property
			switch ( $property ) {
				case 'background':
				case 'background-position':
					$new_value = str_replace( array( 
							'top left', 'top center', 'top right',
							'center left', 'center center', 'center right',
							'bottom left', 'bottom center', 'bottom right'
						), array( 
							'0 0', '50% 0', '100% 0',
							'0 50%', '50% 50%', '100% 50%',
							'0 100%', '50% 100%', '100% 100%'
						), $value ); 

					$new_value = str_replace( array( ' top', ' left', ' center', ' right', ' bottom' ), array( ' 0', ' 0', ' 50%', ' 100%', ' 100%' ), $new_value ); 
				break;

				/* Seems to make it longer
				case 'border':
					return ( 1 == preg_match( '/([0-9]+(?:em|px|%))(?: ([a-z]+))?(?: ([a-z]+|#[0-9a-f]{3,6}))/', $value, $border_values ) ) ? array( array( 'border-width', $border_values[1] ),	array( 'border-style', $border_values[2] ),	array( 'border-color', $border_values[3] ) ) : array( 'border', $value );
				break;
				*/
				
				case 'height':
					if ( !$this->perfect && 'auto' == $value ) {
						unset( $property, $value );
					} else {
						$new_value = $value;
					}
					
				break;
				
				case 'border':
				case 'margin':
				case 'padding':
					$new_value = preg_replace( '/^([^\s]+)\s([^\s]+)\s([^\s]+)\s\\2$/', '$1 $2 $3', $value ); // Make from 4 to 3
					$new_value = preg_replace( '/^([^\s]+)\s([^\s]+)\s\\1$/', '$1 $2', $new_value ); // Make from 3 to 2
					$new_value = preg_replace( '/^([^\s]+)\s\\1$/', '$1', $new_value ); // Make from 2 to 1
				break;
				
				case 'font-weight':
					switch ( $value ) {
						case 'normal':
							$new_value = 400;
							break;
						
						case 'bold':
							$new_value = 700;
							break;
						
						default:
							$new_value = $value;
							break;
					}
				break;
				
				default:
					$new_value = $value;
				break;
			}
		} else {
			if ( DEBUG )
				$this->invalid_properties[] = array( $property, $value );
			
			// It's an invalid property, get rid of it
			$property = $value = $new_value = '';
		}
		
		
		return array( $property, $new_value );
	}
	
	/**
	 * Compresses Declarations
	 *
	 * @param array $declarations the declarations of the selectors
	 * @returns string $declaration
	 */
	private function minimize_properties( $declarations ) {
		foreach ( $declarations as $d ) {
			list( $property, $value ) = explode( ':', $d );
			
			switch ( $property ) {
				/*
				case 'background-color':
				case 'background-image':
				case 'background-repeat':
				case 'background-attachment':
				case 'background-position':
					// Add it to the array
					$new_declarations['background'][array_search( $property, $this->property_orders['background'] )] = $value;
				break;
				
				case 'border-color':
				case 'border-style':
				case 'border-width':
					// Add it to the array
					$new_declarations['border'][array_search( $property, $this->property_orders['border'] )] = $value;
				break;
				/* Font size and family have to be together to be combined to 'font'
				case 'font-size': 
				case 'line-height':
				case 'font-family':
				case 'font-weight':
					if ( 0 != stristr( $value, ' ' ) )
						$value = '"' . $value . '"';
					
					// Add it to the array
					$new_declarations['font'][array_search( $property, $this->property_orders['font'] )] = $value;
				break;*/
				
				case 'list-style-type':
					// Add it to the array
					$new_declarations['list-style'][array_search( $property, $this->property_orders['list-style'] )] = $value;
				break;
				
				default:
					$new_declarations[$property][] = $value;
				break;
			}
		}
		
		unset( $declarations );
		
		foreach ( $new_declarations as $property => $d ) {
			$declarations[] = $property . ':' . implode( ' ', $d );
		}
		
		return $declarations;
	}
	
}
?>