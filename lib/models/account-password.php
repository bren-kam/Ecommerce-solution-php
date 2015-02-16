<?php
class AccountPassword extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_password_id, $website_id, $title, $username, $password, $url, $iv, $notes, $date_created;


    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_passwords' );

        // We want to make sure they match
        if ( isset( $this->website_password_id ) )
            $this->id = $this->website_password_id;
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $cryptoKey = Config::key( 'crypto-key' );
        $iv = $this->create_iv();
        $encrypted_password = $this->encrypt($cryptoKey, $iv, $this->password );

        $this->insert( array(
            'website_id' => $this->website_id
            , 'title' => strip_tags($this->title)
            , 'username' => strip_tags($this->username)
            , 'password' => $encrypted_password
            , 'iv' => base64_encode($iv)
            , 'url' => strip_tags($this->url)
            , 'notes' => strip_tags($this->notes)
            , 'date_created' => $this->date_created
        ), 'isssssss' );

        $this->website_password_id = $this->id = $this->get_insert_id();
    }

    /**
     * Update
     */
    public function save() {
        $cryptoKey = Config::key( 'crypto-key' );
        $iv = $this->create_iv();
        $encrypted_password = $this->encrypt($cryptoKey, $iv, $this->password );

        $this->update( array(
               'title' => strip_tags($this->title)
               , 'username' => strip_tags($this->username)
               , 'password' => $encrypted_password
               , 'iv' => base64_encode($iv)
               , 'url' => strip_tags($this->url)
               , 'notes' => strip_tags($this->notes)
        ), array( 'website_password_id' => $this->id ), 'ssssss', 'i' );
    }

    /**
     * Get Password
     *
     * @param int $account_password_id
     */
    public function get( $account_password_id ) {
        $this->prepare(
            'SELECT `website_password_id`, `title`, `username`, `password`, `url`, `iv`, `notes` FROM `website_passwords` WHERE `website_password_id` = :account_password_id'
            , 'i'
            , array( ':account_password_id' => $account_password_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_password_id;

        $cryptoKey = Config::key( 'crypto-key' );
        $this->password = $this->decrypt($cryptoKey, $this->iv, $this->password );

    }

    /**
     * Get all account passwords
     *
     * @param array $variables ( string $where, array $values, string $order_by, int $limit )
     * @return array
     */
    public function list_all( $variables ) {
        // Get the variables
        list( $where, $values, $order_by, $limit ) = $variables;

        // Get the account
		$passwords =  $this->prepare(
            "SELECT a.`website_password_id`, a.`title`, a.`username`, a.`password`, a.`iv`,  a.`url` FROM `website_passwords` AS a WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'AccountPassword' );

        return $passwords;
    }

    /**
     * Count all account passwords
     *
     * @param array $variables
     * @return int
     */
    public function count_all( $variables ) {
        // Get the variables
        list( $where, $values ) = $variables;

        // Get count of all passwords for an account
        $count = $this->prepare(
            "SELECT COUNT(*) FROM `website_passwords` AS a WHERE 1 $where"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var();

        return $count;
    }

    /**
     * Delete password
     */
    public function remove() {
        parent::delete( array( 'website_password_id' => $this->id ), 'i' );
    }

    public function create_iv(){
        return mcrypt_create_iv(
            mcrypt_get_iv_size(
                MCRYPT_RIJNDAEL_256,
                MCRYPT_MODE_ECB
            ),
            MCRYPT_RAND
        );
    }

    /**
     * Encrypt password
     */
    public function encrypt($sCryptoKey, $sIV, $sDecrypted) {
        return rtrim(
            base64_encode(
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256,
                    base64_decode($sCryptoKey),
                    $sDecrypted,
                    MCRYPT_MODE_CFB,
                    $sIV
                )
            ), "\0"
        );
    }

    /**
     * Decrypt password
     */
    public function decrypt($sCryptoKey, $sIV, $sEncrypted) {
        return rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                base64_decode($sCryptoKey),
                base64_decode($sEncrypted),
                MCRYPT_MODE_CFB,
                base64_decode($sIV)
            ), "\0"
        );
    }

}
