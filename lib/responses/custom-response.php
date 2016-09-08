<?php
/**
 * This will return the HTML response for templates
 */
class CustomResponse extends Response {
    /**
     * Hold the View file that we will be including
     * @var string
     */
    private $_file_to_render = NULL;
    /**
     * Hold the errors that we will be including
     * @var array
     */
    private $_errors = array();

    /**
     * Hold other variables
     * @var array
     */
    protected $variables = array( 'title' => '' );


    /*
     * Custom Header
     * @var string
     */

    protected $custom_header;
    /*
     * Custom Header
     * @var string
     */

    protected $custom_footer;

    /**
     * Pass in which file will be the View
     *
     * @param Resources $resources
     * @param string $file_to_render
     * @param string $title [optional]
     */
    public function __construct( $resources, $file_to_render, $title = '' ) {
        $this->_file_to_render = $file_to_render;
        $this->set( array(
            'title' => $title
            , 'resources' => $resources
        ) );

        if ( isset( $_POST ) )
            $this->set( format::camel_case_to_underscore_deep( $_POST ) );

        if ( isset( $_GET ) )
            $this->set( format::camel_case_to_underscore_deep( $_GET ) );
    }

    /**
     * Set data to variables
     *
     * @param string|array $key
     * @param string $value [optional]
     * @return CustomResponse
     */
    public function set( $key, $value = '' ) {
        if ( is_array( $key ) ) {
            $this->variables = array_merge( $this->variables, $key );
        } else {
            $this->variables[$key] = $value;
        }

        return $this;
    }

    /**
     * Add Title
     *
     * @param string $title
     * @return CustomResponse
     */
    public function add_title( $title ) {
        $this->variables['title'] = $title . ' | ' . $this->variables['title'];

        return $this;
    }

    /**
     * Select knowledge base page
     *
     * @param int $kb_page_id
     * @return CustomResponse
     */
    public function kb( $kb_page_id ) {
        $article = new KnowledgeBaseArticle();
        $this->set( 'kbh_articles', $article->get_by_page( $kb_page_id ) );

        return $this;
    }

    /**
     * Check to see if we have an error
     * @return bool
     */
    protected function has_error() {
        return count( $this->_errors ) > 0;
    }

    /**
     * Add Error
     *
     * @param string $error
     */
    public function add_error( $error ) {
        $this->_errors[] = _($error);
    }

    /**
     * Get Errors
     *
     * @return array
     */
    protected function get_errors() {
        return $this->_errors;
    }

    public function set_custom_header($header){
        $this->custom_header = $header;
    }

    public function set_custom_footer($footer){
        $this->custom_footer = $footer;
    }
    
    /**
     * Including the file
     */
    public function respond() {
        /**
         * @var User $user
         */
        extract( $this->variables );

        // Create template function class
        $template = new Template( $this->variables );

        if($this->custom_header)
            require VIEW_PATH . $this->custom_header . '.php';
        
        require VIEW_PATH . $this->_file_to_render . '.php';

        if($this->custom_footer)
            require VIEW_PATH . $this->custom_footer . '.php';
        
    }
}
