<?php
/**
 * Handles all the craiglist functions
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Craigslist extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
	}
	
	/**
	 * Gets craigslist ads
	 *
	 * @param array $variables( $where, $order_by, $limit )
	 * @return array $craigslist_ads
	 */
	public function get_craigslist_ads( $variables ) {
		// Get the variables
		list( $where, $order_by, $limit ) = $variables;
		
		$craigslist_ads = $this->db->get_results( "SELECT a.`craigslist_ad_id`, a.`text`, b.`headline`, c.`name` AS `product_name`, c.`sku`, a.`date_created`, a.`date_posted` FROM `craigslist_ads` AS a LEFT JOIN `craigslist_ad_headlines` AS b ON ( a.`craigslist_ad_id` = b.`craigslist_ad_id` ) LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) WHERE a.`active` = 1 $where GROUP BY a.`craigslist_ad_id` $order_by LIMIT $limit", ARRAY_A );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $craigslist_ads;
	}

   	/**
	 * Countscraigslist ads
	 *
	 * @param string $where
	 * @return int
	 */
	public function count_craigslist_ads( $where ) {
		$craiglist_ad_count = $this->db->get_var( "SELECT COUNT( DISTINCT a.`craigslist_ad_id` ) FROM `craigslist_ads` AS a LEFT JOIN `craigslist_ad_headlines` AS b ON ( a.`craigslist_ad_id` = b.`craigslist_ad_id` ) LEFT JOIN `products` AS c ON( a.product_id = c.product_id ) WHERE a.`active` = 1 $where GROUP BY a.`craigslist_ad_id`" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to count craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}

		return $craiglist_ad_count;
	}
	
	/**
	 * Gets a single ad
	 *
	 * @param int $craigslist_ad_id
	 * @return array
	 */
	public function get( $craigslist_ad_id ) {
        // Type Juggling
        $craigslist_ad_id = (int) $craigslist_ad_id;

		$ad = $this->db->get_row( "SELECT a.`craigslist_ad_id`, a.`product_id`, a.`text`, a.`price`,  GROUP_CONCAT( b.`headline` SEPARATOR '`' ) AS headlines, c.`title` AS store_name, d.`name` AS product_name,
												 d.`sku`, UNIX_TIMESTAMP( a.`date_created` ) AS date_created, UNIX_TIMESTAMP( a.`date_posted` ) AS date_posted
												 FROM `craigslist_ads` AS a
												 LEFT JOIN `craigslist_ad_headlines` AS b ON ( a.`craigslist_ad_id` = b.`craigslist_ad_id` )
												 LEFT JOIN `websites` AS c ON ( a.`website_id` = c.`website_id` )
												 LEFT JOIN `products` AS d ON ( a.product_id = d.product_id )
												 WHERE a.`craigslist_ad_id` = $craigslist_ad_id GROUP BY a.`craigslist_ad_id`", ARRAY_A );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ads.', __LINE__, __METHOD__ );
			return false;
		}

        // Adjust the headlines
        $ad['headlines'] = explode( '`', $ad['headlines'] );
		
		// Get markets
        $ad['craigslist_markets'] = $this->db->get_col( "SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ad markets.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $ad;
	}

    /**
	 * Gets a random headline
	 *
	 * @param int $category_id
	 * @return string
	 */
	public function get_random_headline( $category_id ) {
        // Type Juggling
        $category_id = (int) $category_id;

		$headlines = $this->db->get_col( "SELECT `headline` FROM `craigslist_headlines` WHERE `category_id` = $category_id" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist headline.', __LINE__, __METHOD__ );
			return false;
		}

        // Get a random headline
        $headline = $headlines[rand( 0, count( $headlines ) - 1 )];

		return ( isset( $headline ) ) ? $headline : '';
	}
	
	/**
	 * Creates a new Craigslist ad
	 *
	 * @param int $product_id
	 * @param array $headlines
	 * @param string $text
     * @param float $price
     * @param array $craigslist_market_ids
	 * @return int craigslist_ad_id
	 */
	public function create( $product_id, $headlines, $text, $price, $craigslist_market_ids ) {
        global $user;

        $this->db->insert( 'craigslist_ads', array(
            'website_id' => $user['website']['website_id']
            , 'product_id' => $product_id
            , 'text' => $text
            , 'price' => $price
            , 'date_created' => dt::date( "Y-m-d H:i:s" )
        ), 'iisds' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to create Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

        // Get the craigslist ad ID
        $craigslist_ad_id = $this->db->insert_id;

        // Set the headlines/markets
        $this->set_headlines( $craigslist_ad_id, $headlines );
        $this->set_markets( $craigslist_ad_id, $craigslist_market_ids );

		return $craigslist_ad_id;
	}

    /**
	 * Updates an existing Craigslist ad
	 *
     * @param int $craigslist_ad_id
	 * @param int $product_id
	 * @param array $headlines
	 * @param string $text
     * @param float $price
     * @param array $craigslist_market_ids
	 * @return bool
	 */
	public function update( $craigslist_ad_id, $product_id, $headlines, $text, $price, $craigslist_market_ids ) {
		global $user;

		$this->db->update( 'craigslist_ads', array(
            'product_id' => $product_id
            , 'text' => $text
            , 'price' => $price
        ), array( 'craigslist_ad_id' => $craigslist_ad_id, 'website_id' => $user['website']['website_id'] ), 'isd', 'ii' );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

        // Set the headlines/markets
        $this->set_headlines( $craigslist_ad_id, $headlines );
        $this->set_markets( $craigslist_ad_id, $craigslist_market_ids );

		return true;
	}
	
	/**
	 * Deletes a craigslist ad from the database
	 *
	 * @param int $craigslist_ad_id
	 * @return bool
	 */
	public function delete( $craigslist_ad_id ) {
        global $user;
		
		if ( !$this->_delete_from_primus( $craigslist_ad_id ) )
			return false;
		
		$this->db->update( 'craigslist_ads', array( 'active' => '0' ), array( 'craigslist_ad_id' => $craigslist_ad_id, 'website_id' => $user['website']['website_id'] ), 'i', 'ii' );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to delete Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}
	
	/**
	 * Clones a craigslist ad from the database
	 *
	 * @var int $craigslist_ad_id
	 * @return bool false if couldn't delete
	 */
	public function copy( $craigslist_ad_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $craigslist_ad_id = (int) $craigslist_ad_id;

        $this->db->query( "INSERT INTO `craigslist_ads` ( `website_id`, `product_id`, `text`, `price`, `date_created` ) SELECT `website_id`, `product_id`, `text`, `price`, NOW() FROM `craigslist_ads` WHERE `craigslist_ad_id` = $craigslist_ad_id AND `website_id` = $website_id" );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to copy Craigslist Ad.', __LINE__, __METHOD__ );
			return false;
		}

        // Get Craigslist ad id
        $new_craigslist_ad_id = (int) $this->db->insert_id;

        // We need to get the new ID
        if ( !$new_craigslist_ad_id )
            return false;

        // Copy headlines
        $this->db->query( "INSERT INTO `craigslist_ad_headlines` ( `craigslist_ad_id`, `headline` ) SELECT $new_craigslist_ad_id, `headline` FROM `craigslist_ad_headlines` WHERE `craigslist_ad_id` = $craigslist_ad_id" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to copy Craigslist Ad Headlines.', __LINE__, __METHOD__ );
			return false;
		}

		return true;
	}

    /**
     * Set Headlines
     *
     * @param int $craigslist_ad_id
     * @param array $headlines
     * @return bool
     */
    public function set_headlines( $craigslist_ad_id, $headlines ) {
        global $user;

        // Type Juggling
        $craigslist_ad_id = (int) $craigslist_ad_id;
        $website_id = (int) $user['website']['website_id'];

        // First delete all the ads
        $this->db->query( "DELETE a.* FROM `craigslist_ad_headlines` AS a LEFT JOIN `craigslist_ads` AS b ON ( a.`craigslist_ad_id` = b.`craigslist_ad_id` ) WHERE a.`craigslist_ad_id` = $craigslist_ad_id AND b.`website_id` = $website_id" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to delete Craigslist Ad Headlines.', __LINE__, __METHOD__ );
			return false;
		}

        // Insert headlines
        $values = '';

        if ( is_array( $headlines ) )
        foreach ( $headlines as $h ) {
            // We don't want blank values
            if ( empty( $h ) )
                continue;

            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $craigslist_ad_id, '" . $this->db->escape( stripslashes( $h ) ) . "' )";
        }

        // If there are no values to add, we're done
        if ( empty( $values ) )
            return true;

        // Add them!
        $this->db->query( "INSERT INTO `craigslist_ad_headlines` VALUES $values" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to add Craigslist Ad Headlines.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Set Craigslist Markets
     *
     * @param int $craigslist_ad_id
     * @param array $craigslist_market_ids
     * @return bool
     */
    public function set_markets( $craigslist_ad_id, $craigslist_market_ids ) {
        global $user;

        // Type Juggling
        $craigslist_ad_id = (int) $craigslist_ad_id;
        $website_id = (int) $user['website']['website_id'];
		
		// Get current market ids
		$current_craigslist_market_ids = $this->db->get_col( "SELECT `craigslist_market_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Current Craigslist Markets IDs.', __LINE__, __METHOD__ );
			return false;
		}


        // Insert headlines
        $values = '';

        // Make it SQL Safe
        foreach ( $craigslist_market_ids as &$cmid ) {
            $cmid = (int) $cmid;
			
			// We only want to add the ones that we don't have
			if ( in_array( $cmid, $current_craigslist_market_ids ) )
				continue;
			
            if ( !empty( $values ) )
                $values .= ',';

            $values .= "( $craigslist_ad_id, $cmid )";
        }

        // Delete all the ones that are not there
        $this->db->query( "DELETE a.* FROM `craigslist_ad_markets` AS a LEFT JOIN `craigslist_ads` AS b ON ( a.`craigslist_ad_id` = b.`craigslist_ad_id` ) WHERE a.`craigslist_ad_id` = $craigslist_ad_id AND a.`craigslist_market_id` NOT IN (" . implode( ',', $craigslist_market_ids ) . ")AND b.`website_id` = $website_id" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to delete Craigslist Ad Markets.', __LINE__, __METHOD__ );
			return false;
		}

        // If there are no values to add, we're done
        if ( empty( $values ) )
            return true;

        // Add them!
        $this->db->query( "INSERT INTO `craigslist_ad_markets` ( `craigslist_ad_id`, `craigslist_market_id` ) VALUES $values ON DUPLICATE KEY UPDATE `craigslist_ad_id` = $craigslist_ad_id" );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to add Craigslist Ad Markets.', __LINE__, __METHOD__ );
			return false;
		}

        return true;
    }

    /**
     * Get Craigslist Market
     *
     * @param int $craigslist_market_id
     * @return array
     */
    public function get_craigslist_market( $craigslist_market_id ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];
        $craigslist_market_id = (int) $craigslist_market_id;

        $market = $this->db->get_row( "SELECT a.`craigslist_market_id`, CONCAT( a.`city`, ', ', IF( '' <> a.`area`, CONCAT( a.`state`, ' - ', a.`area` ), a.`state` ) ) AS market, b.`market_id` FROM `craigslist_markets` AS a LEFT JOIN `craigslist_market_links` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) WHERE a.`craigslist_market_id` = $craigslist_market_id AND b.`website_id` = $website_id", ARRAY_A );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Craigslist Market.', __LINE__, __METHOD__ );
			return false;
		}

        return $market;
    }

    /**
     * Get Craigslist Markets
     *
     * @param int $craigslist_ad_id [optional] If specified it will get the craiglist markets specified by the ad rather than all of them
     * @return array
     */
    public function get_craigslist_markets( $craigslist_ad_id = NULL ) {
        global $user;

        // Type Juggling
        $website_id = (int) $user['website']['website_id'];

        if ( is_null( $craigslist_ad_id ) ) {
            $markets = $this->db->get_results( "SELECT a.`craigslist_market_id`, CONCAT( a.`city`, ', ', IF( '' <> a.`area`, CONCAT( a.`state`, ' - ', a.`area` ), a.`state` ) ) AS market, b.`market_id` FROM `craigslist_markets` AS a LEFT JOIN `craigslist_market_links` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) WHERE b.`website_id` = $website_id", ARRAY_A );
        } else {
            // Type Juggling
            $craigslist_ad_id = (int) $craigslist_ad_id;

            $markets = $this->db->get_results( "SELECT a.`craigslist_market_id`, CONCAT( a.`city`, ', ', IF( '' <> a.`area`, CONCAT( a.`state`, ' - ', a.`area` ), a.`state` ) ) AS market, c.`market_id` FROM `craigslist_markets` AS a LEFT JOIN `craigslist_ad_markets` AS b ON ( a.`craigslist_market_id` = b.`craigslist_market_id` ) LEFT JOIN `craigslist_market_links` AS c ON ( b.`craigslist_market_id` = c.`craigslist_market_id` ) WHERE b.`craigslist_ad_id` = $craigslist_ad_id AND c.`website_id` = $website_id", ARRAY_A );
        }

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Craigslist Markets.', __LINE__, __METHOD__ );
			return false;
		}

        return $markets;
    }

    /**
     * Post Craigslist Ad
     *
     * @param int $craigslist_ad_id
     * @param string $text
     * @return bool
     */
    public function post_ad( $craigslist_ad_id, $text ) {
        // Get craigslist markets
        $markets = $this->get_craigslist_markets( $craigslist_ad_id );

        // If we don't have markets, then we can't post
        if ( !$markets || 0 == count( $markets ) )
            return false;

        global $user;

        $p = new Products();
		$c = new Categories();

        // Get the ad
        $ad = $this->get( $craigslist_ad_id );
		
        // Make sure we have the ad
        if ( !$ad )
            return false;

        // Make sure the headlines aren't empty
        foreach ( $ad['headlines'] as $hl ) {
            if ( empty( $hl ) )
                return false;
        }

        // Get the product
        $product = $p->get_product( $ad['product_id'] );
        $parent_category = $c->get_top( $product['category_id'] );

        // Make sure we have the product
        if ( !$product )
            return false;

        $craigslist_tags = $this->db->get_results( "SELECT `craigslist_tag_id`, `object_id`, `type` FROM `craigslist_tags` WHERE ( `type` = 'category' AND `object_id` IN( " . $product['category_id'] . ", " . $parent_category['category_id'] . " ) ) OR ( `type` = 'product' AND `object_id` = " . $product['product_id'] . ")", ARRAY_A );

        // Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get Craigslist Tags.', __LINE__, __METHOD__ );
			return false;
		}

        // Declare variables
        $product_tag_id = $category_tag_id = $parent_category_tag_id = $tags = false;

        if ( is_array( $craigslist_tags ) )
        foreach ( $craigslist_tags as $ct ) {
            switch ( $ct['type'] ) {
                case 'category':
                    if ( $ct['object_id'] == $ad['category_id'] ) {
                        $category_tag_id = $ct['craigslist_tag_id'];
                    } elseif ( $ct['object_id'] == $parent_category['category_id'] ) {
                        $parent_category_tag_id = $ct['craigslist_tag_id'];
                    }
                break;

                case 'product':
                    $product_tag_id = $ct['craigslist_tag_id'];
                break;
            }
        }

        // Create product tag
        if ( !$product_tag_id ) {
            $tags[$ad['product_id']] = array(
                'type' => 'item'
                , 'name' => $product['sku']
            );
        }

        // Create category tag
        if ( !$category_tag_id ) {
            // Get the category
            $category = $c->get( $product['category_id'] );

            // Add it to the tags array
            $tags[$product['category_id']] = array(
                'type' => 'category'
                , 'name' => $category['name']
            );
        }

        // Create category tag
        if ( !$parent_category_tag_id ) {
            // Add it to the tags array
            $tags[$parent_category['category_id']] = array(
                'type' => 'category'
                , 'name' => $parent_category['name']
            );
        }

        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );

        // If it's an array
        if ( is_array( $tags ) ) {
            // To insert into our database once done
            $tag_values = '';

            $tag_response = $craigslist->add_tags( $tags );

            if ( is_array( $tag_response ) || is_object( $tag_response ) )
            foreach ( $tag_response as $object_id => $tr ) {
                switch ( $tr->type ) {
                    case 'item':
                        // Get the product tag ID
                        $product_tag_id = $tr->id;

                        // Make sure we create separation
                        if ( !empty( $tag_values ) )
                            $tag_values .= ',';

                        // Get the new tag values
                        $tag_values .= '( ' . (int) $tr->id . ', ' . (int) $object_id . ", 'product' )";
                    break;

                    case 'category':
                        if ( $object_id == $product['category_id'] ) {
                            // Get the category tag ID
                            $category_tag_id = $tr->id;
                        } elseif( $object_id == $parent_category['category_id'] ) {
                            // Get the parent category tag ID
                            $parent_category_tag_id = $tr->id;
                        }

                        // Make sure we create separation
                        if ( !empty( $tag_values ) )
                            $tag_values .= ',';

                        // Get the new tag values
                        $tag_values .= '( ' . (int) $tr->id . ', ' . (int) $object_id . ", 'category' )";
                    break;
                }
            }

            // Insert into our database
            if ( !empty( $tag_values ) ) {
                $this->db->query( "INSERT INTO `craigslist_tags` ( `craigslist_tag_id`, `object_id`, `type` ) VALUES $tag_values ON DUPLICATE KEY UPDATE `object_id` = VALUES( `object_id` ), `type` = VALUES( `type` )" );

                // Handle any error
                if( $this->db->errno() ) {
                    $this->err( 'Failed to Insert Craigslist tags.', __LINE__, __METHOD__ );
                    return false;
                }
            }
        }

        // Set post tags
        $post_tags = array( $product_tag_id, $category_tag_id, $parent_category_tag_id );

        // Get product URL
        if ( $p->get_website_product( $product['product_id'] ) ) {
            // Make Product URL
        	$product_url = $c->category_url( $product['category_id'] ) . $product['slug'] . '/';
        } else {
            // We don't have a product URL -- it's not hosted on our site
            $product_url = '';
        }

        // Get the product image URL
        $product_image_url = 'http://' . $product['industry'] . '.retailcatalog.us/products/' . $product['product_id'] . '/large/' . $product['image'];

        $primus_product_ids = array();
        $success = true;
		
		// Delete add from Primus
		if ( !$this->_delete_from_primus( $craigslist_ad_id, $craigslist ) )
			return false;
        
        // Post the ad in each market
        foreach ( $markets as $m ) {
            $response = (object) array( 'status' => 'RETRY' );
            $i = 0;

            while ( 'RETRY' == $response->status && $i < 10 ) {
                $response = $craigslist->add_ad_product( $m['market_id'], $post_tags, $product_url, $product_image_url, $ad['price'], $ad['headlines'], $text );
				
                if ( 'SUCCESS' == $response->status ) {
                    $primus_product_ids[$m['craigslist_market_id']] = $response->product_id;
                } elseif ( 'ERROR' == $response->status ) {
                    $success = false;
                    break 2;
                }

                $i++;
            }
        }
		
        // Get the date
        $date = new DateTime();

        $this->db->update( 'craigslist_ads', array( 'date_posted' => $date->format('Y-m-d H:i:s') ), array( 'craigslist_ad_id' => $craigslist_ad_id ), 's', 'i' );

        // Handle any error
        if( $this->db->errno() ) {
            $this->err( 'Failed to update primus product id and posted.', __LINE__, __METHOD__ );
            return false;
        }
		
        // Update primus product links
		$statement = $this->db->prepare( "UPDATE `craigslist_ad_markets` SET `primus_product_id` = ? WHERE `craigslist_ad_id` = $craigslist_ad_id AND `craigslist_market_id` = ?" );
		$statement->bind_param( 'ii', $primus_product_id, $craigslist_market_id );

		foreach ( $primus_product_ids as $craigslist_market_id => $primus_product_id ) {
			$statement->execute();

			// Handle any error
			if ( $statement->errno ) {
				$this->db->m->error = $statement->error;
				$this->err( 'Failed to update craigslist - primus product id', __LINE__, __METHOD__ );
				return false;
			}
		}
		
        return $success;
    }
	
	/**
	 * Delete an ad from primus
	 *
	 * @param int $craigslist_ad_id
	 * @param Craigslist_API $craigslist
	 * @return bool
	 */
	private function _delete_from_primus( $craigslist_ad_id, $craigslist = NULL ) {
		global $user;

		// Type Juggling
		$craigslist_ad_id = (int) $craigslist_ad_id;
		$website_id = (int) $user['website']['website_id'];

		// Make sure this users has permissions to this user
		$valid = $this->db->get_var( "SELECT `craigslist_ad_id` FROM `craigslist_ads` WHERE `craigslist_ad_id` = $craigslist_ad_id AND `website_id` = $website_id" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get craigslist ad id - validation check.', __LINE__, __METHOD__ );
			return false;
		}
		
		if ( !$valid )
			return false;
		
		if ( is_null( $craigslist ) ) {
			// Load the library
			library( 'craigslist-api' );
	
			// Create API object
			$craigslist = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
		}
		
		
		// Delete old ads and upate the status so that
        $old_primus_product_ids = $this->db->get_col( "SELECT `primus_product_id` FROM `craigslist_ad_markets` WHERE `craigslist_ad_id` = $craigslist_ad_id" );
		
		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to get old primus product ids.', __LINE__, __METHOD__ );
			return false;
		}
		
        if ( is_array( $old_primus_product_ids ) )
        foreach ( $old_primus_product_ids as $key => $oppid ) {
            if ( empty( $oppid ) || '0' == $oppid )
                unset( $old_primus_product_ids[$key] );
        }
		
		// See if we have anything to do
        if ( !is_array( $old_primus_product_ids ) || 0 == count( $old_primus_product_ids ) )
			return true;
		
		// Make sure we successfully remove the old IDs
		if ( !$craigslist->delete_ad_product( $old_primus_product_ids ) )
			return false;

		// Now update the database
		$this->db->update( 'craigslist_ads', array( 'date_posted' => '0000-00-00 00:00:00' ), array( 'craigslist_ad_id' => $craigslist_ad_id ), 's', 'i' );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update craigslist ad date posted.', __LINE__, __METHOD__ );
			return false;
		}

		// Now remove the old primus product_ids
		$this->db->update( 'craigslist_ad_markets', array( 'primus_product_id' => 0 ), array( 'craigslist_ad_id' => $craigslist_ad_id ), 'i', 'i' );

		// Handle any error
		if( $this->db->errno() ) {
			$this->err( 'Failed to update craigslist ad date posted.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return bool
	 */
	private function err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}