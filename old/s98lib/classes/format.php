<?php
/**
 * Format class - formats variables
 *
 * Functions:
 * string|array stripslashes_deep( string|array $value ) - strips slashes
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class format extends Base_Class {
	/**
	 * Navigates through an array and removes slashes from the values.
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * @param array|string $value The array or string to be stripped
	 * @return array|string Stripped array (or string in the callback).
	 */
	public static function stripslashes_deep( $value ) {
		return is_array( $value ) ? array_map( array( 'self', 'stripslashes_deep' ), $value ) : stripslashes( $value );
	}

    /**
	 * Navigates through an array and applies html special chars to the values.
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * @param array|string $value The array or string to be stripped
	 * @return array|string Stripped array (or string in the callback).
	 */
	public static function htmlspecialchars_deep( $value ) {
		return is_array( $value ) ? array_map( array( 'self', 'htmlspecialchars_deep' ), $value ) : htmlspecialchars( $value );
	}

	/**
	 * Navigates through an array and encodes the values to be used in a URL.
	 *
	 * Uses a callback to pass the value of the array back to the function as a
	 * string.
	 *
	 * @param array|string $value The array or string to be encoded.
	 * @return array|string $value The encoded array (or string from the callback).
	 */
	public static function urlencode_deep( $value ) {
		return is_array($value) ? array_map( array( 'self', 'urlencode_deep' ), $value ) : urlencode( $value );
	}

    /**
	 * Navigates through an array and removes spaces
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * @param array|string $value The array or string to be stripped
	 * @return array|string Stripped array (or string in the callback).
	 */
	public static function trim_deep( $value ) {
		return is_array( $value ) ? array_map( array('self', 'trim_deep'), $value ) : trim( $value );
	}

    /**
     * Does an HTML entities but allows you to remove items
     *
     * @param string $string
     * @param array $tags = array()
     * @return string
     */
    public static function htmlentities( $string, $tags = array() ) {
        if ( !is_array( $tags ) || 0 == count( $tags ) )
            return htmlentities( $string );

        // Essentially a custom html entities
        $html_entities = get_html_translation_table( HTML_ENTITIES );

        foreach ( $tags as $t ) {
            unset( $html_entities[$t] );
        }

        return str_replace( array_keys( $html_entities ), array_values( $html_entities ), $string );
    }

    /**
     * Convert Characters to what we want them to be
     *
     * @param string
     * @return string
     */
    public static function convert_characters( $string ) {
        $conversion = array(
            '“' => '"'
            , '”' => '"'
            , 'é' => '&eacute;'
            , '®' => '&reg;'
            , '™' => '&trade;'
            , '’' => "'"
            , '–' => '-'
        );

        return str_replace( array_keys( $conversion ), array_values( $conversion ), $string );
    }

	/**
	 * Limits a phrase to a given number of words.
	 *
	 * @param string $str phrase to limit words of
	 * @param integer $limit (optional) number of words to limit to
	 * @param string $end_char (optional) end character or entity
	 * @return string
	 */
	public static function limit_words( $str, $limit = 100, $end_char = NULL ) {
		$limit = (int) $limit;
		$end_char = ( NULL === $end_char ) ? '...' : $end_char;

		if ( '' === trim( $str ) )
			return $str;

		if ( $limit <= 0 )
			return $end_char;

		preg_match( '/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $str, $matches );

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim( $matches[0] ) . ( strlen( $matches[0] ) === strlen( $str ) ? '' : $end_char );
	}

	/**
	 * Limits a phrase to a given number of characters.
	 *
	 * @param string $str phrase to limit characters of
	 * @param integer $limit (optional) number of characters to limit to
	 * @param string $end_char (optional) end character or entity
	 * @param boolean $preserve_words (optional) enable or disable the preservation of words while limiting
	 * @return string
	 */
	public static function limit_chars( $str, $limit = 100, $end_char = NULL, $preserve_words = TRUE ) {
		$end_char = ( NULL === $end_char ) ? '...' : $end_char;

		$limit = (int) $limit;

		if ( '' === trim($str) || mb_strlen( $str ) <= $limit )
			return $str;

		if ( $limit <= 0 )
			return $end_char;

		if ( $preserve_words == FALSE ) {
			return rtrim( mb_substr( $str, 0, $limit ) ) . $end_char;
		}

		preg_match( '/^.{' . ( $limit - 1 ) . '}\S*/us', $str, $matches );

		return rtrim( $matches[0] ) . ( strlen( $matches[0] ) == strlen( $str ) ? '' : $end_char );
	}

	/**
	 * Converts string to HTML Entity equivalents of the characters
	 *
	 * @since 1.0
	 *
	 * @param string $string
	 * @return string
	 */
	public static function string_to_entity( $string ) {
        $new_string = '';

		foreach ( str_split( $string ) as $char ) {
			$new_string .= '&#' . ord( $char ) . ';';
		}

		return $new_string;
	}

	/**
	 * Prevent functions to be created in memory
	 *
	 * @param array $matches
	 * @return string
	 */
	public static function preserve_new_lines( $matches ) {
		return str_replace( "\n", "<PreserveNewline />", $matches[0] );
	}

	/**
	 * Replaces double line-breaks with paragraph elements.
	 *
	 * A group of regex replaces used to identify text formatted with newlines and
	 * replace double line-breaks with HTML paragraph tags. The remaining
	 * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
	 * or 'false'.
	 *
	 * @param string $pee The text which has to be formatted.
	 * @param int|bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
	 * @return string Text which has been converted into correct paragraph tags.
	 */
	public static function autop( $pee, $br = true ) {
		if ( trim( $pee ) === '' )
			return '';

		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace( '|<br />\s*<br />|', "\n\n", $pee );

		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend)';
		$pee = preg_replace( '!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee );
		$pee = preg_replace( '!(</' . $allblocks . '>)!', "$1\n\n", $pee );
		$pee = str_replace( array( "\r\n", "\r" ), "\n", $pee ); // cross-platform newlines

		if ( strpos( $pee, '<object' ) !== false ) {
			$pee = preg_replace( '|\s*<param([^>]*)>\s*|', "<param$1>", $pee ); // no pee inside object/embed
			$pee = preg_replace( '|\s*</embed>\s*|', '</embed>', $pee );
		}

		$pee = preg_replace( "/\n\n+/", "\n\n", $pee ); // take care of duplicates

		// make paragraphs, including one at the end
		$pees = preg_split( '/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY );
		$pee = '';

		foreach ( $pees as $tinkle )
			$pee .= '<p>' . trim( $tinkle, "\n") . "</p>\n";

		$pee = preg_replace( '|<p>\s*</p>|', '', $pee ); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace( '!<p>([^<]+)</(div|address|form)>!', "<p>$1</p><$2>", $pee );
		$pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee ); // don't pee all over a tag
		$pee = preg_replace( "|<p>(<li.+?)</p>|", "$1", $pee ); // problem with nested lists
		$pee = preg_replace( '|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee );
		$pee = str_replace( '</blockquote></p>', '</p></blockquote>', $pee );
		$pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee );
		$pee = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee) ;

		if ( $br ) {
			$pee = preg_replace_callback( '/<(script|style).*?<\/\\1>/s', 'self::preserve_new_lines' , $pee );
			$pee = preg_replace( '|(?<!<br />)\s*\n|', "<br />\n", $pee ); // optionally make line breaks
			$pee = str_replace( '<PreserveNewline />', "\n", $pee );
		}

		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
		if ( strpos( $pee, '<pre' ) !== false )
			$pee = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'clean_pre', $pee );

		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

		return $pee;
	}

	/**
	 * Reverses autop or turns paragraph and line breaks into newlines
	 *
	 * @param string $string
	 * @return string
	 */
	public static function unautop( $string ) {
		$string  = str_replace('<br />', '', $string );
		$string  = ltrim( str_replace('<p>', "\n", $string ) );
		return str_replace('</p>', '', $string );
	}

	/**
	 * Opposite of strip_tags -- strips specific tags
	 *
	 * @param string $str
	 * @param string|array $tags the tags to remove
	 * @param bool $strip_content (optional|false) whether to remove the content in the tags or not
     * @return string
	 */
	public static function strip_only( $str, $tags, $strip_content = false ) {
		$content = '';

		// If tags are not an array
		if ( !is_array( $tags ) ) {
			// Remove greater-than and lesser-than symbols and turn it into an array
			$tags = ( false !== ( strpos( $str, '>' ) ) ? explode( '>', str_replace( '<', '', $tags ) ) : array( $tags ) );

			// Make sure there are no empty tags
			if ( '' == end( $tags ) )
				array_pop( $tags );
		}

		// Go through the tags
		foreach ( $tags as $tag ) {
			// Find out if we're removing the inner content
			if ( $strip_content )
				 $content = '(.+</' . $tag . '[^>]*>|)';

			 // Strip the tags
			 $str = preg_replace( '#</?' . $tag . '[^>]*>' . $content . '#is', '', $str );
		}

		return $str;
	}

	/**
	 * Converts links in text to anchor tags that link to those links
	 * This does not take into account that there may already be links
	 *
	 * @param string $string
	 * @param bool $title_attribute (optional|true) whether you want the "title" attribute to be there (will match the link)
	 * @param bool $new_window (optional|false) whether the link should open a new tab/window
	 * @return string
	 */
	public static function links_to_anchors( $string, $title_attribute = true, $new_window = false ) {
		$title = ( $title_attribute ) ? ' title="\\1"' : '';
		$target = ( $new_window ) ? ' target="_blank"' : '';

        // Tricket to make sure there is always an http://
		return str_replace( 'http://http', 'http', regexp::replace( $string, 'url', '<a href="http://\\1"' . $title . $target . '>\\1</a>' ) );
	}

	/**
	 * Turns something into a slug (devoid of all symbols/spaces)
	 *
	 * @param string $string the string you want to turn into a slug
	 * @return string
	 */
	public static function slug( $string ) {
		return strtolower( preg_replace( array( '/[^-a-zA-Z0-9\s]/', '/[\s]/' ), array( '', '-' ), $string ) );
	}

    /**
     * Turns a slug (only letters, numbers and dashes into a proper word)
     *
     * For instance: 'living-room' to 'Living Room'
     *
     * @param string $string
     * @return string
     */
    public static function slug_to_name( $string ) {
        return ucwords( str_replace( '-', ' ', $string ) );
    }

    /**
     * String Last Replace
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function str_lreplace( $search, $replace, $subject ) {
        $pos = strrpos( $subject, $search );

        if ( false !== $pos )
            $subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );

        return $subject;
    }
}