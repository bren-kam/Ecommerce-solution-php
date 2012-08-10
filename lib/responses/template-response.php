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
     * @param Resources $resources
     * @param string $file_to_render
     * @param string $title
     */
    public function __construct( $resources, $file_to_render, $title ) {
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
     * @return Template Response
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
     * @return TemplateResponse
     */
    public function add_title( $title ) {
        $this->variables['title'] = $title . ' | ' . $this->variables['title'];

        return $this;
    }

    /**
     * Select section/page
     *
     * @param $section
     * @param $page [optional]
     * @return TemplateResponse
     */
    public function select( $section, $page = '' ) {
        $this->set( $section, true );

        if ( !empty( $page ) )
            $this->set( $page, true );

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

        // Get notifications
        $notification = new Notification();
        $notifications = $notification->get_by_user( $user->user_id );

        if ( is_array( $notifications ) ) {
            // We ony want to show them once
            $notification->delete_by_user( $user->user_id );

            foreach ( $notifications as $n ) {
                $notification_html = '<div class="notification sticky hidden">';
                $notification_html .= '<a class="close" href="#"><img src="/images/icons/close.png" alt="' . _('Close') . '" /></a>';
                $notification_html .= '<p>' . $n->message . '</p>';
                $notification_html .= '</div>';

                $template->add_top( $notification_html );
            }
        }

        require VIEW_PATH . 'header.php';

        require VIEW_PATH . $this->_file_to_render . '.php';

        require VIEW_PATH . 'footer.php';
    }
}
