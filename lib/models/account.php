<?php
class Account extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $website_id;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'websites' );
    }

    /**
	 * Get all information of the websites
	 *
     * @param User $user
     * @param array $variables ( User $user, string $where, array $values, string $order_by, int $limit )
	 * @return array
	 */
	public function list_all( $user, $variables ) {
		// Get the variables
		list( $where, $values, $order_by, $limit ) = $variables;

        if ( 251 == $user->id ) {
            $where = ( empty( $where ) ) ? ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user->company_id . ' )' : $where . ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user->company_id . ' )';
        } else {
            // If they are below 8, that means they are a partner
            if ( !$user->has_permission(8) )
                $where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user->company_id : $where . ' AND b.`company_id` = ' . $user->company_id;
        }

		// What other sites we might need to omit
		$omit_sites = ( !$user->has_permission(8) ) ? ', 96, 114, 115, 116' : '';

		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";

        $sql = "SELECT a.`website_id`, a.`domain`, a.`title`, b.`user_id`, b.`company_id`, b.`contact_name`, b.`store_name`, IF ( '' = b.`cell_phone`, b.`work_phone`, b.`cell_phone` ) AS phone, c.`contact_name` AS online_specialist FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) $where GROUP BY a.`website_id` $order_by LIMIT $limit";

        if ( 0 == count( $values ) ) {
            // Get the websites
            $accounts = $this->get_results( $sql );
        } else {
            $accounts = $this->prepare( $sql, 's', $values )->get_results();
        }

		return $accounts;
	}

	/**
	 * Count all the websites
	 *
     * @param User $user
	 * @param array $variables
	 * @return int
	 */
	public function count_all( $user, $variables ) {
        // Get the variables
		list( $where, $values ) = $variables;

        if ( 251 == $user->id ) {
            $where = ( empty( $where ) ) ? ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user->company_id . ' )' : $where . ' AND ( a.`social_media` = 1 OR b.`company_id` = ' . $user->company_id . ' )';
        } else {
            // If they are below 8, that means they are a partner
            if ( !$user->has_permission(8) )
                $where = ( empty( $where ) ) ? ' AND b.`company_id` = ' . $user->company_id : $where . ' AND b.`company_id` = ' . $user->company_id;
        }

		// What other sites we might need to omit
		$omit_sites = ( !$user->has_permission(8) ) ? ', 96, 114, 115, 116' : '';

		// Form the where
		$where = ( empty( $where ) ) ? "WHERE a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )" : "WHERE 1 $where AND a.`website_id` NOT IN ( 75, 76, 77, 95{$omit_sites} )";

		// Get the website count
		$sql = "SELECT COUNT( DISTINCT a.`website_id` ) FROM `websites` as a LEFT JOIN `users` as b ON ( a.`user_id` = b.`user_id` ) LEFT JOIN `users` AS c ON ( a.`os_user_id` = c.`user_id` ) $where";

        if ( 0 == count ( $values ) ) {
            // Get the websites
            $website_count = $this->get_var( $sql );
        } else {
            $website_count = $this->prepare( $sql, 's', $values )->get_var();
        }

		return $website_count;
	}
}
