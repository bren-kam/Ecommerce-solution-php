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
}
