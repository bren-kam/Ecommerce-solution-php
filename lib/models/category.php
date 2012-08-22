<?php
class Category extends ActiveRecordBase {
    // The columns we will have access to
    public $id, $category_id, $parent_category_id, $name, $slug, $sequence;

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
     * Get All Categories
     *
     * @return array
     */
    public function get_all() {
		Category::$categories = $this->get_results( "SELECT `category_id`, `parent_category_id`, `name`, `slug` FROM `categories` ORDER BY `parent_category_id` ASC, sequence ASC", PDO::FETCH_CLASS, 'Category' );

        return Category::$categories;
    }

    /**
     * Get all children categories
     *
     * @param int $category_id
     * @param array $child_categories [optional] Pseudo-optional -- shouldn't be filled in
     * @return array
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
     * Get Categories By Parent
     *
     * @param int $parent_category_id
     * @return array
     */
    public function get_by_parent( $parent_category_id ) {
        // Get the categories
        $categories_by_parent = Category::$categories_by_parent;

        if ( is_null( $categories_by_parent ) ) {
            $this->sort_by_parent();

            $categories_by_parent = Category::$categories_by_parent;
        }

        return ( isset( $categories_by_parent[$parent_category_id] ) ) ? $categories_by_parent[$parent_category_id] : false;
    }

    /**
     * Sort by parent
     */
    protected function sort_by_parent() {
        // Get categories if they exist
        $categories = Category::$categories;

        // If they don't exist, get them
        if ( is_null( $categories ) )
            self::$categories = $categories = $this->get_all();

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
     * @return array
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
