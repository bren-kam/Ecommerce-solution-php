<?php
class KnowledgeBaseCategory extends ActiveRecordBase {
    const SECTION_ADMIN = 'admin';
    const SECTION_ACCOUNT = 'account';

    // The columns we will have access to
    public $id, $parent_id, $section, $name;

    // Artificial field
    public $depth;

    // Hold the categories
    public static $categories, $categories_by_parent;

    /**
     * Setup the account initial data
     *
     * @param string $section ('account','admin')
     */
    public function __construct( $section = NULL ) {
        parent::__construct( 'kb_category' );

        if ( !is_null( $section ) )
            $this->section = $section;
    }

    /**
     * Get a category
     *
     * @param int $id
     * @return KnowledgeBaseCategory
     */
    public function get( $id ) {
        if ( !isset( self::$categories[$id] ) ) {
            $this->prepare(
                'SELECT `id`, COALESCE( `parent_id`, 0 ) AS parent_id, `section`, `name` FROM `kb_category` WHERE `id` = :id'
                , 'i'
                , array( ':id' => $id )
            )->get_row( PDO::FETCH_INTO, $this );
        } else {
            $this->id = $id;
            $this->parent_id = self::$categories[$id]->parent_id;
            $this->name = self::$categories[$id]->name;
        }
    }

    /**
     * Get All Categories
     *
     * @return array
     */
    public function get_all() {
		$categories_array = $this->prepare(
            'SELECT `id`, COALESCE( `parent_id`, 0 ) AS parent_id, `section`, `name` FROM `kb_category` WHERE `section` = :section ORDER BY `parent_id` ASC'
            , 's'
            , array( ':section' => $this->section )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseCategory' );
        $categories = array();

        foreach ( $categories_array as $c ) {
            $categories[$c->id] = $c;
        }

        KnowledgeBaseCategory::$categories = $categories;

        return KnowledgeBaseCategory::$categories;
    }

    /**
     * Search
     *
     * @param string $search
     * @return KnowledgeBaseCategory[]
     */
    public function search( $search ) {
        return $this->prepare(
            "SELECT `id`, COALESCE( `parent_id`, 0 ) AS parent_id, `section`, `name` FROM `kb_category` WHERE `name` LIKE :search"
            , 's'
            , array( ':search' => '%' . $search . '%' )
        )->get_results( PDO::FETCH_CLASS, 'KnowledgeBaseCategory' );
    }

    /**
     * Get all children categories
     *
     * @param int $id
     * @param array $child_categories [optional] Pseudo-optional -- shouldn't be filled in
     * @return KnowledgeBaseCategory[]
     */
    public function get_all_children( $id, array $child_categories = array() ) {
        $categories = $this->get_by_parent( $id );

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
     * @param int $id
     * @param array $parent_categories [optional] Pseudo-optional -- shouldn't be filled in
     * @return KnowledgeBaseCategory[]
     */
    public function get_all_parents( $id, array $parent_categories = array() ) {
        if ( 0 == $id )
            return $parent_categories;

        $category = new KnowledgeBaseCategory( $this->section );
        $category->get( $id );

        if ( 0 != $category->parent_id ) {
            $parent_category = new KnowledgeBaseCategory( $this->section );
            $parent_category->get( $category->parent_id );

            $parent_categories[] = $parent_category;

            $parent_categories = $this->get_all_parents( $category->parent_id, $parent_categories );
        }

        return $parent_categories;
    }

    /**
     * Get Categories By Parent
     *
     * @param int $parent_id
     * @return KnowledgeBaseCategory[]
     */
    public function get_by_parent( $parent_id ) {
        // Get the categories
        $categories_by_parent = KnowledgeBaseCategory::$categories_by_parent;

        if ( is_null( $categories_by_parent ) ) {
            $this->sort_by_parent();

            $categories_by_parent = KnowledgeBaseCategory::$categories_by_parent;
        }

        return ( $this->has_children( $parent_id ) ) ? $categories_by_parent[$parent_id] : array();
    }

    /**
     * Check to see if a category has a parent
     *
     * @param int $id [optional]
     * @return bool
     */
    public function has_children( $id = NULL ) {
        // Get the categories
        $categories_by_parent = KnowledgeBaseCategory::$categories_by_parent;

        if ( is_null( $categories_by_parent ) ) {
            $this->sort_by_parent();

            $categories_by_parent = KnowledgeBaseCategory::$categories_by_parent;
        }

        if ( is_null( $id ) )
            $id = $this->id;

        return isset( $categories_by_parent[$id] );
    }

    /**
     * Create a Category
     */
    public function create() {
        $this->insert( array(
            'parent_id' => $this->parent_id
            , 'name' => $this->name
            , 'section' => $this->section
        ), 'iss' );

        $this->id = $this->get_insert_id();
    }

    /**
     * Update a Category
     */
    public function save() {
        // We cannot let this happen
        if ( $this->id == $this->parent_id )
            return;

        parent::update( array(
            'parent_id' => $this->parent_id
            , 'name' => $this->name
        ), array( 'id' => $this->id ), 'is', 'i' );
    }

    /**
     * Delete a category and dependents
     */
    public function delete() {
        if ( is_null( $this->id ) )
            return;

        $this->prepare(
            'DELETE FROM `kb_category` WHERE ( `id` = :id OR `parent_id` = :parent_id ) AND `section` = :section'
            , 'iis'
            , array( ':id' => $this->id, ':parent_id' => $this->id, ':section' => $this->section )
        )->query();
    }

    /**
     * Sort by parent
     */
    protected function sort_by_parent() {
        // Get categories if they exist
        $categories = KnowledgeBaseCategory::$categories;

        // If they don't exist, get them
        if ( is_null( $categories ) )
            self::$categories = $categories = $this->get_all();

        // Sort by parent
        $categories_by_parent = array();

        foreach ( $categories as $category ) {
            $categories_by_parent[$category->parent_id][] = $category;
        }

        KnowledgeBaseCategory::$categories_by_parent = $categories_by_parent;
    }

    /**
     * Sort by hierarchy
     *
     * @param int $parent_id [optional]
     * @param int $depth [optional]
     * @param array $hierarchical_categories
     * @return KnowledgeBaseCategory[]
     */
    public function sort_by_hierarchy( $parent_id = 0, $depth = 0, array $hierarchical_categories = array() ) {
        $categories = $this->get_by_parent( $parent_id );

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
