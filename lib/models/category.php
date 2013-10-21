<?php
class Category extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $category_id, $parent_category_id, $name, $slug, $google_taxonomy, $sequence;

    // Artificial field
    public $depth;

    // Hold the categories
    public static $categories, $categories_by_parent;

    /**
     * Setup the account initial data
     */
    public function __construct() {
        parent::__construct( 'categories' );

        // We want to make sure they match
        if ( isset( $this->category_id ) )
            $this->id = $this->category_id;
    }

    /**
     * Get a category
     *
     * @param int $category_id
     * @return Category
     */
    public function get( $category_id ) {
        if ( !isset( Category::$categories[$category_id] ) ) {
            $this->prepare(
                'SELECT * FROM `categories` WHERE `category_id` = :category_id'
                , 'i'
                , array( ':category_id' => $category_id )
            )->get_row( PDO::FETCH_INTO, $this );

            $this->id = $this->category_id;
            Category::$categories[$this->id]->id = Category::$categories[$this->id]->category_id = $this->category_id;
            Category::$categories[$this->id]->parent_category_id = $this->parent_category_id;
            Category::$categories[$this->id]->name = $this->name;
            Category::$categories[$this->id]->slug = $this->slug;
            Category::$categories[$this->id]->google_taxonomy = $this->google_taxonomy;
            Category::$categories[$this->id]->sequence = $this->sequence;
        } else {
            $this->id = $this->category_id = $category_id;
            $this->parent_category_id = Category::$categories[$category_id]->parent_category_id;
            $this->name = Category::$categories[$category_id]->name;
            $this->slug = Category::$categories[$category_id]->slug;
            $this->google_taxonomy = Category::$categories[$category_id]->google_taxonomy;
            $this->sequence = Category::$categories[$category_id]->sequence;
        }
    }

    /**
     * Get All Categories
     *
     * @return array
     */
    public function get_all() {
		$categories_array = $this->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug`, `google_taxonomy` FROM `categories` ORDER BY `parent_category_id` ASC, sequence ASC", PDO::FETCH_CLASS, 'Category' );
        $categories = array();

        foreach ( $categories_array as $c ) {
            $categories[$c->id] = $c;
        }

        Category::$categories = $categories;

        return Category::$categories;
    }

    /**
     * Get all children categories
     *
     * @param int $category_id
     * @param array $child_categories [optional] Pseudo-optional -- shouldn't be filled in
     * @return Category[]
     */
    public function get_all_children( $category_id, array $child_categories = array() ) {
        $categories = $this->get_by_parent( $category_id );

        if ( is_array( $categories ) )
        foreach ( $categories as $category ) {
            $child_categories[] = $category;

            $child_categories = $this->get_all_children( $category->id, $child_categories );
        }

        return $child_categories;
    }

    /**
     * Get all parent categories
     *
     * @param int $category_id
     * @param array $parent_categories [optional] Pseudo-optional -- shouldn't be filled in
     * @return Category[]
     */
    public function get_all_parents( $category_id, array $parent_categories = array() ) {
        if ( 0 == $category_id )
            return $parent_categories;

        $category = new Category();
        $category->get( $category_id );

        if ( 0 != $category->parent_category_id ) {
            $parent_category = new Category();
            $parent_category->get( $category->parent_category_id );

            $parent_categories[] = $parent_category;

            $parent_categories = $this->get_all_parents( $category->parent_category_id, $parent_categories );
        }

        return $parent_categories;
    }

    /**
	 * Get Top Category
	 *
	 * @param int $category_id
	 */
	public function get_top( $category_id ) {
		if ( 0 == $category_id )
			return;

        $this->get( $category_id );

        if ( 0 != $this->parent_category_id )
            $this->get_top( $this->parent_category_id );
	}

    /**
     * Get all parent ids
     *
     * @param int $category_id
     * @return array
     */
    public function get_all_parent_category_ids( $category_id ) {
        // Get parent categories
        $parent_categories = $this->get_all_parents( $category_id );

        $parent_category_ids = array();

        foreach ( $parent_categories as $pc ) {
            $parent_category_ids[] = $pc->id;
        }

        return $parent_category_ids;
    }

    /**
     * Get Categories By Parent
     *
     * @param int $parent_category_id
     * @return Category[]
     */
    public function get_by_parent( $parent_category_id ) {
        // Get the categories
        $categories_by_parent = Category::$categories_by_parent;

        if ( is_null( $categories_by_parent ) ) {
            $this->sort_by_parent();

            $categories_by_parent = Category::$categories_by_parent;
        }

        return ( $this->has_children( $parent_category_id ) ) ? $categories_by_parent[$parent_category_id] : array();
    }

    /**
     * Get URL
     *
     * @param int $category_id [optional]
     * @return string
     */
    public function get_url( $category_id = NULL ) {
        if ( is_null( $category_id ) )
            $category_id = $this->id;

        if ( empty( Category::$categories ) )
            $this->get_all();

		// If there is no category, return
		if ( !array_key_exists( $category_id, Category::$categories ) )
			return '';

        // Get Main category
        $category = Category::$categories[$category_id];

		// Get the parent cateogires
		$parent_categories = $this->get_all_parents( $category_id );

		// Initialize category URL
		$category_url = '';

        /**
         * @var Category $parent_category
         */
        foreach ( $parent_categories as $parent_category ) {
			$category_url = $parent_category->slug . '/' . $category_url;
		}

		return '/' . $category_url . $category->slug . '/';
    }

    /**
     * Check to see if a category has a parent
     *
     * @param int $category_id [optional]
     * @return bool
     */
    public function has_children( $category_id = NULL ) {
        // Get the categories
        $categories_by_parent = Category::$categories_by_parent;

        if ( is_null( $categories_by_parent ) ) {
            $this->sort_by_parent();

            $categories_by_parent = Category::$categories_by_parent;
        }

        if ( is_null( $category_id ) )
            $category_id = $this->id;

        return isset( $categories_by_parent[$category_id] );
    }

    /**
     * Create a Category
     */
    public function create() {
        $this->insert( array(
            'parent_category_id' => $this->parent_category_id
            , 'name' => strip_tags($this->name)
            , 'slug' => strip_tags($this->slug)
            , 'google_taxonomy' => strip_tags($this->google_taxonomy)
        ), 'isss' );

        $this->id = $this->get_insert_id();
    }

    /**
     * Update a Category
     */
    public function save() {
        // We cannot let this happen
        if ( $this->id == $this->parent_category_id )
            return;

        parent::update( array(
            'parent_category_id' => $this->parent_category_id
            , 'name' => strip_tags($this->name)
            , 'slug' => strip_tags($this->slug)
            , 'google_taxonomy' => strip_tags($this->google_taxonomy)
        ), array( 'category_id' => $this->id ), 'isss', 'i' );
    }

    /**
     * Update the sequence of many categories
     *
     * @param int $parent_category_id
     * @param array $categories
     */
    public function update_sequence( $parent_category_id, array $categories ) {
        // Starting with 0 for a sequence
		$sequence = 0;

		// Prepare statement
		$statement = $this->prepare_raw( 'UPDATE `categories` SET `sequence` = :sequence WHERE `parent_category_id` = :parent_category_id AND `category_id` = :category_id' );
		$statement->bind_param( ':sequence', $sequence, 'i' )
		    ->bind_value( ':parent_category_id', $parent_category_id, 'i' )
		    ->bind_param( ':category_id', $category_id, 'i' );

		// Loop through the statement and update anything as it needs to be updated
		foreach ( $categories as $category_id ) {
			$statement->query();

			$sequence++;
		}
    }

    /**
     * Delete a category and dependents
     */
    public function delete() {
        if ( is_null( $this->id ) )
            return;

        $this->prepare(
            'DELETE FROM `categories` WHERE `category_id` = :category_id OR `parent_category_id` = :parent_category_id'
            , 'ii'
            , array( ':category_id' => $this->id, ':parent_category_id' => $this->id )
        )->query();
    }

    /**
     * Sort by parent
     */
    protected function sort_by_parent() {
        // Get categories if they exist
        $categories = Category::$categories;

        // If they don't exist, get them
        if ( is_null( $categories ) )
            Category::$categories = $categories = $this->get_all();

        // Sort by parent
        $categories_by_parent = array();

        foreach ( $categories as $category ) {
            $categories_by_parent[$category->parent_category_id][] = $category;
        }

        Category::$categories_by_parent = $categories_by_parent;
    }

    /**
     * Sort by hierarchy
     *
     * @param int $parent_category_id [optional]
     * @param int $depth [optional]
     * @param array $hierarchical_categories
     * @return Category[]
     */
    public function sort_by_hierarchy( $parent_category_id = 0, $depth = 0, array $hierarchical_categories = array() ) {
        $categories = $this->get_by_parent( $parent_category_id );

        if ( !is_array( $categories ) )
            return $hierarchical_categories;

        if ( is_array( $categories ) )
        foreach ( $categories as $c ) {
            $c->depth = $depth;

            $hierarchical_categories[] = $c;

            $hierarchical_categories = $this->sort_by_hierarchy( $c->id, $depth + 1, $hierarchical_categories );
        }

        return $hierarchical_categories;
    }
}
