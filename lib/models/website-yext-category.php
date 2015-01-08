<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 28/11/14
 * Time: 14:51
 */

class WebsiteYextCategory extends ActiveRecordBase {

    public $id, $name;

    public function __construct() {
        parent::__construct( 'website_yext_category' );
    }

    /**
     * Get All
     * @return WebsiteYextCategory[]
     */
    public function get_all( ) {
        return $this->prepare(
            "SELECT * FROM website_yext_category ORDER BY `name`"
            , ''
            , [  ]
        )->get_results( PDO::FETCH_CLASS, 'WebsiteYextCategory' );
    }

}
