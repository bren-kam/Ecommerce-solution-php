<?php
class Notification extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $user_id, $message, $success;

    /**
     * Setup the initial data
     */
    public function __construct() {
        parent::__construct( 'notification' );
    }

    /**
     * Create a notification
     *
     * @throws InvalidParametersException
     */
    public function create() {
        if ( is_null( $this->user_id ) || is_null( $this->message ) )
            throw new InvalidParametersException( 'Both user_id and message parameters must be filled' );

        $this->id = $this->insert( array(
            'user_id' => $this->user_id
            , 'message' => strip_tags( $this->message, '<a><p><strong>' )
            , 'success' => $this->success
        ), 'isi' );
    }

    /**
     * Gets all the notifications
     *
     * @param int $user_id
     * @return Notification[]
     */
    public function get_by_user( $user_id ) {
        return $this->prepare(
            'SELECT `message`, `success` FROM `notification` WHERE `user_id` = :user_id'
            , 'i'
            , array( ':user_id' => $user_id )
        )->get_results( PDO::FETCH_CLASS, 'Notification' );
    }

    /**
     * Deletes all the notifications
     *
     * @param int $user_id
     */
    public function delete_by_user( $user_id ) {
        $this->delete( array( 'user_id' => $user_id ), 'i' );
    }
}
