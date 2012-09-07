<?php
class Tag extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $tag_id, $object_id, $type, $value;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'tags' );

        // We want to make sure they match
        if ( isset( $this->tag_id ) )
            $this->id = $this->tag_id;
    }

    /**
     * Add Bulk
     *
     * @param string $type
     * @param int $object_id
     * @param array $tags
     */
    public function add_bulk( $type, $object_id, array $tags ) {
        // Determine how many tags we have
        $tag_count = count( $tags );

        // Don't want to add no tags
        if ( 0 == $tag_count )
            return;

        // Declare variable
        $values_array = array();

        // Create the array for all the values
        foreach ( $tags as $tag ) {
            $values_array[] = $object_id;
            $values_array[] = $type;
            $values_array[] = $tag;
        }

        // Determine the SQL setting to get the values
        $sql_values = '( ?, ?, ? )' . str_repeat( ',( ?, ?, ? )', $tag_count - 1 );

        // Insert the values
        $this->prepare(
            "INSERT INTO `tags` ( `object_id`, `type`, `value` ) VALUES $sql_values"
            , str_repeat( 'iss', $tag_count )
            , $values_array
        )->query();
    }

    /**
     * Get by the type
     *
     * @param string $type
     * @param int $object_id
     * @return array
     */
    public function get_value_by_type( $type, $object_id ) {
        return $this->prepare( 'SELECT `value` FROM `tags` WHERE `type` = :type AND `object_id` = :object_id'
            , 'si'
            , array( ':type' => $type, ':object_id' => $object_id )
        )->get_col();
    }

    /**
     * Delete by Type
     */
    public function delete_by_type( $type, $object_id ) {
        $this->prepare(
            'DELETE FROM `tags` WHERE `type` = :type AND `object_id` = :object_id'
            , 'si'
            , array( ':type' => $type, ':object_id' => $object_id )
        )->query();
    }
}
