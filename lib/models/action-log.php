<?php
class ActionLog extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $user_id, $website_id, $action, $description, $extra, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'action_log' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->id = $this->insert( array(
            'user_id' => $this->user_id
            , 'website_id' => $this->website_id
            , 'action' => $this->action
            , 'description' => $this->description
            , 'extra' => json_encode( $this->extra )
            , 'date_created' => $this->date_created
        ), 'iissss' );
    }

    /**
     * Cleanup
     */
    public function cleanup() {
        $this->query('DELETE FROM `action_log` WHERE `date_created` > DATE_SUB(NOW(), INTERVAL 30 DAY)');
    }
}