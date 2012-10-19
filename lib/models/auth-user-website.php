<?php
class AuthUserWebsite extends ActiveRecordBase {
    public $id, $auth_user_website_id, $user_id, $website_id, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart;

    // Emails that should not be emailed
    protected $blocked_emails = array(
        'dcribbs@ashleyfurniture.com'
        , 'jwilliams@ashleyfurniture.com'
        , 'bherrmann@ashleyfurniture.com'
        , 'rcoppola@ashleyfurniture.com'
        , 'GGesualdo@Ashleyfurniture.com'
        , 'RLachenmaier@Ashleyfurniture.com'
        , 'AMatthews@Ashleyfurniture.com'
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
    public function add( $contact_name, $email, $account_id, $pages, $products, $analytics, $blog, $email_marketing, $shopping_cart, $role = 1 ) {
        $user = new User();

        $user->get_by_email( $email );

        if ( $user->id ) {
            // You must have role 1 to be an authorized user
            if ( $user->role > 1 || $this->is_authorized( $user->id, $account_id ) )
                return;

            // need to setup email helper class
            $account = new Account();
            $account->get( $account_id );
            $from    = "{$account->title} <{$user->email}>";
            $headers = "CC: {$from}\r\n";
            $subject = $account->title . ' has added you as an Authorized User at ' . DOMAIN . '.';
            $message = '<br /><strong>' . $account->title . '</strong> is using ' . DOMAIN . ' to build and manage a website. You have been added as an Authorized User to their account.<br /><br />Please click this link to login:<br /><br />';
            $message .= '<a href="http://account.' . DOMAIN . '/login/" title="Login">http://account.' . DOMAIN . '/login/</a>';
            $message .= '<br /><br />Please contact ' . DOMAIN . ' if you have any questions. Thank you for your time.<br /><br />';
            $message .= '<strong>Email:</strong> info@' . DOMAIN . '<br /><strong>Phone:</strong> (800) 549-9206<br /><br />';
        } else {
            $user->contact_name = $contact_name;
            $user->email = $email;
            $user->role = $role;
            $user->create();
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

        Emails::template( $email, $subject, $message , $from , $headers );
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
     * Get
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
}
