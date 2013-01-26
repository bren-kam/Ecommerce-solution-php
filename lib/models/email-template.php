<?php
class EmailTemplate extends ActiveRecordBase {
    public $id, $email_template_id, $name, $template, $image, $thumbnail, $type, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'email_templates' );

        // We want to make sure they match
        if ( isset( $this->email_template_id ) )
            $this->id = $this->email_template_id;
    }

    /**
     * Get Default
     *
     * @param int $account_id
     */
    public function get_default( $account_id ) {
        $this->prepare(
            "SELECT et.* FROM `email_templates` AS et LEFT JOIN `email_template_associations` AS eta ON ( eta.`email_template_id` = et.`email_template_id` ) WHERE et.`type` = 'default' AND eta.`object_id` = :object_id AND eta.`type` = 'website'"
            , 'i'
            , array( ':object_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
		
		$this->id = $this->email_template_id;
    }

    /**
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'name' => $this->name
            , 'template' => $this->template
            , 'image' => $this->image
            , 'thumbnail' => $this->thumbnail
            , 'type' => $this->type
            , 'date_created' => $this->date_created
        ), 'ssssss' );

        $this->id = $this->email_template_id = $this->get_insert_id();
    }

    /**
     * Update Email List
     */
    public function save() {
        $this->update( array(
            'name' => $this->name
            , 'template' => $this->template
            , 'image' => $this->image
            , 'thumbnail' => $this->thumbnail
            , 'type' => $this->type
        ), array( 'email_template_id' => $this->id, ), 'sssss', 'i' );
    }

    /**
     * Add Association
     *
     * @param int $object_id
     * @param string $type
     */
    public function add_association( $object_id, $type ) {
        $this->prepare(
            'INSERT INTO `email_template_associations` ( `email_template_id`, `object_id`, `type` ) VALUES ( :email_template_id, :object_id, :type ) ON DUPLICATE KEY UPDATE `object_id` = :object_id2'
            , 'iiis'
            , array(
                ':email_template_id' => $this->id
                , ':object_id' => $object_id
                , ':object_id2' => $object_id
                , ':type' => $type
            )
        )->query();
    }
}
