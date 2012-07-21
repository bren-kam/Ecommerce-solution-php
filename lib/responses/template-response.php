<?php
/**
 * This will return the HTML response for templates
 */
class TemplateResponse extends Response {
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

    /**
     * Pass in which file will be the View
     *
     * @param string $file_to_render
     * @param string $title
     */
    public function __construct( $file_to_render, $title ) {
        $this->_file_to_render = $file_to_render;
        $this->add( 'title', $title . ' | ' . TITLE );
    }

    /**
     * Add data to variables
     *
     * @param string|array $key
     * @param string $value [optional]
     */
    public function add( $key, $value = '' ) {
        if ( is_array( $key ) ) {
            $this->variables = array_merge( $this->variables, $key );
        } else {
            $this->variables[$key] = $value;
        }
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

    /**
     * Including the file
     */
    public function respond() {
        // Define defaults for resources
        $this->add( 'resources', new Resources() );
        $this->add( 'template', new Template() );

        // Make available the variables
        extract( $this->variables );

        require VIEW_PATH . 'header.php';
        require VIEW_PATH . $this->_file_to_render . '.php';
        require VIEW_PATH . 'footer.php';
    }

}
