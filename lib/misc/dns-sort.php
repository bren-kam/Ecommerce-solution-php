<?php
/**
 * DNS Sort class
 *
 * Contains basic functions that can be used anywhere
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class DNSSort {
    /**
     *
     * @param array $array
     */
    public function __construct( array &$array ) {
        usort( $array, array( $this, 'dns_sort' ) );
    }

    /**
     * Do an array sort for DNS
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public function dns_sort( $a, $b ) {
        if ( $a['Type'] == $b['Type'] )
            return 0;

        if ( 'SOA' == $b['Type'] && 'SOA' != $a['Type'] || 'NS' == $b['Type'] && 'NS' != $a['Type'] )
            return 1;

        if ( 'SOA' == $a['Type'] && 'SOA' != $b['Type'] || 'NS' == $a['Type'] && 'NS' != $b['Type'] )
            return -1;

        return strcmp( $a['Type'], $b['Type'] );
    }
}