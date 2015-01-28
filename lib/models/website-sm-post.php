<?php

class WebsiteSmPost extends ActiveRecordBase {

    public $id, $website_sm_post_id, $website_sm_account_id, $content, $photo, $link, $post_at, $timezone, $posted, $created_at, $sm_message;

    public $website_id, $sm;

    public function __construct() {
        parent::__construct( 'website_sm_post' );
    }

    /**
     * Get
     * @param $id
     * @param $website_id
     */
    public function get( $id, $website_id ) {
        $this->prepare(
            "SELECT p.*, a.sm, a.website_id FROM website_sm_post p INNER JOIN website_sm_account a ON p.website_sm_account_id = a.website_sm_account_id WHERE p.website_sm_post_id = :id AND a.website_id = :website_id"
            , 'ii'
            , [  ':id' => $id, ':website_id' => $website_id ]
        )->get_row( PDO::FETCH_INTO, $this );

        $this->id = $this->website_sm_post_id;
    }

    /**
     * Get By SM Account ID
     * @param $m_account_id
     * @param $website_id
     */
    public function get_by_sm_account_id( $sm_account_id, $website_id ) {
        $all = $this->prepare(
            "SELECT p.*, a.sm, a.website_id FROM website_sm_post p INNER JOIN website_sm_account a ON p.website_sm_account_id = a.website_sm_account_id WHERE p.website_sm_account_id = :website_sm_account_id AND a.website_id = :website_id"
            , 'ii'
            , [  ':website_sm_account_id' => $sm_account_id, ':website_id' => $website_id ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteSmPost' );

        foreach ( $all as $l ) {
            $l->id = $l->website_sm_post_id;
        }
        return $all;
    }

    /**
     * Get All
     * @param $website_id
     * @return WebsiteSmPost[]
     */
    public function get_all( $website_id ) {
        $all = $this->prepare(
            "SELECT p.*, a.sm, a.website_id FROM website_sm_post p INNER JOIN website_sm_account a ON p.website_sm_account_id = a.website_sm_account_id WHERE website_id = :website_id"
            , 'i'
            , [ ':website_id' => $website_id ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteSmPost' );

        foreach ( $all as $l ) {
            $l->id = $l->website_sm_post_id;
        }
        return $all;
    }

    /**
     * List All
     * @param $variables
     * @return WebsiteSmPost[]
     */
    public function list_all( $variables ) {
        list( $where, $values, $order_by, $limit ) = $variables;

        $all = $this->prepare(
            "SELECT p.*, a.sm, a.website_id FROM website_sm_post p INNER JOIN website_sm_account a ON p.website_sm_account_id = a.website_sm_account_id WHERE 1 $where $order_by LIMIT $limit"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_results( PDO::FETCH_CLASS, 'WebsiteSmPost' );

        foreach ( $all as $l ) {
            $l->id = $l->website_sm_post_id;
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
            "SELECT COUNT(*) FROM website_sm_post WHERE 1 $where $order_by"
            , str_repeat( 's', count( $values ) )
            , $values
        )->get_var(  );
    }

    /**
     * Create
     */
    public function create() {
        $this->id = $this->website_sm_post_id = $this->insert(
            [
                'website_sm_account_id' => $this->website_sm_account_id
                , 'content' => $this->content
                , 'photo' => $this->photo
                , 'link' => $this->link
                , 'post_at' => $this->post_at
                , 'timezone' => $this->timezone
                , 'posted' => $this->posted
                , 'sm_message' => $this->sm_message
            ]
            , 'issssi'
        );
    }

    /**
     * Save
     */
    public function save() {
        $this->update(
            [
                'content' => $this->content
                , 'photo' => $this->photo
                , 'link' => $this->link
                , 'post_at' => $this->post_at
                , 'timezone' => $this->timezone
                , 'posted' => $this->posted
                , 'sm_message' => $this->sm_message
            ]
            , [  'website_sm_post_id' => $this->id ]
            , 'ssssi'
            , 'i'
        );
    }

    /**
     * remove
     */
    public function remove() {
        parent::delete(
            [  'website_sm_post_id' => $this->id ]
            , 'i'
        );
    }

    /**
     * Post
     * @return bool
     */
    public function post() {

        try {
            switch ($this->sm) {
                case 'facebook';
                    $success = $this->post_facebook();
                    break;
                case 'twitter';
                    $success = $this->post_twitter();
                    break;
            }

            $this->posted = 1;
            $this->sm_message = '';
            $this->save();
            return true;

        } catch( Exception $e ) {
            $this->posted = 2;
            $this->sm_message = $e->getMessage();
            $this->save();
        }

        return false;

    }

    /**
     * Post Facebook
     * @return bool
     */
    private function post_facebook() {

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get( $this->website_sm_account_id, $this->website_id );

        library('facebook_v4/facebook');
        Facebook\FacebookSession::setDefaultApplication( Config::key( 'facebook-key' ) , Config::key( 'facebook-secret' ) );

        // Default: write on user's wall
        $post_url = '/me/feed';
        $access_token = $website_sm_account->auth_information_array['access-token'];
        if ( isset( $website_sm_account->auth_information_array['post-as']['id'] ) ) {
            // Post somewhere else
            $post_url = "/{$website_sm_account->auth_information_array['post-as']['id']}/feed";
            $access_token = $website_sm_account->auth_information_array['post-as']['access_token'];
        }

        $session = new Facebook\FacebookSession( $access_token );

        $post_fields = [];

        $post_fields['message'] = $this->content;

        if ( $this->link ) {
            $post_fields['link'] = $this->link;
        }

        if ( $this->photo ) {
            $post_fields['picture'] = $this->photo;
        }

        $request = new Facebook\FacebookRequest(
            $session
            , 'POST'
            , $post_url
            , $post_fields
        );

        $response = $request->execute();
        $graph_object = $response->getGraphObject()->asArray();

        return !empty( $graph_object['id'] );
    }

    /**
     * Post Twitter
     * @return bool
     */
    private function post_twitter() {

        $website_sm_account = new WebsiteSmAccount();
        $website_sm_account->get( $this->website_sm_account_id, $this->website_id );

        library('twitteroauth/autoload');

        $connection = new Abraham\TwitterOAuth\TwitterOAuth(
            Config::key( 'twitter-key' )
            , Config::key( 'twitter-secret' )
            , $website_sm_account->auth_information_array['access-token']
            , $website_sm_account->auth_information_array['access-token-secret']
        );

        $data = [];
        $tweet_msg = $this->content;

        if ( $this->link ) {
            $tweet_msg .= ' ' . url::reduce_url( $this->link );
        }

        if ( $this->photo ) {
            $media = $connection->upload( 'media/upload', [ 'media' => $this->photo ] );
            if ( $media->errors )
                throw new Exception( $media->errors[0]->message );
            $data['media_ids'] = [ $media->media_id ];
        }

        $data['status'] = $tweet_msg;
        $tweet = $connection->post( 'statuses/update', $data );
        if ( $tweet->errors )
            throw new Exception( $tweet->errors[0]->message );

        return $tweet->id;
    }

    /**
     * Get Unposted
     * @return WebsiteSmPost[]
     */
    public function get_all_unposted() {
        return $this->get_results(
            "SELECT p.*, p.website_sm_post_id as id, a.sm, a.website_id FROM website_sm_post p INNER JOIN website_sm_account a ON p.website_sm_account_id = a.website_sm_account_id WHERE p.posted = 0 ORDER BY p.post_at"
            , PDO::FETCH_CLASS, 'WebsiteSmPost'
        );
    }


    /**
     * Can Post
     * @return bool
     */
    public function can_post() {
        // no schedule, can be posted anytime
        if ( !$this->post_at ) {
            return true;
        }

        $now = new DateTime();
        $now->getTimezone();

        $post_at = new DateTime( $this->post_at, new DateTimeZone( $this->timezone ) );

        return $post_at < $now;
    }

}
