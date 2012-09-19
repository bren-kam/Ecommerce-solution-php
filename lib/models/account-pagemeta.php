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
     * Get by keys
     *
     * @param array $template_account_page_ids
     * @param array $pagemeta_keys
     * @return array
     */
    public function get_by_keys( array $template_account_page_ids, array $pagemeta_keys ) {
        foreach ( $template_account_page_ids as &$tapid ) {
            $tapid = (int) $tapid;
        }

        $pagemeta_keys_count = count( $pagemeta_keys );

        return $this->prepare(
            "SELECT `website_page_id`, `key`, `value` FROM `website_pagemeta` WHERE `website_page_id` IN ( $template_account_page_ids ) AND `key` IN( ?" . str_repeat( ', ?', $pagemeta_keys_count - 1 ) . ' )', PDO::FETCH_CLASS, 'AccountPagemeta'
            , str_repeat( 's', $pagemeta_keys_count )
            , $pagemeta_keys
        )->get_results( PDO::FETCH_CLASS, 'AccountPagemeta' );
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
}
