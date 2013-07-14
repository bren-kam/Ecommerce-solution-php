<?php
class AuthUserWebsite extends ActiveRecordBase {
    public $id, $auth_user_website_id, $user_id, $website_id, $pages, $products, $analytics, $blog, $email_marketing
        , $shopping_cart;

    // Columns from other tables
    public $contact_name, $email;

    // Emails that should not be emailed
    protected $blocked_emails = array(
        'dcribbs@ashleyfurniture.com'
        , 'jwilliams@ashleyfurniture.com'
        , 'bherrmann@ashleyfurniture.com'
        , 'rcoppola@ashleyfurniture.com'
        , 'GGesualdo@Ashleyfurniture.com'
        , 'RLachenmaier@Ashleyfurniture.com'
        , 'AMatthews@Ashleyfurniture.com'
        , 'gkammer@ashleyfurniture.com'
        , 'csianko@ashleyfurniture.com'
    );

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'auth_user_websites' );

        // We want to make sure they match
        if ( isset( $this->auth_user_website_id ) )
            $this->id = $this->auth_user_website_id;
    }

    /**
     * Get By Account
     *
     * @param int $account_id
     * @param string $where [optional]
     * @return User[]
     */
    public function get_by_account( $account_id, $where = '' ) {
        return $this->prepare(
            "SELECT u.`user_id`, u.`contact_name`, u.`email`, u.`role` FROM `users` AS u LEFT JOIN `auth_user_websites` AS auw ON ( auw.`user_id` = u.`user_id` ) WHERE u.`status` = 1 AND auw.`website_id` = :account_id AND u.`contact_name` <> '' $where ORDER BY u.`contact_name`"
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'User' );
    }

    /**
     * Get
     *
     * @param int $user_id
     * @param int $account_id
     */
    public function get( $user_id, $account_id ) {
        $this->prepare(
            "SELECT auw.*, u.`contact_name`, u.`email` FROM `auth_user_websites` AS auw LEFT JOIN `users` AS u ON ( u.`user_id` = auw.`user_id` ) WHERE auw.`user_id` = :user_id AND auw.`website_id` = :account_id"
            , 'ii'
            , array( ':user_id' => $user_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );
    }

    /**
     * Add an authorized user
     *
     * @param string $contact_name
     * @param string $email
     * @param int $account_id
     * @param bool $pages
     * @param bool $products
     * @param bool $analytics
     * @param bool $blog
     * @param bool $email_marketing
     * @param bool $shopping_cart
     * @param int $role (optional|1)
     */
    public function add( $contact_name, $email, $account_id, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart, $role = User::ROLE_AUTHORIZED_USER ) {
        // Setup variables
        $user = new User();
        $user->get_by_email( $email, false );

        $account = new Account();
        $account->get( $account_id );

        // See if they already exist as a user
        if ( $user->id ) {
            // You must have role 1 to be an authorized user
            if ( !in_array( $user->role, array( User::ROLE_AUTHORIZED_USER, User::ROLE_MARKETING_SPECIALIST ) ) || $this->is_authorized( $user->id, $account_id ) )
                return;

            $message = '<br /><strong>' . $account->title . '</strong> is using ' . DOMAIN . ' to build and manage a website. You have been added as an Authorized User to their account.<br /><br />Please click this link to login:<br /><br />';
            $message .= '<a href="http://account.' . DOMAIN . '/login/" title="Login">http://account.' . DOMAIN . '/login/</a>';
            $message .= '<br /><br />Please contact ' . DOMAIN . ' if you have any questions. Thank you for your time.<br /><br />';
            $message .= '<strong>Email:</strong> info@' . DOMAIN . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';

            if ( 1 != $user->status ) {
                $user->contact_name = $contact_name;
                $user->role = $role;
                $user->status = 1;
                $user->save();
            }
        } else {
            // Create base user
            $user->company_id = $account->company_id;
            $user->contact_name = $contact_name;
            $user->email = $email;
            $user->role = $role;
            $user->status = 1;
            $user->create();

            // Create token for them to authorize their account
            $expires = dt::hours_to_date( 72 );
            $token = new Token();
            $token->user_id = $user->id;
            $token->type = 'activate-account';
            $token->date_valid = $expires->format('Y-m-d H:i:s');
            $token->create();

            // Create message for email
            $message = '<br /><strong>' . $account->title . '</strong> is using ' . DOMAIN . ' to build and manage a website. You have been added as an Authorized User to their account.<br /><br />Please click this link to create your own password:<br /><br />';
            $message .= 'http://account.' . DOMAIN . "/activate-account/?t={$token->key}";
            $message .= '<br /><br />Please contact ' . DOMAIN . ' if you have any questions. Thank you for your time.<br /><br />';
            $message .= '<strong>Email:</strong> info@' . DOMAIN . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';
        }

        // Create the auth user website
        $this->user_id = $user->id;
        $this->website_id = $account_id;
        $this->pages = $pages;
        $this->products = $products;
        $this->analytics = $analytics;
        $this->blog = $blog;
        $this->email_marketing = $email_marketing;
        $this->shopping_cart = $shopping_cart;
        $this->create();

        // Send email if it's not in the blocked list
        if ( in_array( $email, $this->blocked_emails ) )
            return;

        $intro = new EmailHelper();
        $intro->to = $email;
        $intro->message = $message;
        $intro->from = "{$account->title} <{$user->email}>";
        $intro->extra_headers = "CC: {$intro->from}\r\n";
        $intro->subject = $account->title . ' has added you as an Authorized User at ' . DOMAIN . '.';
        $intro->send();
    }

    /**
     * Create
     */
    public function create() {
        $this->insert( array(
            'user_id' => $this->user_id
            , 'website_id' => $this->website_id
            , 'pages' => $this->pages
            , 'products' => $this->products
            , 'analytics' => $this->analytics
            , 'blog' => $this->blog
            , 'email_marketing' => $this->email_marketing
            , 'shopping_cart' => $this->shopping_cart
        ), 'iiiiiiii' );

        $this->auth_user_website_id = $this->id = $this->get_insert_id();
    }

    /**
     * Save
     */
    public function save() {
        $this->update( array(
            'pages' => $this->pages
            , 'products' => $this->products
            , 'analytics' => $this->analytics
            , 'blog' => $this->blog
            , 'email_marketing' => $this->email_marketing
            , 'shopping_cart' => $this->shopping_cart
        ), array(
            'user_id' => $this->user_id
            , 'website_id' => $this->website_id
        ), 'iiiiii', 'ii' );
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array(
            'user_id' => $this->user_id
            , 'website_id' => $this->website_id
        ), 'ii' );
    }

    /**
     * Is Authorized
     *
     * @param int $user_id
     * @param int $account_id
     * @return int
     */
    public function is_authorized( $user_id, $account_id ) {
        return $this->prepare(
            'SELECT `user_id` FROM `auth_user_websites` WHERE `user_id` = :user_id AND `website_id` = :account_id'
            , 'ii'
            , array( ':user_id' => $user_id, 'account_id' => $account_id )
        )->get_var();
    }

    /**
     * List
     *
     * @param $variables array( $where, $order_by, $limit )
     * @return AuthUserWebsite[]
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT u.`user_id`, u.`email`, auw.`pages`, auw.`products`, auw.`analytics`, auw.`blog`, auw.`email_marketing`, auw.`shopping_cart` FROM `users` AS u LEFT JOIN `auth_user_websites` AS auw ON ( auw.`user_id` = u.`user_id` ) WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AuthUserWebsite' );
    }

    /**
     * Count all
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get the website count
        return $this->prepare(
            "SELECT COUNT( u.`user_id` ) FROM `users` AS u LEFT JOIN `auth_user_websites` AS auw ON ( auw.`user_id` = u.`user_id` ) WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();
    }
}
