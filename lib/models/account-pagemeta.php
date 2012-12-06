<?php
class AccountPagemeta extends ActiveRecordBase {
    public $id, $website_pagemeta_id, $website_page_id, $key, $value;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_pagemeta' );

        // We want to make sure they match
        if ( isset( $this->website_pagemeta_id ) )
            $this->id = $this->website_pagemeta_id;
    }

    /**
	 * Gets a metadata for a page
	 *
	 * @param int $account_id
	 * @param string $key_1, $key_2, $key_3, etc.
	 * @return array|bool
	 */
	public function get_by_account_and_keys( $account_id, $key_1 ) {
		// Get the arguments
		$arguments = func_get_args();

		// Needs to have at least two arguments
		if ( count( $arguments ) <= 2 )
			return false;

		// Typecast
		$account_id = (int) array_shift( $arguments );

		// Get keys, escape them and turn them into comma separated values
        $key_count = count( $arguments );
        $keys = '?' . str_repeat( ',?', $key_count - 1 );

		// Get the meta data
		$metadata = $this->prepare(
            "SELECT wpm.`key`, wpm.`value` FROM `website_pagemeta` AS wpm LEFT JOIN `website_pages` AS wp ON ( wpm.`website_page_id` = wp.`website_page_id` ) WHERE wpm.`key` IN ($keys) AND wp.`website_id` = $account_id"
            , str_repeat( 's', $key_count )
            , $arguments
        )->get_results( PDO::FETCH_ASSOC );


		// Set the array
		$new_metadata = array_fill_keys( $arguments, '' );

		// Decrypt any meta data
		if ( is_array( $metadata ) )
		foreach ( $metadata as $md ) {
			$new_metadata[$md['key']] = html_entity_decode( $md['value'], ENT_QUOTES, 'UTF-8' );
		}

		return ( 1 == count( $new_metadata ) ) ? array_shift( $new_metadata ) : $new_metadata;
	}

    /**
     * Get by keys
     *
     * @param int $account_page_id
     * @param string $pagemeta_keys
     * @return array
     */
    public function get_by_keys( $account_page_id, $pagemeta_keys ) {
        // Get arguments
        $arguments = func_get_args();
        $account_page_id = array_shift( $arguments );

        // Setup keys
        $pagemeta_keys = $arguments;
        $pagemeta_keys_count = count( $pagemeta_keys );

        // Get pagemeta
        $pagemeta_array = $this->get_for_pages_by_keys( array( $account_page_id ), $pagemeta_keys );
        $pagemeta_count = count( $pagemeta_array );

        // Format
        if ( 1 == $pagemeta_count )
            return $pagemeta_array[0]->value;

        $pagemeta = array();

        /**
         * @var AccountPagemeta $pm
         */
        foreach ( $pagemeta_array as $pm ) {
            $pagemeta[$pm->key] = $pm->value;
        }

        if ( $pagemeta_count != $pagemeta_keys_count )
        foreach ( $pagemeta_keys as $key ) {
            if ( !array_key_exists( $key, $pagemeta ) )
                $pagemeta[$key] = '';
        }

        return $pagemeta;
    }

    /**
     * Get For Pages By Keys
     *
     * @param array $account_page_ids
     * @param array $pagemeta_keys
     * @return array
     */
    public function get_for_pages_by_keys( $account_page_ids, $pagemeta_keys ) {
        foreach ( $account_page_ids as &$apid ) {
            $apid = (int) $apid;
        }

        $pagemeta_keys_count = count( $pagemeta_keys );
        $account_page_ids = implode( ', ', $account_page_ids );

        $pagemeta = $this->prepare(
            "SELECT `website_page_id`, `key`, `value` FROM `website_pagemeta` WHERE `website_page_id` IN ( $account_page_ids ) AND `key` IN( ?" . str_repeat( ', ?', $pagemeta_keys_count - 1 ) . ' )'
            , str_repeat( 's', $pagemeta_keys_count )
            , $pagemeta_keys
        )->get_results( PDO::FETCH_CLASS, 'AccountPagemeta' );

        return $pagemeta;
    }

    /**
     * Add Bulk
     *
     * @param array $pagemeta
     */
    public function add_bulk( array $pagemeta ) {
        $values = '';
        $key_values = array();

        foreach ( $pagemeta as $pm ) {
            if ( !empty( $values ) )
                $values .= ', ';

            $values .= '( ' . (int) $pm['website_page_id'] . ', ?, ? )';
            $key_values[] = $pm['key'];
            $key_values[] = $pm['value'];
        }

        $this->prepare(
            "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )"
            , str_repeat( 's', count( $pagemeta ) * 2 )
            , $key_values
        )->query();
    }

    /**
     * Add Bulk by Page
     *
     * @param int
     * @param array $pagemeta
     */
    public function add_bulk_by_page( $account_page_id, array $pagemeta ) {
        $values = '';
        $account_page_id = (int) $account_page_id;
        $key_values = array();

        foreach ( $pagemeta as $key => $value ) {
            if ( !empty( $values ) )
                $values .= ', ';

            $values .= "( $account_page_id, ?, ? )";
            $key_values[] = $key;
            $key_values[] = $value;
        }

        $this->prepare(
            "INSERT INTO `website_pagemeta` ( `website_page_id`, `key`, `value` ) VALUES $values ON DUPLICATE KEY UPDATE `value` = VALUES( `value` )"
            , str_repeat( 's', count( $pagemeta ) * 2 )
            , $key_values
        )->query();
    }
}
