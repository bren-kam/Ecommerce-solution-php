<?php
class AccountFile extends ActiveRecordBase {
    public $id, $website_file_id, $website_id, $file_path, $date_created;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'website_files' );

        // We want to make sure they match
        if ( isset( $this->website_file_id ) )
            $this->id = $this->website_file_id;
    }

    /**
     * Get
     *
     * @param int $account_file_id
     * @param string $domain
     * @param int $account_id
     * @return AccountFile
     */
    public function get( $account_file_id, $domain, $account_id ) {
        $this->prepare(
            "SELECT `website_file_id`, `website_id`, REPLACE( `file_path`, '[domain]', :domain ) AS file_path, `date_created` FROM `website_files` WHERE `website_file_id` = :account_file_id AND `website_id` = :account_id"
            , 'sii'
            , array( ':domain' => $domain, ':account_file_id' => $account_file_id, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_file_id;
    }

    /**
	 * Get a file by path
	 *
	 * @param string $file_path
     * @param string $domain
     * @param int $account_id
	 * @return array
	 */
	public function get_by_file_path( $file_path, $domain, $account_id ) {
	    $this->prepare(
            "SELECT `website_file_id`, `website_id`, REPLACE( `file_path`, '[domain]', :domain ) AS file_path, `date_created` FROM `website_files` WHERE file_path = :file_path AND `website_id` = :account_id"
            , 'ssi'
            , array( ':file_path' => $file_path, ':domain' => $domain, ':account_id' => $account_id )
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_file_id;
	}

    /**
     * Get By Account
     *
     * @param int $account_id
     * @return array
     */
    public function get_by_account( $account_id ) {
        return $this->prepare(
            'SELECT * FROM `website_files` WHERE `website_id` = :account_id'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_CLASS, 'AccountFile' );
    }

    /**
     * Create
     */
    public function create() {
        $this->date_created = dt::now();

        $this->insert( array(
            'website_id' => $this->website_id
            , 'file_path' => $this->file_path
            , 'date_created' => $this->date_created
        ), 'iss' );

        $this->id = $this->website_file_id = $this->get_insert_id();
    }

    /**
     * Remove
     */
    public function remove() {
        $this->delete( array( 'website_file_id' => $this->id ), 'i' );
    }
}
