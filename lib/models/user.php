<?php
class User extends ActiveRecordBase {
    public $user_id, $company_id, $email, $contact_name, $store_name, $products, $role;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'users' );
    }

    /**
     * Get By Email
     *
     * @param string $email
     */
    public function get_by_email( $email ) {
        $columns = $this->prepare( 'SELECT `user_id`, `company_id`, `email`, `contact_name`, `store_name`, `products`, `role` FROM `users` WHERE `status` = 1 AND `email` = :email', 's', $email )->get_row();

        foreach ( $columns as $col => $value ) {
            $this->{$col} = $value;
        }

        $this->id = $columns->user_id;
    }

    /**
     * Check if the user has permissions
     *
     * @param int $permission
     * @return bool
     */
    public function has_permission( $permission ) {
        if ( $this->role >= $permission )
            return true;

        return false;
    }
}
