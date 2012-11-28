<?php
class Products extends ActiveRecordBase {
    // The columns we will have access to
    public $sm_facebook_page_id, $fb_page_id, $key, $content, $date_created;

    // Columns from other tables
    public $website_id, $domain;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'sm_products' );
    }

    /**
	 * Get Tab
	 *
	 * @param string $fb_page_id
	 * @return string
	 */
	public function get_tab( $fb_page_id ) {
		// Get the tab
		$tab_data = $this->get_tab_data( $fb_page_id );

		if ( 'no-catalog' == $tab_data->content ) {
            // Initial variables
			$tab = '';

			// Get Top Categories
            $top_categories = $this->get_top_categories( $tab_data->website_id );

            // Declare variables for loop
			$i = 1;
            $open = false;
			$total_categories = count( $top_categories );
			$ssl = security::is_ssl();

			// Create rows
            if ( is_array( $top_categories ) )
			foreach ( $top_categories as $category ) {
				if ( 1 == $i % 3 ) {
					$last_class = ( $total_categories - $i < 3 ) ? ' last' : '';
					$tab .= "<ul class='clear{$last_class}'>";
					$open = true;
				}

				$image_link = ( $ssl ) ? str_replace( 'http://', 'https://s3.amazonaws.com/', $category->image_url ) : $category->image_url;

				// Create tab listing
				$tab .= '<li><a href="http://' . $tab_data->domain . '/' . $category->slug . '/" class="img" title="' . $category->name . '" target="_blank"><img src="' . $image_link . '" width="200" height="200" alt="' . $category->name . '" /></a><br /><a href="http://' . $tab_data->domain . '/' . $category->slug . '/" title="' . $category->name . '" target="_blank">' . $category->name . "</a></li>\n";

				if ( 0 == $i % 3 ) {
					$tab .= "</ul>\n";
					$open = false;
				}

				$i++;
			}

			if ( $open )
				$tab .= "</ul>\n";
		} else {
			$tab = $tab_data->content;
		}

		return $tab;
	}

    /**
     * Get Tab Data
     *
     * @param string $fb_page_id
     * @return Products
     */
    protected function get_tab_data( $fb_page_id ) {
        return $this->prepare(
            "SELECT IF( 0 = `product_catalog`, smp.`content`, 'no-catalog' ) AS content, smfbp.`website_id`, w.`domain` FROM `sm_products` AS smp LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`id` = smp.`sm_facebook_page_id` ) LEFT JOIN `websites` AS w ON ( w.`website_id` = smfbp.`website_id` ) WHERE smp.`fb_page_id` = :fb_page_id AND smfbp.`status` = 1"
            , 's'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_CLASS, 'Products' );
    }

    /**
     * Get Top Categories
     *
     * @param int $account_id
     * @return stdClass
     */
    protected function get_top_categories( $account_id ) {
        $this->prepare(
            'SELECT c.`name`, c.`slug`, wc.`image_url` FROM `categories` AS c LEFT JOIN `website_categories` AS wc ON ( wc.`category_id` = c.`category_id` ) WHERE c.`parent_category_id` = 0 AND wc.`website_id` = :account_id GROUP BY c.`category_id`'
            , 'i'
            , array( ':account_id' => $account_id )
        )->get_results( PDO::FETCH_OBJ );
    }

    /**
	 * Get Connected Website
	 *
	 * @param int $fb_page_id
	 * @return stdClass
	 */
	public function get_connected_website( $fb_page_id ) {
		return $this->prepare(
            'SELECT w.`title`, smp.`key` FROM `websites` AS w LEFT JOIN `sm_facebook_page` AS smfbp ON ( smfbp.`website_id` = w.`website_id` ) LEFT JOIN `sm_products` AS smp ON ( smp.`sm_facebook_page_id` = smfbp.`id` ) WHERE smp.`fb_page_id` = :fb_page_id'
            , 'i'
            , array( ':fb_page_id' => $fb_page_id )
        )->get_row( PDO::FETCH_OBJ );
	}

    /**
     * Connect
     *
     * @param int $fb_page_id
     * @param string $key
     */
    public function connect( $fb_page_id, $key ) {
        parent::update( array(
            'fb_page_id' => $fb_page_id
        ), array(
            'key' => $key
        ), 'i', 's' );
    }
}