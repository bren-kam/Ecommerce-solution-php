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
     */
    protected $_user;

    /**
     * Pass in which file will be the View
     *
     * @param string $file_to_render
     */
    public function __construct( $file_to_render ) {
        $this->_file_to_render = $file_to_render;
    }

    /**
     * Set User
     *
     * @param User $user
     */
    public function set_user( $user ) {
        $this->_user = $user;
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
        $resources = new Resources();
        $template = new Template();
        $user = $this->_user;

        require VIEW_PATH . 'header.php';
        require VIEW_PATH . $this->_file_to_render . '.php';
        require VIEW_PATH . 'footer.php';
    }

}
