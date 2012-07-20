<?php
class User extends ActiveRecordBase {
    /**
     * Hold the User ID
     * @var int
     */
    private $_id = NULL;

    /**
     * Hold the columns
     *
     * @var array
     */
    private $_columns = array();

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'users' );
    }

    /**
     * Get By Mail
     *
     * @param string $email
     */
    public function get_by_email( $email ) {
        try {
            $this->_columns = $this->prepare( 'SELECT `user_id`, `company_id`, `email`, `contact_name`, `store_name`, `products`, `role` FROM `users` WHERE `status` = 1 AND `email` = :email', 's', $email )->get_row();
        } catch ( ModelException $e ){
            // Do what?
        }

        $this->_id = $this->_columns['user_id'];
    }
}
