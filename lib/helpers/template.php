<?php
class Template {
    /**
     * Hold available variables
     * @var array
     */
    protected $variables;

    /**
     * Hold available variables
     */
    public function __construct( $variables ) {
        $this->variables = $variables;
    }

    /**
     * Start of the template
     *
     * @param string $title [optional]
     * @param string|bool $sidebar_file [optional]
     * @return string
     */
    public function start( $title = '', $sidebar_file = 'sidebar' ) {
        $start_html = '<div id="content">';

        if ( !empty( $title ) )
            $start_html .= '<h1>' . $title . '</h1><br clear="all" /><br />';

        $start_html .= '<div id="subcontent-wrapper"><div id="subcontent">';

        // Get the sidebar if it's not false
        if ( $sidebar_file ) {
            extract( $this->variables );
            require VIEW_PATH . $this->variables['view_base'] . $sidebar_file . '.php';
        }

        return $start_html;
    }

    /**
     * End of the template
     *
     * @return string
     */
    public function end() {
        return '</div></div></div>';
    }

    /**
     * Set data to variables
     *
     * @param string|array $key
     * @param string $value [optional]
     */
    public function set( $key, $value = '' ) {
        if ( is_array( $key ) ) {
            $this->variables = array_merge( $this->variables, $key );
        } else {
            $this->variables[$key] = $value;
        }
    }

    /**
     * Return the variable if it exists
     *
     * @param string $string
     * @return string
     */
    public function v( $string ) {
        return ( isset( $this->variables[$string] ) ) ? $this->variables[$string] : '';
    }

    /**
     * Spit out the input value if it exists
     *
     * @param string $string
     */
    public function value( $string ) {
        if ( isset( $this->variables[$string] ) )
            echo ' value="' . $this->variables[$string] . '"';
    }

    /**
     * Spit of selected if something is selected
     *
     * @param string $string
     * @param bool $class [optional] Whether to include class attribute
     */
    public function select( $string, $class = false ) {
        if ( isset( $this->variables[$string] ) && true === $this->variables[$string] )
            echo ( $class ) ? ' class="selected"' : ' selected';
    }

    /**
     * Show Errors
     */
    public function show_errors() {
        if ( isset( $this->variables['errs'] ) )
            echo '<p class="red">', $this->variables['errs'], '</p>';
    }

    /**
     * Get Head
     */
    public function get_head() {
        // Do stuff
    }

    /**
     * Get Top
     */
    public function get_top() {
        // Do stuff
    }

    /**
     * Get Footer
     */
    public function get_footer() {
        // Do stuff
    }
}