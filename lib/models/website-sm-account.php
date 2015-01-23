<?php

class WebsiteSmAccount extends ActiveRecordBase {

    public $id, $website_sm_account_id, $website_id, $sm, $sm_reference_id, $title, $photo, $auth_information;

    public $auth_information_array;

    public function __construct() {
        parent::__construct( 'website_sm_account' );
    }

    /**
     * Get
     * @param $id
     * @param $website_id
     */
    public function get( $id, $website_id ) {
        $this->prepare(
            "SELECT * FROM website_sm_account WHERE website_sm_account_id = :id AND website_id = :website_id"
            , 'i'
            , [  ':id' => $id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_sm_account_id;
        $this->auth_information_array = json_decode($this->auth_information, true);

    }

    /**
     * Get By SM Reference ID
     * @param $sm
     * @param $sm_reference_id
     * @param $website_id
     */
    public function get_by_sm_reference_id( $sm, $sm_reference_id, $website_id ) {
        $this->prepare(
            "SELECT * FROM website_sm_account WHERE sm = :sm AND sm_reference_id = :sm_reference_id AND website_id = :website_id"
            , 'i'
            , [  ':sm' => $sm, ':sm_reference_id' => $sm_reference_id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_sm_account_id;
        $this->auth_information_array = json_decode($this->auth_information, true);
    }

    /**
     * Get All
     * @param $website_id
     * @return WebsiteSmAccount[]
     */
    public function get_all( $website_id ) {
        $all = $this->prepare(
            "SELECT * FROM website_sm_account WHERE website_id = :website_id"
            , 'i'
            , [ ':website_id' => $website_id ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteSmAccount' );

        foreach ( $all as $l ) {
            $l->id = $l->website_sm_account_id;
            $l->auth_information_array = json_decode($l->auth_information, true);
        }
        return $all;
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteSmAccount[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        $all = $this->prepare(
            "SELECT * FROM website_sm_account WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteSmAccount' );

        foreach ( $all as $l ) {
            $l->id = $l->website_sm_account_id;
            $l->auth_information_array = json_decode($l->auth_information, true);
        }
        return $all;
    }

    /**
     * Count All
     * @param $variables
     * @return int
     */
    public function count_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        return $this->prepare(
            "SELECT COUNT(*) FROM website_sm_account WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Create
     */
    public function create() {
        $this->id = $this->website_sm_account_id = $this->insert(
            [
                'website_id' => $this->website_id
                , 'sm' => $this->sm
                , 'sm_reference_id' => $this->sm_reference_id
                , 'title' => $this->title
                , 'photo' => $this->photo
                , 'auth_information' => $this->auth_information
            ]
            , 'issss'
        );
    }

    /**
     * Save
     */
    public function save() {
        $this->update(
            [
                'website_id' => $this->website_id
                , 'sm' => $this->sm
                , 'sm_reference_id' => $this->sm_reference_id
                , 'title' => $this->title
                , 'photo' => $this->photo
                , 'auth_information' => $this->auth_information
            ]
            , [  'website_sm_account_id' => $this->id, 'website_id' => $this->website_id ]
            , 'issss'
            , 'ii'
        );
    }

    /**
     * remove
     */
    public function remove() {
        parent::delete(
            [  'website_sm_account_id' => $this->id, 'website_id' => $this->website_id ]
            , 'ii'
        );
    }

}
