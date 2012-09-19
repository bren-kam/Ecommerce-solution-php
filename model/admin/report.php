<?php
class Report extends ActiveRecordBase {
    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( '' );
    }

    /**
     * Main Search
     *
     * @param string $where
     * @return array
     */
    public function search( $where ) {
        return $this->get_results( "SELECT a.`website_id` AS id, a.`title`, c.`name` AS company, CONCAT( SUM( COALESCE( d.`active`, 0 ) ), ' / ', a.`products` ) AS products, DATE( a.`date_created` ) AS date_created FROM `websites` AS a LEFT JOIN `users` AS b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `companies` AS c ON ( b.`company_id` = c.`company_id` ) LEFT JOIN `website_products` AS d ON ( a.`website_id` = d.`website_id` ) LEFT JOIN `products` AS e ON ( d.`product_id` = e.`product_id` ) LEFT JOIN `brands` AS f ON ( e.`brand_id` = f.`brand_id` ) WHERE a.`status` = 1 $where GROUP BY a.`website_id` ORDER BY a.`title` ASC" );
    }

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_brands( $query ) {
		// Get results
		return $this->prepare(
            "SELECT `brand_id` AS object_id, `name` AS brand FROM `brands` WHERE `name` LIKE :query ORDER BY `name` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
     * @param string $where [optional]
	 * @return bool
	 */
	public function autocomplete_online_specialists( $query, $where = '' ) {
		// Get results
		return $this->prepare(
            "SELECT a.`user_id` AS object_id, a.`contact_name` AS online_specialist FROM `users` AS a LEFT JOIN `websites` AS b ON ( a.`user_id` = b.`os_user_id` ) WHERE a.`contact_name` LIKE :query $where GROUP BY a.`user_id` ORDER BY a.`contact_name` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
	 * @return bool
	 */
	public function autocomplete_marketing_specialists( $query ) {
		// Get results
		return $this->prepare(
            "SELECT `user_id` AS object_id, `contact_name` AS marketing_specialist FROM `users` WHERE `role` = 6 AND `contact_name` LIKE :query AND `status` = 1 ORDER BY `contact_name` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
     * @param string $where [optional]
	 * @return bool
	 */
	public function autocomplete_companies( $query, $where = '' ) {
		// Get results
		return $this->prepare(
            "SELECT `company_id` AS object_id, `name` AS company FROM `companies` WHERE `name` LIKE :query $where ORDER BY `name` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}

    /**
	 * Gets the data for an autocomplete request
	 *
	 * @param string $query
     * @param string $where [optional]
	 * @return bool
	 */
	public function autocomplete_company_packages( $query, $where = '' ) {
		// Get results
		return $this->prepare(
            "SELECT `company_package_id` AS object_id, `name` AS package FROM `company_packages` WHERE `name` LIKE :query $where ORDER BY `name` LIMIT 10"
            , 's'
            , array( ':query' => $query . '%')
        )->get_results( PDO::FETCH_ASSOC );
	}
}
