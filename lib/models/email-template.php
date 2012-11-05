<?php
class EmailTemplate extends ActiveRecordBase {
    public $id, $email_template_id, $name, $template, $type, $date_created;

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
     * Create Email List
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'name' => $this->name
            , 'template' => $this->template
            , 'type' => $this->type
            , 'date_created' => $this->date_created
        ), 'ssss' );

        $this->id = $this->email_template_id = $this->get_insert_id();
    }

    /**
     * Add Association
     *
     * @param int $object_id
     * @param string $type
     */
    public function add_association( $object_id, $type ) {
        $this->prepare(
            'INSERT INTO `email_template_associations` ( `email_template_id`, `object_id`, `type` ) VALUES ( :email_template_id, :object_id, :type )'
            , 'iis'
            , array(
                ':email_template_id' => $this->id
                , ':object_id' => $object_id
                , ':type' => $type
            )
        )->query();
    }
}
